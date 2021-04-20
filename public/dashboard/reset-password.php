<?php

require_once(__DIR__ . '/../../src/init.php');

//check if user is already loggedIn
$users = new Users();

if ($users->isLoggedIn()) {
    Redirect::page(__DIR__ . '/trips.php');
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
                    <div class="group">
                        <input class="new-password" type="password" name="new-password" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>New Password (min 6 characters)</label>
                    </div>
                    <div class="group">
                        <input class="confirm-password" type="password" name="confirm-password" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>Confirm Password</label>
                    </div>
                    <p class="error-message"></p>
                    <p class="confirmation-message"></p>

                    <button type="button">Change</button>
                </form>
                <div class="return-to-boatie">
                    <a href="../dashboard/index.php">&larr; return to login page</a>
                </div>
            </div>

        </main>


    </div>




    <?php

    $insideFooter = '<script src="../scripts/dashboard/reset-password.js"></script>';
    require_once(__DIR__ . '/../elements/dashboard-elements/footer.php');
    ?>