<?php

/**
 * Update the assigned captain of a trip
 */

require_once(__DIR__ . '/../../src/init.php');

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

$params = [
    'trip_id' => 'Trip id',
    'capt_id' => 'Captain id'
];

//Check for values existence and sanitize them
foreach ($params as $key => $param) {
    if (!@val_exists($_POST[$key])) {
        ApiResponse::error("{$param} value is missing");
    }
    $params[$key] = is_numeric($_POST[$key]) ? $_POST[$key] : ht($_POST[$key]); //If non-number value, sanitize it
}

$trips = new Trips();
$result = $trips->updateTripCaptainById($params['trip_id'], $params['capt_id']);

if ($result['status'] == 0) {
    ApiResponse::error("Captain update failed");
}

// Send API response
ApiResponse::success((object) [
    "message" => "Captain successfully updated"
]);
