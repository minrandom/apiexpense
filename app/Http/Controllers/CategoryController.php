<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // Get both default categories and user-specific categories
        $categories = Category::where('is_default', true)
            ->orWhere('user_id', Auth::id())
            ->get();
        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:income,expense',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:income,expense',
        ]);

        $category->update($request->only('name', 'type'));

        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->is_default) {
            return response()->json(['error' => 'Default categories cannot be deleted'], 403);
        }

        $category->delete();

        return response()->json( ['messages'=>'delete success'], 204);
    }
}

