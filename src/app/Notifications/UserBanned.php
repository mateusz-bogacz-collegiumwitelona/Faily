<?php

namespace App\Notifications;

use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBanned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $lastReport;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, ?Report $lastReport = null)
    {
        $this->user = $user;
        $this->lastReport = $lastReport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Your account has been suspended')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We regret to inform you that your account has been suspended due to violation of community guidelines.')
            ->line('Your account has received multiple reports from other users.');

        if ($this->lastReport && $this->lastReport->reason) {
            $mailMessage->line('The most recent report included the following reason:')
                ->line('Reason: ' . $this->lastReport->reason);
        }

        return $mailMessage
            ->line('If you believe this action was taken in error, please contact our support team.')
            ->line('You can reach us at: whatever')
            ->line('We will review your case and respond within 48 hours.')
            ->salutation('Best regards,')
            ->salutation('Team Faily');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'report_id' => $this->lastReport ? $this->lastReport->id : null,
            'banned_at' => now()->toDateTimeString(),
            'reason' => $this->lastReport ? $this->lastReport->reason : null,
        ];
    }
}
