<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FarmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $farm;
    protected $action;

    /**
     * Create a new notification instance.
     * 
     * @param $farm
     * @param string $action (created, updated, deleted)
     */
    public function __construct($farm, $action)
    {
        $this->farm = $farm;
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
                $message = "A new farm '{$this->farm->name}' has been added.";
                break;
            case 'updated':
                $message = "Farm '{$this->farm->name}' has been updated.";
                break;
            case 'deleted':
                $message = "Farm '{$this->farm->name}' has been removed.";
                break;
        }

        return [
            'farm_id' => $this->farm->id,
            'farm_name' => $this->farm->name,
            'action' => $this->action,
            'message' => $message,
            'thumbnail' => $this->farm->thumbnail,
            'city' => $this->farm->city,
        ];
    }
}
