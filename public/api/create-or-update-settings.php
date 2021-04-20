<?php


require_once(__DIR__ . '/../../src/init.php');

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

if (!isset($_POST['key-name'])) {
    ApiResponse::error('Something went wrong');
}
if (!isset($_POST['key-value'])) {
    ApiResponse::error('Missing link');
}


$keyName = ht($_POST['key-name']);
$keyValue = $_POST['key-value'];

$settings = new Settings();
$result = $settings->createOrUpdateKeyValue($keyName, $keyValue);


if ($result->status == 0) {
    //deletion failed
    ApiResponse::error('Update failed');
} else {
    //deletion success
    ApiResponse::success((object) [
        "message" => $result->message
    ]);
}
