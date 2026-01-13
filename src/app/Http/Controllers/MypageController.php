<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;



class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'sell');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        switch ($page) {
            case 'buy':
                $items = Item::whereHas('purchase', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                    ->with('purchase')->get();
                break;

            case 'sell':
            default:
                $items = $user->items()->with('purchase')->get();
                break;
        }

        return view('mypage.index', compact('items', 'page'));
    }

    public function editProfile(Request $request)
    {
        $profile = Auth::user()->profile;
        $from = $request->query('from', 'mypage');
        return view('mypage.profile', compact('profile', 'from'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $profile = Auth::user()->profile;

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->username = $request->username;
        $profile->postal_code = $request->postal_code;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();

        if ($request->from === 'first') {
            return redirect()->route('items.index');
        }

        return redirect()->route('mypage.index');
    }
}
