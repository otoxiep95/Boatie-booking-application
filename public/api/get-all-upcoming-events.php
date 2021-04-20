<?php
require_once(__DIR__ . '/../../src/init.php');
$user = new Users();


/**
 * Return all upcoming events for the frontend
 */


// Get events with pagination
$events = new Events();
$data = $events->getAllUpcomingEvents();

if (!$data['status']) {
    ApiResponse::error('Could not access events');
}

// Send API response with paginated events
ApiResponse::success((object) [
    "message" => "Success, events fetched",
    "data" => $data['events']
]);
