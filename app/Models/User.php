<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification($this));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($this, $token));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'notify_run_completed_mail',
    ];

    /** Mail notifications use the user's stored locale. */
    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'onboarded_at' => 'datetime',
            'notify_run_completed_mail' => 'boolean',
        ];
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    public function benchmarks(): HasMany
    {
        return $this->hasMany(Benchmark::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(Run::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(BenchmarkCollection::class);
    }

    public function marketplaceItems(): HasMany
    {
        return $this->hasMany(MarketplaceItem::class, 'publisher_id');
    }

    public function marketplaceInstalls(): HasMany
    {
        return $this->hasMany(MarketplaceInstall::class);
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function configPresets(): HasMany
    {
        return $this->hasMany(ConfigPreset::class);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * IDs of users who share at least one team with this user
     * (including the user themselves and all team owners).
     */
    public function teamMateIds(): Collection
    {
        $teamIds = Team::query()
            ->where(function ($q) {
                $q->where('owner_id', $this->id)
                    ->orWhereHas('members', fn ($m) => $m->where('user_id', $this->id));
            })
            ->pluck('id');

        if ($teamIds->isEmpty()) {
            return collect([$this->id]);
        }

        $memberIds = TeamMember::whereIn('team_id', $teamIds)->pluck('user_id');
        $ownerIds = Team::whereIn('id', $teamIds)->pluck('owner_id');

        return $memberIds->merge($ownerIds)->push($this->id)->unique();
    }
}
