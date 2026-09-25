<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomalies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pointage_id')->nullable()->constrained('pointages')->nullOnDelete();
            $table->string('type')->index();
            $table->text('description');
            $table->timestamp('detected_at')->index();
            $table->boolean('resolved')->default(false);
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('anomaly_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('unread')->index();
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('anomalies');
    }
};
