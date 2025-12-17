@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchases/address.css') }}">
@endsection

@section('content')
<div class="form__container">

    <div class="form__wrapper">
        <h2 class="form__title">住所の変更</h2>

        <div class="form__card">
            <form
                action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="form__form">
                @csrf
                @method('PATCH')

                <div class="form__group">
                    <label for="postal_code" class="form__label">郵便番号</label>
                    <input
                        type="text"
                        name="postal_code"
                        id="postal_code"
                        class="form__input"
                        value="{{ old('postal_code', $address['postal_code']) }}">
                    @error('postal_code')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="address" class="form__label">住所</label>
                    <input type="text" name="address" id="address" class="form__input" value="{{ old('address', $address['address']) }}">

                    @error('address')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="building" class="form__label">建物名</label>
                    <input type="text" name="building" id="building" class="form__input" value="{{ old('building', $address['building']) }}">
                </div>

                <button type="submit" class="form__btn">更新する</button>
            </form>
        </div>
    </div>
</div>
@endsection