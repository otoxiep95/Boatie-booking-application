<?php
$activePage = 'overview';
require_once(__DIR__ . '/../elements/dashboard-elements/header.php');
require_once(__DIR__ . '/../../src/init.php');

$users = new Users();

if (!$users->isLoggedIn()) {
    Redirect::page(__DIR__ . '/index.php');
}
if (!$users->hasPrivilege()) {
    Redirect::page(__DIR__ . '/trips.php');
}
?>


<div id="main-dashboard-content-container">
    <div id="main-dashboard-content">

        <!-- main box content goes here  -->
        <main id="dashboard-overview">
            <div class="dashboard-header-handler">
                <div class="left">
                    <h4 class="active">Weekly overview</h4>
                </div>
            </div>
            <div class="g-calendar">
                <iframe src="https://calendar.google.com/calendar/embed?src=boatiedk%40gmail.com&ctz=Europe%2FCopenhagen" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
        </main>

    </div>

</div>

<?php require_once(__DIR__ . '/../elements/dashboard-elements/footer.php'); ?>