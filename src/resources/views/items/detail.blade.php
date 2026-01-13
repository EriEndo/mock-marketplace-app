@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/detail.css') }}">
@endsection

@section('content')
<div class="item-detail">

    <div class="item-detail__main">

        <div class="item-detail__image-wrapper">
            <img src="{{ $item->image_url }}" alt="商品画像" class="item-detail__image">

            @if ($item->purchase)
            <div class="item-card__sold">SOLD</div>
            @endif
        </div>

        <div class="item-detail__info">
            <div class="item-detail__header">
                <h2 class="item-detail__name">{{ $item->name }}</h2>
                <p class="item-detail__brand">{{ $item->brand}}</p>

                <p class="item-detail__price">
                    <span class="item-detail__price-mark">¥</span>{{ number_format($item->price) }}
                    <span class="item-detail__price-tax">(税込)</span>
                </p>

                <div class="item-detail__meta">
                    <div class="item-detail__meta-item">

                        <form action="{{ route('item.like', $item->id) }}" method="post">
                            @csrf
                            <button class="like-button">
                                <div class="item-detail__icon-box">
                                    <img src="{{ asset($item->likes->contains('user_id', Auth::id())
                                            ? 'images/like_logo-pink.png'
                                            : 'images/like_logo-default.png'
                                            ) }}" alt="いいね" class="item-detail__icon-image">
                                </div>
                            </button>
                        </form>

                        <span class="item-detail__meta-count">
                            {{ $item->likes_count }}
                        </span>
                    </div>

                    <div class="item-detail__meta-item">
                        <div class="item-detail__icon-box">
                            <img
                                src="{{ asset('images/comment_logo.png') }}"
                                alt="コメント"
                                class="item-detail__icon-image">
                        </div>
                        <span class="item-detail__meta-count">{{ $comments->count() }}</span>
                    </div>
                </div>

                <div class="item-detail__purchase">
                    @if ($item->purchase)
                    <button class="item-detail__purchase-button item-detail__purchase-button--disabled" disabled>
                        SOLD
                    </button>
                    @elseif ($item->user_id === Auth::id())
                    <button class="item-detail__purchase-button item-detail__purchase-button--disabled" disabled>
                        出品した商品は購入できません
                    </button>
                    @else
                    <a href="{{ route('purchase.form', $item->id) }}" class="item-detail__purchase-button">
                        購入手続きへ
                    </a>
                    @endif
                </div>
            </div>

            <div class="item-detail__section">
                <h3 class="item-detail__section-title">商品説明</h3>
                <p class="item-detail__description"> {!! nl2br(e($item->description)) !!}</p>
            </div>

            <div class="item-detail__section">
                <h3 class="item-detail__section-title">商品の情報</h3>

                <dl class="item-detail__attributes-list">
                    <div class="item-detail__attributes-row">
                        <dt>カテゴリー</dt>
                        <dd>
                            @foreach ($item->categories as $category)
                            <span class="item-detail__tag">{{ $category->name }}</span>
                            @endforeach
                        </dd>
                    </div>
                    <div class="item-detail__attributes-row">
                        <dt>商品の状態</dt>
                        <dd>{{ $item->condition->name }}</dd>
                    </div>
                </dl>
            </div>

            <div class="item-detail__section item-detail__section--comments">
                <h3 class="item-detail__section-title">
                    コメント({{ $comments->count() }})
                </h3>

                <div class="item-detail__comments">
                    @foreach ($comments as $comment)
                    <div class="item-comment">
                        <div class="item-comment__header">
                            <div class="item-comment__avatar">
                                @if (!empty($comment->user->profile->profile_image))
                                <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="プロフィール画像" class="item-comment__avatar-image">
                                @endif
                            </div>
                            <span class="item-comment__user-name"> {{ $comment->user->profile->username }}</span>
                        </div>
                        <p class="item-comment__body">
                            {{ $comment->content }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="item-detail__comment-form">
                    <h4 class="item-detail__comment-form-title">商品へのコメント</h4>

                    <form action="/item/{{ $item->id }}/comment" method="post">
                        @csrf
                        <textarea name="content" class="item-detail__comment-textarea" rows="4">{{ old('content') }}</textarea>
                        @error('content')
                        <p class="item-detail__error-message">
                            {{ $message }}
                        </p>
                        @enderror

                        <button type="submit" class="item-detail__comment-submit">コメントを送信する</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection