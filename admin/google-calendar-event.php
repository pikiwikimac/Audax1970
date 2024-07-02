<?php

/**
 * Plugin Name: Google Calendar Event
 * Description: create custom google calendar events.
 * Version: 1.0.0
 * Author: Finegap
 * Author URI: https://finegap.com
 * Text Domain: Google Calendar
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
ini_set('display_errors', 1);

/**
 *  update google code
 */
add_action('wp_footer', 'create_google_calendar_events');
function create_google_calendar_events()
{
    $credentials = __DIR__ . '/credentials.json';
    require __DIR__ . '/vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setApplicationName('testoo');
    $client->setScopes(array(Google_Service_Calendar::CALENDAR));
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');
    $client->getAccessToken();
    $client->getRefreshToken(); 

    $service = new Google_Service_Calendar($client);

    $event   = new Google_Service_Calendar_Event(array(
        'summary' => 'testing',
        'location' => '800 Howard St., San Francisco, CA 94103',
        'description' => 'A chance to hear more about Google\'s developer products.',
        'start' => array(
        'dateTime' => '2022-05-28T09:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
        ),
        'end' => array(
        'dateTime' => '2022-05-28T17:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
        ),
        'recurrence' => array(
            'RRULE:FREQ=DAILY;COUNT=2'
        ),
        'attendees' => array(),  
        'reminders' => array(
        'useDefault' => FALSE,
        'overrides' => array(
            array('method' => 'email', 'minutes' => 24 * 60),
            array('method' => 'popup', 'minutes' => 10),
        ),
        ),
    ));
    
    $calendarId = 'fa37c6489ef9aed2af9b4f6b76f10858d4be0e91f217a5ea6b07d74af1f9aa8e@group.calendar.google.com';
    $event      = $service->events->insert($calendarId, $event);
    print_r($event->htmlLink);
}
