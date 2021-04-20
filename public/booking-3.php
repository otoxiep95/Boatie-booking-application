<?php

require_once(__DIR__ . '/elements/user-elements/header.php')

?>
<!-- Main content of page below here -->
<main id="index" class="user-frontend">
    <div class="booking-shape">
        <img src="assets/shapes/header-waves-2.svg" alt="">
    </div>
    <!-- start section -->
    <div class="booking user-section booking-3">
        <div class="booking-container content-container">
            <!-- Real limited width content goes here -->
            <div class="booking-content">
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="line"></div>
                        <div class="step">
                            <a href="booking.php">1</a>
                            <p>WHEN</p>
                        </div>
                        <div class="step">
                            <a href="booking-2.php">2</a>
                            <p>WHERE</p>
                        </div>
                        <div class="step active">
                            <a href="booking-3.php">3</a>
                            <p>WHO</p>
                        </div>
                    </div>

                </div>
                <div class="booking-content-left">
                    <form action="">
                        <div class="name">
                            <div class="group">
                                <input type="text" name="first-name" required>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label>First Name</label>
                            </div>
                            <div class="group">
                                <input type="text" name="last-name" required>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label>Last Name</label>
                            </div>
                        </div>
                        <div class="group">
                            <input type="text" name="email" required>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label>E-mail</label>
                        </div>
                        <div class="group">
                            <input type="text" name="phone" required>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label>Phone number</label>
                        </div>
                        <div>
                            <h5>THOUGHTS?</h5>
                            <textarea placeholder="write something here" cols="30" rows="5"></textarea>
                        </div>
                    </form>
                    <!--  <form action="">  
                        <input type="text" placeholder="Name">
                        <input type="text" placeholder="E-mail">
                        <input type="text" placeholder="Phone number">
                        <div>
                            <h5>THOUGHTS?</h5>
                            <textarea placeholder="write something here" cols="30" rows="10"></textarea>
                        </div>
                    </form> -->
                </div>
                <div class="booking-content-right booking-card-container">
                    <div class="booking-card">
                        <div class="booking-card-img">
                            <h3 class="c-duration">1hr 30min trip</h3>
                        </div>
                        <div class="booking-card-info">
                            <h5>LOCATION</h5>
                            <p class="c-location">Sluseholmen &rarr; Islands Brygge</p>
                            <h5>DATE</h5>
                            <p class="c-date">06/11/19</p>
                            <h5>TIME</h5>
                            <p class="c-time">11:30 - 12:30</p>
                            <h5>PRICE</h5>
                            <p class="c-price">DKK 1700</p>
                        </div>
                    </div>
                </div>

                <div class="booking-button">
                    <a class="back-link" href="booking-2.php">Back</a>
                    <a class="button inactive" id="third-next-step-button" href="booking-confirmation.php">Complete</a>
                    <div class="error-messages">
                        <p></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end section -->
</main>


<?php
$insideFooter = '<script src="scripts/booking-trip-3.js"></script>';
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>