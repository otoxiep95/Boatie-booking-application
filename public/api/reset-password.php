<?php

/**
 * Reset a user password
 */

require_once(__DIR__ . '/../../src/init.php');


if (empty($_POST['new-password'])) {
    ApiResponse::error('Password is missing');
}

if (empty($_POST['confirm-password'])) {
    ApiResponse::error('Confirm Password is missing');
}

if (empty($_POST['id'])) {
    ApiResponse::error('Something went wrong');
}

if (empty($_POST['recovery-link'])) {
    ApiResponse::error('Something went wrong');
}

if (strlen($_POST['new-password']) < 6) {
    ApiResponse::error('Password is too short');
}

if (strlen($_POST['confirm-password']) < 6) {
    ApiResponse::error('Confirm Password is too short');
}
if ($_POST['new-password'] != $_POST['confirm-password']) {
    ApiResponse::error('Passwords do not match');
}


$newPassword = $_POST['new-password'];
$recoveryLink = $_POST['recovery-link'];
$id = $_POST['id'];

$users = new Users();

$result = $users->changePassord($id, $recoveryLink, $newPassword);


// Send API response with paginated trips
ApiResponse::success((object) [
    "data" => $result
]);
