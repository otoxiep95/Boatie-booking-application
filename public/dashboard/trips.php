<?php

$activePage = 'trips';
require_once(__DIR__ . '/../elements/dashboard-elements/header.php');
require_once(__DIR__ . '/../../src/init.php');

$users = new Users();

if (!$users->isLoggedIn()) {
    Redirect::page(__DIR__ . '/index.php');
}


?>

<div id="main-dashboard-content-container">
    <div id="main-dashboard-content">

        <!-- main box content goes here  -->
        <main id="dashboard-trips">
            <div class="dashboard-header-handler">
                <div class="left">
                    <h4 class="upcoming-trips shift-trips active" data-past="false">Upcoming trips</h4>
                    <h4 class="divider"> | </h4>
                    <h4 class="past-trips shift-trips" data-past="true">Past trips</h4>
                </div>
            </div>
            <div class="dashboard-list">
                <div class="list" data-simplebar>
                    <table id="trips-table">
                        <tr>
                            <th>Time</th>
                            <th>Date</th>
                            <th>Pickup Loc.</th>
                            <th>Name</th>
                            <th>Paid</th>
                            <th>Assigned captain</th>
                            <th> </th>
                        </tr>
                        <!-- insert trip template here  -->
                    </table>
                </div>
                <div id="pagination">
                    <a href="#" class="previous">Prev</a>
                    <p class="pagination-status">1/4</p>
                    <a class="next">Next</a>
                </div>
            </div>

            <!-- trips template start -->
            <template id="trips-template">
                <tr>
                    <td class="trip-start-time">
                        <!-- start time -->
                    </td>
                    <td class="trip-date">
                        <!-- date-->
                    </td>
                    <td class="trip-pickup">
                        <!-- pickup location name-->
                    </td>
                    <td class="trip-name">
                        <!-- first name -->
                        <!-- last name -->
                    </td>
                    <td class="trip-paid">
                        <!-- paid-->
                    </td>
                    <td class="trip-captain">
                        <!-- captain name -->
                    </td>
                    <td data-trip_id="0" class="manage-button">Manage</td>
                </tr>
            </template>
            <!-- trips template end -->

        </main>

    </div>

</div>

<div id="manage-trip-modal">
    <div class="container">
        <div class="shadow-background"></div>
        <div class="modal-box">
            <div class="close-button">
            </div>
            <div class="modal-box-sub-wrapper" data-simplebar>
                <h3 class="modal-title">Manage trip</h3>
                <div class="modal-content">
                    <h5>Trip details</h5>
                    <div class="trip-details">
                        <div class="item trip-modal-start">
                            <p class="item-title">Start Time</p>
                            <p class="item-value">12:30</p>
                        </div>
                        <div class="item trip-modal-end">
                            <p class="item-title">End Time</p>
                            <p class="item-value">12:30</p>
                        </div>
                        <div class="item trip-modal-date">
                            <p class="item-title">Date</p>
                            <p class="item-value">06/11/19</p>
                        </div>
                        <div class="item trip-modal-captain">
                            <p class="item-title">Assigned Captain <span class="status">Success</span></p>
                            <div class="select-wrapper">
                                <select data-tripid="" data-name="captain-select" name="captain-select" id="captain-select" required>
                                    <?php
                                    $trips = new Trips();
                                    $captains = $trips->getAllCaptains();
                                    foreach ($captains as $captain) :
                                    ?>
                                        <option value="<?php echo $captain['user_id']; ?>">
                                            <?php echo $captain['first_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="item trip-modal-pickup">
                            <p class="item-title">Pickup Location</p>
                            <p class="item-value">Sluseholmen</p>
                        </div>
                        <div class="item trip-modal-dropoff">
                            <p class="item-title">Dropoff Location</p>
                            <p class="item-value">Sluseholmen</p>
                        </div>
                    </div>

                    <h5>Client details</h5>
                    <div class="bottom-wrapper">
                        <div class="client-details">
                            <div>
                                <div class="item trip-modal-name">
                                    <p class="item-title">Name</p>
                                    <p class="item-value">John Doe</p>
                                </div>
                                <div class="item trip-modal-phone">
                                    <p class="item-title">Phone</p>
                                    <p class="item-value">28920183</p>
                                </div>
                                <div class="item trip-modal-email">
                                    <p class="item-title">E-mail</p>
                                    <p class="item-value">john@example.com</p>
                                </div>
                                <div class="item trip-modal-paid">
                                    <p class="item-title">Paid</p>
                                    <p class="item-value">No</p>
                                </div>
                            </div>
                            <div class="item trip-customer-thoughts">
                                <p class="item-title">Thoughts</p>
                                <div>
                                    <p class="item-value">Sluseholmen</p>
                                </div>
                            </div>
                        </div>

                        <div class="delete-wrapper">
                            <div>
                                <button id="delete-trip">Delete trip</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$insideFooter = '<script src="../scripts/dashboard/trips.js"></script>';

require_once(__DIR__ . '/../elements/dashboard-elements/footer.php');
?>