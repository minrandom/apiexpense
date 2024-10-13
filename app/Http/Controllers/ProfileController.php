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
        // Validate incoming request
        $request->validate([
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'job' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // file is optional
        ]);
    
        // Retrieve the user's existing profile
        $profile = Profile::where('user_id', Auth::id())->first();
    
        // Check if file is uploaded
        $image = null;
        if ($request->hasFile('file')) {
            // If there is an existing profile picture, delete it from Google Drive
            if ($profile && $profile->profile_pic_url) {
                // Extract the Google Drive file ID from the profile_pic_url
                $existingFileId = $this->getFileIdFromUrl($profile->profile_pic_url);
                if ($existingFileId) {
                    // Call GoogleDriveService to delete the existing file
                    $this->googleDriveService->deleteFile($existingFileId);
                }
            }
    
            // Upload the new file to Google Drive and get the URL
            $result = $this->googleDriveService->uploadFile($request->file('file'), 'profile');
    
            // Handle file upload failure
            if (!$result || !isset($result['file_url'])) {
                return response()->json(['message' => 'File upload failed'], 500);
            }
    
            // Save the uploaded file URL
            $image = $result['file_url'];
        }
    
        // Update or create the profile
        $profile = Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'birthday' => $request->birthday,
                'gender' => $request->gender,
                'job' => $request->job,
                'profile_pic_url' => $image ?? ($profile->profile_pic_url ?? null),
            ]
        );
    
        // Return updated profile as response
        return response()->json($profile, Response::HTTP_OK);
    }
    
    /**
     * Helper function to extract the file ID from the Google Drive URL.
     * The URL format is: https://drive.google.com/uc?id={file_id}
     */
    protected function getFileIdFromUrl($url)
    {
        $matches = [];
        preg_match('/uc\?id=([^&]+)/', $url, $matches);
        return $matches[1] ?? null;
    }
    
}
