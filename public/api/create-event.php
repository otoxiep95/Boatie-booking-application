<?php

require_once(__DIR__ . '/../../src/init.php');


/**
 * Create an event
 **/
$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}


if (empty($_POST['title'])) {
    ApiResponse::error('title is missing');
}
if (empty($_POST['date'])) {
    ApiResponse::error('date is missing');
}
if (empty($_POST['start_time'])) {
    ApiResponse::error('start time is missing');
}
if (empty($_POST['end_time'])) {
    ApiResponse::error('end time is missing');
}
if (empty($_POST['price'])) {
    ApiResponse::error('price is missing');
}
if (empty($_POST['description'])) {
    ApiResponse::error('description is missing');
}
if (empty($_POST['captain-select'])) {
    ApiResponse::error('captain is missing');
}

if (empty($_FILES) && !isset($_FILES['image'])) {
    ApiResponse::error('image is missing');
}

if (strlen($_POST['title']) < 2) {
    ApiResponse::error('title is too short');
}

if (strlen($_POST['title']) > 80) {
    ApiResponse::error('title is too long');
}

if (strlen($_POST['description']) < 2) {
    ApiResponse::error('description is too short');
}

if (strlen($_POST['description']) > 1200) {
    ApiResponse::error('description is too long');
}

//Sanitize image

//Get the extension of the uploaded file
$imageExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

//Convert to lowercase so we don't have to worry about case sensitivity
$imageExtension = strtolower($imageExtension);

//Create an array of allowed extensions and check if it matches
$allowedExtensions = ['jpg', 'png', 'jpeg'];

if (!in_array($imageExtension, $allowedExtensions)) {
    ApiResponse::error('file extension not supported');
}

//Check if the file type is allowed
$imageType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['image']['tmp_name']);
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
if (!in_array($imageType, $allowedTypes)) {
    ApiResponse::error('the file type is not allowed');
}

//Check that the file is not too small or too big
$imageSize = $_FILES['image']['size']; //below 1MB = 1024KB = 
if (!$imageSize > 20 * 1024 && !$imageSize < 1024 * 1024) {
    ApiResponse::error('The file size is not allowed');
}

//Unique image id 
$imageId = uniqid();

//unique image name
$uniqueImageName = 'boatie-event-' . $imageId . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

//get path and upload image 
$imageUploadPath = __DIR__ . "/../uploads/events/";
move_uploaded_file($_FILES['image']['tmp_name'], $imageUploadPath . $uniqueImageName);


/**
 * Set variables
 **/

$eventTitle = ht($_POST['title']);
$eventDate = $_POST['date'];
$eventStart = $_POST['start_time'];
$eventEnd = $_POST['end_time'];
$eventPrice = $_POST['price'];
$eventDescription = ht($_POST['description']);
$eventImg = $uniqueImageName;
$eventCaptain = $_POST['captain-select'];



$events = new Events();
$data = $events->createNewEvent(
    $eventTitle,
    $eventDate,
    $eventStart,
    $eventEnd,
    $eventPrice,
    $eventDescription,
    $eventImg,
    $eventCaptain
);


// Send API response
ApiResponse::success((object) [
    "message" => "Success, event created",
    "data" => $data
]);
