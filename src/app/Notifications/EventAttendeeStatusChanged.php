<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EventAttendee;
use App\Models\Event;
use Carbon\Carbon;

class EventAttendeeStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $eventAttendee;
    protected $event;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(EventAttendee $eventAttendee, Event $event, $oldStatus)
    {
        $this->eventAttendee = $eventAttendee;
        $this->event = $event;
        $this->oldStatus = $oldStatus;
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
        if ($this->eventAttendee->status === 'accepted') {
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
            'event_attendee_id' => $this->eventAttendee->id,
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->eventAttendee->status,
            'attendees_count' => $this->eventAttendee->attendees_count,
        ];
    }

    protected function buildAcceptedEmail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your event attendance has been approved!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your request to attend the event "' . $this->event->title . '" has been accepted by the organizer.')
            ->line('Event details:')
            ->line('• Title: ' . $this->event->title)
            ->line('• Date: ' . Carbon::parse($this->event->date)->format('d.m.Y H:i'))
            ->line('• Location: ' . ($this->event->location_name ?? $this->event->location ?? 'TBA'))
            ->when($this->eventAttendee->attendees_count > 1, function ($message) {
                return $message->line('• Number of attendees: ' . $this->eventAttendee->attendees_count);
            })
            ->when($this->event->user && $this->event->user->phone, function ($message) {
                return $message->line('• Organizer contact: ' . $this->event->user->phone);
            })
            ->action('View event details', url('/events/' . $this->event->id))
            ->line('We remind you to be punctual and inform the organizer of any changes in your plans.')
            ->salutation('Thank you for using our application!')
            ->salutation('Team Faily');
    }

    protected function buildRejectedEmail($notifiable)
    {
        return (new MailMessage)
            ->subject('Information about your event attendance request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Unfortunately, your request to attend the event "' . $this->event->title . '" has been rejected by the organizer.')
            ->when($this->eventAttendee->message, function ($message) {
                return $message->line('Organizer\'s note: ' . $this->eventAttendee->message);
            })
            ->line('Don\'t worry - there are many other interesting events waiting for you!')
            ->action('Browse other events', url('/events'))
            ->salutation('Have a great day!')
            ->salutation('Team Faily');
    }
}
