<?php

use Illuminate\Support\Facades\Route;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\GoogleController;

Route::get('/callback', [GoogleController::class, 'handleGoogleCallback']);



// Route::get('/callback', function (Request $request, GoogleDriveService $googleDriveService) {
//     $code = $request->get('code');
//     if ($code) {
//         $token = $googleDriveService->authenticate($code);
//         // Store the refresh token in your .env or database
//         file_put_contents(base_path('.env'), "GOOGLE_REFRESH_TOKEN={$token['refresh_token']}\n", FILE_APPEND);
//         return 'Google Drive connected successfully!';
//     }
// });



