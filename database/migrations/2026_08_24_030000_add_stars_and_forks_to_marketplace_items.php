<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->unsignedBigInteger('stars_count')->default(0)->after('downloads');
            $table->unsignedBigInteger('forks_count')->default(0)->after('stars_count');
        });

        Schema::create('marketplace_item_stars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'marketplace_item_id']);
            $table->index(['marketplace_item_id']);
        });

        Schema::create('marketplace_item_forks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forked_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->foreignId('forked_benchmark_id')->nullable()->constrained('benchmarks')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'marketplace_item_id']);
            $table->index(['marketplace_item_id']);
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropColumn(['stars_count', 'forks_count']);
        });

        Schema::dropIfExists('marketplace_item_stars');
        Schema::dropIfExists('marketplace_item_forks');
    }
};
