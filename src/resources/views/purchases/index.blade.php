@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/index.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <form action="{{ route('purchase.execute', $item->id) }}" method="POST">
        @csrf
        <div class="purchase-content">

            <div class="purchase-left">
                <div class="purchase-item">
                    <div class="purchase-item__image">
                        <img src="{{ $item->image_url }}" alt="商品画像">
                    </div>
                    <div class="purchase-item__info">
                        <p class="purchase-item__name">{{ $item->name }}</p>
                        <p class="purchase-item__price"><span>¥</span>{{ number_format($item->price)}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="purchase-section">
                    <div class="purchase-section__title">支払い方法</div>

                    <div class="select-wrapper">
                        <select name="payment_method" class="purchase-select" id="payment_method">
                            <option value="" disabled hidden {{ old('payment_method') ? '' : 'selected' }}>
                                選択してください
                            </option>
                            <option value="convenience">コンビニ払い</option>
                            <option value="card">カード払い</option>
                        </select>
                    </div>
                    @error('payment_method')
                    <p class="purchase-error">{{ $message }}</p>
                    @enderror
                </div>
                <hr>
                <div class="purchase-section">
                    <div class="purchase-address-header">
                        <div class="purchase-section__title">配送先</div>
                        <a href="{{ route('purchase.address.form', $item->id) }}" class="purchase-address-link">
                            変更する
                        </a>
                    </div>
                    <p class="purchase-address">
                        〒{{ $address['postal_code'] }}<br>
                        {{ $address['address'] }}{{ $address['building'] }}
                    </p>
                </div>
                <hr>
            </div>

            <div class="purchase-right">
                <div class="purchase-summary">
                    <div class="purchase-summary-row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="purchase-summary-row">
                        <span>支払い方法</span>
                        <span id="payment_method_display">未選択</span>
                    </div>
                </div>

                <button type="submit" class="purchase-button">
                    購入する
                </button>

            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('payment_method');
        const display = document.getElementById('payment_method_display');

        const labels = {
            convenience: 'コンビニ払い',
            card: 'カード払い',
        };

        select.addEventListener('change', function() {
            const value = select.value;
            display.textContent = labels[value] ?? '未選択';
        });
    });
</script>
@endsection