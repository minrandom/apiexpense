<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Illuminate\Http\UploadedFile;
use App\Models\GoogleApiCredential;
require 'vendor/autoload.php';


class GoogleDriveService
{
    protected $client;
    protected $folderIds = [
        'profile' => '1W-f53bH0ZLe2JUyPC9Dpr-5UHBnhGRws', // Replace with actual folder ID
        'income' => '1kBkg97VeSct4o6mKXt02l2jQY0D_e6DT', // Replace with actual folder ID
        'expense' => '193Kjf-t_M-fDX22ipM_nOtQJlB-H8qRW', // Replace with actual folder ID
    ];

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Your Application Name');
        $this->client->setScopes(Google_Service_Drive::DRIVE_FILE);
        // Load Google API credentials from the database
        $this->loadCredentialsFromDB();
        // Load the access token from the database
        $this->loadAccessToken();
    }

    
    protected function loadCredentialsFromDB()
    {
        // Retrieve credentials from the database
        $credentials = GoogleApiCredential::first();

        if ($credentials && $credentials->credentials_json) {
            // Set the client configuration using the credentials stored in DB
            $this->client->setAuthConfig(json_decode($credentials->credentials_json, true));
        } else {
            throw new \Exception('Google API credentials not found in the database.');
        }
    }




    protected function loadAccessToken()
{
    // Retrieve the access token from the database
    $credentials = GoogleApiCredential::first();

    if ($credentials && $credentials->access_token) {
        $this->client->setAccessToken(json_decode($credentials->access_token, true));
    }

    // If token is expired, refresh and save it back to the database
    if ($this->client->isAccessTokenExpired()) {
        $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        $credentials->update(['access_token' => json_encode($this->client->getAccessToken())]);
    }
}

    public function uploadFile(UploadedFile $file, string $type)
    {
        if (!in_array($type, ['profile', 'income', 'expense'])) {
            throw new \Exception('Invalid folder type');
        }


        if ($this->client->isAccessTokenExpired()) {
            // Here, handle refreshing the token if expired
            $this->refreshAccessToken();
        }

        $driveService = new Google_Service_Drive($this->client);
        $fileMetadata = new \Google_Service_Drive_DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [$this->folderIds[$type]],
        ]);

        $content = file_get_contents($file->getPathname());
        $uploadedFile = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return [
            'file_id' => $uploadedFile->id,
            'file_url' => "https://drive.google.com/uc?id={$uploadedFile->id}",
        ];
    }


    public function deleteFile($fileId)
    {
        try {
            $driveService = new Google_Service_Drive($this->client);
            $driveService->files->delete($fileId);
        } catch (\Exception $e) {
            // Handle error (optional logging or return a meaningful message)
            throw new \Exception("Failed to delete file: " . $e->getMessage());
        }
    }


    protected function refreshAccessToken()
    {
        $refreshToken = $this->client->getRefreshToken();
        if ($refreshToken) {
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            // Save the new token back to the database
            GoogleApiCredential::first()->update(['access_token' => json_encode($this->client->getAccessToken())]);
        } else {
            throw new \Exception('No refresh token available.');
        }
    }
}

