<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class IncomingHeartbeatDidNotComplete extends Notification implements ShouldQueue
{
    use Queueable;

    protected $monitor;

    /**
     * Create a new notification instance.
     *
     * @param $monitor
     */
    public function __construct($monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$notifiable->channelType()];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // TODO: Style a mail message
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toSlack($notifiable)
    {
        // TODO: Style slack message
        return new SlackMessage;
    }

    public function toParentDatabase()
    {
        // TODO: Set what needs to be stored on the parent
        return [

        ];
    }
}
