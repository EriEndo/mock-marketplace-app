<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\LikeController;

/*
|--------------------------------------------------------------------------
| 認証不要
|--------------------------------------------------------------------------
*/

// 商品一覧
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'detail'])->name('items.detail');

// 検索機能
Route::get('/search', [ItemController::class, 'search'])->name('items.search');


/*
|--------------------------------------------------------------------------
| 認証
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // コメント投稿
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

    // いいね押下
    Route::post('/item/{item_id}/like', [LikeController::class, 'toggle'])->name('item.like');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.form');

    // 商品購入実行
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.execute');

    // 住所変更ページ
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'showAddressForm'])->name('purchase.address.form');

    // 住所更新
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 出品画面
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');

    // 出品処理
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    /*
    |--------------------------------------------------------------------------
    | マイページ
    |--------------------------------------------------------------------------
    */

    // プロフィール画面（通常 / 購入 / 出品）
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

    // プロフィール編集画面
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');

    // プロフィール更新
    Route::patch('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.profile.update');
});
