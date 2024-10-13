<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleDriveService;
use Exception;

class GoogleController extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function uploadFile(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Adjust as needed
            'type' => 'required|in:profile,income,expense', // Ensure type is one of the allowed values
        ]);

        try {
            // Upload the file
            $result = $this->googleDriveService->uploadFile($request->file('file'), $request->input('type'));

            // Return success response with file information
            return response()->json([
                'message' => 'File uploaded successfully',
                'file_id' => $result['file_id'],
                'file_url' => $result['file_url'],
            ], 200);
        } catch (Exception $e) {
            // Handle errors
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

