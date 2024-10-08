<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',           // Foreign key to the User
        'birthday',          // User's birthday
        'gender',            // User's gender
        'job',               // User's job title
        'profile_pic_url',   // URL for the profile picture
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
