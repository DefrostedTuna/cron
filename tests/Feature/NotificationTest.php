<?php

namespace Tests\Unit;

use App\Models\Ping;
use App\Notifications\CronRanLongerThanUsual;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Monitor;
use App\Models\EmailIntegration;
use App\Models\SlackIntegration;
use App\Models\NotificationChannel;
use App\Notifications\CronDidNotRun;
use App\Notifications\CronDidNotComplete;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Notifications\IncomingHeartbeatDidNotComplete;

class NotificationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_notification_can_be_sent_to_an_email_channel()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'cron',
            'expression' => '* * * * *'
        ]);
        $email = create(EmailIntegration::class);
        $monitor->createNotificationChannelFromIntegration($email);

        // Send the notification
        $monitor->notifyChildren(new CronDidNotRun($monitor));

        // Verify the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            CronDidNotRun::class
        );
    }

    /** @test */
    public function a_notification_can_be_sent_to_a_slack_channel()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'cron',
            'expression' => '* * * * *'
        ]);
        $slack = create(SlackIntegration::class);
        $monitor->createNotificationChannelFromIntegration($slack);

        // Send the notification
        $monitor->notifyChildren(new CronDidNotRun($monitor));

        // Verify the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            CronDidNotRun::class
        );
    }
    
    /** @test */
    public function notification_is_sent_when_a_cron_does_not_run()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'cron',
            'expression' => '* * * * *'
        ]);
        $notificationChannel = create(NotificationChannel::class, [
            'monitor_id' => $monitor->id
        ]);

        // Make a failed ping to trigger the rule violation
        $ping = $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'run',
            'ip' => '192.168.1.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        // Rule should fail, triggering the notification
        if($monitor->verifyCronDidNotRunViolation()) {
            $monitor->notifyChildren(new CronDidNotRun($monitor));
        }

        // Verify that the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            CronDidNotRun::class
        );
    }

    /** @test */
    public function notification_is_sent_when_a_cron_does_not_complete()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'cron',
            'expression' => '* * * * *'
        ]);
        $notificationChannel = create(NotificationChannel::class, [
            'monitor_id' => $monitor->id
        ]);

        // Make a failed ping to trigger the rule violation
        $ping = $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'complete',
            'ip' => '192.168.1.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        // Rule should fail, triggering the notification
        if($monitor->verifyCronDidNotCompleteViolation()) {
            $monitor->notifyChildren(new CronDidNotComplete($monitor));
        }

        // Verify that the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            CronDidNotComplete::class
        );
    }

    /** @test */
    public function notification_is_sent_when_a_cron_runs_longer_than_usual()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'cron',
            'expression' => '* * * * *'
        ]);
        $notificationChannel = create(NotificationChannel::class, [
            'monitor_id' => $monitor->id
        ]);

        // Make a failed condition to trigger the rule violation
        $runPingOne = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'endpoint' => 'run',
            'created_at' => Carbon::now()->subMinutes(1)
        ]);
        $completePingOne = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'pair_id' => $runPingOne->id,
            'endpoint' => 'complete',
            'created_at' => Carbon::now()->subMinutes(1)->addSeconds(10)
        ]);
        $runPingOne->pair()->associate($completePingOne)->save();

        $runPingTwo = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'endpoint' => 'run',
            'created_at' => Carbon::now()
        ]);
        $completePingTwo = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'pair_id' => $runPingTwo->id,
            'endpoint' => 'complete',
            'created_at' => Carbon::now()->addSeconds(30)
        ]);
        $runPingTwo->pair()->associate($completePingTwo)->save();

        // Rule should fail, triggering the notification
        if($monitor->verifyCronRanLongerThanUsualViolation()) {
            $monitor->notifyChildren(new CronRanLongerThanUsual($monitor));
        }

        // Verify that the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            CronRanLongerThanUsual::class
        );
    }

    /** @test */
    public function notification_is_sent_when_a_heartbeat_does_not_complete()
    {
        Notification::fake(); // Prevent notifications from being sent

        // Create the monitor and the notification channel
        $monitor = create(Monitor::class, [
            'type' => 'heartbeat',
            'expression' => '* * * * *'
        ]);
        $notificationChannel = create(NotificationChannel::class, [
            'monitor_id' => $monitor->id
        ]);

        // Make a failed ping to trigger the rule violation
        $ping = $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'heartbeat', // TODO: Make this incoming-heartbeat?
            'ip' => '192.168.1.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        // Rule should fail, triggering the notification
        if($monitor->verifyIncomingHeartbeatDidNotCompleteViolation()) {
            $monitor->notifyChildren(new IncomingHeartbeatDidNotComplete($monitor));
        }

        // Verify that the notification was sent
        Notification::assertSentTo(
            $monitor->notificationChannels,
            IncomingHeartbeatDidNotComplete::class
        );
    }
}
