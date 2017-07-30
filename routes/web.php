<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/m/{monitor_shortcode}/run')->name('endpoints.run')->uses('EndpointController@runEndpoint');
Route::get('/m/{monitor_shortcode}/complete')->name('endpoints.complete')->uses('EndpointController@completeEndpoint');
Route::get('/m/{monitor_shortcode}/heartbeat')->name('endpoints.heartbeat')->uses('EndpointController@heartbeatEndpoint');

Route::get('slack/oauth', function () {

    // Set up the request information based on the App credentials
    $provider = new \AdamPaterson\OAuth2\Client\Provider\Slack([
        'clientId' => '194805650644.212120582757',
        'clientSecret' => '1aa32b0c3edf38d5017f1accae2781f3',
        'redirectUri' => 'https://cron.localtunnel.me/slack/oauth', // Redirect back here after sending a request
    ]);

    // If no 'code' parameter was passed to the request, redirect and get one, then get sent back here
    if (! request()->query('code')) {
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => 'incoming-webhook,commands', // Permissions the App needs to access from Slack
        ]);

        return redirect()->away($authUrl);
    }

    // Fetch the oauth.access method and return the response to the $token variable (The User info from Slack)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => request()->query('code'),
    ]);

    $user = \App\Models\User::first(); // Grab the first user because yolo, and no login system is built yet

    // Create the SlackIntegration database entry and associate it with the user we just grabbed
    $user->slackIntegrations()->create([
        'access_token' => $token->getToken(),
        'team_id' => $token->getValues()['team_id'],
        'team_name' => $token->getValues()['team_name'],
        'webhook_channel_id' => $token->getValues()['incoming_webhook']['channel_id'],
        'webhook_channel_name' => $token->getValues()['incoming_webhook']['channel'],
        'webhook_config_url' => $token->getValues()['incoming_webhook']['configuration_url'],
        'webhook_url' => $token->getValues()['incoming_webhook']['url'],
    ]);

    // Show the new integration that is attached to the user
    dd($user->slackIntegrations);
});
