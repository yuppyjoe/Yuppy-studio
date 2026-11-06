<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Phase 1 stub: ownership, branding and reference links only. */
    public function up(): void
    {
        Schema::create('photobook_projects', function (Blueprint $table) {
            $table->id();
            $table->enum('owner_type', ['yuppy', 'photographer', 'customer'])->index();
            $table->foreignId('photographer_profile_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('branding_mode', ['yuppy', 'photographer', 'unbranded'])->default('unbranded');
            $table->foreignId('occasion_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('book_format_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('status', ['draft', 'in_design', 'in_review', 'approved', 'archived'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['photographer_profile_id', 'status']);
            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photobook_projects');
    }
};
