<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('report_status_history', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32)->index();
            $table->text('note')->nullable();
            $table->boolean('visible_to_resident')->default(true);
            $table->timestamps();
            $table->index(['report_id', 'created_at']);
        });
        Schema::create('report_updates', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->text('body');
            $table->boolean('visible_to_resident')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('report_resolutions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->unique()->constrained('reports')->cascadeOnDelete();
            $table->foreignId('resolved_by')->constrained('users')->restrictOnDelete();
            $table->text('notes');
            $table->string('evidence_path')->nullable();
            $table->unsignedTinyInteger('resident_rating')->nullable();
            $table->text('resident_feedback')->nullable();
            $table->timestamp('resolved_at');
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('subject_type');
            $table->string('subject_id', 64);
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['actor_id', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('audit_logs'); Schema::dropIfExists('notifications'); Schema::dropIfExists('report_resolutions'); Schema::dropIfExists('report_updates'); Schema::dropIfExists('report_status_history'); }
};
