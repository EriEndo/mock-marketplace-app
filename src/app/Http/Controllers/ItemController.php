<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        $query = Item::with('purchase');

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        if ($tab === 'mylist') {

            if (!Auth::check()) {
                $items = collect();
                return view('items.index', compact('items', 'tab'));
            }

            $query->whereHas('likes', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%");
        });

        $items = $query->get();

        return view('items.index', compact('items', 'tab'));
    }

    public function detail($item_id)
    {
        $item = Item::with(['purchase'])
            ->withCount('likes')
            ->findOrFail($item_id);
        $comments = $item->comments()->with('user')->get();

        return view('items.detail', compact('item', 'comments'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'brand'       => $request->brand,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $path,
            'condition_id' => $request->condition_id,
        ]);

        $item->categories()->sync($request->categories);

        return redirect('/');
    }
}
