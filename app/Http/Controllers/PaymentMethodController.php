<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index()
    {
        // Get both default payment methods and user-specific methods
        $paymentMethods = PaymentMethod::where('is_default', true)
            ->orWhere('user_id', Auth::id())
            ->get();
        return response()->json($paymentMethods, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return response()->json($paymentMethod, 201);
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string',
        ]);

        $paymentMethod->update($request->only('name'));

        return response()->json($paymentMethod, 200);
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($paymentMethod->is_default) {
            return response()->json(['error' => 'Default payment methods cannot be deleted'], 403);
        }

        $paymentMethod->delete();

        return response()->json(null, 204);
    }
}
