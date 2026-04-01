<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Models\Admin;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\CorrectionRequest;


class AdminController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(Request $request, LoginResponse $loginResponse)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'login_error' => ['管理者のログイン情報が正しくありません'],
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return $loginResponse->toResponse($request);
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

public function list(Request $request)
{
    $targetDate = $request->query('date')
        ? Carbon::createFromFormat('Y-m-d', $request->query('date'))->startOfDay()
        : now()->startOfDay();

    $prevDate = $targetDate->copy()->subDay()->format('Y-m-d');
    $nextDate = $targetDate->copy()->addDay()->format('Y-m-d');

    $attendances = Attendance::with(['user', 'breakTimes'])
        ->whereDate('work_date', $targetDate->toDateString())
        ->get();

foreach ($attendances as $attendance) {
        $breakTotalSeconds = $attendance->breakTimes->sum(
            fn($break) => $this->getBreakSeconds($break)
        );

        $workTotalSeconds = ($attendance->clock_in_at && $attendance->clock_out_at)
            ? $this->timeToSeconds($attendance->clock_out_at)
                - $this->timeToSeconds($attendance->clock_in_at)
                - $breakTotalSeconds
            : 0;

        $attendance->break_time = $this->formatSecondsToHoursMinutes($breakTotalSeconds);
        $attendance->work_time = $attendance->clock_out_at
            ? $this->formatSecondsToHoursMinutes($workTotalSeconds)
            : '';
    }

    return view('admin.attendance.list', compact(
        'targetDate',
        'prevDate',
        'nextDate',
        'attendances'
    ));
}

private function getBreakSeconds($break): int
{
    if (!$break->break_start_at || !$break->break_end_at) {
        return 0;
    }

    return $this->timeToSeconds($break->break_end_at)
        - $this->timeToSeconds($break->break_start_at);
}

private function timeToSeconds(Carbon $time): int
{
    return $time->hour * 3600 + $time->minute * 60 + $time->second;
}

private function formatSecondsToHoursMinutes(int $seconds): string
{
    return sprintf('%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60));
}

public function detail($id)
{
    $attendance = Attendance::with(['breakTimes', 'user'])
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $breakTotalSeconds = $attendance->breakTimes->sum(function ($break) {
        if (!$break->break_start_at || !$break->break_end_at) {
            return 0;
        }

        return $this->timeToSeconds($break->break_end_at)
            - $this->timeToSeconds($break->break_start_at);
    });

    $workTotalSeconds = 0;

    if ($attendance->clock_in_at && $attendance->clock_out_at) {
        $workTotalSeconds = $this->timeToSeconds($attendance->clock_out_at)
            - $this->timeToSeconds($attendance->clock_in_at)
            - $breakTotalSeconds;
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


public function update(Request $request, $id)
{
    $attendance = Attendance::with(['user', 'breakTimes'])->findOrFail($id);

    $request->validate([
        'clock_in' => ['nullable', 'date_format:H:i'],
        'clock_out' => ['nullable', 'date_format:H:i', 'after:clock_in'],
        'breaks.*.start' => ['nullable', 'date_format:H:i'],
        'breaks.*.end' => ['nullable', 'date_format:H:i'],
        'note' => ['nullable', 'string', 'max:255'],
    ]);

    foreach ($request->input('breaks', []) as $index => $break) {
        if (!empty($break['start']) && !empty($break['end']) && $break['end'] <= $break['start']) {
            return back()
                ->withErrors(["breaks.$index.end" => '休憩終了時間は休憩開始時間より後にしてください。'])
                ->withInput();
        }
    }

    $attendance->clock_in_at = $request->clock_in ?: null;
    $attendance->clock_out_at = $request->clock_out ?: null;
    $attendance->note = $request->note;
    $attendance->save();

    $existingBreaks = $attendance->breakTimes->values();

    foreach ($request->input('breaks', []) as $index => $break) {
        $start = $break['start'] ?? null;
        $end = $break['end'] ?? null;

        $hasInput = !empty($start) || !empty($end);

        if (isset($existingBreaks[$index])) {
            if ($hasInput) {
                $existingBreaks[$index]->break_start_at = $start ?: null;
                $existingBreaks[$index]->break_end_at = $end ?: null;
                $existingBreaks[$index]->save();
            } else {
                $existingBreaks[$index]->delete();
            }
        } else {
            if ($hasInput) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $start ?: null,
                    'break_end_at' => $end ?: null,
                ]);
            }
        }
    }

    return redirect()->route('admin.attendance.detail', $attendance->id);
}
}
