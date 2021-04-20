<?php ob_start(); ?>
<?php

/**
 * THIS IS THE DASHBOARD HEADER
 */
// session_start();
require_once(__DIR__ . '/../../../src/init.php');
$users = new Users();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- SEO -->
    <!-- COMMON TAGS -->
    <!-- Search Engine -->
    <meta name="description" content="Lej en tømmerflåde med kaptajn og 70'er hjørnesofa / rent a pontoon with a Captain and brown 70's interior. Perfekt til events, polterabends, pigemiddage, firmature eller bare familiehygge. ">
    <meta name="image" content="https://cloud.boatie.dk/boatie-orange-logo.png">
    <!-- Schema.org for Google -->
    <meta itemprop="name" content="Boatie">
    <meta itemprop="description" content="Lej en tømmerflåde med kaptajn og 70'er hjørnesofa / rent a pontoon with a Captain and brown 70's interior. Perfekt til events, polterabends, pigemiddage, firmature eller bare familiehygge. ">
    <meta itemprop="image" content="https://cloud.boatie.dk/boatie-orange-logo.png">
    <!-- Open Graph general (Facebook, Pinterest & Google+) -->
    <meta name="og:title" content="Boatie">
    <meta name="og:description" content="Lej en tømmerflåde med kaptajn og 70'er hjørnesofa / rent a pontoon with a Captain and brown 70's interior. Perfekt til events, polterabends, pigemiddage, firmature eller bare familiehygge. ">
    <meta name="og:image" content="https://cloud.boatie.dk/boatie-orange-logo.png">
    <meta name="og:url" content="https://boatie.dk">
    <meta name="og:site_name" content="Boatie">
    <meta name="og:type" content="website">

    <!-- Third party Styling elements -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css" /> <!-- Stylesheet for custom scrollbar library -->
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
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

    <!-- Custom css  -->
    <link rel="stylesheet" href="../styles/main.css" />
    <!-- Styling needed to reset scrollbar when no javascript is available (else people cant scroll) -->
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

    <title>Dashboard</title>
</head>

<body id="dashboard">
    <div class="dashboard-background-shape">
        <img src="../assets/shapes/header-waves-2.svg" alt="">
    </div>
    <div id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div id="sidebar-container">
        <div id="sidebar">
            <div class="dashboard-logo">
                <img src="../assets/logos/boatie-logo-no-tag.svg" alt="">
            </div>
            <div class="tabs">
                <?php if ($users->hasPrivilege()) : ?>
                    <ul>
                        <li><a class="<?php echo $activePage == 'overview' ? 'active' : ''; ?>" href="overview.php">Overview</a></li>
                        <li><a class="<?php echo $activePage == 'trips' ? 'active' : ''; ?>" href="trips.php">Trips</a></li>
                        <li><a class="<?php echo $activePage == 'events' ? 'active' : ''; ?>" href="events.php">Events</a></li>
                        <li><a class="<?php echo $activePage == 'customers' ? 'active' : ''; ?>" href="customers.php">Customers</a></li>
                        <li><a class="<?php echo $activePage == 'employees' ? 'active' : ''; ?>" href="employees.php">Employees</a></li>
                        <li><a class="<?php echo $activePage == 'settings' ? 'active' : ''; ?>" href="settings.php">Settings</a></li>
                    </ul>
                <?php else : ?>
                    <ul>
                        <li><a class="<?php echo $activePage == 'trips' ? 'active' : ''; ?>" href="trips.php">Trips</a></li>
                        <li><a class="<?php echo $activePage == 'events' ? 'active' : ''; ?>" href="events.php">Events</a></li>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="profile">
                <p class="user-name"><?= $_SESSION['user']['name'] ?></p>
                <a href="logout.php" class="logout-button">Log out</a>
            </div>
        </div>
    </div>