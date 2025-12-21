<?php
namespace App\Helpers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client as GoogleClient;
use Google\Service\Calendar;
use App\Config\Env;

class GoogleCalendar {

    public static function createEventFromRefreshToken($refreshToken, $summary, $description, $startDateTime, $endDateTime, $timezone = 'UTC') {
        Env::load();

        try {
            $client = new GoogleClient();
            $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
            $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
            $client->refreshToken($refreshToken);

            $service = new Calendar($client);

            $event = new \Google_Service_Calendar_Event([
                'summary' => $summary,
                'description' => $description,
                'start' => ['dateTime' => $startDateTime, 'timeZone' => $timezone],
                'end'   => ['dateTime' => $endDateTime,   'timeZone' => $timezone]
            ]);

            $created = $service->events->insert('primary', $event);
            return ['success' => true, 'eventId' => $created->getId()];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public static function deleteEvent($refreshToken, $eventId) {
        Env::load();
        try {
            $client = new GoogleClient();
            $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
            $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
            $client->refreshToken($refreshToken);

            $service = new Calendar($client);
            $service->events->delete('primary', $eventId);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateEvent($refreshToken, $eventId, $summary, $description, $startDateTime, $endDateTime, $timezone = 'UTC') {
        Env::load();

        try {
            $client = new GoogleClient();
            $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
            $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
            $client->refreshToken($refreshToken);

            $service = new Calendar($client);

            // Ambil event lama
            $event = $service->events->get('primary', $eventId);

            // PACK start time sebagai object
            $start = new \Google\Service\Calendar\EventDateTime();
            $start->setDateTime($startDateTime);
            $start->setTimeZone($timezone);

            // PACK end time sebagai object
            $end = new \Google\Service\Calendar\EventDateTime();
            $end->setDateTime($endDateTime);
            $end->setTimeZone($timezone);

            // Update fields
            $event->setSummary($summary);
            $event->setDescription($description);
            $event->setStart($start);
            $event->setEnd($end);

            // Kirim update ke Google Calendar
            $service->events->update('primary', $eventId, $event);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }


}
