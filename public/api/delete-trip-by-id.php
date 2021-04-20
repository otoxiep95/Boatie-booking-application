<?php
require_once(__DIR__ . '/../../src/init.php');

/**
 * Delete a trip by its id
 */

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Id value is missing');
}


$trips = new Trips();
$deletedTrip = $trips->deleteTripById($_POST['id']);

if ($deletedTrip->status == 0) {
    //deletion failed
    ApiResponse::error('Deletion failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $deletedTrip->message
    ]);
}
