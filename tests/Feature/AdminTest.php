<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('forbids non-admins from admin pages', function () {
    $user = User::factory()->create();

    $this->get('/admin')->assertRedirect('/login');
    $this->actingAs($user)->get('/admin')->assertForbidden();
    $this->actingAs($user)->get('/admin/users')->assertForbidden();
});

it('allows admins to view the dashboard and user list', function () {
    User::factory()->count(3)->create();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($admin)->get('/admin/users?q=admin')->assertOk();
});

it('toggles admin status but protects self-demotion', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();

    $this->actingAs($admin)->post("/admin/users/{$user->id}/toggle-admin");
    expect($user->fresh()->is_admin)->toBeTrue();

    $this->actingAs($admin)->post("/admin/users/{$admin->id}/toggle-admin")->assertSessionHas('error');
    expect($admin->fresh()->is_admin)->toBeTrue();
});

it('deletes users but not themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();

    $this->actingAs($admin)->delete("/admin/users/{$user->id}");
    expect(User::find($user->id))->toBeNull();

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error');
    expect(User::find($admin->id))->not->toBeNull();
});
