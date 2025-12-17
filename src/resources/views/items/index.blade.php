@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<div class="items-container">

    <div class="items-tabs">
        <a href="/?tab=recommend&keyword={{ request('keyword') }}"
            class="items-tab {{ $tab !== 'mylist' ? 'active' : '' }}">
            おすすめ
        </a>

        <a href="/?tab=mylist&keyword={{ request('keyword') }}"
            class="items-tab {{ $tab === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-list">
        @foreach ($items as $item)
        @if ($item->purchase)
        {{-- SOLD：リンク不可 --}}
        <div class="item-card item-card--sold">
            <div class="item-card__image-wrapper">
                <img src="{{ $item->image_url }}" alt="商品画像" class="item-card__image">
                <div class="item-card__sold">Sold</div>
            </div>
            <div class="item-card__name">{{ $item->name }}</div>
        </div>
        @else
        {{-- 通常商品：リンク有効 --}}
        <a href="/item/{{ $item->id }}" class="item-card">
            <div class="item-card__image-wrapper">
                <img src="{{ $item->image_url }}" alt="商品画像" class="item-card__image">
            </div>
            <div class="item-card__name">{{ $item->name }}</div>
        </a>
        @endif
        @endforeach
    </div>
</div>
@endsection