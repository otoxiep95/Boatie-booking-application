<?php

require_once(__DIR__ . '/../../src/init.php');

/**
 * Customer books a trip
 */


if (!isset($_POST['date'])) {
    ApiResponse::error('Date is missing');
}
if (empty($_POST['duration']) && !strlen($_POST['duration'])) {
    ApiResponse::error('Duration is missing');
}
if (empty($_POST['pickup_loc_id']) && !strlen($_POST['pickup_loc_id'])) {
    ApiResponse::error('Pickup is missing');
}
if (empty($_POST['dropoff_loc_id']) && !strlen($_POST['dropoff_loc_id'])) {
    ApiResponse::error('Dropoff is missing');
}
if (!isset($_POST['pickup_name'])) {
    ApiResponse::error('Pickup is missing');
}
if (!isset($_POST['dropoff_name'])) {
    ApiResponse::error('Dropoff is missing');
}
if (!isset($_POST['first_name'])) {
    ApiResponse::error('First Name is missing');
}
if (!isset($_POST['last_name'])) {
    ApiResponse::error('Last Name is missing');
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error('Invalid email');
}
if (!isset($_POST['phone'])) {
    ApiResponse::error('Phone number is missing');
}



$thoughts = isset($_POST['thoughts']) ? ht($_POST['thoughts']) : null;
// Set variables
$data = [
    'date' => ht($_POST['date']),
    'duration' => (int)  ht($_POST['duration']),
    'pickup_loc_id' => (int) ht($_POST['pickup_loc_id']),
    'dropoff_loc_id' => (int) ht($_POST['dropoff_loc_id']),
    'pickup_name' => ht($_POST['pickup_name']),
    'dropoff_name' => ht($_POST['dropoff_name']),
    'start_time' => ht($_POST['start_time']),
    'first_name' => ht($_POST['first_name']),
    'last_name' => ht($_POST['last_name']),
    'email' => ht($_POST['email']),
    'phone' => ht($_POST['phone']),
    'thoughts' => $thoughts
];

// Create the new trip
$trips = new Trips();
$data = $trips->newTrip($data);

// Check the created trip status and send respective Api response
if ($data->status == 0) {
    ApiResponse::error($data->message);
}

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, booked and mail sent",
    "data" => $data->data,
    "trip_id" => $data->trip_id,
    'thoughts' => $thoughts
]);
