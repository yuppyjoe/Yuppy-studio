<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photographer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('studio_name');
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('city', 120)->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->index('studio_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photographer_profiles');
    }
};
