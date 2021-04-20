<?php

require_once(__DIR__ . '/elements/user-elements/header.php');
session_start();
$errorMessage = "Oh snap, looks like something went wrong :/";

?>
<!-- Main content of page below here -->
<main id="error" class="user-frontend">
    <div class="wave-shape">
        <img src="assets/shapes/header-waves-2-small.svg" alt="">
    </div>
    <!-- start section -->
    <div class="user-section">

        <div class="content-container">
            <div class="sinking-boat">
                <img src="assets/images/sinking-boat.svg" alt="">
            </div>
            <!-- Real limited width content goes here -->
            <div class="error-container">
                <h1>Error</h1>
                <p><?php echo $errorMessage; ?></p>
            </div>
            <div class="return-to-boatie">
                <a class="button" href="../index.php">&larr; return to boatie.com</a>
            </div>

        </div>

    </div>

    </div>

</main>

<?php
require_once(__DIR__ . '/elements/user-elements/footer.php')
?>