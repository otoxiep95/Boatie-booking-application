<?php

require_once(__DIR__ . '/../src/init.php');
$trips = new Trips();

$trips->getTrips(1, 20, FALSE);
