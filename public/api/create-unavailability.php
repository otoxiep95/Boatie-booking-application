<?php

/**
 * Set unavailabilites dates
 */

require_once(__DIR__ . '/../../src/init.php');

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}


if (!isset($_POST['start-date'])) {
    ApiResponse::error('Start date is missing');
}

if (!isset($_POST['end-date'])) {
    ApiResponse::error('End date is missing');
}
if ($_POST['end-date'] < $_POST['start-date']) {
    ApiResponse::error('End date is before start date');
}


$startDate = ht($_POST['start-date']);
$endDate = ht($_POST['end-date']);

$unavailabilities = new Unavailabilities();
$data = $unavailabilities->createUnavailability($startDate, $endDate);


// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, unavailability created",
    "data" => $data
]);
