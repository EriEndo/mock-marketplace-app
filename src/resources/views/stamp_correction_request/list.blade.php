@extends('layouts.list')

@section('title', '申請一覧')



@section('table')

<div class="status-tag">
    <a href="{{ route('stamp_correction_request.list', ['status' => 'pending']) }}"
       class="items-tab {{ request('status', 'pending') === 'pending' ? 'active' : '' }}">
        承認待ち
    </a>

    <a href="{{ route('stamp_correction_request.list', ['status' => 'approved']) }}"
       class="items-tab {{ request('status') === 'approved' ? 'active' : '' }}">
        承認済み
    </a>
</div>

<table class="correction-request-list_table">
    <thead>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($correctionRequests as $correctionRequest)
            <tr>
                <td>{{ $correctionRequest->status_label }}</td>
                <td>{{ $correctionRequest->user->name }}</td>
                <td>{{ optional($correctionRequest->attendance->work_date)->format('Y/m/d') }}</td>
                <td>{{ $correctionRequest->note }}</td>
                <td>{{ optional($correctionRequest->created_at)->format('Y/m/d') }}</td>
                <td>
                    <a href="{{ route('attendance.detail', $correctionRequest->attendance_id) }}">
    詳細
</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection