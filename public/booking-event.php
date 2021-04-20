<?php

require_once(__DIR__ . '/elements/user-elements/header.php');
require_once(__DIR__ . '/../src/init.php');

if (!isset($_GET['id'])) {
    Redirect::page(__DIR__ . '/events.php');
}

$events = new Events();
$event = $events->getEventById($_GET['id'])->event;


?>
<!-- Main content of page below here -->
<main id="booking-event" class="user-frontend">
    <div class="booking-shape">
        <img src="assets/shapes/header-waves-2.svg" alt="">
    </div>
    <!-- start section -->
    <div class="booking user-section booking-3">
        <div class="booking-container content-container">
            <div class="booked-out">
                <p>This event is booked out!</p>
            </div>
            <!-- Real limited width content goes here -->
            <div class="booking-content">

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
                        <div class="event-group-size-container">
                            <p class="title">Group size</p>
                            <div class="group-size">
                                <p class="minus">-</p>
                                <input type="text" readonly name="group-size" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                                <p class="plus">+</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="booking-content-right booking-card-container">
                    <div class="booking-card">
                        <div class="booking-card-img" style="background: url('uploads/events/<?php echo $event['img']; ?>')no-repeat center center;
            background-size: cover;">
                            <h3 class="c-duration"> <?php echo $event['name']; ?></h3>
                        </div>
                        <div class="booking-card-info">
                            <h5>LOCATION</h5>
                            <p class="c-location">
                                <?php echo $event['pickup_loc_name']; ?> &rarr; <?php echo $event['dropoff_loc_name']; ?></p>
                            <h5>DATE</h5>
                            <p class="c-date"><?php echo $event['date']; ?></p>
                            <h5>TIME</h5>
                            <p class="c-time"> <?php echo $event['start_time']; ?> - <?php echo $event['end_time']; ?></p>
                            <h5>PRICE</h5>
                            <p class="c-price">DKK <?php echo $event['price_person']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="booking-button">
                    <a class="back-link" href="events.php">Back</a>
                    <a class="button inactive" id="booking-event-button" data-id="<?php echo $event['event_id']; ?>" href="booking-confirmation.php">Complete</a>
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
$insideFooter = '<script src="scripts/booking-event.js"></script>';

require_once(__DIR__ . '/elements/user-elements/footer.php')
?>