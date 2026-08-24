<?php

use App\Models\PromptTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists prompt templates', function (): void {
    PromptTemplate::factory()->count(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/prompts/templates/list');

    $response->assertOk()->assertJsonCount(3);
});

it('filters prompt templates by category', function (): void {
    PromptTemplate::factory()->create(['category' => 'marketing']);
    PromptTemplate::factory()->create(['category' => 'coding']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/prompts/templates/list?category=marketing');

    $response->assertOk()->assertJsonCount(1);
});

it('shows a single prompt template', function (): void {
    $template = PromptTemplate::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)->getJson("/prompts/templates/{$template->id}")
        ->assertOk()
        ->assertJsonFragment(['name' => $template->name]);
});
