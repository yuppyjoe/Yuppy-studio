<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * All measurement columns are nullable on purpose: values must come from the
     * manufacturer's own spec sheet and be verified by an admin. Never invented.
     */
    public function up(): void
    {
        Schema::create('manufacturer_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_format_id')->constrained()->restrictOnDelete();
            $table->string('manufacturer_name');
            $table->string('product_code')->nullable();
            $table->decimal('trim_width_mm', 7, 2)->nullable();
            $table->decimal('trim_height_mm', 7, 2)->nullable();
            $table->decimal('bleed_mm', 5, 2)->nullable();
            $table->decimal('safe_margin_mm', 5, 2)->nullable();
            $table->decimal('gutter_mm', 5, 2)->nullable();
            $table->decimal('spine_width_mm', 6, 2)->nullable();
            $table->unsignedSmallInteger('dpi')->nullable();
            $table->unsignedSmallInteger('min_pages')->nullable();
            $table->unsignedSmallInteger('max_pages')->nullable();
            $table->unsignedSmallInteger('page_increment')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['manufacturer_name', 'book_format_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturer_templates');
    }
};
