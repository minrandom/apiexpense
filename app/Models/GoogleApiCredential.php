<?php
// app/Models/GoogleApiCredential.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleApiCredential extends Model
{
    protected $table = 'google_api_credentials';

    protected $fillable = [
        'credentials_json',
        'access_token',
    ];
}
