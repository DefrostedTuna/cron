<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class CronDidNotRun extends Notification implements ShouldQueue
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
        return [$notifiable->channelType()]; // Notifiable is the instance of the model with the 'notifiable' trait
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('There was a problem with one of your monitors.')
            ->markdown('mail.markdown.cron-did-not-run', ['monitor' => $this->monitor,  'url' => url('/')]); // TODO: Set the proper references for this
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content('*There was a problem with one of your monitors.*')
            ->attachment(function ($attachment) {
                $attachment->title($this->monitor->name, 'http://uptilt.io') // TODO: Send link to monitor
                ->fields([
                    'Status' => 'Did not run', // TODO: Make status method
                    'Last ping' => 'Placeholder', // TODO: Make last ping method
                ]);
            });
    }

    /**
     * Get the data to be stored in the database for the parent Monitor.
     *
     * @return array
     */
    public function toParentDatabase()
    {
        return [
            'status' => 'Did not run',
            'One' => 'Two'
        ];
    }
}
