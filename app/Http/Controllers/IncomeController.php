<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // For file storage
use Carbon\Carbon;



class IncomeController extends Controller
{
    //
    public function index()
    {
        $incomes = Income::where('user_id', Auth::id())->with(['category', 'paymentMethod'])->get();
        return response()->json($incomes, 200);
    }    

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $profile = Profile::where('user_id', Auth::id())->first();

        $imageUrl = null;
        if ($request->hasFile('file')) {
            // Upload the new file to the server
            $uploadedFile = $request->file('file');
            $fileName = time() . '_income_'.$profile->id.".".$uploadedFile->getClientOriginalExtension() ;
            $filePath = $uploadedFile->storeAs('incomes', $fileName, 'public'); // Save to 'storage/app/public/profile_pictures/'

            // Save the uploaded file URL
            $imageUrl = Storage::url($filePath); // This will create a public URL
        }


        $income = Income::create([
            'user_id' => Auth::id(),
            'value' => $request->value,
            'category_id' => $request->category_id,
            'payment_method_id' => $request->payment_method_id,
            'notes' => $request->notes,
            'datetime' => Carbon::now(),
            'receipt_url' => $imageUrl,
        ]);

        return response()->json($income, 201);
    }

    public function show($id)
    {
        // Fetch the income by ID, ensure it belongs to the authenticated user
        $income = Income::with('category', 'paymentMethod')
                    ->where('user_id', Auth::id())
                    ->findOrFail($id);
    
        return response()->json($income, 200);
    }
    
    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        
        $request->validate([
            'value' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',
            'payment_method_id' => 'sometimes|required|exists:payment_methods,id',
            'notes' => 'nullable|string',
            'datetime' => 'sometimes|required|date',
            'receipt_url' => 'nullable|string',
        ]);

        $income->update($request->only('value', 'category_id', 'payment_method_id', 'notes', 'datetime', 'receipt_url'));

        return response()->json($income, 200);
    }

    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();

        return response()->json(null, 204);
    }
}
