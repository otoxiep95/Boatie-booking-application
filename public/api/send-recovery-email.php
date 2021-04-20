<?php

/**
 * Send the recovery email on requests
 */

require_once(__DIR__ . '/../../src/init.php');


if (empty($_POST['email'])) {
    ApiResponse::error('Email is missing');
}
//THEN VALIDATE THE CONTENT OF THE INPUT EMAIL
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error('Email is not valid');
}

$email = ht($_POST['email']);

$users = new Users();

$recovery = $users->getRecoveryLink($email);

if (!($recovery['status'] == "exists")) {
    ApiResponse::error('Email sent if it exists');
}


$recoveryLink = ht($recovery['recovery']['recovery_link']);
$recoveryId = ht($recovery['recovery']['user_id']);


$data = [
    'recipient_email' => $email,
    'recovery-link' => $recoveryLink,
    'id' => $recoveryId,
];

$sendMail = Mail::sendRecoveryLink($data);


if ($sendMail['status'] == 0) {
    ApiResponse::error('Email send fail');
}

$setActive = $users->setUnactive($recoveryId);
//maybe an error

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Email sent if it exists",
    "data" => $sendMail
]);
