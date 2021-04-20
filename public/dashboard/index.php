<?php

require_once(__DIR__ . '/../../src/init.php');

//check if user is already loggedIn
$users = new Users();
$displayError = false;
if ($users->isLoggedIn()) {
    Redirect::page(__DIR__ . '/trips.php');
}

global $displayError, $errorMessage;
if ($_POST) {

    if (empty($_POST['login-email'])) {

        $displayError = true;
        $errorMessage = "Email is missing";
    }
    if (empty($_POST['login-password'])) {
        $displayError = true;
        $errorMessage = "Password is missing";
    }
    //THEN VALIDATE THE CONTENT OF THE INPUT EMAIL
    if (!filter_var($_POST['login-email'], FILTER_VALIDATE_EMAIL)) {
        $displayError = true;
        $errorMessage = "Email not valid";
    }
    // //VALIDATE LENGTH OF PASSWORD
    // if (strlen($_POST['login-password']) < 6) {
    //     return;
    // }
    // if (strlen($_POST['login-password']) > 100) {
    //     return;
    // }

    $email = $_POST['login-email'];
    $password = $_POST['login-password'];

    $users = new Users();
    $result = $users->login($email, $password);

    if ($result['status']) {
        //User authenticated
        Redirect::page(__DIR__ . '/trips.php');
    } else {
        $displayError = true;
        $errorMessage = "Email and password combination did match";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styles/main.css" />
    <title>Dashboard</title>
</head>

<body>
    <div class="dashboard-login-container">
        <div class="dashboard-background-shape">
            <img src="../assets/shapes/header-waves-2.svg" alt="">
        </div>
        <!-- main box content goes here  -->
        <main id="dashboard-login">
            <div class="login-container">
                <img src="../assets/logos/boatie-logo-no-tag.svg" alt="">
                <form action="" method="POST">
                    <p class="error-message"><?php
                                                global $displayError, $errorMessage;
                                                echo $displayError ? $errorMessage : ""  ?></p>
                    <div class="group">
                        <input type="text" name="login-email" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>E-mail</label>
                    </div>
                    <div class="group">
                        <input type="password" name="login-password" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>Password</label>
                    </div>
                    <a href="forgot-password.php">
                        <p>Forgot your password?</p>
                    </a>
                    <button>Log in</button>
                </form>
                <div class="return-to-boatie">
                    <a href="../index.php">&larr; return to boatie.com</a>
                </div>
            </div>

        </main>


    </div>




    <?php require_once(__DIR__ . '/../elements/dashboard-elements/footer.php'); ?>