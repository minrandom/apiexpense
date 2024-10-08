<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    // Fetch all expenses for the authenticated user
    public function index()
    {
        $expenses = Expense::with('category', 'paymentMethod')
            ->where('user_id', Auth::id())
            ->get();
        return response()->json($expenses, 200);
    }

    // Store a new expense for the authenticated user
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return response()->json($expense, 201);
    }

    public function show($id)
    {
        // Fetch the expense by ID, ensure it belongs to the authenticated user
        $expense = Expense::with('category', 'paymentMethod')
                    ->where('user_id', Auth::id())
                    ->findOrFail($id);
    
        return response()->json($expense, 200);
    }
    


    // Update an existing expense
    public function update(Request $request, $id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'payment_method_id' => 'sometimes|exists:payment_methods,id',
            'amount' => 'sometimes|numeric',
            'date' => 'sometimes|date',
            'description' => 'nullable|string',
        ]);

        $expense->update($request->only(['category_id', 'payment_method_id', 'amount', 'date', 'description']));

        return response()->json($expense, 200);
    }

    // Delete an expense
    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return response()->json(null, 204);
    }
}