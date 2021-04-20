<?php

require_once(__DIR__ . '/elements/user-elements/header.php')

?>
<!-- Main content of page below here -->
<main id="events" class="user-frontend">
    <!-- start section -->
    <div class="hero user-section first-user-section">
        <div class="bottom-shape">
            <img src="assets/shapes/header-waves-2.svg" alt="" />
        </div>
    </div>
    <!-- end section -->

    <!-- start section -->
    <div class="highlights user-section">
        <div class="menubar">
            <h5>Month</h5>
            <ul>

            </ul>
        </div>

        <div class="content-container user-section">
            <div class="left-container">
                <!-- LEFT CONTENT -->
            </div>

            <div class="right-container">

                <!-- AUGUST -->
                <!-- <h3 class="first_h3" id="august">August</h3> -->
                <!-- <div class="event_box">
                    <div class="event_img"></div>
                    <div class="event_info">
                        <h4 class="event_title">SUNSET DRIVE</h4>
                        <h5 class="event_date">3. September 2019</h5>
                        <p class="event_info_text">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore
                            modi et doloremque cum nihil dicta asperiores magni veniam.
                            Doloribus molestias voluptatibus facere quae beatae! Dolorem,
                            facilis! Omnis laudantium voluptates est.
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore
                            modi et doloremque cum nihil dicta asperiores magni veniam.
                            Doloribus molestias voluptatibus facere quae beatae! Dolorem,
                            facilis! Omnis laudantium voluptates est.</p>
                        <div class="expand_info"></div>
                    </div>

                    <div class="event_book">
                        <div class="event_book_info">
                            <h5 class="time_book">12:00 - 13:00</h5>
                            <h5 class="price_book">120 DKK/person</h5>
                            <a href="#" class="button book-event-button">Book</a>
                        </div>
                    </div>
                </div> -->



            </div>
        </div>
    </div>


    <!-- events template start -->
    <template id=event-template>
        <div class="event_box">
            <div class="event_img"></div>
            <div class="event_info">
                <h4 class="event_title">SUNSET DRIVE</h4>
                <h5 class="event_date">3. September 2019</h5>
                <p class="event_info_text">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore
                    modi et doloremque cum nihil dicta asperiores magni veniam.
                    Doloribus molestias voluptatibus facere quae beatae! Dolorem,
                    facilis! Omnis laudantium voluptates est.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore
                    modi et doloremque cum nihil dicta asperiores magni veniam.
                    Doloribus molestias voluptatibus facere quae beatae! Dolorem,
                    facilis! Omnis laudantium voluptates est.</p>
                <div class="expand_info"></div>
            </div>

            <div class="event_book">
                <div class="event_book_info">
                    <h5 class="time_book">12:00 - 13:00</h5>
                    <h5 class="price_book">120 DKK/person</h5>
                    <a href="#" class="button book-event-button">Book</a>
                </div>
            </div>
        </div>
    </template>
    <!-- events template end -->

    <!-- end section -->
</main>

<?php
$insideFooter = '<script src="scripts/user-events.js"></script>';

require_once(__DIR__ . '/elements/user-elements/footer.php')
?>