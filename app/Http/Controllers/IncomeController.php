<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleDriveService;
use Carbon\Carbon;


class IncomeController extends Controller
{
    //
    public function index()
    {
        $incomes = Income::where('user_id', Auth::id())->with(['category', 'paymentMethod'])->get();
        return response()->json($incomes, 200);
    }

    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }


    

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string',
            'datetime' => 'required|date',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $result = $this->googleDriveService->uploadFile($request->file('file'), 'income');

        // Save expense details to the database (you'll need to implement this part)
        if (!$result || !isset($result['file_url'])) {
            return response()->json([
                'message' => 'File upload failed',
            ], 500);
        }
        // Return success response
        $image = $result['file_url'];



        $income = Income::create([
            'user_id' => Auth::id(),
            'value' => $request->value,
            'category_id' => $request->category_id,
            'payment_method_id' => $request->payment_method_id,
            'notes' => $request->notes,
            'datetime' => Carbon::now(),
            'receipt_url' => $image,
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
