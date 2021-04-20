<?php
$activePage = 'employees';
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
        <main id="dashboard-employees">
            <div class="dashboard-header-handler">
                <div class="left">
                    <h4 class="active">Employees</h4>
                </div>
                <div class="right">
                    <button data-exists="false" class="create-employee-btn">Create employee</button>
                </div>
            </div>
            <div class="dashboard-list">
                <div class="list" data-simplebar>
                    <table id="employees-table">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Priviledge</th>

                            <th> </th>
                        </tr>

                    </table>
                </div>
                <div id="pagination">
                    <a href="#" class="previous">Prev</a>
                    <p class="pagination-status">1/4</p>
                    <a class="next">Next</a>
                </div>
            </div>

            <template id="employees-template">
                <tr>
                    <td class="employee-name">
                        <!-- first name -->
                        <!-- last name -->
                    </td>
                    <td class="employee-email">
                        <!-- email-->
                    </td>
                    <td class="employee-phone">
                        <!-- phone-->
                    </td>
                    <td class="employee-privilege">
                        <!-- privilege -->
                    </td>

                    <td data-exists="true" data-employee_id="0" class="manage-button">Manage</td>
                </tr>
            </template>

        </main>
    </div>
</div>
<div id="employees-modal">
    <div class="container">
        <div class="shadow-background"></div>
        <div class="modal-box">
            <div class="close-button"></div>
            <h5 class="modal-title">Employee</h5>
            <form class="handle-employee-container" enctype="multipart/form-data">

                <p class="error-message"></p>
                <div class="group first-name-group">
                    <input data-type="string" data-min="2" data-max="50" type="text" name="first-name" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>First Name</label>
                </div>
                <div class="group last-name-group">
                    <input data-type="string" data-min="2" data-max="50" type="text" name="last-name" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Last Name</label>
                </div>
                <div class="group email-group">
                    <input data-type="email" data-min="2" data-max="100" type="text" name="email" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Email</label>
                </div>
                <div class="group phone-group">
                    <input type="text" name="phone" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Phone</label>
                </div>
                <div id="password" class="group password-group">
                    <input type="text" name="password" required>
                    <span class="highlight"></span>
                    <label>Password</label>
                </div>
                <div class="select-wrapper">
                    <select class="privilege-group" name="privilege-select">
                        <option value="0" required>Admin</option>
                        <option value="1">Manager</option>
                        <option value="2">Sailor</option>
                    </select>
                </div>

                <button onclick="fvValidateUser(this);" type="button" class="update-btn">Update</button>
                <button type="button" class="delete-btn">Delete</button>
                <button onclick="fvValidateUser(this);" type="button" class="create-btn">Create</button>
            </form>
        </div>
    </div>
</div>


<?php

$insideFooter = '<script src="../scripts/dashboard/employees.js"></script> <script src="../scripts/vendors/validate.js"></script>';

require_once(__DIR__ . '/../elements/dashboard-elements/footer.php');
?>