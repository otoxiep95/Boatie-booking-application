<?php

/**
 * Get all events
 */

require_once(__DIR__ . '/../../src/init.php');

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}


if (empty($_POST['page']) && !strlen($_POST['page'])) {
    ApiResponse::error('Page value is missing');
}
if (empty($_POST['perPage']) && !strlen($_POST['perPage'])) {
    ApiResponse::error('perPage value is missing');
}
if (is_bool($_POST['pastEvents'])) {
    ApiResponse::error('pastEvents value is missing');
}


// Set page variables
$page = (int) $_POST['page'];
$perPage = (int) $_POST['perPage'];
$pastEvents = filter_var($_POST['pastEvents'], FILTER_VALIDATE_BOOLEAN);

// Get events with pagination
$events = new Events();
$data = $events->getEvents($page, $perPage, $pastEvents);

// Send API response with paginated events
ApiResponse::success((object) [
    "message" => "Success, properties fetched",
    "data" => $data
]);
