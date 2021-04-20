<?php

/**
 * Get single customer by its id
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

if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Customer id is missing');
}

$id = (int) $_POST['id'];

// Get single trip
$customers = new Customers();
$data = Customers::getCustomerById($id);


// Send API response with paginated customers
ApiResponse::success((object) [
    "message" => "Success, customer fetched",
    "data" => $data
]);
