<?php
require_once(__DIR__ . '/../../src/init.php');

/**
 * Delete a customer by its id
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


$customers = new Customers();
$deletedCustomer = $customers->deleteCustomerById($_POST['id']);

if ($deletedCustomer->status == 0) {
    //deletion failed
    ApiResponse::error('Deletion failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $deletedCustomer->message
    ]);
}
