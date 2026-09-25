<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justificatifs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->text('reason');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_comment')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_original_name')->nullable();
            $table->string('file_mime_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificatifs');
    }
};
