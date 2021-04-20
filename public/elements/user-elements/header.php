<?php


/**
 * THIS IS THE USER / VISITOR PAGES HEADER
 */

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>




    <!-- Styling elements -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css" />
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
    <link rel="stylesheet" href="styles/main.css" />
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/favicon/site.webmanifest">
    <link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="assets/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="../assets/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <title>Boatie</title>
    <noscript>
        <style>
            /**
            * Reinstate scrolling for non-JS clients
            */
            .simplebar-content-wrapper {
                overflow: auto;
            }
        </style>
    </noscript>

    <?php
    /**
     * Add additional, page specific items inside the header
     */
    echo isset($insideHEAD) ? $insideHEAD : null;
    ?>
    <title>Document</title>

    <!-- Google Analytics -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-156880061-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-156880061-1');
    </script>
</head>

<body>
    <nav id="main-nav">
        <div id="nav-container">
            <div class="logo-container">
                <a href="index.php"><img src="assets/logos/boatie-logo-bad.png" alt="" /></a>

            </div>
            <div class="links-container">
                <ul>
                    <li id="join_event_nav"><a href="events.php" class="button">Join Event</a></li>
                    <li id="rent_pantoon_nav"><a href="booking.php" class="button">Book Trip</a></li>
                </ul>
            </div>
        </div>
    </nav>