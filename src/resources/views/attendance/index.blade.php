@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css')}}">
@endsection

@section('content')
<div class="index-page">
    <div class="index-page__inner">
        <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>

        <div class="current-datetime">
            <div id="current-date"></div>
            <div id="current-time"></div>
        </div>

        <div class="attendance-action">
            @if ($status['code'] === 'off')
            <form action="{{ route('attendance.clock_in') }}" method="POST">
                @csrf
                <button type="submit" class="attendance-btn">出勤</button>
            </form>

            @elseif ($status['code'] === 'working')
            <div class="attendance-action__btns">
                <form action="{{ route('attendance.clock_out') }}" method="POST">
                    @csrf
                    <button type="submit" class="attendance-btn">退勤</button>
                </form>

                <form action="{{ route('attendance.break_start') }}" method="POST">
                    @csrf
                    <button type="submit" class="attendance-btn attendance-btn--sub">休憩入</button>
                </form>
            </div>

            @elseif ($status['code'] === 'break')
            <form action="{{ route('attendance.break_end') }}" method="POST">
                @csrf
                <button type="submit" class="attendance-btn--sub">休憩戻</button>
            </form>

            @elseif ($status['code'] === 'done')
            <p class="attendance-message">お疲れ様でした。</p>
            @endif
        </div>

    </div>
</div>

<script>
    function updateDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const day = now.getDate();
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        const weekday = weekdays[now.getDay()];
        const date = `${year}年${month}月${day}日(${weekday})`;
        const time = now.toLocaleTimeString('ja-JP', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('current-date').textContent = date;
        document.getElementById('current-time').textContent = time;
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>

@endsection