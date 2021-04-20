<?php

class Events
{

    private $totalEventsAmount;
    private $perPage = 20; // Maximum of events to show on a page during pagination
    private $defaultLocation = 'Ved slusen (Sluseholmen)';


    /**
     * Constructor class
     * 
     * Run the getTotalEventsAmount() on each instance
     */
    public function __construct($options = [])
    {
        // Check if the perPage option has been defined, else use default of 20
        if (array_key_exists("perPage", $options)) {
            $this->perPage = $options['perPage']; // set new amount of trips per page (limit)
        }

        $this->getTotalEventsAmount();
    }

    /**
     * Get a single event using its id
     * If the event doesn't exist, return FALSE
     * 
     * @return array|false Returns either the data of the single event or false if it doesn't exist
     */
    public function getEventById(int $id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT events.event_id, 
        DATE_FORMAT(events.date, '%d-%m-%Y') as date, 
        DATE_FORMAT(events.start_time, '%H:%i') as start_time, 
        DATE_FORMAT(events.end_time, '%H:%i') as end_time, 
        pd_locations_pickup.name as pickup_loc_name, 
        pd_locations_dropoff.name as dropoff_loc_name, 
        events.name, events.img, 
        events.description, 
        events.price_person, 
        events.committed, users.first_name as captain_name
        FROM events
        LEFT JOIN pd_locations as pd_locations_pickup ON events.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON events.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON events.assigned_captain_user_id = users.user_id
        WHERE event_id = :idVal;
        ");
        $stmt->execute([
            'idVal' => $id
        ]);
        $result = $stmt->fetch();
        $count = $stmt->rowCount();

        if ($count < 1) {
            return (object) [
                'event' => null,
                'status' => 1,
                'found' => 0
            ];
        }

        Database::disconnect();
        $output = (object) [
            'event' => $result,
            'found' => 1
        ];

        return $output;
    }

    /**
     * Get all events with pagination
     * 
     * @param int $page The number of the page for pagination
     * @param int $perPage The amount of events to return per page
     * @param bool $past Switch between upcoming or past events using this boolean variable. $past = TRUE; will return events older than yesterday
     * 
     * @return array Returns array of associative arrays containing all trips respecting pagination parameters
     */
    public function getEvents(int $page, int $perPage, bool $past = FALSE)
    {

        // Create the WHERE statement to either get past or upcoming events
        $pastStatement = $past ? "events.date < subdate(current_date, 1)" : "events.date > subdate(current_date, 1)";

        // Based on past or upcoming events order by DESCENDING dates (for past) or ASCENDING DATES (For upcoming)
        $orderStatement = $past ? "events.date DESC, events.start_time DESC" : "events.date ASC, events.start_time ASC";

        // Since MySQL pages start at 0, we deduct 1 of the value and add it back after the query
        $page = $page - 1;

        $offset = $page * $perPage;

        // Run the query and get events limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT events.event_id, 
        DATE_FORMAT(events.date, '%d-%m-%Y') as date, 
        DATE_FORMAT(events.start_time, '%H:%i') as start_time,
        DATE_FORMAT(events.end_time, '%H:%i') as end_time, 
        pd_locations_pickup.name as pickup_loc_name, 
        pd_locations_dropoff.name as dropoff_loc_name, 
        events.name, 
        users.first_name as captain_name
        FROM events
        LEFT JOIN pd_locations as pd_locations_pickup ON events.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON events.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON events.assigned_captain_user_id = users.user_id
        WHERE {$pastStatement}
        ORDER BY {$orderStatement}
        LIMIT :offsetVal,:perPageVal;
        ");
        $stmt->execute([
            'perPageVal' => $perPage,
            'offsetVal' => $offset
        ]);

        $result = $stmt->fetchAll();
        Database::disconnect();

        // Update totalEventsAmount based on upcoming or past events
        $this->getTotalEventsAmount($past);

        // Get max pages possible with pagination limits
        $outOfPages = ceil($this->totalEventsAmount / $perPage);

        // Create output object
        $output = (object) [
            'page' => $page + 1, // add 1 back to the page value
            'per_page' => $perPage,
            'out_of_pages' => $outOfPages,
            'events' => $result
        ];

        return $output;
    }


    /**
     * Get all events with pagination
     * 
     * @param int $page The number of the page for pagination
     * @param int $perPage The amount of events to return per page
     * @param bool $past Switch between upcoming or past events using this boolean variable. $past = TRUE; will return events older than yesterday
     * 
     * @return array Returns array of associative arrays containing all trips respecting pagination parameters
     */
    public function getThreeUpcomingEvents(int $page, int $perPage, bool $past = FALSE)
    {

        // Create the WHERE statement to either get past or upcoming events
        $pastStatement = $past ? "events.date < subdate(current_date, 1)" : "events.date > subdate(current_date, 1)";

        // Based on past or upcoming events order by DESCENDING dates (for past) or ASCENDING DATES (For upcoming)
        $orderStatement = $past ? "events.date DESC, events.start_time DESC" : "events.date ASC, events.start_time ASC";

        // Since MySQL pages start at 0, we deduct 1 of the value and add it back after the query
        $page = $page - 1;

        //$offset = $page * $perPage;

        // Run the query and get events limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT events.event_id, 
        DATE_FORMAT(events.date, '%d-%m-%Y') as date, 
        DATE_FORMAT(events.start_time, '%H:%i') as start_time,
        DATE_FORMAT(events.end_time, '%H:%i') as end_time, 
        events.name, events.description, events.img
        FROM events
        WHERE {$pastStatement}
        ORDER BY {$orderStatement}
        LIMIT :perPageVal;
        ");
        $stmt->execute([
            'perPageVal' => $perPage
        ]);

        $result = $stmt->fetchAll();
        Database::disconnect();

        // Update totalEventsAmount based on upcoming or past events
        $this->getTotalEventsAmount($past);

        // Get max pages possible with pagination limits
        $outOfPages = ceil($this->totalEventsAmount / $perPage);

        // Create output object
        $output = (object) [
            'page' => $page + 1, // add 1 back to the page value
            'per_page' => $perPage,
            'out_of_pages' => $outOfPages,
            'events' => $result
        ];

        return $output;
    }




    /**
     * Get the total amount of events
     * 
     * @param bool $past Switch the amount between upcoming or past events using this boolean variable. $past = TRUE; will return the total of passed events. This value is optional and by default FALSE
     * 
     * @var int $totalEventsAmount Get and set the amount of total events
     * @return int Return the amount of total events
     */
    public function getTotalEventsAmount(bool $past = FALSE)
    {
        $pastStatement = $past ? "events.date < subdate(current_date, 1)" : "events.date > subdate(current_date, 1)";
        $conn = Database::connect();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM events 
        WHERE {$pastStatement}");

        $stmt->execute();
        $result = $stmt->fetch();
        Database::disconnect();
        $count = $result['COUNT(*)'];
        $this->totalEventsAmount = $count;
        return $count;
    }

    /**
     * Get all attendees of an event using the event id
     * 
     * @param int $event_id The id of the event
     * 
     * @return array
     */
    public function getAttendeesByEventId(int $id)
    {
        // Check if id exists
        if (empty($id) && !strlen($id)) {
            return [
                'status' => 0,
                'message' => 'No event specified',
                'attendees' => []
            ];
        }

        $conn = Database::connect();

        // 1. Get all attendees id's
        $stmtAttndIds = $conn->prepare("SELECT customer_id FROM event_customer_link 
        WHERE event_id = :idVal");
        $stmtAttndIds->execute([
            'idVal' => $id
        ]);
        $attendeesIds = $stmtAttndIds->fetchAll(); // save all attendees id's

        // 2.A if no attendees exists, return success
        if ($stmtAttndIds->rowCount() == 0) {
            return [
                'status' => 1,
                'message' => 'No attendees found',
                'attendees' => []
            ];
        }

        // 2.B if attendees exists, fetch their data
        $attendeesIdsString = [];
        foreach ($attendeesIds as $id) {
            array_push($attendeesIdsString, "{$id['customer_id']}");
        }
        $attendeesIdsString;
        $attendeesIdsString = implode(',', $attendeesIdsString);
        $stmtAttnd = $conn->prepare("SELECT * FROM customers
        WHERE customer_id 
        IN ({$attendeesIdsString}); "); // here we have to directly inject the string as PDO doesn't support binding IN values -> https://stackoverflow.com/a/1586650/3673659
        $stmtAttnd->execute();
        $attendees = $stmtAttnd->fetchAll();
        if ($stmtAttnd->rowCount() == 0) {
            return [
                'status' => 0,
                'message' => 'Attendees do not exist in the Database',
                'attendees' => []
            ];
        }

        // 3. Return attendees
        return [
            'status' => 1,
            'message' => 'Attendees found',
            'attendees' => $attendees
        ];
    }

    /**
     * Get the amount of people for a single event using the event id
     * 
     * @param int $event_id The id of the event
     * 
     * @return int The amount of people joining an event
     */
    public function getPeopleAmountByEventId(int $id)
    {
        // Check if id exists
        if (empty($id) && !strlen($id)) {
            return [
                'status' => 0,
                'message' => 'No event specified',
                'people' => 0
            ];
        }

        $attendees = $this->getAttendeesByEventId($id);

        if (count($attendees['attendees']) < 1) {
            //No attendees
            return [
                'status' => 1,
                'message' => 'No attendees found',
                'people' => 0
            ];
        }
        $people = 0;

        foreach ($attendees['attendees'] as $attendee) {
            $people += (int) $attendee['group_size'];
        }

        return [
            'status' => 1,
            'message' => 'Successfully found the amount of people',
            'people' => $people
        ];
    }

    /**
     * Get all captains
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
     * Create a new event
     * 
     * @param array $data An associative array of data that are required to create a new event. E.g. data['title'], data['time'], data['image'],...
     * 
     * @return array Return a copy of the created event with it's id.
     */

    public function createNewEvent(
        $eventTitle,
        $eventDate,
        $eventStart,
        $eventEnd,
        $eventPrice,
        $eventDescription,
        $eventImg,
        $eventCaptain
    ) {
        $conn = Database::connect();
        $stmt = $conn->prepare("INSERT INTO events
        (date, start_time, end_time, pickup_loc_id, dropoff_loc_id, name, img, description, price_person, assigned_captain_user_id)
        VALUES
        (:dateVal, :startTimeVal, :endTimeVal, '1', '1', :nameVal, :imgVal, :descriptionVal, :priceVal, :captainVal );");
        $stmtEx = $stmt->execute([
            'dateVal' => $eventDate,
            'startTimeVal' => $eventStart,
            'endTimeVal' => $eventEnd,
            'nameVal' => $eventTitle,
            'imgVal' => $eventImg,
            'descriptionVal' => $eventDescription,
            'priceVal' => $eventPrice,
            'captainVal' => $eventCaptain
        ]);

        if ($stmtEx) {
            //statement was successful
            $lastId = $conn->lastInsertId();
            return $this->getEventById($lastId);
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'insert failed'
            ];
        }
    }

    /**
     * Add event booking
     * 
     * @param array $args = [
     *                  'event_id'      =>  (int)       The id of the event
     *                  'first_name'    =>  (string)    The first name of the customer
     *                  'last_name'     =>  (string)    The last name of the customer
     *                  'email'         =>  (string)    The email of the customer
     *                  'phone'         =>  (string)    The phone number of the customer
     *                  'group_size'    =>  (string)    The amount of people the customer wants to book for
     *              ]
     * 
     * @return array The status code 0 = false; 1 = true; with a message
     */
    public function customerEventBooking($args = [])
    {
        // Create the return template with default status 0
        $return = [
            'status' => 0,
            'message' => 'Failed',
            'error_info' => 'none'
        ];

        // Check for the values existence using custom val_exists() method inside functions.php
        // + sanitize the inputs
        $params = [
            'event_id' => 'Event id',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'phone' => 'Phone',
            'group_size' => 'Group size'
        ];

        foreach ($params as $key => $param) {
            if (!@val_exists($args[$key])) {
                return $return['message'] = "{$param} value is missing";
            }
            $args[$key] = is_numeric($args[$key]) ? $args[$key] : ht($args[$key]); //If non-number value, sanitize it
        }

        if (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
            return $return['message'] = "Invalid email";
        }

        // Create new customer and link it up with the event
        $conn = Database::connect();
        try {

            // Start transaction - only commit a book after every step was successfull
            $conn->beginTransaction();

            // Create new customer
            $stmtCust = $conn->prepare("INSERT INTO customers
            (first_name, last_name, email, phone, group_size, is_event, paid)
            VALUES
            (:firstNameVal, :lastNameVal, :emailVal, :phoneVal, :groupSizeVal, 1, 0);");
            $stmtCust->execute([
                'firstNameVal' => $args['first_name'],
                'lastNameVal' => $args['last_name'],
                'emailVal' => $args['email'],
                'phoneVal' =>  $args['phone'],
                'groupSizeVal' => $args['group_size']
            ]);
            $cResult = $stmtCust->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$cResult) {
                // Couldn't create customer
                throw new Exception("Couldn't create new customer");
            }

            $customerId = $conn->lastInsertId();

            // Link the customer up with the event
            $stmtEvt = $conn->prepare("INSERT INTO event_customer_link
            (event_id, customer_id)
            VALUES
            (:eventIdVal, :customerIdVal);");
            $stmtEvt->execute([
                'eventIdVal' => $args['event_id'],
                'customerIdVal' => $customerId
            ]);
            $eResult = $stmtEvt->rowCount();
            if (!$cResult) {
                // Couldn't create customer
                throw new Exception("Couldn't link the customer with event");
            }

            $conn->commit(); // commit the transaction
            Database::disconnect();

            // $return['status'] = 1;
            // $return['message'] = "Booking successfull";
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            return $return['message'] = $e->getMessage();
        }

        // Send confirmation email
        $event = $this->getEventById($args['event_id'])->event;
        $pickupMail = $event['start_time'] . ' - ' . $this->defaultLocation;
        $dropoffMail = $event['end_time'] . ' - ' . $this->defaultLocation;
        $price = (int) $args['group_size'] * $event['price_person'];

        // Send confirmation email
        $sendMail = Mail::sendBookingConfirmation([
            'recipient_mail' => $args['email'],
            'recipient_name' => $args['first_name'],
            'from_name' => 'Boatie Booking',
            'pickup' => $pickupMail,
            'dropoff' => $dropoffMail,
            'price' => $price,
            'date' => convertDateToFriendly($event['date'], 'd-m-Y'),
            'is_event' => true,
            'event_name' => $event['name'],
            'group_size' => $args['group_size'],
            'title' => "Booking confirmation {$event['name']}",

        ]);

        if ($sendMail['status'] == 0) {
            $return['message'] = $sendMail['message'];
            $return['status'] = 0;
            $return['error_info'] = $sendMail['errorInfo'];

            return $return;
        }



        return $return;
    }

    /**
     * Update an existing event
     * 
     *  @param int $id The id of the event to update
     * 
     * @return boolean Return a true or false upon successfull/failed deletion 
     */

    public function updateEvent(
        int $id,
        $eventTitle,
        $eventDate,
        $eventStart,
        $eventEnd,
        int $eventPrice,
        $eventDescription,
        $eventImg,
        int $eventCaptain
    ) {
        $conn = Database::connect();
        $stmt = $conn->prepare("UPDATE events 
        SET
            date = :dateVal,
            start_time = :startTimeVal,
            end_time = :endTimeVal,
            name = :nameVal,
            img = :imgVal,
            description = :descriptionVal,
            price_person = :priceVal,
            assigned_captain_user_id = :captainVal
        WHERE
            event_id = :idVal;");

        $stmtUp = $stmt->execute([
            'idVal' => $id,
            'dateVal' => $eventDate,
            'startTimeVal' => $eventStart,
            'endTimeVal' => $eventEnd,
            'nameVal' => $eventTitle,
            'imgVal' => $eventImg,
            'descriptionVal' => $eventDescription,
            'priceVal' => $eventPrice,
            'captainVal' => $eventCaptain
        ]);

        if ($stmtUp) {
            //statement was successful
            return $this->getEventById($id);
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'update failed'
            ];
        }
    }


    /**
     * Delete event by Id
     * 
     *  @param int $id The id of the event to delete
     * 
     * @return boolean Return a true or false upon successfull/failed deletion 
     */

    public function deleteEventByID(int $id)
    {
        $conn = Database::connect();

        try {

            // Start transaction - only commit a delete if every step is successful
            $conn->beginTransaction();

            // Get all customer id's from event linking table 
            $stmtAttndIds = $conn->prepare("SELECT customer_id FROM event_customer_link
            WHERE event_id = :idVal");
            $attndIdsResult =  $stmtAttndIds->execute([
                'idVal' => $id
            ]);

            // Save attendee id's in variable 
            $attendeesIds = $stmtAttndIds->fetchAll();

            // If no attendees exist, throw exception
            if (!$attndIdsResult) {
                throw new Exception("Couldn't get attendees");
            }

            // If attendees exist, delete them from the customers table
            if ($stmtAttndIds->rowCount() != 0) {
                $attendeesIdsString = [];
                foreach ($attendeesIds as $attendeeId) {
                    array_push($attendeesIdsString, "{$attendeeId['customer_id']}");
                }
                $attendeesIdsString;
                $attendeesIdsString = implode(',', $attendeesIdsString);
                var_dump($attendeesIdsString);

                // Delete customer from customers table //DC (delete customer)
                $stmtDC = $conn->prepare("DELETE FROM customers
                WHERE customer_id
                IN ({$attendeesIdsString});");

                $stmtDC->execute();
                if ($stmtDC->rowCount() == 0) {
                    throw new Exception("Couldn't delete attendees");
                }
            }


            // Delete the event from events table //DE (delete event)
            $stmtDE = $conn->prepare("DELETE FROM events
            WHERE event_id = :idVal;");
            $stmtDE->execute([
                'idVal' => $id
            ]);

            $eResult = $stmtDE->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$eResult) {
                // Couldn't delete event
                throw new Exception("Couldn't delete event");
            }

            //Delete event from event_customer_link table //DEL (delete event link)
            if ($stmtAttndIds->rowCount() != 0) {
                $stmtDEL = $conn->prepare("DELETE FROM event_customer_link
            WHERE event_id = :idVal;");
                $stmtDEL->execute([
                    'idVal' => $id
                ]);
                $elResult = $stmtDEL->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

                if (!$elResult) {
                    // Couldn't delete event
                    throw new Exception("Couldn't delete event");
                }
            }

            $conn->commit(); // commit the transaction
            Database::disconnect();

            // Create output object
            $output = (object) [
                'message' => "The event with the event_id = {$id} have successfully been deleted",
                'event_id' => $id,
                'status' => 1
            ];
            return $output;
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'event_id' => $id,
                'status' => 0
            ];
            return $output;
        }
    }


    /**
     * Delete attendee/customer from event
     * 
     *  @param int $id The id of the attendee to delete
     * 
     * @return boolean Return a true or false upon successfull/failed deletion 
     */

    public function deleteAttendeeByID(int $id)
    {
        $conn = Database::connect();

        try {

            // Start transaction - only commit a delete if every step is successfull
            $conn->beginTransaction();

            // Delete the attendee from the customers table
            $stmtDC = $conn->prepare("DELETE FROM customers
            WHERE customer_id = :idVal;");
            $stmtDC->execute([
                'idVal' => $id
            ]);

            $cResult = $stmtDC->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$cResult) {
                // Couldn't delete attendee
                throw new Exception("Couldn't delete attendee from customers");
            }

            // Delete the attendee from the events_customers linking table
            $stmtDCE = $conn->prepare("DELETE FROM event_customer_link
            WHERE customer_id = :idVal;");
            $stmtDCE->execute([
                'idVal' => $id
            ]);

            $ceResult = $stmtDCE->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$ceResult) {
                // Couldn't delete attendee
                throw new Exception("Couldn't delete attendee from event_customers_link");
            }


            $conn->commit(); // commit the transaction

            Database::disconnect();

            // Create output object
            $output = (object) [
                'message' => "The attendee with the customer_id = {$id} have successfully been deleted",
                'customer_id' => $id,
                'status' => 1
            ];
            return $output;
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'status' => 0
            ];
            return $output;
        }
    }


    /**
     * Get all events to display on the frontend for visitors
     * 
     * @return array Returns array of associative arrays containing all trips respecting pagination parameters
     */
    public function getAllUpcomingEvents()
    {
        // Run the query and get events limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT events.event_id, DATE_FORMAT(events.date, '%d-%m-%Y') as date, DATE_FORMAT(events.start_time, '%H:%i') as start_time, DATE_FORMAT(events.end_time, '%H:%i') as end_time, events.name, events.img, events.description, events.price_person
        FROM events
        LEFT JOIN pd_locations as pd_locations_pickup ON events.pickup_loc_id = pd_locations_pickup.location_id
        LEFT JOIN pd_locations as pd_locations_dropoff ON events.dropoff_loc_id = pd_locations_dropoff.location_id
        LEFT JOIN users ON events.assigned_captain_user_id = users.user_id
        WHERE events.date > subdate(current_date, 1)
        ORDER BY events.date ASC, events.start_time ASC;
        ");
        $stmtStatus = $stmt->execute();

        $result = $stmt->fetchAll();
        Database::disconnect();

        if (!$stmtStatus) {
            return  [
                'status' => 0,
                'message' => 'Could not get events'
            ];
        }

        // Create output object
        return  [
            'message' => 'Events success',
            'status' => 1,
            'events' => $result
        ];
    }
}
