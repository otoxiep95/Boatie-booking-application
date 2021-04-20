<?php
require_once(__DIR__ . '/../src/init.php');
require_once(__DIR__ . '/elements/user-elements/header.php')

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
            <div id="booking-time" class="booking-content">
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="line"></div>
                        <div class="step">
                            <a href="booking.php">1</a>
                            <p>WHEN</p>
                        </div>
                        <div class="step active">
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
                    <h5>DATE</h5>
                    <div class="date-picker-container">
                        <input name="datePicker" id="date-picker" type="text" placeholder="Tuesday, 5. November 2019">
                    </div>
                </div>
                <div class="booking-content-right">
                    <h5>PICKUP TIME</h5>
                    <div class="booking-times-wrapper">
                        <div class="booking-times" id="booking-time-slots">
                            <p>You first need to select the duration, pickup & dropoff location and the date to see available time slots.</p>
                        </div>
                    </div>
                </div>
                <div class="booking-button">
                    <a class="back-link" href="booking.php">Back</a>
                    <a href="booking-3.php" id="second-next-step-button" class="button inactive">Next step</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end section -->
</main>


<?php
$insideFooter = '<script src="scripts/booking-trip-2.js"></script>';
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>