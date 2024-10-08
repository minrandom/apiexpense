<?php

// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    // Create or Update user profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'job' => 'nullable|string|max:255',
            'profile_pic_url' => 'nullable|url',
        ]);

        $profile = Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only(['birthday', 'gender', 'job', 'profile_pic_url'])
        );

        return response()->json($profile, Response::HTTP_OK);
    }
}
