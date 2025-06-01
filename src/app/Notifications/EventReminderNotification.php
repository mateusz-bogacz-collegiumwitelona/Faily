<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Ride;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $ride;

    public function __construct(Event $event, ?Ride $ride = null)
    {
        $this->event = $event;
        $this->ride = $ride;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Reminder of the event: ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A reminder that the event you signed up for will be held tomorrow:')
            ->line('Event: ' . $this->event->title)
            ->line('Date: ' . $this->event->date->format('d.m.Y H:i'))
            ->line('Location: ' . $this->event->location_name);

        if ($this->ride) {
            $mailMessage->line('Information about the ride:');

            if ($this->ride->vehicle_description) {
                $mailMessage->line('Vehicle: ' . $this->ride->vehicle_description);
            }

            if ($this->ride->meeting_location_name) {
                $mailMessage->line('Meeting Location: ' . $this->ride->meeting_location_name);
            }

            if ($this->ride->driver) {
                $mailMessage->line('Driver: ' . $this->ride->driver->name);

                if ($this->ride->driver->phone) {
                    $mailMessage->line('Driver contact: ' . $this->ride->driver->phone);
                }
            }
        }

        return $mailMessage
            ->line("Can't take part? Remember to cancel your registration.")
            ->action('View event details', url('/events/' . $this->event->id))
            ->salutation('Best regards,')
            ->salutation('Team Faily');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_date' => $this->event->date->toISOString(),
            'ride_id' => $this->ride?->id,
            'has_ride' => !is_null($this->ride),
        ];
    }
}
