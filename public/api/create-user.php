<?php


/**
 * Create a new user
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
//VALIDATE THE CONTENT OF THE INPUT EMAIL
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error('email is not valid');
}
if (!filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) {
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

/**
 * Set variables
 * 
 */

$firstName = $_POST['first-name'];
$lastName = $_POST['last-name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$privilege = $_POST['privilege-select'];
$password = $_POST['password'];


$data = $users->createNewUser($firstName, $lastName, $email, $password, $phone, $privilege);


// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, user created",
    "data" => $data
]);
