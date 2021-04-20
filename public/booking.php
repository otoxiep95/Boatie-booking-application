<?php

$insideHEAD = "
<script src='https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.css' rel='stylesheet' />";

require_once(__DIR__ . '/../src/init.php');
require_once(__DIR__ . '/elements/user-elements/header.php');

?>

<!-- Main content of page below here -->
<main id="index" class="user-frontend">
    <div class="booking-shape">
        <img src="assets/shapes/header-waves-2.svg" alt="">
    </div>
    <!-- start section -->
    <div class="booking user-section">
        <div class="booking-container content-container">
            <!-- Real limited width content goes here -->
            <div class="booking-content">
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="line"></div>
                        <div class="step active">
                            <a href="booking.php">1</a>
                            <p>WHEN</p>
                        </div>
                        <div class="step">
                            <a href="booking-2.php">2</a>
                            <p>WHERE</p>
                        </div>
                        <div class="step step-3">
                            <a href="#">3</a>
                            <p>WHO</p>
                        </div>
                    </div>

                </div>
                <div class="booking-content-left">
                    <div class="dropdown">
                        <h5>DURATION</h5>
                        <div class="select-wrapper">
                            <select name="duration-select" id="duration-select" required>
                                <option value="60" required>1 hr</option>
                                <option value="90">1 hr 30 min</option>
                                <option value="120">2 hr</option>
                                <option value="150">2 hr 30 min</option>
                                <option value="180">3 hr</option>
                                <option value="210">3 hr 30 min</option>
                                <option value="240">4 hr</option>
                                <option value="270">4 hr 30 min</option>
                                <option value="300">5 hr</option>
                            </select>
                        </div>
                        <h5>PICKUP LOCATION</h5>
                        <div class="select-wrapper">
                            <select name="pickup-select" id="pickup-select" required>
                                <?php
                                $trips = new Trips();
                                $locations = $trips->getAllLocations();
                                foreach ($locations as $location) :
                                    ?>
                                    <option value="<?php echo $location['location_id']; ?>" data-latitude="<?php echo $location['latitude']; ?>" data-longitude="<?php echo $location['longitude'] ?>">
                                        <?php echo $location['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="show-on-map show-pickup">Show on map <span>&#8594;</span></p>
                        </div>
                        <h5>DROP OFF LOCATION</h5>
                        <div class="select-wrapper">
                            <select name="dropoff-select" id="dropoff-select">
                                <?php foreach ($locations as $location) :
                                    ?>
                                    <option value="<?php echo $location['location_id']; ?>" data-latitude="<?php echo $location['latitude']; ?>" data-longitude="<?php echo $location['longitude'] ?>">
                                        <?php echo $location['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="show-on-map show-dropoff">Show on map <span>&#8594;</span></p>
                        </div>
                    </div>
                </div>
                <div class="booking-content-right">
                    <!-- mapbox map -->
                    <div id='locations-map' style='width: 100%; height: 300px;'></div>
                </div>
                <div class="booking-button">
                    <a class="back-link" href="index.php">Cancel</a>
                    <a href="booking-2.php" id="first-next-step-button" class="button">Next step</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end section -->
</main>


<?php

$insideFooter = '<script src="scripts/booking-trip-1.js"></script>';
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>