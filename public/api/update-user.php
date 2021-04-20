<?php

/**
 * Update the user/employee information
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
if (empty($_POST['first-name'])) {
    ApiResponse::error('first name is missing');
}
if (empty($_POST['last-name'])) {
    ApiResponse::error('last name is missing');
}
if (empty($_POST['email'])) {
    ApiResponse::error('email is missing');
}
if (empty($_POST['phone'])) {
    ApiResponse::error('phone is missing');
}
if (strlen($_POST['first-name']) < 2) {
    ApiResponse::error('first name is too short');
}
if (strlen($_POST['first-name']) > 100) {
    ApiResponse::error('first name is too long');
}
if (strlen($_POST['last-name']) < 2) {
    ApiResponse::error('last name is too short');
}
if (strlen($_POST['last-name']) > 100) {
    ApiResponse::error('last name is too long');
}
if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
    ApiResponse::error('Something went wrong');
}

//VALIDATE THE CONTENT OF THE INPUT EMAIL
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error('email is not valid');
}

if (!filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT)) {
    ApiResponse::error('phone is not valid');
}
if (strlen($_POST['phone']) < 5) {
    ApiResponse::error('phone is too short');
}
if (strlen($_POST['phone']) > 100) {
    ApiResponse::error('phone is too long');
}

$id = $_POST['id'];
$firstName = $_POST['first-name'];
$lastName = $_POST['last-name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$privilege = $_POST['privilege-select'];



$user = new Users();
$data = $user->updateUserById($id, $firstName, $lastName, $email, $phone, $privilege);

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, user updated",
    "data" => $data
]);
