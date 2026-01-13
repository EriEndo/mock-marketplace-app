<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'detail'])->name('items.detail');
Route::get('/search', [ItemController::class, 'search'])->name('items.search');

Route::middleware('auth')->group(function () {
    Route::get('/verify-guide', function () {
        return view('auth.verify-guide');
    })->middleware('auth')->name('verify.guide');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('mypage.profile.edit', ['from' => 'first']);
    })->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/item/{item_id}/like', [LikeController::class, 'toggle'])->name('item.like');
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.form');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.execute');
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'showAddressForm'])->name('purchase.address.form');
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->middleware('verified')->name('mypage.profile.edit');
    Route::patch('/mypage/profile', [MypageController::class, 'updateProfile'])->middleware('verified')->name('mypage.profile.update');
});
