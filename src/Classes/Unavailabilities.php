<?php

class Unavailabilities
{

    /**
     * Get future unavailabilities
     * 
     * 
     * 
     * 
     * 
     * @return array Return an array of associative arrays containing all future unavailabilities
     * 
     */


    public function getUnavailabilities()
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT unavailability.id, DATE_FORMAT(unavailability.start_date, '%d/%m/%Y') as start_date, DATE_FORMAT(unavailability.end_date, '%d/%m/%Y') as end_date FROM unavailability
        WHERE unavailability.start_date > subdate(current_date, 1);
        ");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $output = (object) [
            'unavailabilities' => $result
        ];
        return $output;
    }

    /**
     * Check if a date is available
     * 
     * @param string $date The date to check for, as "YYYY-MM-DD" format
     * 
     * @return boolean
     */
    public function isDateAvailable(string $date)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT *
        FROM unavailability
        WHERE  unavailability.start_date <= :dateVal1
        AND unavailability.end_date >= :dateVal2;
        ");
        $stmt->execute([
            ":dateVal1" => $date,
            ":dateVal2" => $date
        ]);
        $result = $stmt->fetchAll();
        if ($stmt->rowCount() > 0) {
            return [
                'status' => 0,
                'message' => 'Unavailable date'
            ];
        }
        return [
            'status' => 1,
            'message' => 'Date available'
        ];
    }

    public function createUnavailability($startDate, $endDate)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("INSERT INTO unavailability
        (start_date, end_date)
        VALUES
        (:startDateVal, :endDateVal);
        ");
        $stmtDate = $stmt->execute([
            'startDateVal' => $startDate,
            'endDateVal' => $endDate
        ]);
        if ($stmtDate) {
            //statement was successful
            $lastId = $conn->lastInsertId();
            return $this->getUnavailabilityById($lastId);
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'insert faild'
            ];
        }
    }

    public function deleteUnavailabilityById(int $id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("DELETE FROM unavailability
        WHERE id = :idVal;");
        $stmt->execute([
            'idVal' => $id
        ]);
        $cResult = $stmt->rowCount();
        if (!$cResult) {
            // Couldn't delete unavailability
            return (object) [
                'status' => 0,
                'message' => 'Couldnt delete unavailability'
            ];
        }
        return (object) [
            'status' => 1,
            'message' => "unavailability with id = {$id} deleted successfuly"
        ];
    }

    public function getUnavailabilityById(int $id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT unavailability.id, DATE_FORMAT(unavailability.start_date, '%d/%m/%Y') as start_date, DATE_FORMAT(unavailability.end_date, '%d/%m/%Y') as end_date FROM unavailability
        WHERE id = :idVal;
        ");
        $stmt->execute([
            'idVal' => $id
        ]);
        $result = $stmt->fetchAll();
        $output = (object) [
            'unavailabilities' => $result
        ];
        return $output;
    }
}
