<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * photographer_profile_id NULL means this is the single Yuppy house brand
     * (is_yuppy = true). Photographer brands are one-per-studio.
     */
    public function up(): void
    {
        Schema::create('branding_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_profile_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_yuppy')->default(false)->index();
            $table->string('brand_name');
            $table->string('tagline')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_mime', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_profiles');
    }
};
