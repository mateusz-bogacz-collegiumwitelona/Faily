<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ride;
use App\Models\RideRequest;

class RideRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rideRequest;
    protected $ride;

    /**
     * Create a new notification instance.
     */
    public function __construct(RideRequest $rideRequest, Ride $ride)
    {
        $this->rideRequest = $rideRequest;
        $this->ride = $ride;
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
        if ($this->rideRequest->status === 'accepted') {
            return $this->buildAcceptedEmail($notifiable);
        } else {
            return $this->buildRejectedEmail($notifiable);
        }
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
            'status' => $this->rideRequest->status,
            'event_id' => $this->ride->event_id,
            'event_title' => $this->ride->event ? $this->ride->event->title : null,
            'driver_name' => $this->ride->driver ? $this->ride->driver->name : null,
        ];
    }

    protected function buildAcceptedEmail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your ride request has been approved!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your request for a ride to the event "' . ($this->ride->event ? $this->ride->event->title : 'Unknown event') . '" has been accepted by the driver ' . ($this->ride->driver ? $this->ride->driver->name : 'Unknown driver') . '.')
            ->line('Ride details:')
            ->line('• Event date: ' . ($this->ride->event ? $this->ride->event->date->format('d.m.Y H:i') : 'Unknown date'))
            ->line('• Meeting place: ' . $this->ride->meeting_location_name)
            ->line('• Vehicle: ' . $this->ride->vehicle_description)
            ->when($this->ride->driver && $this->ride->driver->phone, function ($message) {
                return $message->line('• Driver contact: ' . $this->ride->driver->phone);
            })
            ->action('View event details', url('/events/' . $this->ride->event_id))
            ->line('Please be punctual and inform the driver of any changes in your plans.')
            ->salutation('Have a great trip!')
            ->salutation('Team Faily');
    }

    protected function buildRejectedEmail($notifiable)
    {
        return (new MailMessage)
            ->subject('Information about your ride request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Unfortunately, your request for a ride to the event "' . ($this->ride->event ? $this->ride->event->title : 'Unknown event') . '" has been rejected by the driver.')
            ->when($this->rideRequest->message, function ($message) {
                return $message->line('Your message was: ' . $this->rideRequest->message);
            })
            ->line('Don\'t worry - you can look for other rides or create your own!')
            ->action('Browse other rides', url('/events/' . $this->ride->event_id))
            ->salutation('Have a great day!')
            ->salutation('Team Faily');
    }
}
