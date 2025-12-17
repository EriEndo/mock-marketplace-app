<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    public function showPurchaseForm($item_id)
    {
        $item = Item::findOrFail($item_id);

        // SOLD の場合は一覧に戻る
        if (Purchase::where('item_id', $item_id)->exists()) {
            return redirect()->back();
        }

        $profile = Auth::user()->profile;

        $address = session('purchase_address') ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];

        return view('purchases.index', compact('item', 'address'));
    }

    public function purchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if (Purchase::where('item_id', $item_id)->exists()) {
            return redirect()->back();
        }

        $profile = Auth::user()->profile;
        $address = session('purchase_address') ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];

        Purchase::create([
            'user_id'     => Auth::id(),
            'item_id'     => $item_id,
            'payment_method' => $request->payment_method,
            'postal_code' => $address['postal_code'],
            'address'     => $address['address'],
            'building'    => $address['building'],
        ]);

        session()->forget('purchase_address');

        return redirect('/');
    }

    public function showAddressForm($item_id)
    {
        $item = Item::findOrFail($item_id);
        $profile = Auth::user()->profile;

        $address = session('purchase_address') ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];

        return view('purchases.address', compact('item', 'address'));
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        session([
            'purchase_address' => [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        ]);

        return redirect()->route('purchase.form', $item_id);
    }
}
