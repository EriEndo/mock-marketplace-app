@extends('layouts.list')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css')}}">
<link rel="stylesheet" href="{{ asset('css/layouts/list.css') }}">
@endsection

@section('title', '勤怠詳細')

@section('table')

<form action="{{ auth('admin')->check() ? route('admin.attendance.update', $attendance->id) : route('correction_request.store', $attendance->id) }}" method="POST">
    @csrf
    @if (auth('admin')->check())
        @method('PATCH')
    @endif

    <table class="attendance-detail_table">
        <tr class="detail__row">
            <th class="detail__label">名前</th>
            <td class="detail__data">{{ $attendance->user->name }}</td>
        </tr>
        <tr class="detail__row">
            <th class="detail__label">日付</th>
            <td class="detail__data">
                <div class="detail__date-group">
                    <span>{{ $attendance->work_date->format('Y年') }}</span>
                    <span>{{ $attendance->work_date->format('n月j日') }}</span>
                </div>
            </td>

        </tr>
        <tr class="detail__row">
            <th class="detail__label">出勤・退勤</th>
            <td class="detail__data">
                <div class="detail__time-group">
                    <input type="time" name="clock_in"
                        value="{{ old('clock_in', $attendance->clock_in_at?->format('H:i')) }}">
                    <span>～</span>
                    <input type="time" name="clock_out"
                        value="{{ old('clock_in', $attendance->clock_out_at?->format('H:i')) }}">
                </div>
                @error('clock_in')
                <p class="error-message">{{ $message }}</p>
                @enderror

                @error('clock_out')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
        </tr>

        @foreach ($attendance->breakTimes as $index => $break)
        <tr class="detail__row">
            <th class="detail__label">
                {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
            </th>
            <td class="detail__data">
                <div class="time-group">
                    <input type="time" name="breaks[{{ $index }}][start]" value="{{ old("breaks.$index.start", $break->break_start_at?->format('H:i')) }}">
                    <span>～</span>
                    <input type="time" name="breaks[{{ $index }}][end]" value="{{ old("breaks.$index.end", $break->break_end_at?->format('H:i')) }}">

                </div>
            </td>
        </tr>
        @endforeach

        <tr class="detail__row">
            <th class="detail__label">
                {{ $attendance->breakTimes->count() === 0 ? '休憩' : '休憩' . ($attendance->breakTimes->count() + 1) }}
            </th>
            <td class="detail__data">
                <div class="time-group">
                    <input type="time" name="breaks[{{ $attendance->breakTimes->count() }}][start]" value="{{ old('breaks.' . $attendance->breakTimes->count() . '.start') }}">
                    <span>～</span>
                    <input type="time" name="breaks[{{ $attendance->breakTimes->count() }}][end]" value="{{ old('breaks.' . $attendance->breakTimes->count() . '.end') }}">
                </div>
            </td>
        </tr>

        <tr class="detail__row">
            <th class="detail__label">備考</th>
            <td class="detail__data">
                <textarea name="note" id="note">{{ old('note') }}</textarea>
                @error('note')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
        </tr>
    </table>
    
    @if (auth('admin')->check())
        <button type="submit" class="detail__submit-btn">修正</button>
    @else
        @if ($pendingRequest)
            <p class="pending-message">
                ※承認待ちのため修正はできません。
            </p>
        @else
            <button type="submit" class="detail__submit-btn">修正</button>
        @endif
    @endif

</form>

@endsection