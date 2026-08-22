<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 8)->default('en')->after('remember_token');
            $table->boolean('is_admin')->default(false)->after('locale');
            $table->timestamp('onboarded_at')->nullable()->after('is_admin');
        });

        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('visibility', 16)->default('private')->index();
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('content');
            $table->string('changelog')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['prompt_id', 'version']);
        });

        Schema::table('prompts', function (Blueprint $table) {
            $table->foreign('current_version_id')
                ->references('id')->on('prompt_versions')
                ->nullOnDelete();
        });

        Schema::create('benchmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 32)->default('general')->index();
            $table->string('visibility', 16)->default('private')->index();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('benchmark_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benchmark_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('input');
            $table->decimal('weight', 5, 2)->default(1.0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['benchmark_id', 'position']);
        });

        Schema::create('benchmark_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benchmark_case_id')->constrained()->cascadeOnDelete();
            $table->string('type', 24);
            $table->string('label');
            $table->json('config');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['benchmark_case_id', 'position']);
        });

        Schema::create('benchmark_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('benchmark_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('benchmark_collections')->cascadeOnDelete();
            $table->foreignId('benchmark_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['collection_id', 'benchmark_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_collection_items');
        Schema::dropIfExists('benchmark_collections');
        Schema::dropIfExists('benchmark_criteria');
        Schema::dropIfExists('benchmark_cases');
        Schema::dropIfExists('benchmarks');
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
        });
        Schema::dropIfExists('prompt_versions');
        Schema::dropIfExists('prompts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['locale', 'is_admin', 'onboarded_at']);
        });
    }
};
