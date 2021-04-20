<?php

class Customers
{

    private $totalCustomersAmount; // total amount of customers
    private $perPage = 30; // Maximum of customers to show on a page during pagination


    /**
     * Constructor class
     * 
     * Run the getTotalCustomersAmount() on each instance
     */
    public function __construct($options = [])
    {
        // Check if the perPage option has been defined, else use default of 30
        if (array_key_exists("perPage", $options)) {
            $this->perPage = $options['perPage']; // set new amount of trips per page (limit)
        }

        //$this->getTotalCustomersAmount();
    }

    /**
     * Get single customer by id
     * 
     * @param int $id Id of the customer to delete
     * 
     * @return array Data of a single customer
     */
    public function getCustomerById(int $id)
    {
        //return an array of customers with pagination
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT *
        FROM customers 
        WHERE customer_id = :idVal;
        ");

        $result = $stmt->execute([
            'idVal' => $id
        ]);

        $customer = $stmt->fetch();

        if ($customer["is_event"]) {

            $stmtEvents = $conn->prepare("SELECT
            customers.customer_id,
            customers.first_name, 
            customers.last_name,
            customers.email, 
            customers.phone,
            DATE_FORMAT(events.date, '%d-%m-%Y') as date, 
            DATE_FORMAT(events.start_time, '%H:%i') as start_time,
            DATE_FORMAT(events.end_time, '%H:%i') as end_time,
            events.name,
            customers.group_size,
            pd_locations_pickup.name as pickup_loc_name, 
            pd_locations_dropoff.name as dropoff_loc_name

            FROM event_customer_link
            LEFT JOIN events ON event_customer_link.event_id = events.event_id
            LEFT JOIN customers ON event_customer_link.customer_id = customers.customer_id
            LEFT JOIN pd_locations as pd_locations_pickup ON events.pickup_loc_id = pd_locations_pickup.location_id
            LEFT JOIN pd_locations as pd_locations_dropoff ON events.dropoff_loc_id = pd_locations_dropoff.location_id
            WHERE event_customer_link.customer_id = :idVal;
            ");

            $stmtEvents->execute([
                'idVal' => $id
            ]);

            $result = $stmtEvents->fetch();
            $output = (object) [
                'customer' => $result
            ];
            return $output;
        } else {
            $stmtTrips = $conn->prepare("SELECT
            customers.customer_id,
            customers.first_name, 
            customers.last_name,
            customers.email, 
            customers.phone,
            DATE_FORMAT(trips.date, '%d-%m-%Y') as date, 
            DATE_FORMAT(trips.start_time, '%H:%i') as start_time,
            DATE_FORMAT(trips.end_time, '%H:%i') as end_time,
            pd_locations_pickup.name as pickup_loc_name, 
            pd_locations_dropoff.name as dropoff_loc_name

            FROM customers
            LEFT JOIN trips ON customers.customer_id = trips.customer_id
            LEFT JOIN pd_locations as pd_locations_pickup ON trips.pickup_loc_id = pd_locations_pickup.location_id
            LEFT JOIN pd_locations as pd_locations_dropoff ON trips.dropoff_loc_id = pd_locations_dropoff.location_id
            WHERE customers.customer_id = :idVal;
            ");

            $stmtTrips->execute([
                'idVal' => $id
            ]);

            $result = $stmtTrips->fetch();
            $output = (object) [
                'customer' => $result
            ];
            return $output;
        }
    }

    /**
     * Get all customers with pagination
     * 
     * @param int $page The page number (remember to convert the human page to mysql page) 1->0
     * @param int $perPage The amount of items per page
     * 
     * @return array Of all customers
     */
    public function getCustomers(int $page, int $perPage)
    {
        // Since MySQL pages start at 0, we deduct 1 of the value and add it back after the query
        $page = $page - 1;
        $offset = $page * $perPage;

        $orderStatement = "customers.time_of_booking DESC, customers.date DESC";

        // Run the query and get customers limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT customers.customer_id, 
        DATE_FORMAT(customers.time_of_booking, '%d-%m-%Y') as date, 
        DATE_FORMAT(customers.time_of_booking, '%H:%i') as time_of_booking, 
        customers.first_name, 
        customers.last_name, 
        customers.email,
        customers.phone, 
        customers.is_event, 
        customers.group_size
        FROM customers
        ORDER BY DATE(customers.time_of_booking) DESC
        LIMIT :offsetVal,:perPageVal;
        ");

        $stmt->execute([
            'perPageVal' => $perPage,
            'offsetVal' => $offset
        ]);

        $result = $stmt->fetchAll();

        $this->getTotalCustomersAmount();

        $outOfPages = ceil($this->totalCustomersAmount / $perPage);

        $output = (object) [
            'page' => $page + 1, // add 1 back to the page value
            'per_page' => $perPage,
            'out_of_pages' => $outOfPages,
            'customers' => $result
        ];
        return $output;
    }

    public function getTotalCustomersAmount()
    {
        $conn = Database::connect();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM customers 
        ");

        $stmt->execute();
        $result = $stmt->fetch();
        Database::disconnect();
        $count = $result['COUNT(*)'];
        $this->totalCustomersAmount = $count;
        return $count;
    }

    /**
     * Delete a customer by Id
     * 
     * @param int $id Id of the customer to delete
     * 
     * @return array Message of failed/successfull delete of customer
     */
    public function deleteCustomerById(int $id)
    {
        // delete a single customer by id
        $conn = Database::connect();

        try {
            // Start transaction - only commit a delete if every step is successfull
            $conn->beginTransaction();

            // Get customer data
            $conn = Database::connect();
            $stmtCust = $conn->prepare("SELECT *
            FROM customers 
            WHERE customer_id = :idVal;
            ");

            $resultCust = $stmtCust->execute([
                'idVal' => $id
            ]);

            if ($stmtCust->rowCount() < 1) {
                throw new Exception("Couldn't find customer");
            }

            // Prepare link delete sql
            $custIsEvent = $resultCust['is_event'];

            if ($custIsEvent == 0) {
                // not an event -> delete the linked trip
                $removeCustomerLinkSQL = "DELETE FROM trips 
                WHERE
                    customer_id = :idVal;";
            } else {
                // event -> delete row from event_customer_link
                $removeCustomerLinkSQL = "DELETE FROM event_customer_link 
                WHERE
                    customer_id = :idVal;";
            }

            // Execute delete sql
            $stmtDelLink = $conn->prepare($removeCustomerLinkSQL);
            $stmtDelLink->execute([
                'idVal' =>  $id
            ]);

            if ($stmtDelLink->rowCount() < 1) {

                throw new Exception("Couldn't remove customers other tables link");
            }

            // Delete the customer
            $stmtDel = $conn->prepare("DELETE FROM customers
            WHERE customer_id = :idVal;");
            $stmtDel->execute([
                'idVal' => $id
            ]);
            $eResult = $stmtDel->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if ($eResult < 1) {
                // Couldn't delete customer
                throw new Exception("Couldn't delete customer");
            }

            $conn->commit(); // commit the transaction

            // Create output object
            $output = (object) [
                'message' => "The customer with the customer_id = {$id} has been deleted",
                'customer_id' => $id,
                'status' => 1
            ];
            return $output;
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'customer_id' => $id,
                'status' => 0
            ];
            return $output;
        }
    }


    /**
     * Search inside customers table for first_name, last_name, email or phone number
     * Works with pagination
     * 
     * @param string $term The string to search for, needs minimum 2 characters
     * 
     * @return 
     */
    public function search(int $page, int $perPage, string $term)
    {
        // Create the return template with default status 0
        $return = [
            'status' => 0,
            'message' => 'Failed',
            'error_info' => 'none',
            'customers' => [],
            'page' => 1, // add 1 back to the page value
            'per_page' => 20,
            'out_of_pages' => 0,
        ];

        if (!val_exists($term)) {
            //Invalid value
            $return['message'] = 'No text given or invalid';

            return $return;
        }

        // Only allow search if there are more than two characters
        if (strlen($term) < 2) {

            $return['message'] = 'No text given or invalid';
            $return['status'] = 1;

            return $return;
        }

        $searchTerm =  ht($term) . '%'; // sanitize

        // START SEARCH
        $page = $page - 1;
        $offset = $page * $perPage;

        // Run the query and get customers limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT customers.customer_id, 
        DATE_FORMAT(customers.time_of_booking, '%d-%m-%Y') as date, 
        DATE_FORMAT(customers.time_of_booking, '%H:%i') as time_of_booking, 
        customers.first_name, 
        customers.last_name, 
        customers.email,
        customers.phone, 
        customers.is_event, 
        customers.group_size
        FROM customers
        WHERE customers.first_name LIKE :termVal1
        OR customers.last_name LIKE :termVal2
        OR customers.email LIKE :termVal3
        OR customers.phone LIKE :termVal4
        ORDER BY customers.time_of_booking DESC, date DESC
        LIMIT :offsetVal,:perPageVal;
        ");

        $eResult = $stmt->execute([
            'perPageVal' => $perPage,
            'offsetVal' => $offset,
            'termVal1' => $searchTerm,
            'termVal2' => $searchTerm,
            'termVal3' => $searchTerm,
            'termVal4' => $searchTerm
        ]);

        if (!$eResult) {
            $return['message'] = 'Search query failed';
            return $return;
        }

        $result = $stmt->fetchAll();

        $this->getTotalCustomersAmount();

        $outOfPages = ceil($this->totalCustomersAmount / $perPage);

        $return['status'] = 1;
        $return['customers'] = $result;
        $return['page'] = $page + 1;
        $return['perPage'] = $perPage;
        $return['out_of_pages'] = $outOfPages;
        $return['message'] = 'Successfully searched';

        return $return;
    }
}
