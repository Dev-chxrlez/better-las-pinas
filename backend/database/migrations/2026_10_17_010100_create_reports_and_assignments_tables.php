<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('reference', 32)->unique();
            $table->foreignId('reporter_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('category_id')->constrained('report_categories')->restrictOnDelete();
            $table->foreignUuid('barangay_id')->constrained('barangays')->restrictOnDelete();
            $table->string('title', 120);
            $table->text('description');
            $table->text('location_text');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 32)->default('submitted')->index();
            $table->timestamp('submitted_at')->index();
            $table->timestamps();
            $table->index(['reporter_id', 'submitted_at']);
            $table->index(['barangay_id', 'status']);
        });
        Schema::create('report_attachments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('disk', 40)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->timestamps();
            $table->index(['report_id', 'created_at']);
        });
        Schema::create('report_assignments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->foreignUuid('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('assigned_at');
            $table->timestamps();
            $table->index(['assigned_to_user_id', 'is_active']);
            $table->index(['report_id', 'is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('report_assignments'); Schema::dropIfExists('report_attachments'); Schema::dropIfExists('reports'); }
};
