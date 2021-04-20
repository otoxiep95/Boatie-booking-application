<?php

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
            <div class="booking-content">
                <div id="booking-confirmation">
                    <h2>THANK YOU <span class="conf-name"></span>!</h2>

                    <p> We've sent a confirmation mail to <span class="conf-email">jlo@gmail.com</span> with
                        all details and the exact addresses of pickup and drop off.
                        Please arrive 15min prior the trip beginning.</p>

                    <a href="index.php" class="button">Checkout</a>
                </div>

            </div>
            <!-- end section -->
</main>


<?php
$insideFooter = '<script src="scripts/booking-confirmation.js"></script>';
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>