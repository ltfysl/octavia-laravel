<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_token_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->constrained('personal_access_tokens')->cascadeOnDelete();
            $table->string('method', 8);
            $table->string('path', 512);
            $table->unsignedSmallInteger('status')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->unsignedInteger('tokens_used')->default(0);
            $table->string('ip', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['token_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_token_uses');
    }
};
