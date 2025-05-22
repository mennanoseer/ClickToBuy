<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $link
     * @param string $icon
     * @param string $iconClass
     * @return void
     */
    public function __construct($title, $message, $link = null, $icon = 'fa-bell', $iconClass = 'bg-primary')
    {
        $this->data = [
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'icon_class' => $iconClass
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->data;
    }
}
