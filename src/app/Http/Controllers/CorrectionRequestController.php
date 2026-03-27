<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequestStoreRequest;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\DB;


class CorrectionRequestController extends Controller
{
    public function store(CorrectionRequestStoreRequest $request, Attendance $attendance)
    {
        if ($attendance->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($request, $attendance) {
            $correctionRequest = CorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => auth()->id(),
                'requested_clock_in_at' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_in,
                'requested_clock_out_at' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_out,
                'note' => $request->note,
                'status' => 'pending',
            ]);

            foreach ($request->input('breaks', []) as $index => $break)  {
                $start = $break['start'] ?? null;
                $end = $break['end'] ?? null;

                if (!$start && !$end) {
                    continue;
                }

                $correctionRequest->requestBreaks()->create([
                     'break_no' => $index + 1,
                    'break_start_at' => $start ? $attendance->work_date->format('Y-m-d') . ' ' . $start : null,
                    'break_end_at' => $end ? $attendance->work_date->format('Y-m-d') . ' ' . $end : null,
                ]);
            }
        });

        return redirect()->route('stamp_correction_request.list');
    }

public function index(Request $request)
{

 $status = $request->input('status', 'pending');

    $correctionRequests = CorrectionRequest::with(['user', 'attendance'])
      ->where('status', $status)   
    ->latest()
        ->get();

    return view('stamp_correction_request.list', compact('correctionRequests', 'status'));
}

    }
