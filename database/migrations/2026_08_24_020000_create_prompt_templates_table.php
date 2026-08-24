<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 32)->default('general')->index();
            $table->string('difficulty', 16)->default('medium');
            $table->string('tags')->default('');
            $table->longText('body');
            $table->text('example_use_cases')->nullable();
            $table->string('recommended_benchmark_type', 32)->nullable();
            $table->boolean('is_custom')->default(false);
            $table->timestamps();

            $table->index(['category', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_templates');
    }
};
