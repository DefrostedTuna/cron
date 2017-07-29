<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CronIsOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    protected $monitor;

    /**
     * Create a new notification instance.
     *
     * @return void
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
        return ['mail', 'slack'];
        // TODO: Group $monitor->webhooks by type and place only the keys into an array here
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
            ->markdown('mail.markdown.test', ['monitor' => $this->monitor,  'url' => url('/')]); // TODO: Set the proper references for this
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
                ->error()
                ->content('*There was a problem with one of your monitors.*')
                ->attachment(function ($attachment) {
                    $attachment->title($this->monitor->name, 'http://uptilt.io') // TODO: Send link to monitor
                        ->fields([
                            'Status' => 'Overdue', // TODO: Make status method
                            'Last ping' => $this->monitor->lastPing()->created_at->format('D M jS, g:ia'), // TODO: Make last ping method
                        ]);
                });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toParentDatabase()
    {
        return [
            'key' => 'value',
            'One' => 'Two'
        ];
    }
}
