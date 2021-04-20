<?php

require_once(__DIR__ . '/../../../src/init.php');
/**
 * THIS IS THE USER / VISITOR PAGES FOOTER
 */

$getSettings = new Settings();

$settings = $getSettings->getSettings();

$link = $settings->settings[0]["s_value"];


?>

<footer>
    <div class="footer-waves">
        <img src="assets/shapes/footer-waves.svg" alt="">
    </div>
    <div class="footer-wrapper">
        <div class="footer-container">
            <div class="contact-details">
                <img class="footer-icon" src="assets/icons/position-icon.svg" alt="" srcset="">
                <p class="address">4 Kongens Nytorv <br>Copenhagen, Denmark</p>
            </div>

            <div class="soMe">
                <a class="facebook" href="https://www.facebook.com/boatiecph"><img class="footer-icon" src="assets/icons/facebook-icon.svg" alt="Fb" srcset=""></a>
                <a class="instagram" href="https://www.instagram.com/boatiecph/"><img class="footer-icon" src="assets/icons/instagram-icon.svg" alt="Ig" srcset=""></a>
            </div>

            <div class="phoneemail">
                <div class="phone-div">
                    <img class="footer-icon" src="assets/icons/phone-icon.svg" alt="Phone" srcset="">

                    <a href="tel:004560566609">+45 60 56 66 09</a>
                </div>
                <div>
                    <img class="footer-icon" src="assets/icons/mail-icon.svg" alt="Mail" srcset="">

                    <a href="mailto:info@boatie.dk">info@boatie.dk</a>
                </div>
            </div>

            <div class="termsprivacy">
                <img src="" alt="" srcset="">
                <a href="privacy-policy.php">Privacy Policy</a>
                <img src="" alt="" srcset="">
                <a href="terms-and-conditions.php">Terms and conditions</a>
            </div>
        </div>
        <div class="newsletter-link-div">

            <a href="<?php echo $link == "" ? '#' : $link; ?>">
                <p class="link">Subscribe to Boatie's newsletter</p>
            </a>
        </div>
        <div class="copyright">
            <p>© 2019 Boatie</p>
        </div>
    </div>
</footer>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
// Use this variable to add specific JS files (e.g. trips.js, events.js,...)
echo !empty($insideFooter) ? $insideFooter : null;
?>

</html>