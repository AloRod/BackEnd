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
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
           $table->foreignId('associated_profiles')->constrained('restricted_users')->onDelete('cascade');          
            $table->timestamps();
        });

     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
