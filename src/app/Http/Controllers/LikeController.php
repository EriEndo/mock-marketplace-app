<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($item_id)
    {
        $item = Item::findOrFail($item_id);
        $existingLike = Like::where('item_id', $item->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($existingLike) {
            $existingLike->delete();
        } else {
            Like::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
            ]);
        }
        return redirect()->back();
    }
}
