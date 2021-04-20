<?php

/**
 * Get settings data
 */

require_once(__DIR__ . '/../../src/init.php');


$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}
$settings = new Settings();

$data = $settings->getSettings();

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, settings fetched",
    "data" => $data
]);
