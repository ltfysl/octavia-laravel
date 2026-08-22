<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('benchmark_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('benchmark_collections')->nullOnDelete();
            $table->string('name');
            $table->string('mode', 16)->default('optimize');
            $table->string('status', 16)->default('pending')->index();
            $table->string('provider', 32)->default('mock');
            $table->string('model', 64)->nullable();
            $table->unsignedInteger('max_steps')->default(8);
            $table->decimal('target_score', 4, 3)->default(0.950);
            $table->decimal('best_score', 4, 3)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['prompt_id', 'created_at']);
        });

        Schema::create('run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->string('phase', 16);
            $table->longText('prompt_content');
            $table->decimal('score', 4, 3)->nullable();
            $table->string('mutation_type', 16)->nullable();
            $table->text('rationale')->nullable();
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->unique(['run_id', 'number']);
        });

        Schema::create('case_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('benchmark_case_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 4, 3)->default(0);
            $table->boolean('passed')->default(false);
            $table->longText('output')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('run_step_id');
        });

        Schema::create('criterion_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criterion_id')->nullable()->constrained('benchmark_criteria')->nullOnDelete();
            $table->string('criterion_label');
            $table->boolean('passed')->default(false);
            $table->json('detail')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('case_result_id');
        });

        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type', 16)->index();
            $table->foreignId('prompt_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('benchmark_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->json('snapshot')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->unsignedBigInteger('downloads')->default(0);
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['item_type', 'published_at']);
            $table->unique(['item_type', 'prompt_id']);
            $table->unique(['item_type', 'benchmark_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_items');
        Schema::dropIfExists('criterion_results');
        Schema::dropIfExists('case_results');
        Schema::dropIfExists('run_steps');
        Schema::dropIfExists('runs');
    }
};
