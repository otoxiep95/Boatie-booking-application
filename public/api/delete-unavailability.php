<?php

/**
 * Delete an unavailability date area
 */

require_once(__DIR__ . '/../../src/init.php');

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}


if (!isset($_POST['id'])) {
    ApiResponse::error('Something went wrong');
}

$id = ht($_POST['id']);

$unavailabilities = new Unavailabilities();
$deletedUnavailability = $unavailabilities->deleteUnavailabilityById($id);


if ($deletedUnavailability->status == 0) {
    //deletion failed
    ApiResponse::error('Deletion failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $deletedUnavailability->message
    ]);
}
