<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('qr_token_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->timestamp('occurred_at')->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->boolean('within_geofence')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'occurred_at']);
            $table->index(['site_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
