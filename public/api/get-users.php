<?php

/**
 * Get all users
 */

require_once(__DIR__ . '/../../src/init.php');


$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

/**
 * Checking for numerical values in an API POST request:
 * 
 * Check if empty OR if the variable contains no value. 
 * (Both are needed since the value "0" is seen as empty 
 * and would result into true-> ApiResponse::error('Page value is missing'))
 */
if (empty($_POST['page']) && !strlen($_POST['page'])) {
    ApiResponse::error('Page value is missing');
}
if (empty($_POST['page']) && !strlen($_POST['page'])) {
    ApiResponse::error('perPage value is missing');
}

// Set page variables
$page = (int) $_POST['page'];
$perPage = (int) $_POST['perPage'];

// Get trips with pagination
$users = new Users();
$data = $users->getAllUsers($page, $perPage);

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, users fetched",
    "data" => $data
]);
