<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Http\Response;
use App\Services\GoogleDriveService;

class ProfileController extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    // Get user profile
    public function getProfile()
    {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($profile, Response::HTTP_OK);
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        return $request;
        // Validate incoming request
        $request->validate([
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'job' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // file is optional
        ]);
      

        // Check if file is uploaded
        $image = null;
        if ($request->hasFile('file')) {
            // Upload file to Google Drive and get the URL
            $result = $this->googleDriveService->uploadFile($request->file('file'), 'profile');

            // Handle file upload failure
            if (!$result || !isset($result['file_url'])) {
                return response()->json(['message' => 'File upload failed'], 500);
            }

            // Save the uploaded file URL
            $image = $result['file_url'];
        }

       
        // Retrieve the user's existing profile or create a new one if it doesn't exist
        $profile = Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'birthday' => $request->birthday,
                'gender' => $request->gender,
                'job' => $request->job,
                'profile_pic_url' => $image ?? Auth::user()->profile->profile_pic_url ?? null,
            ]
        );

        // Return updated profile as response
        return response()->json($profile, Response::HTTP_OK);
    }
}
