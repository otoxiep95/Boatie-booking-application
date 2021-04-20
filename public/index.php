<?php

require_once(__DIR__ . '/elements/user-elements/header.php');
require_once(__DIR__ . '/../src/init.php');

$getEvents = new Events();

$eventsData = $getEvents->getThreeUpcomingEvents(1, 3, false);

$events = $eventsData->events;

$maxDescLength = 100;

?>
<!-- Main content of page below here -->
<main id="index" class="user-frontend">
  <!-- start section -->
  <div class="hero user-section first-user-section">
    <div class="background-media">
      <figure>
        <video autoplay muted loop id="boatie-video" playsinline="true">
          <source src="assets/videos/boatie-480.mp4" type="video/mp4">
        </video>
      </figure>
    </div>
    <div class="content-container">
      <!-- Real limited width content goes here -->
      <div>
        <h1>DISCOVER <br> COPENHAGEN</h1>
        <p>
          sofas, boats and stuff that floats
        </p>
      </div>

    </div>
    <div class="bottom-shape">
      <img src="assets/shapes/hero-waves.svg" alt="" />
    </div>
    <div class="scroll-icon-container">
      <span class="scroll-icon">
        <span class="scroll-icon__dot"></span>
      </span>
      <p class="scroll-info">Scroll for more</p>
    </div>
  </div>
  <!-- end section -->

  <!-- start section -->
  <div class="highlights user-section">
    <div class="content-container">
      <!-- Real limited width content goes here -->

      <div>
        <h3>All aboard</h3>
        <p class="highlight-p">Boatie is an extravagant way to experience the canals and harbours around Copenhagen, all on a pontoon full of sofas, a fridge and a grill for up to 12 people! Price starts at 1200dkk/hour. </p>
        <a href="booking.php" class="button cat-content">Rent private trip</a>
      </div>
      <div class="highlight-image-container" id="highlight-image1">
        <div class="highlight-image" style="background-image: url('assets/images/boatiegram/boatiegram-1.jpg')"></div>
      </div>

      <div class="highlight-image-container">
        <div class="highlight-image" style="background-image: url('assets/images/boatiegram/boatiegram-5.jpg')"></div>
      </div>
      <div>
        <h3>Unforgettable experiences</h3>
        <p class="highlight-p">We always have all sorts of events, where you can go with your family, your girlfriend, or just by yourself. Come with us, and enjoy the sofas, the water, the sun and the company of others.</p>
        <a href="events.php" class="button cat-content">Join an event</a>
      </div>
    </div>
  </div>
  <!-- end section -->

  <!-- start section -->
  <div class="boatiegram user-section">
    <div class="content-container">
      <!-- Real limited width content goes here -->
      <h2>BOATIEGRAM</h2>
      <div class="img-gallery">
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-1.jpg')"></div>
        </div>
        <div class="img-container big-image">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-2.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-3.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-4.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-5.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-6.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-7.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-8.jpg')"></div>
        </div>
        <div class="img-container">
          <div class="image" style="background-image:url('assets/images/boatiegram/boatiegram-9.jpg')"></div>
        </div>


      </div>
    </div>
    <div class="bottom-shape">
      <img src="assets/shapes/hero-waves-2.svg" alt="" />
    </div>
  </div>
  <!-- end section -->

  <!-- start section -->
  <div class="upcoming-events user-section">
    <div class="content-container">
      <!-- Real limited width content goes here -->
      <h2>UPCOMING EVENTS</h2>
      <div class="upcoming-events-wrapper">
        <?php if ($events)
          foreach ($events as $event) : ?>
          <a href="events.php?event=id-<?= $event['event_id'] ?>">
            <div class="upcoming-events-card" id="<?= $event['event_id'] ?>">
              <div class="event-image" style="background-image: url('uploads/events/<?= $event['img'] ?>')"></div>
              <h5><?= $event['name'] ?></h5>
              <span><?php $eventDate = convertDateToFriendly($event['date'], 'd-m-Y');
                    echo $eventDate ?></span>
              <p><?php echo strlen($event['description']) > $maxDescLength ? substr($event['description'], 0, $maxDescLength) . '...' :  $event['description']; ?></p>
            </div>
          </a>
        <?php endforeach;

        else {
          echo "<p class='no-events'>New events are coming soon!</p>";
        } ?>

      </div>
      <?php echo $events ? '<a class="button" href="events.php">All events</a>' : ''; ?>


    </div>
  </div>
  <!-- end section -->

  <!-- start section -->
  <div class="faq user-section">
    <div class="content-container">
      <!-- Real limited width content goes here -->
      <h2>FAQs</h2>
      <div class="questions">

        <div class="column">
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>How many people will it hold? </h5>
            </div>
            <div class="content">
              <p>12 - plus the captain</p>
            </div>
          </div>
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>When are you open?</h5>
            </div>
            <div class="content">
              <p>Weekends, nights.. You can book us anytime!
              </p>
            </div>
          </div>
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>Do you sell drinks? </h5>
            </div>
            <div class="content">
              <p>No, we do not have a license for that</p>
            </div>
          </div>
        </div>

        <div class="column">
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>Can we bring food or drinks? </h5>
            </div>
            <div class="content">
              <p>Yes of course - there's also a grill, one-time plates, forks and knives, coals and everything needed aboard. Just bring your own food. Note that it costs 200 kr. extra.</p>
            </div>
          </div>
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>Can we do corporate events with the boat?</h5>
            </div>
            <div class="content">
              <p>Of course. If you have any special requests, just give us a call and we'll figure it out</p>
            </div>
          </div>
          <div class="question-wrapper">
            <div class="collapsible">
              <div class="arrow"></div>
              <h5>What is the name of the sofa pontoon?</h5>
            </div>
            <div class="content">
              <p>Donna Summer - named after the legendary disco queen.</p>
            </div>
          </div>


        </div>


      </div>

    </div>
    <!-- end section -->
    <div class="orange-bottom"></div>
    <a href="booking.php" class="button bottom-book">BOOK NOW</a>
</main>

<?php

$insideFooter = '<script src="scripts/user-index.js"></script>';
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>