@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')
<div class="mypage-container">

    <div class="mypage-profile">
        <div class="mypage-profile-left">
            <div class="mypage-profile-image">
                @if(Auth::user()->profile?->profile_image)
                <img src="{{ asset('storage/' . Auth::user()->profile->profile_image) }}" alt="プロフィール画像">
                @else
                <div class="profile-placeholder"></div>
                @endif
            </div>

            <div class="mypage-username">
                {{ Auth::user()->profile->username ?? 'ユーザー名' }}
            </div>
        </div>

        <a href="{{ route('mypage.profile.edit') }}" class="profile-edit-button">
            プロフィールを編集
        </a>
    </div>

    <div class="mypage-tabs  mypage-tabs--sticky">
        <a href="{{ route('mypage.index', ['page' => 'sell']) }}"
            class="mypage-tab {{ $page === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('mypage.index', ['page' => 'buy']) }}"
            class="mypage-tab {{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    <div class="mypage-items">
        @forelse($items as $item)
        <a href="{{ route('items.detail', $item->id) }}" class="mypage-item">
            <div class="mypage-item-image">
                <img src="{{ $item->image_url }}" alt="商品画像">

                @if($item->purchase)
                <div class="item-sold-badge">SOLD</div>
                @endif
            </div>
            <p class="mypage-item-name">{{ $item->name }}</p>
        </a>
        @empty
        <p class="mypage-empty">
            {{ $page === 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}
        </p>
        @endforelse
    </div>

</div>
@endsection