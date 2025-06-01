<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ride;
use App\Models\RideRequest;
use App\Models\User;

class NewRideRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rideRequest;
    protected $ride;
    protected $passenger;

    /**
     * Create a new notification instance.
     */
    public function __construct(RideRequest $rideRequest, Ride $ride, User $passenger)
    {
        $this->rideRequest = $rideRequest;
        $this->ride = $ride;
        $this->passenger = $passenger;
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
        return (new MailMessage)
            ->subject('New ride request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new ride request from ' . $this->passenger->name . '.')
            ->line('Event: ' . ($this->ride->event ? $this->ride->event->title : 'Unknown event'))
            ->line('Date: ' . ($this->ride->event ? $this->ride->event->date->format('d.m.Y H:i') : 'Unknown date'))
            ->when($this->rideRequest->message, function ($message) {
                return $message->line('Passenger message: ' . $this->rideRequest->message);
            })
            ->when($this->passenger->phone, function ($message) {
                return $message->line('Passenger contact: ' . $this->passenger->phone);
            })
            ->action('Manage ride requests', url('/ride-requests?ride_id=' . $this->ride->id))
            ->line('Please respond to this request as soon as possible.')
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
            'ride_request_id' => $this->rideRequest->id,
            'ride_id' => $this->ride->id,
            'passenger_id' => $this->passenger->id,
            'passenger_name' => $this->passenger->name,
            'event_id' => $this->ride->event_id,
            'event_title' => $this->ride->event ? $this->ride->event->title : null,
            'message' => $this->rideRequest->message,
        ];
    }
}
