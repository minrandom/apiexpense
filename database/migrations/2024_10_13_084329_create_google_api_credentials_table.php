<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_api_credentials', function (Blueprint $table) {
            $table->id();
            $table->longText('credentials_json')->nullable(); // Store credentials.json content
            $table->longText('access_token')->nullable(); // Store the access token
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_api_credentials');
    }
};
