<?php

/**
 * Get all unavailabilites dates
 */

require_once(__DIR__ . '/../../src/init.php');


$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}


// Get trips with pagination
$unavailabilities = new Unavailabilities();
$data = $unavailabilities->getUnavailabilities();




// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, users fetched",
    "data" => $data
]);
