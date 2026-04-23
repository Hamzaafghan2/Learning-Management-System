<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderComplete extends Notification
{
    use Queueable;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('New Order Received!')
            ->line($this->name . ' has purchased a course.')
            ->action('View Dashboard', url('/admin'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->name . ' has enrolled in a course.'  
        ];
    }
}
