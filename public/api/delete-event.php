<?php

require_once(__DIR__ . '/../../src/init.php');

/**
 * Delete an event by its id
 */

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

//check if id exists
if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Id value is missing');
}

/**
 * Set variables
 * 
 */

$eventId = (int) $_POST['id'];

$events = new Events();
$data = $events->deleteEventByID($eventId);



if ($data->status == 0) {
    //deletion failed
    ApiResponse::error('Deletion failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $data->message
    ]);
}
