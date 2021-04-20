<?php

/**
 * Get the amount of people already registred for an event -> used on frontend to calculate the amount of space left
 */

require_once(__DIR__ . '/../../src/init.php');


if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Id value is missing');
}

$events = new Events();
$people = $events->getPeopleAmountByEventId($_POST['id']);

if ($people == 0) {
    ApiResponse::error('Failed to get people amount');
}

// Send API response
ApiResponse::success((object) [
    "message" => "Successfully fetched people amount",
    "people_amount" => $people
]);
