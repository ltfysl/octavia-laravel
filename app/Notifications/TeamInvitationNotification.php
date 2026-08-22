<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Team $team,
        private readonly User $inviter,
        private readonly string $role,
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $locale = $notifiable->preferredLocale();

        return (new MailMessage)
            ->subject(__('teams.invitation.subject', ['team' => $this->team->name], $locale))
            ->greeting(__('teams.invitation.greeting', ['name' => $notifiable->name], $locale))
            ->line(__('teams.invitation.intro', [
                'inviter' => $this->inviter->name,
                'team' => $this->team->name,
                'role' => __('teams.roles.'.$this->role, [], $locale),
            ], $locale))
            ->line(__('teams.invitation.benefit', [], $locale))
            ->action(__('teams.invitation.action'), url('/dashboard'));
    }

    public function toArray(User $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'inviter_name' => $this->inviter->name,
            'role' => $this->role,
        ];
    }
}
