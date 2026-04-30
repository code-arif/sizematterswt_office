<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RancheNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ranche;
    protected $action;

    /**
     * Create a new notification instance.
     * 
     * @param $ranche
     * @param string $action (created, updated, deleted)
     */
    public function __construct($ranche, $action)
    {
        $this->ranche = $ranche;
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
                $message = "A new ranch '{$this->ranche->name}' has been added.";
                break;
            case 'updated':
                $message = "Ranch '{$this->ranche->name}' has been updated.";
                break;
            case 'deleted':
                $message = "Ranch '{$this->ranche->name}' has been removed.";
                break;
        }

        return [
            'ranche_id' => $this->ranche->id,
            'ranche_name' => $this->ranche->name,
            'action' => $this->action,
            'message' => $message,
            'thumbnail' => $this->ranche->thumbnail,
            'city' => $this->ranche->city,
        ];
    }
}
