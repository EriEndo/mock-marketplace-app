@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="form__container">
    <div class="form__wrapper">

        <h2 class="form__title">プロフィール設定</h2>

        <div class="form__card">
            <form
                action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data" class="form__form">
                @csrf
                @method('PATCH')

                <div class="profile-image-section">
                    <div class="profile-image">
                        <img
                            id="profilePreview"
                            src="{{ $profile && $profile->profile_image
            ? asset('storage/' . $profile->profile_image)
            : '' }}"
                            class="{{ empty($profile?->profile_image) ? 'is-hidden' : '' }}"
                            alt="プロフィール画像">
                    </div>

                    <label class="profile-image-button">
                        画像を選択する
                        <input
                            type="file"
                            id="profileImageInput"
                            name="profile_image"
                            accept="image/png,image/jpeg"
                            hidden>
                    </label>
                </div>

                <div class="form__group">
                    <label class="form__label">ユーザー名</label>
                    <input
                        type="text"
                        name="username"
                        class="form__input"
                        value="{{ old('username', $profile->username ?? '') }}">
                    <p class="form__error">
                        @error('username')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="form__group">
                    <label class="form__label">郵便番号</label>
                    <input
                        type="text"
                        name="postal_code"
                        class="form__input"
                        value="{{ old('postal_code', $profile->postal_code ?? '') }}">
                    <p class="form__error">
                        @error('postal_code')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="form__group">
                    <label class="form__label">住所</label>
                    <input
                        type="text"
                        name="address"
                        class="form__input"
                        value="{{ old('address', $profile->address ?? '') }}">
                    <p class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="form__group">
                    <label class="form__label">建物名</label>
                    <input
                        type="text"
                        name="building"
                        class="form__input"
                        value="{{ old('building', $profile->building ?? '') }}">
                </div>

                <button type="submit" class="form__btn">
                    更新する
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('profileImageInput');
        const preview = document.getElementById('profilePreview');

        if (!input || !preview) {
            console.error('preview or input not found');
            return;
        }

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('jpeg または png の画像を選択してください');
                input.value = '';
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.classList.remove('is-hidden');
        });
    });
</script>
@endsection