<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenditureController extends Controller
{
    public function index()
    {
        $expenditures = Expenditure::latest()->get();
        return view('admin.expenditures.index', compact('expenditures'));
    }

    public function create()
    {
        return view('admin.expenditures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        Expenditure::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug('exp-' . uniqid()),
        ]);

        return redirect()->route('admin.expenditures.index')->with('success', 'Expenditure recorded.');
    }

    public function edit(Expenditure $expenditure)
    {
        return view('admin.expenditures.edit', compact('expenditure'));
    }

    public function update(Request $request, Expenditure $expenditure)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);
        $expenditure->update($request->only('title', 'amount', 'expense_date', 'description', 'is_active'));
        return redirect()->route('admin.expenditures.index')->with('success', 'Expenditure updated.');
    }

    public function destroy(Expenditure $expenditure)
    {
        $expenditure->delete();
        return redirect()->route('admin.expenditures.index')->with('success', 'Record removed.');
    }
}
