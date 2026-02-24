<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::latest()->get();
        return view('admin.boards.index', compact('boards'));
    }

    public function create()
    {
        return view('admin.boards.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50|unique:boards,name']);
        Board::create(['name' => $request->name, 'is_active' => $request->is_active ?? 1, 'slug' => Str::slug($request->name . '-' . uniqid())]);
        return redirect()->route('admin.boards.index')->with('success', 'Board created.');
    }

    public function edit(Board $board)
    {
        return view('admin.boards.edit', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        $request->validate(['name' => 'required|string|max:50|unique:boards,name,' . $board->id]);
        $board->update(['name' => $request->name, 'is_active' => $request->is_active ?? 1]);
        return redirect()->route('admin.boards.index')->with('success', 'Board updated.');
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return redirect()->route('admin.boards.index')->with('success', 'Board removed.');
    }
}
