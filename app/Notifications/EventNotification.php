<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $action;

    /**
     * Create a new notification instance.
     * 
     * @param $event
     * @param string $action (created, updated, deleted)
     */
    public function __construct($event, $action)
    {
        $this->event = $event;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = "";
        switch ($this->action) {
            case 'created':
                $message = "A new event '{$this->event->title}' has been scheduled.";
                break;
            case 'updated':
                $message = "Event '{$this->event->title}' has been updated.";
                break;
            case 'deleted':
                $message = "Event '{$this->event->title}' has been cancelled or removed.";
                break;
        }

        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'action' => $this->action,
            'message' => $message,
            'image' => $this->event->image,
            'start_date' => $this->event->start_date?->format('Y-m-d H:i:s'),
            'city' => $this->event->city,
        ];
    }
}
