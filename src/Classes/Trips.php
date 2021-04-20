<?php

/**
 * Trips class containing all methods used to CRUD the trips table
 */
class Trips
{


    private $totalTripsAmount; // total amount of trips
    private $perPage = 30; // Maximum of trips to show on a page during pagination
    private $defaultCaptainId = 1; //The default id to use for new trips
    private $pricePerHour = 1200;
    /**
     * Constructor class
     * 
     * Get the total amount of trips from the DB.
     * Also set the options on how to limit
     * 
     */
    public function __construct($options = [])
    {
        // Check if the perPage option has been defined, else use default of 30
        if (array_key_exists("perPage", $options)) {
            $this->perPage = $options['perPage']; // set new amount of trips per page (limit)
        }
        $this->getTotalTripsAmount();
        // // Set total amount of trips
        // $this->getTotalTripsAmount();
    }


    /**
     * Get a single trip by using its id
     * 
     * @param integer $id Id of the trip
     * 
     * @return array Return an associative array containing a single trips values
     */
    public function getTripById(int $id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT 
        trips.trip_id, 
        DATE_FORMAT(trips.date, '%a %D, %M %Y') as date,  
        DATE_FORMAT(trips.start_time, '%H:%i') as start_time, 
        DATE_FORMAT(trips.end_time, '%H:%i') as end_time, trips.customer_thoughts, 
        customers.customer_id, 
        customers.first_name, 
        customers.last_name, 
        customers.paid, 
        customers.email, 
        customers.phone , 
        pd_locations_pickup.name as pickup_loc_name, 
        pd_locations_dropoff.name as dropoff_loc_name, 
        users.first_name as captain_name, 
        users.user_id as captain_id
        FROM trips
        LEFT JOIN customers ON trips.customer_id = customers.customer_id
        LEFT JOIN pd_locations as pd_locations_pickup ON trips.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON trips.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON trips.assigned_captain_user_id = users.user_id
        WHERE trip_id = :idVal;
        ");
        $stmt->execute([
            'idVal' => $id
        ]);
        $result = $stmt->fetch();

        $output = (object) [
            'trip' => $result
        ];
        return $output;
    }

    /**
     * Get all trips with pagination 
     * 
     * @param int $page The number of the page for pagination, page starts at 1
     * @param int $perPage The amount of items to return per page
     * @param bool $past Switch between upcoming trips or past trips. $past = TRUE; will return trips older than yesterday.
     * 
     * @return array Return an array of associative arrays containing all trips used with pagination
     * 
     */
    public function getTrips(int $page, int $perPage, bool $past = FALSE)
    {


        // Create the WHERE statement to either get past or upcoming trips
        $pastStatement = $past ? "trips.date < subdate(current_date, 1)" : "trips.date > subdate(current_date, 1)";

        // Based on past or upcoming trips order by DESCENDING dates (for past) or ASCENDING DATES (For upcoming)
        $orderStatement = $past ? "trips.date DESC, trips.start_time DESC" : "trips.date ASC, trips.start_time ASC";

        // Since MySQL pages start at 0, we deduct 1 of the value and add it back after the query
        $page = $page - 1;

        /**
         * Pagination in MySQL works like this
         * 
         * 1. Select the items you want
         * 2. Use "LIMIT offset, row_count" for pagination
         * 
         * offset is the value where to start from
         * row_count is the value that says how many rows starting from offset should be fetched.
         * 
         * So, e.g.
         *      - Page 0, Limit 10 per page(aka, get first 10) is written like this:
         *          LIMIT 0,10;
         * 
         *      - Page 2, Limit 10 per page(aka, get from 20 - 30) is written like this:
         *          LIMIT 20,10;
         * 
         * More info: https://stackoverflow.com/a/3799223/3673659
         */
        $offset = $page * $perPage;

        // Run the query and get trips limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT 
            trips.trip_id, 
            DATE_FORMAT(trips.date, '%d-%m-%Y') as date, 
            DATE_FORMAT(trips.start_time, '%H:%i') as start_time, 
            DATE_FORMAT(trips.end_time, '%H:%i') as end_time, 
            customers.first_name, 
            customers.last_name, 
            customers.paid, 
            pd_locations_pickup.name as pickup_loc_name, 
            pd_locations_dropoff.name as dropoff_loc_name, 
            users.first_name as captain_name
        FROM trips
        LEFT JOIN customers ON trips.customer_id = customers.customer_id
        LEFT JOIN pd_locations as pd_locations_pickup ON trips.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON trips.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON trips.assigned_captain_user_id = users.user_id
        WHERE {$pastStatement}
        ORDER BY {$orderStatement}
        LIMIT :offsetVal,:perPageVal;
        ");
        $stmt->execute([
            'perPageVal' => $perPage,
            'offsetVal' => $offset
        ]);
        $result = $stmt->fetchAll();

        // var_dump($page);

        // Update totalTripsAmount based on upcoming or past trips
        $this->getTotalTripsAmount($past);

        // Get max pages possible with pagination limits
        $outOfPages = ceil($this->totalTripsAmount / $perPage);

        $page = $page < 1 ? 0 : $page;

        // Create output object
        $output = (object) [
            'page' => $page + 1, // add 1 back to the page value
            'per_page' => $perPage,
            'out_of_pages' => $outOfPages,
            'trips' => $result
        ];
        return $output;
    }

    /**
     * Get all trips without pagination (not really in use)
     * 
     * @return array Return an array of associative arrays containing all trips
     */
    public function getAllTrips()
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT 
        trips.trip_id, 
        trips.date, 
        trips.start_time, 
        trips.end_time, 
        customers.first_name, 
        customers.last_name, 
        customers.paid, 
        pd_locations_pickup.name as pickup_loc_name, 
        pd_locations_dropoff.name as dropoff_loc_name, 
        users.first_name as captain_name
        FROM trips
        LEFT JOIN customers ON trips.customer_id = customers.customer_id
        LEFT JOIN pd_locations as pd_locations_pickup ON trips.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON trips.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON trips.assigned_captain_user_id = users.user_id;");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $output = (object) [
            'amount_of_trips' => $this->totalTripsAmount,
            'trips' => $result
        ];
        return $output;
    }

    /**
     * Get the value of totalTripsAmount
     * 
     * @param bool $past The switch variable used to return the total amount of upcoming or passed trips
     * 
     * @return int The amount of trips for either upcoming or passed trips
     */

    public function getTotalTripsAmount(bool $past = FALSE)
    {
        // Create the WHERE statement to either get past or upcoming trips
        $pastStatement = $past ? "trips.date < subdate(current_date, 1)" : "trips.date > subdate(current_date, 1)";

        $conn = Database::connect();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM trips 
        WHERE {$pastStatement}");

        $stmt->execute();
        $result = $stmt->fetch();
        $count = $result['COUNT(*)'];
        $this->totalTripsAmount = $count;
        return $count;
    }

    /**
     * Delete a trip and the customer by the trips id
     * 
     * @param int $id The id of the requested trip to be deleted
     * 
     * @return object Returns object with upon success/failed deletion.
     */

    public function deleteTripById(int $id)
    {
        $conn = Database::connect();

        try {

            // Start transaction - only commit a delete if every step is successfull
            $conn->beginTransaction();

            // Get customer_id of the trip
            $stmtCId = $conn->prepare("SELECT customer_id FROM trips
            WHERE trip_id = :idVal;");
            $stmtCId->execute([
                'idVal' => $id
            ]);
            $customerId = $stmtCId->fetch()['customer_id'];

            if (!$customerId) {
                // Customer ID does not exist
                throw new Exception("Associated customer doesn't exist");
            }

            // Delete the associated customer of the trip
            $stmtDC = $conn->prepare("DELETE FROM customers
            WHERE customer_id = :idVal;");
            $stmtDC->execute([
                'idVal' => $customerId
            ]);
            $cResult = $stmtDC->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$cResult) {
                // Couldn't delete customer
                throw new Exception("Couldn't delete customer");
            }

            // Delete the trip
            $stmtDT = $conn->prepare("DELETE FROM trips
            WHERE trip_id = :idVal;");
            $stmtDT->execute([
                'idVal' => $id
            ]);
            $tResult = $stmtDT->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$tResult) {
                // Couldn't delete trip
                throw new Exception("Couldn't delete trip");
            }

            $conn->commit(); // commit the transaction


            // Create output object
            $output = (object) [
                'message' => "The trip with the trip_id = {$id} and the associated customer with the customer_id = {$customerId} have successfully been deleted.",
                'customer_id' => $customerId,
                'trip_id' => $id,
                'status' => 1
            ];
            return $output;
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'trip_id' => $id,
                'status' => 0
            ];
            return $output;
        }
    }


    /**
     * Create a new trip (Book a new trip)
     * 
     * @param array $args Array containing the necessary arguments
     *          $args = [
     *              'duration'          => (integer)    The duration of the trip,
     *              'pickup_loc_id'     => (integer)    The pickup location id,
     *              'dropoff_loc_id'    => (integer)    The pickup location id,
     *              'pickup_name'       => (string)     The name of the pickup location,
     *              'dropoff_name'      => (string)     The name of the dropoff location,
     *              'date'              => (date)       The date of the trip with format YYYY-MM-DD,
     *              'start_time'        => (time)       The start time of the trip,
     *              'first_name'        => (string)     The first name of the customer,
     *              'last_name'         => (string)     The last name of the customer,
     *              'email'             => (string)     The email of the customer,
     *              'phone'             => (string)     The phone of the customer,
     *              'thoughts'          => (string)     The additional thoughts of the customer
     *          ]
     * 
     * @return bool
     */
    public function newTrip(array $args = [])
    {
        // Check if the required arguments exist and are not null -> else return false
        if (
            !isset($args['duration']) || !isset($args['pickup_loc_id']) || !isset($args['dropoff_loc_id']) || !isset($args['date']) || !isset($args['start_time'])  ||
            !isset($args['first_name'])  || !isset($args['last_name'])  || !isset($args['email'])  || !isset($args['phone']) || !isset($args['pickup_name']) || !isset($args['dropoff_name'])
        ) {
            return (object) [
                'status' => 0,
                'message' => 'One or more arguments are missing'
            ];
        }

        $thoughts = isset($args['thoughts']) ? $args['thoughts'] : NULL;

        //Sanitize values which are not proven numeric values
        foreach ($args as $key => $arg) {
            if (!is_numeric($arg)) {
                $args[$key] = ht($arg);
            }
        }

        // Check if the date, start time and duration are available


        $endTimeMin = $this->convertTimeIntoMinutes($args['start_time']) + $args['duration'];
        $endTime = $this->convertMinutesIntoTimes($endTimeMin);

        $conn = Database::connect();

        try {
            //Create new customer and fetch the id
            // Start transaction - only commit a booking if every step is successfull
            $conn->beginTransaction();
            $stmtCust = $conn->prepare("INSERT INTO customers
            (first_name, last_name, email, phone, group_size, is_event, paid)
            VALUES 
            (:firstNameVal,:lastNameVal,:emailVal,:phoneVal, NULL,'0','0');
            ");
            $customerInsert = $stmtCust->execute([
                'firstNameVal' => $args['first_name'],
                'lastNameVal' => $args['last_name'],
                'emailVal' => $args['email'],
                'phoneVal' => $args['phone']

            ]);
            if (!$customerInsert) {
                throw new Exception("Couldn't add customer");
            }

            //Grab the customers id
            $customerId = $conn->lastInsertId();

            //Create new event
            $stmtTrip = $conn->prepare("INSERT INTO trips
            (date, start_time, end_time, pickup_loc_id, dropoff_loc_id, assigned_captain_user_id, customer_id , customer_thoughts)
            VALUES
            (:dateVal, :startTimeVal, :endTimeVal, :pickupLocIdVal, :dropoffLocIdVal, $this->defaultCaptainId, :customerIdVal,  :thoughtsVal);
            ");
            $tripsInsert = $stmtTrip->execute([
                'dateVal' => $args['date'],
                'startTimeVal' => $args['start_time'],
                'endTimeVal' => $endTime,
                'pickupLocIdVal' => $args['pickup_loc_id'],
                'dropoffLocIdVal' => $args['dropoff_loc_id'],
                'customerIdVal' => $customerId,
                'thoughtsVal' =>  $thoughts
            ]);
            if (!$tripsInsert) {
                throw new Exception("Couldn't book trip");
            }
            $tripId = $conn->lastInsertId();

            $conn->commit(); // commit the transaction
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            return (object) [
                'message' => $e->getMessage(),
                'status' => 0,
                'error' => $e
            ];
        }

        $price = ($this->pricePerHour / 60) * $args['duration'];


        $pickupMail = $args['start_time'] . ' - ' . $args['pickup_name'];
        $dropoffMail = $endTime . ' - ' . $args['dropoff_name'];

        // Send confirmation email
        $sendMail = Mail::sendBookingConfirmation([
            'recipient_mail' => $args['email'],
            'recipient_name' => $args['first_name'],
            'from_name' => 'Boatie Booking',
            'pickup' => $pickupMail,
            'dropoff' => $dropoffMail,
            'price' => $price,
            'date' => convertDateToFriendly($args['date']),
            'is_event' => false
        ]);

        if ($sendMail['status'] == 0) {
            return (object) [
                'message' => $sendMail['message'],
                'status' => 0,
                'error' => $sendMail['errorInfo']
            ];
        }

        return (object) [
            "message" => "Booking successfull",
            "status" => 1,
            "trip_id" => $tripId,
            "data" => $args,
            "price" => $price
        ];
    }

    /**
     * Return available hours of a date
     * 
     * @param date $date The date of the trip to check
     * @param integer $duration The duration of the trip to check in minutes
     * @param integer $pickup_loc_id The pickup location of the trip to check
     * @param integer $dropoff_loc_id The dropoff location of the trip to check
     * 
     * @return array Returns an array of available times (in 15min steps) based on the provided arguments
     */
    public function availableTimeSlots($date, $duration, $pickup_loc_id, $dropoff_loc_id)
    {
        // Check if the required arguments exist and are not null -> else return false
        if (!isset($date) || !isset($duration) || !isset($pickup_loc_id) || !isset($dropoff_loc_id)) {
            return [
                'status' => 0,
                'message' => 'One or more arguments are missing',
                'date_available' => 0
            ];
        }

        // Check if date is available using the unavailable dates
        $unavail =  new Unavailabilities();
        $isAvail = $unavail->isDateAvailable((string) $date);
        if ($isAvail['status'] == 0) {
            // date unavailable, return false
            return [
                'status' => 1,
                'message' => 'Date is not available',
                'date_available' => 0,
                'slots' => []
            ];
        }

        // Fetch all trips from that date
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT trips.trip_id, DATE_FORMAT(trips.date, '%d-%m-%Y') as date, DATE_FORMAT(trips.start_time, '%H:%i') as start_time, DATE_FORMAT(trips.end_time, '%H:%i') as end_time, pd_locations_pickup.name as pickup_loc_name, pd_locations_dropoff.name as dropoff_loc_name,  pd_locations_pickup.time_from_slus as pickup_time_from_slus, pd_locations_dropoff.time_from_slus as dropoff_time_from_slus, trips.pickup_loc_id, trips.dropoff_loc_id
        FROM trips
        LEFT JOIN pd_locations as pd_locations_pickup ON trips.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON trips.dropoff_loc_id = pd_locations_dropoff.location_id
        WHERE date = :dateVal
        ORDER BY start_time ASC
        ");
        $stmt->execute([
            'dateVal' => $date
        ]);
        $existingTrips = $stmt->fetchAll();


        // Make an array of all possible minutes time slots
        $avilableMinuteSlots = $this->dayInQuartMinutesInterval();
        $requestedPickupTimeFromSlus = 0;
        $requestedDropoffTimeFromSlus = 0;
        //Get all locations in an array, and add the time from slus values into their respective variables
        $locations = $this->getAllLocations();
        foreach ($locations as $i => $location) {
            if ($pickup_loc_id == $location['location_id']) {
                $requestedPickupTimeFromSlus = $location['time_from_slus'];
            }
            if ($dropoff_loc_id == $location['location_id']) {
                $requestedDropoffTimeFromSlus = $location['time_from_slus'];
            }
        }

        // Check based on start_time, end_time, and the distance difference using time_from_slus to remove impossible time slots
        foreach ($existingTrips as $i => $trip) {
            $start_time = $this->convertTimeIntoMinutes($trip['start_time']);
            $end_time = $this->convertTimeIntoMinutes($trip['end_time']);
            //$i is round
            //$trip is the array of a single trip
            //Calculate the minimum of time needed before and after the trip based on pickup and dropoff locations
            $existingPickupTimeFromSlus = $trip['pickup_time_from_slus'];
            $existingDropoffTimeFromSlus = $trip['dropoff_time_from_slus'];

            $gapBeforeTrip = abs($existingPickupTimeFromSlus - $requestedDropoffTimeFromSlus); //minimun timegap before the existing trip
            $gapAfterTrip = abs($existingDropoffTimeFromSlus - $requestedPickupTimeFromSlus); //minimun timegap after the existing trip

            $existingStart = $start_time - $gapBeforeTrip;
            $existingEnd = $end_time + $gapAfterTrip;

            // remove the start and end slots inside the availableMinutesSlots
            foreach ($avilableMinuteSlots as $i => $slot) {
                if ($existingStart < $slot  &&  $slot < $existingEnd) {
                    //remove slot
                    // unset($avilableMinuteSlots[$i]);
                    $avilableMinuteSlots[$i] = "unavailable";
                    // array_push($unavailableIndexSlots, $i);
                }
            }
        }

        // remove gaps between trips which are shorter than the requested duration
        $gapIndexes = 1 + ($duration / 15);
        $avilableMinuteSlotsCount = count($avilableMinuteSlots) -  $gapIndexes;

        for ($i = 0; $i < $avilableMinuteSlotsCount; $i++) {
            //Check if index start is numeric, if false, abort the round
            if (is_numeric($avilableMinuteSlots[$i])) {

                $intermissionArray = [];
                $index = $i;
                for ($is = 0; $is < $gapIndexes; $is++) {


                    if (is_numeric($avilableMinuteSlots[$index])) {
                        array_push($intermissionArray, $index);
                    }
                    $index++;
                }
                //check if the gap is bigger than the duration
                $intermissionSum = count($intermissionArray);
                // echo "{$intermissionSum} : {$gapIndexes}; <br>";
                if ($intermissionSum < $gapIndexes) {

                    //gap not big enough, mark all indexes as unavailble
                    foreach ($intermissionArray as $iq => $indexU) {
                        $avilableMinuteSlots[$indexU] = "unavailable";
                    }
                }
            }
        }

        //Convert all minutes back into time
        $avilableMinuteSlotsCount2 = count($avilableMinuteSlots);
        for ($i = 0; $i < $avilableMinuteSlotsCount2; $i++) {
            $value = $avilableMinuteSlots[$i];
            if (is_numeric($avilableMinuteSlots[$i])) {
                $avilableMinuteSlots[$i] = $this->convertMinutesIntoTimes($value);
            }
        }

        // Mark all time slots after 22.00 as on-request
        for ($i = 88; $i < 96; $i++) {
            $avilableMinuteSlots[$i] = 'on-request';
        }

        return [
            'slots' => $avilableMinuteSlots,
            'date_available' => 1
        ];
    }

    /**
     * Get all available pd_locations
     */
    public function getAllLocations()
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT *
        FROM pd_locations
        ORDER BY location_id ASC
        ");
        $stmt->execute();
        $locations = $stmt->fetchAll();
        return $locations;
    }

    /**
     * Get all captains
     * 
     * @return array Array of captains
     */
    public function getAllCaptains()
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT *
        FROM users");
        $stmt->execute();
        $captains = $stmt->fetchAll();

        return $captains;
    }

    /**
     * Update the captain of a trip
     * 
     * @param int $trip_id The id of the trip to update
     * @param int $capt_id The id of the captain to update to
     * 
     * @return array
     */
    public function updateTripCaptainById(int $trip_id, int $capt_id)
    {

        $params = [
            'trip_id' => 'Trip id',
            'capt_id' => 'Captain id'
        ];

        if (!@val_exists($trip_id)) {
            return [
                'status' => 0,
                'message' => "Trip id value is missing"
            ];
        }

        if (!@val_exists($capt_id)) {
            return [
                'status' => 0,
                'message' => "Captain id value is missing"
            ];
        }

        $conn = Database::connect();
        // Only update the capt_id inside the trip IF the capt_id exists inside users
        $stmt = $conn->prepare("UPDATE trips
        SET trips.assigned_captain_user_id = :captIdVal1
        WHERE EXISTS (SELECT * FROM users
                      WHERE users.user_id = :captIdVal2)
        AND
        trips.trip_id = :tripIdval;");
        $stmt->execute([
            'captIdVal1' => $capt_id,
            'captIdVal2' => $capt_id,
            'tripIdval' => $trip_id
        ]);
        if ($stmt->rowCount() < 1) {
            return [
                'status' => 0,
                'message' => "Couldn't update captain or captain does not exist"
            ];
        }

        return [
            'status' => 1,
            'message' => "Captain successfully updated"
        ];
    }

    /**
     * Return an array of all possible minutes in a 24h day with 15min intervals
     */
    public function dayInQuartMinutesInterval()
    {
        $quartMinutes = [];
        $minutes = 0;
        for ($i = 1; $i < 24 * 4 + 1; $i++) {
            array_push($quartMinutes, $minutes);
            // Add 15minutes for the next round
            $minutes += 15;
        }
        return $quartMinutes;
    }

    /**
     * Convert a time with format HH:MM or HH:MM:SS into minutes for a 24h day
     * E.g. 13:15 resolves into 13*60 = 780, and that additionally with the 15min is then 795 min.
     * 
     * @param time $time Time in format HH:mm to convert
     * @return integer
     */
    public function convertTimeIntoMinutes($time)
    {
        $timeArray = explode(':', $time);
        $hourInMin = $timeArray[0] * 60;
        $minutes = $hourInMin + $timeArray[1];
        return $minutes;
    }

    /**
     * Convert a minutes format of a 24h day into time format (HH:MM)
     * E.g. 795min resolves into 13:15
     * 
     * @param integer $minutes Minutes to convert
     * @return time Timeformat in HH:mm
     */
    public function convertMinutesIntoTimes($minutes)
    {
        $hours = sprintf("%02d", floor($minutes / 60));
        $min = sprintf("%02d", $minutes % 60);
        return "$hours:$min";
    }


    /**
     * Return an array of all possible hours:minutes in a 24h day with 15min intervals
     * 
     * @return array Array of possible hours:minutes combination during a 24h day with 15min intervals
     */
    public function quartHourTimes()
    {
        $quartHours = [];
        $hour = 0;
        $minutes = 0;
        for ($i = 1; $i < 24 * 4 + 1; $i++) {
            // Every 4th round, add 1 to hour and reset minute
            if ($i % 4 == 1 && $i != 1) {
                ++$hour;
                $minutes = 0;
            }

            //Convert hour and minutes into two digit
            $hour = sprintf("%02d", $hour);
            $minutes = sprintf("%02d", $minutes);

            $time = "{$hour}:{$minutes}";
            array_push($quartHours, $time);

            // Add 15minutes for the next round
            $minutes += 15;
        }
        return $quartHours;
    }
}
