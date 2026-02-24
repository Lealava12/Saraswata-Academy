<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::with('board')->latest()->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $boards = Board::where('is_active', 1)->get();
        return view('admin.classes.create', compact('boards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'board_id' => 'required|exists:boards,id',
            'monthly_fee' => 'required|numeric|min:0',
        ]);
        Classes::create([
            'name' => $request->name,
            'board_id' => $request->board_id,
            'monthly_fee' => $request->monthly_fee,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);
        return redirect()->route('admin.classes.index')->with('success', 'Class created.');
    }

    public function edit(Classes $class)
    {
        $boards = Board::where('is_active', 1)->get();
        return view('admin.classes.edit', compact('class', 'boards'));
    }

    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'board_id' => 'required|exists:boards,id',
            'monthly_fee' => 'required|numeric|min:0',
        ]);
        $class->update($request->only('name', 'board_id', 'monthly_fee', 'is_active'));
        return redirect()->route('admin.classes.index')->with('success', 'Class updated.');
    }

    public function destroy(Classes $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class removed.');
    }
}
