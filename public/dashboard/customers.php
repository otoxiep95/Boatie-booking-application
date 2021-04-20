<?php
$activePage = 'customers';
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
    <div class="notification">Heyoooo</div>
    <div id="main-dashboard-content">

        <!-- main box content goes here  -->
        <main id="dashboard-customers">
            <div class="dashboard-header-handler">
                <div class="left">
                    <h4 class="active">Customers</h4>
                </div>
                <div class="right">
                    <div class="search-container">
                        <input type="text" name="search" id="search-field" placeholder="Search...">
                        <p class="close-search">&#10005;</p>
                    </div>
                </div>
            </div>
            <div class="dashboard-list">
                <div class="list" data-simplebar>
                    <table id="customers-table">
                        <tr>
                            <th>Time</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>E-mail</th>
                            <th>Event</th>
                            <th>Group size</th>
                            <th> </th>
                        </tr>

                        <tr>
                            <td>No customers yet!</td>
                        </tr>

                        <!--
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-customer_id="234" class="manage-button">Manage</td>
                        </tr>

                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>John Doe</td>
                            <td>jd@gmail.com</td>
                            <td></td>
                            <td></td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr>
                        <tr>
                            <td>12:30</td>
                            <td>09/11/19</td>
                            <td>Susan Doe</td>
                            <td>susdo@gmail.com</td>
                            <td>Gløggnight</td>
                            <td>5</td>
                            <td data-trip_id="234" class="manage-button">Manage</td>
                        </tr> -->


                    </table>
                </div>
                <div id="pagination">
                    <a href="#" class="previous">Prev</a>
                    <p class="pagination-status">1/4</p>
                    <a class="next">Next</a>
                </div>
            </div>

            <template id="customers-template">
                <tr>
                    <td class="customer-time-of-booking"></td>
                    <td class="customer-date"></td>
                    <td class="customer-name"></td>
                    <td class="customer-email"></td>
                    <td class="customer-phone"></td>
                    <td class="customer-paid"></td>
                    <td data-event_id="0" class="manage-button">Manage</td>
                </tr>
            </template>


        </main>

    </div>

</div>


<div id="manage-customers-modal">
    <div class="container">
        <div class="shadow-background"></div>
        <div class="modal-box">
            <div class="close-button">
            </div>
            <div class="modal-box-sub-wrapper" data-simplebar>
                <h3 class="modal-title">Manage client</h3>
                <div class="modal-content">

                    <h5>Client details</h5>
                    <div class="customer-details">
                        <div class="item customer-modal-name">
                            <p class="item-title">Name</p>
                            <p class="item-value">John Doe</p>
                        </div>
                        <div class="item customer-modal-email">
                            <p class="item-title">E-mail</p>
                            <p class="item-value">john@example.com</p>
                        </div>
                        <div class="item customer-modal-phone">
                            <p class="item-title">Phone</p>
                            <p class="item-value">28920183</p>
                        </div>
                        <div class="item customer-modal-date">
                            <p class="item-title">Date</p>
                            <p class="item-value">06/12/19</p>
                        </div>
                        <div class="item customer-modal-start-time">
                            <p class="item-title">Start time</p>
                            <p class="item-value">12:30</p>
                        </div>
                        <div class="item customer-modal-end-time">
                            <p class="item-title">End time</p>
                            <p class="item-value">13:30</p>
                        </div>
                        <div class="item customer-modal-event">
                            <p class="item-title">Event</p>
                            <p class="item-value">Not an event</p>
                        </div>
                        <div class="item customer-modal-group-size">
                            <p class="item-title">Group size</p>
                            <p class="item-value">5</p>
                        </div>
                        <div class="item customer-modal-pickup-location">
                            <p class="item-title">Pickup location</p>
                            <p class="item-value">Slusholmen</p>
                        </div>
                        <div class="item customer-modal-dropoff-location">
                            <p class=" item-title">Dropoff location</p>
                            <p class="item-value">Slusholmen</p>
                        </div>

                    </div>

                    <button id="delete-customer">Delete client</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$insideFooter = '<script src="../scripts/dashboard/customers.js"></script>';

require_once(__DIR__ . '/../elements/dashboard-elements/footer.php'); ?>