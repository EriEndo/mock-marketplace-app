@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="form__container">
    <div class="form__wrapper">

        <h2 class="form__title">商品の出品</h2>

        <div class="form__card">
            <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form__group">
                    <label class="form__label">商品画像</label>
                    <div class="image-upload">
                        <img
                            id="image-preview"
                            class="image-preview"
                            style="display: none;"
                            alt="画像プレビュー">
                        <label class="image-upload__button">
                            画像を選択する
                            <input
                                type="file"
                                name="image"
                                id="image-input"
                                accept="image/jpeg,image/png"
                                hidden>
                        </label>
                    </div>
                    @error('image')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>


                <div class="sell-section">
                    <h3 class="sell-subtitle">商品の詳細</h3>
                    <div class="form__group">
                        <label class="form__label">カテゴリー</label>
                        <div class="category-list">
                            @foreach ($categories as $category)
                            <label class="category-item">
                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id }}"
                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('categories')
                        <p class="form__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form__group">
                        <label class="form__label">商品の状態</label>

                        <div class="select-wrapper">
                            <select name="condition_id" class="condition-select">
                                <option value="" disabled hidden {{ old('condition_id') ? '' : 'selected' }}>
                                    選択してください
                                </option>
                                @foreach ($conditions as $condition)
                                <option
                                    value="{{ $condition->id }}"
                                    {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                    {{ $condition->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('condition_id')
                        <p class="form__error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sell-section">
                    <h3 class="sell-subtitle">商品名と説明</h3>

                    <div class="form__group">
                        <label class="form__label">商品名</label>
                        <input
                            type="text"
                            name="name"
                            class="form__input"
                            value="{{ old('name') }}">
                        @error('name')
                        <p class="form__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form__group">
                        <label class="form__label">ブランド名</label>
                        <input
                            type="text"
                            name="brand"
                            class="form__input"
                            value="{{ old('brand') }}">
                    </div>

                    <div class="form__group">
                        <label class="form__label">商品の説明</label>
                        <textarea name="description" class="sell-textarea">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="form__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form__group">
                        <label class="form__label">販売価格</label>
                        <div class="price-input">
                            <span>¥</span>
                            <input
                                type="text"
                                name="price"
                                value="{{ old('price') }}">
                        </div>
                        @error('price')
                        <p class="form__error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button class="form__btn">出品する</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('image-input');
        const preview = document.getElementById('image-preview');
        const container = document.querySelector('.image-upload');

        if (!input || !container) return;

        input.addEventListener('change', () => {
            const file = input.files[0];

            if (!file || !file.type.startsWith('image/')) {
                preview.style.display = 'none';
                preview.src = '';
                container.classList.remove('has-image');
                return;
            }

            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            container.classList.add('has-image');
        });
    });
</script>

@endsection