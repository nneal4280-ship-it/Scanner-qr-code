<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->json('content');
            $table->timestamp('generated_at');
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};
