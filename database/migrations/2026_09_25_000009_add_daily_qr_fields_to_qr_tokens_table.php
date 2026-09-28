<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_tokens', function (Blueprint $table): void {
            $table->date('valid_on')->nullable()->after('context')->index();
            $table->foreignId('created_by')->nullable()->after('site_id')->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('expires_at')->index();
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->index(['site_id', 'valid_on', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('qr_tokens', function (Blueprint $table): void {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['site_id', 'valid_on', 'is_active']);
            $table->dropColumn(['valid_on', 'created_by', 'is_active', 'deactivated_at']);
        });
    }
};
