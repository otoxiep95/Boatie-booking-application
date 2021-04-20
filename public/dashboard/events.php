<?php
$activePage = 'events';

require_once(__DIR__ . '/../elements/dashboard-elements/header.php');
require_once(__DIR__ . '/../../src/init.php');


$users = new Users();

if (!$users->isLoggedIn()) {
    Redirect::page(__DIR__ . '/index.php');
}

?>


<div id="main-dashboard-content-container">
    <div id="main-dashboard-content">
        <div class="notification">Heyoooo</div>

        <!-- main box content goes here  -->
        <main id="dashboard-events">
            <div class="dashboard-header-handler">
                <div class="left">
                    <h4 class="upcoming-events shift-events active" data-past="false">Upcoming events</h4>
                    <h4 class="divider"> | </h4>
                    <h4 class="past-events shift-events" data-past="true">Past events</h4>
                </div>
                <div class="right">
                    <button data-exists="false" class="create-event-btn">Create event</button>
                </div>
            </div>
            <div class="dashboard-list">
                <div class="list" data-simplebar>
                    <table id="events-table">
                        <tr>
                            <th>Time</th>
                            <th>Date</th>
                            <th>Pickup Loc.</th>
                            <th>Name</th>
                            <th>Assigned captain</th>
                            <th> </th>
                        </tr>
                    </table>
                </div>

                <div id="pagination">
                    <a href="#" class="previous">Prev</a>
                    <p class="pagination-status">1/4</p>
                    <a href="#" class="next">Next</a>
                </div>
            </div>

            <!-- event template start -->
            <template id="events-template">
                <tr>
                    <td class="event-start-time">
                        <!-- start time -->
                    </td>
                    <td class="event-date">
                        <!-- date-->
                    </td>
                    <td class="event-pickup">
                        <!-- pickup location name-->
                    </td>
                    <td class="event-name">
                        <!-- first name -->
                        <!-- last name -->
                    </td>
                    <td class="event-captain">
                        <!-- captain name -->
                    </td>
                    <td data-exists="true" data-event_id="0" class="manage-button">Manage</td>
                </tr>
            </template>
            <!-- event template end -->

        </main>

    </div>

</div>

<div id="events-modal">
    <div class="container">
        <div class="shadow-background"></div>
        <div class="modal-box">
            <div class="close-button">
            </div>
            <div class="modal-box-sub-wrapper" data-simplebar>

                <div class="modal-content">
                    <h5 data-exists="false" class="create-event-title">Create event</h5>

                    <form class="handle-event-container" metod="POST" novalidate onsubmit="return false" enctype="multipart/form-data">

                        <div class="event-details">
                            <div class="group title-group">
                                <input data-name="title" data-min="2" data-max="80" maxlength="81" data-type="string" type="text" class="event-title-value" name="title" required>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label>Title</label>
                            </div>
                            <div class="group date-group">
                                <input data-name="date" type="text" class="event-date-value" name="date" required id="datepicker" placeholder="Date">
                                <label>Date</label>
                            </div>
                            <div class="group start-group">
                                <input data-name="start_time" type="text" name="start_time" required id="start-time-picker" placeholder="Start time">
                                <label>Start time</label>
                            </div>
                            <div class="group end-group">
                                <input data-name="end_time" type="text" name="end_time" required id="end-time-picker" placeholder="End time">
                                <label>End time</label>

                            </div>
                            <div class="group price-group">
                                <input data-name="price" class="event-price-value" name="price" type="text" required id="price-input" data-min="1" data-max="1000000" maxlength="7" data-type="integer" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
                                <span class=" highlight"></span>
                                <span class="bar"></span>
                                <label>Price</label>
                                <p class="currency">DKK </p>
                            </div>
                            <p class="error-message"></p>
                        </div>

                        <div class="event-description">
                            <div class="textarea-group">
                                <textarea data-name="description" name="description" class="event-description-value" required data-min="2" data-max="1200" maxlength="1201" placeholder="Description of the event..." cols="30" rows="10"></textarea>
                            </div>
                            <p class="error-message-mobile">hey</p>
                        </div>

                        <div class="event-image">
                            <p class="title">Image of the event</p>
                            <div class="image-preview"></div>
                            <input onchange="fvValidateImage(this);" data-name="img" id="event-image" type="file" name="image" required class="img-file button">
                        </div>

                        <div class="assigned-captain">
                            <h5 class="title">Assigned captain</h5>
                            <div class="select-wrapper">
                                <select data-name="captain-select" name="captain-select" id="captain-select" selected="1" required>
                                    <?php
                                    $events = new Events();
                                    $captains = $events->getAllCaptains();
                                    foreach ($captains as $captain) :
                                        ?>
                                        <option value="<?php echo $captain['user_id']; ?>">
                                            <?php echo $captain['first_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="button-wrapper">
                            <button data-name="button" onclick="fvValidateEvent(this);" class="submit-event">Create Event</button>
                            <button data-name="button" onclick="fvValidateEvent(this);" type="button" class="update-event">Update Event</button>
                            <button data-name="button" type="button" class="delete-event">Delete Event</button>
                        </div>

                    </form>





                    <div class="client-details">
                        <h5>Attendees</h5><span></span>
                        <div class="dashboard-list">
                            <div class="list">
                                <table id="attendees-table">

                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Group size</th>
                                    <th>Paid</th>
                                    <th> </th>
                                </table>
                                <template id="attendees-template">
                                    <tr>
                                        <td class="event-attendee-name">
                                            <!-- attendee full name -->
                                        </td>
                                        <td class="event-attendee-email">
                                            <!-- attendee email -->
                                        </td>
                                        <td class="event-attendee-phone">
                                            <!-- attendee phone number -->
                                        </td>
                                        <td class="event-attendee-groupsize">
                                            <!-- attendee group size -->
                                        </td>
                                        <td class="event-attendee-paid">
                                            <!-- attendee paid -->
                                        </td>
                                        <td data-id="234" class="remove-button">Remove</td>
                                    </tr>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$insideFooter = '<script src="../scripts/dashboard/events.js"></script> <script src="../scripts/vendors/validate.js"></script>';
require_once(__DIR__ . '/../elements/dashboard-elements/footer.php');
?>