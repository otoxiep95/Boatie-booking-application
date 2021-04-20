<?php
require_once(__DIR__ . '/../../src/init.php');
/**
 * Customer books a spot for an event
 */
$params = [
    'event_id' => 'Event id',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'email' => 'Email',
    'phone' => 'Phone',
    'group_size' => 'Group size'
];

//Check for values existence and sanitize them
foreach ($params as $key => $param) {
    if (!@val_exists($_POST[$key])) {
        ApiResponse::error("{$param} value is missing");
    }
    $params[$key] = is_numeric($_POST[$key]) ? $_POST[$key] : ht($_POST[$key]); //If non-number value, sanitize it
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error("Invalid email");
}

$events = new Events();
$booking = $events->customerEventBooking($params);

if (!$booking) {
    ApiResponse::error("Booking failed");
}

// Send API response
ApiResponse::success((object) [
    "message" => "Booking successful"
]);
