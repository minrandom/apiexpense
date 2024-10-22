<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // For file storage
use App\Models\Profile;
use Illuminate\Http\Response;


class ProfileController extends Controller
{
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
        $imageUrl = null;
        if ($request->hasFile('file')) {
            // If there is an existing profile picture, delete it from the server
            if ($profile && $profile->profile_pic_url) {
                // Get the filename from the URL and delete it
                $existingFileName = basename($profile->profile_pic_url);
                Storage::disk('public')->delete('profile_pictures/' . $existingFileName);
            }

            // Upload the new file to the server
            $uploadedFile = $request->file('file');
            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('profile_pictures', $fileName, 'public'); // Save to 'storage/app/public/profile_pictures/'

            // Save the uploaded file URL
            $imageUrl = Storage::url($filePath); // This will create a public URL
        }

        // Update or create the profile
        $profile = Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'birthday' => $request->birthday,
                'gender' => $request->gender,
                'job' => $request->job,
                'profile_pic_url' => $imageUrl ?? ($profile->profile_pic_url ?? null),
            ]
        );

        // Return updated profile as response
        return response()->json($profile, Response::HTTP_OK);
    }
    
    /**
     * Helper function to extract the file ID from the Google Drive URL.
     * The URL format is: https://drive.google.com/uc?id={file_id}
     */

    
}
