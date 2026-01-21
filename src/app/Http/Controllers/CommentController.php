<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        return redirect()->back();
    }
}
