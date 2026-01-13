<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header-left">
                <h1 class="header__heading">
                    <a href="/">
                        <img src="{{ asset('images/header_logo.png') }}" alt="coachtech ヘッダーロゴ" class="header__logo">
                    </a>
                </h1>
            </div>

            <div class="header-center">
                @if (!request()->is('login') && !request()->is('register'))
                <form action="/search" method="get" class="header-search-form">
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                    <input type="text" name="keyword" class="header-search-input" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                </form>
                @endif
            </div>

            <div class=" header-right">
                @if (!request()->is('login') && !request()->is('register'))
                <nav class="header-nav">
                    <ul class="header-nav-list">
                        <li class="header-nav-link">
                            @auth
                            <form action="/logout" method="post">
                                @csrf
                                <button type="submit" class="logout-btn">ログアウト</button>
                            </form>
                            @endauth

                            @guest
                            <a href="/login">ログイン</a>
                            @endguest
                        </li>
                        <li class="header-nav-link"><a href="/mypage">マイページ</a></li>
                        <li class="header-nav-button"><a href="/sell">出品</a></li>
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