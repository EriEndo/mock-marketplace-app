<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header-left">
                <h1 class="header__heading">
                    <a href="/attendance">
                        <img src="{{ asset('images/header_logo.png') }}" alt="coachtech ヘッダーロゴ" class="header__logo">
                    </a>
                </h1>
            </div>


            <div class=" header-right">
                @if (!request()->is('login') && !request()->is('register'))
                <nav class="header-nav">
                    <ul class="header-nav-list">
                        <li class="header-nav-link">
                        <li class="header-nav-link"><a href="/attendance">勤怠</a></li>
                        <li class="header-nav-link"><a href="/attendance/list">勤怠一覧</a></li>
                        <li class="header-nav-link"><a href="/stamp_correction_request/list">申請</a></li>

                        <li class="header-nav-link"><a href="/attendance/list">今月の出勤一覧</a></li>
                        <li class="header-nav-link"><a href="/stamp_correction_request/list">申請一覧</a></li>

                        <form action="/logout" method="post">
                            @csrf
                            <button type="submit" class="logout-btn">ログアウト</button>
                        </form>
                        </li>
                    </ul>
                </nav>
                @endif
            </div>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>