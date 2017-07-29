<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\Monitor::class, function (Faker\Generator $faker) {
    $expressions = [
        '* * * * *', // Every minute
        '*/5 * * * *', // Every 5 minutes
        '*/10 * * * *', // Every 10 minutes
        '*/15 * * * *', // Every 15 minutes
        '*/30 * * * *', // Every 30 minutes
        '0 * * * *', // Every hour
        '0 */2 * * *', // Every 2 hours
        '0 */6 * * *', // Every 6 hours
        '0 */12 * * *', // Every 12 hours
        '00 00 * * *', // Once a day
        '00 00 * * 0', // Sunday at midnight (Start of day to be more precise)
        '00 00 * * 1', // Monday at midnight (Start of day to be more precise)
        '00 00 * * 2', // Tuesday at midnight (Start of day to be more precise)
        '00 00 * * 3', // Wednesday at midnight (Start of day to be more precise)
        '00 00 * * 4', // Thursday at midnight (Start of day to be more precise)
        '00 00 * * 5', // Friday at midnight (Start of day to be more precise)
        '00 00 * * 6', // Saturday at midnight (Start of day to be more precise)
    ];

    return [
        'owner_id' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'name' => $faker->word,
        'shortcode' => str_random(6),
        'expression' => $expressions[array_rand($expressions)],
        'type' => 'cron',
        'description' => $faker->sentence,
        'paused' => false,
        'alert_sent' => false,
        'delay_until' => null,

    ];
});

$factory->define(App\Models\Ping::class, function (Faker\Generator $faker) {

    $endpoints = [
        'run',
        'complete',
        //'heartbeat (incoming)',
        //'heartbeat (outgoing)',
    ];

    return [
        'monitor_id' => function () {
            return factory(App\Models\Monitor::class)->create()->id;
        },
        'pair_id' => null,
        'type' => 'incoming',
        'status' => 'success',
        'endpoint' => $endpoints[array_rand($endpoints)],
        'ip' => $faker->ipv4
    ];
});

$factory->define(App\Models\NotificationChannel::class, function (Faker\Generator $faker) {

    $integrations = [
        App\Models\EmailIntegration::class,
        App\Models\SmsIntegration::class,
        App\Models\SlackIntegration::class,
    ];

    $integration = $integrations[array_rand($integrations)];

    return [
        'monitor_id' => function() {
            return factory(App\Models\Monitor::class)->create()->id;
        },
        'integration_id' => function() use ($integration) {
            return factory($integration)->create()->id;
        },
        'integration_type' => function() use ($integration) {
            return $integration;
        },
        'type' => function() use ($integration) {
            return class_basename(new $integration);
        },
    ];
});

$factory->define(App\Models\EmailIntegration::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
            return factory(App\Models\User::class)->create()->id;
        },
        'email' => $faker->email
    ];
});

$factory->define(App\Models\SmsIntegration::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
            return factory(App\Models\User::class)->create()->id;
        },
        'sms_number' => $faker->phoneNumber,
    ];
});

$factory->define(App\Models\SlackIntegration::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
            return factory(App\Models\User::class)->create()->id;
        },
        'access_token' => $faker->text(76),
        'team_name' => $faker->word,
        'team_id' => str_random(9),
        'webhook_channel_id' => str_random(9),
        'webhook_channel_name' => '#' . $faker->word,
        'webhook_config_url' => $faker->url,
        'webhook_url' => $faker->url,
    ];
});