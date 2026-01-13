@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-guide.css') }}">
@endsection

@section('content')
<div class="verify-guide">

    @if (session('status') === 'verification-link-sent')
    <p class="verify-guide__notice">
        認証メールを送信しました。メールをご確認ください。
    </p>
    @endif

    <p class="verify-guide__text">
        登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-guide__button"> 認証はこちらから </button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-guide__resend"> 認証メールを再送する </button>
    </form>

</div>
@endsection