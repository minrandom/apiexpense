<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Illuminate\Http\UploadedFile;

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
        $this->client->setAuthConfig(base_path('app/credentials.json')); // Path to your credentials.json file
        $this->loadAccessToken();
    }

    

    protected function loadAccessToken()
    {
        $tokenFilePath = base_path('app/drivetoken.json');
        
        if (file_exists($tokenFilePath)) {
            $accessToken = json_decode(file_get_contents($tokenFilePath), true);
            $this->client->setAccessToken($accessToken);
        }
    }

    protected function saveAccessToken($accessToken)
    {
        $tokenFilePath = storage_path('app/drivetoken.json');
        file_put_contents($tokenFilePath, json_encode($accessToken));
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

    protected function refreshAccessToken()
    {
        $refreshToken = $this->client->getRefreshToken();
        if ($refreshToken) {
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            $this->saveAccessToken($this->client->getAccessToken());
        } else {
            throw new \Exception('No refresh token available.');
        }
    }
}

