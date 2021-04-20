<?php

/**
 * Delete a user/employee
 */

require_once(__DIR__ . '/../../src/init.php');
$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

/*
 *VALIDATE FORM INPUT 
 */
if (empty($_POST['id'])) {
    ApiResponse::error('Something went wrong');
}
if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
    ApiResponse::error('Something went wrong');
}
$id = $_POST['id'];


$deletedUser = $users->deleteUserById($id);

if ($deletedUser->status == 0) {
    //deletion failed
    ApiResponse::error('Deletion failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $deletedUser->message
    ]);
}
