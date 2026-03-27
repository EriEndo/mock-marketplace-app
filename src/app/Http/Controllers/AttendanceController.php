<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\CorrectionRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        $status = $this->determineStatus($attendance);

        return view('attendance.index', compact('attendance', 'status'));
    }

    private function determineStatus($attendance)
    {
        if (!$attendance) {
            return [
                'code' => 'off',
                'label' => '勤務外',
                'class' => 'badge--off',
            ];
        }

        if ($attendance->clock_out_at) {
            return [
                'code' => 'done',
                'label' => '退勤済',
                'class' => 'badge--done',
            ];
        }

        $isBreaking = $attendance->breakTimes
            ->whereNull('break_end_at')
            ->isNotEmpty();

        if ($isBreaking) {
            return [
                'code' => 'break',
                'label' => '休憩中',
                'class' => 'badge--break',
            ];
        }

        if ($attendance->clock_in_at) {
            return [
                'code' => 'working',
                'label' => '出勤中',
                'class' => 'badge--working',
            ];
        }

        return [
            'code' => 'off',
            'label' => '勤務外',
            'class' => 'badge--off',
        ];
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        if ($attendance) {
            return back();
        }

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today,
            'clock_in_at' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->clock_out_at) {
            return back()->with('error', 'すでに退勤済みです');
        }

        $isBreaking = $attendance->breakTimes()
            ->whereNull('break_end_at')
            ->exists();

        if ($isBreaking) {
            return back()->with('error', '休憩中は退勤できません');
        }

        $attendance->update([
            'clock_out_at' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function breakStart()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->clock_out_at) {
            return back()->with('error', 'すでに退勤済みです');
        }

        $isBreaking = $attendance->breakTimes()
            ->whereNull('break_end_at')
            ->exists();

        if ($isBreaking) {
            return back()->with('error', 'すでに休憩中です');
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function breakEnd()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        $break = $attendance->breakTimes()
            ->whereNull('break_end_at')
            ->latest()
            ->first();

        if (!$break) {
            return back()->with('error', '休憩中ではありません');
        }

        $break->update([
            'break_end_at' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        $targetMonth = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $prevMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->work_date->format('Y-m-d');
            });


        $days = [];

        foreach (\Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth) as $date) {


            $attendance = $attendances->get($date->format('Y-m-d'));
            $hasAttendance = (bool) $attendance;

            $breakTotalSeconds = 0;
            if ($attendance) {
                foreach ($attendance->breakTimes as $break) {
                    if ($break->break_start_at && $break->break_end_at) {
                        $breakTotalSeconds += $break->break_start_at
                            ->diffInSeconds($break->break_end_at);
                    }
                }
            }


            $workTotalSeconds = 0;

            if ($attendance && $attendance->clock_in_at && $attendance->clock_out_at) {
                $workTotalSeconds = $attendance->clock_in_at
                    ->diffInSeconds($attendance->clock_out_at) - $breakTotalSeconds;
            }


            $days[] = [
                'row_class' => $hasAttendance ? 'worked-day' : 'empty-day',

                'date_label' => $date->isoFormat('M/D(ddd)'),

                'clock_in' => $attendance?->clock_in_at?->format('H:i') ?? '',
                'clock_out' => $attendance?->clock_out_at?->format('H:i') ?? '',

                'break_time' => $hasAttendance
                    ? gmdate('H:i', $breakTotalSeconds)
                    : '',


                'work_time' => ($hasAttendance && $attendance?->clock_out_at)
                    ? gmdate('H:i', $workTotalSeconds)
                    : '',


                'detail_url' => $attendance
                    ? route('attendance.detail', $attendance->id)
                    : '',
            ];
        }

        return view('attendance.list', compact(
            'targetMonth',
            'prevMonth',
            'nextMonth',
            'days'
        ));
    }

    public function detail($id)
    {
        $attendance = Attendance::with(['breakTimes', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $breakTotalSeconds = 0;

        foreach ($attendance->breakTimes as $break) {
            if ($break->break_start_at && $break->break_end_at) {
                $breakTotalSeconds += $break->break_start_at
                    ->diffInSeconds($break->break_end_at);
            }
        }

        $workTotalSeconds = 0;

        if ($attendance->clock_in_at && $attendance->clock_out_at) {
            $workTotalSeconds = $attendance->clock_in_at
                ->diffInSeconds($attendance->clock_out_at) - $breakTotalSeconds;
        }

        $pendingRequest = CorrectionRequest::where('attendance_id', $id)
    ->where('status', 'pending')
    ->exists();

        return view('attendance.detail', compact(
            'attendance',
            'breakTotalSeconds',
            'workTotalSeconds',
            'pendingRequest'
        ));
    }
}
