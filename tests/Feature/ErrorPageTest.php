<?php

use App\Models\User;

it('renders branded 404 page for guests', function () {
    $this->get('/nonexistent-page')
        ->assertStatus(404)
        ->assertSee('Octavia')
        ->assertSee('Page not found');
});

it('renders branded 403 page for authenticated users without permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/admin')->assertStatus(403);
});
