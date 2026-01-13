<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
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

        if ($item->purchase || $item->user_id === Auth::id()) {
            return redirect('/');
        }

        $profile = Auth::user()->profile;

        $address = session("purchase.$item_id.purchase_address") ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];

        return view('purchases.index', [
            'item' => $item,
            'address' => $address,
            'paymentMethod' => session("purchase.$item_id.payment_method"),
        ]);
    }

    public function purchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->user_id === Auth::id() || $item->purchase) {
            abort(403);
        }

        if (Purchase::where('item_id', $item_id)->exists()) {
            return redirect('/');
        }

        session([
            "purchase.$item_id.payment_method" => $request->payment_method,
        ]);

        $profile = Auth::user()->profile;
        $address = session("purchase.$item_id.purchase_address") ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];

        if ($request->payment_method === 'konbini') {
            Purchase::create([
                'user_id'        => Auth::id(),
                'item_id'        => $item_id,
                'payment_method' => 'konbini',
                'postal_code'    => $address['postal_code'],
                'address'        => $address['address'],
                'building'       => $address['building'],
            ]);

            session()->forget("purchase.$item_id");

            return redirect('/');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item_id], true),
            'cancel_url' => route('purchase.form', $item_id, true),
        ]);

        return redirect($session->url);
    }


    public function showAddressForm(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $profile = Auth::user()->profile;

        if ($request->filled('payment_method')) {
            session([
                "purchase.$item_id.payment_method" => $request->payment_method,
            ]);
        }

        return view('purchases.address', [
            'item' => $item,
            'address' => session("purchase.$item_id.purchase_address") ?? [
                'postal_code' => $profile->postal_code,
                'address'     => $profile->address,
                'building'    => $profile->building,
            ],
            'paymentMethod' => session("purchase.$item_id.payment_method"),
        ]);
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        session([
            "purchase.$item_id.purchase_address" => [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ],
            "purchase.$item_id.payment_method" => $request->payment_method,
        ]);

        return redirect()->route('purchase.form', $item_id);
    }


    public function success(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if (Purchase::where('item_id', $item_id)->exists()) {
            return redirect('/');
        }

        $profile = Auth::user()->profile;
        $address = session("purchase.$item_id.purchase_address") ?? [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ];
        $paymentMethod = session("purchase.$item_id.payment_method");

        Purchase::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item_id,
            'payment_method' => $paymentMethod,
            'postal_code'    => $address['postal_code'],
            'address'        => $address['address'],
            'building'       => $address['building'],
        ]);

        session()->forget("purchase.$item_id");

        return redirect('/');
    }
}
