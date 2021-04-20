<?php
$activePage = 'settings';
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
    <div id="main-dashboard-settings1">

        <h4>Mailchimp link</h4>
        <form class="handle-mailchimp-container" enctype="multipart/form-data">
            <div class="group title-group">
                <input type="text" name="mailchimp" required>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Link</label>
            </div>
            <button type="button">save</button>
            <span class="status">Success</span>
        </form>

        <h4>Google Calendar API</h4>
        <form class="handle-calendar-container" enctype="multipart/form-data">
            <div class="group title-group">
                <input type="text" name="calendar" required>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>API</label>
            </div>
            <button type="button">save</button>
            <span class="status">Success</span>
        </form>

    </div>
    <div id="main-dashboard-settings2">
        <h4>Create new unavailability</h4>
        <form class="handle-unavailability-container" enctype="multipart/form-data">
            <div class="group date-group">
                <input type="text" name="start-date" required class="start-date" id="datepicker" placeholder="Start Date">
            </div>
            <div class="group date-group">
                <input type="text" name="end-date" required class="end-date" id="datepicker" placeholder="End Date">
            </div>
            <button class="create-button" type="button">Create</button>
        </form>
        <h4>Unavailabilities</h4>
        <div class="dashboard-list">
            <div class="list" data-simplebar>
                <table id="unavailabilities-table">
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th> </th>
                    </tr>
                </table>
            </div>
        </div>
        <template id="unavailabilities-template">
            <tr>
                <td class="unavailability-start-date">
                    <!-- start date -->
                </td>
                <td class="unavailability-end-date">
                    <!-- end date -->
                </td>
                <td data-unavailability_id="0" class="delete-button">X</td>
            </tr>
        </template>

    </div>
</div>


<?php

$insideFooter = '<script src="../scripts/dashboard/settings.js"></script>';
require_once(__DIR__ . '/../elements/dashboard-elements/footer.php'); ?>