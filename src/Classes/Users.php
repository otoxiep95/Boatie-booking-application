<?php

//use ___PHPSTORM_HELPERS\object;

class Users
{
    private $totalAmountActiveUsers; //total number of users
    private $page = 0; // The page to request during pagination - this is also called offset
    private $perPage = 30; // Amount of trips per page (limit)
    //private $authActive = false; used to skip login for developing purposes
    /**
     * Get a single user by using its id
     * 
     * @param integer $id Id of the user
     * @return array Return an associative array containing a single trips values
     */
    public function getUserById(int $id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT users.user_id, users.first_name, users.last_name, users.email, users.phone, users.privilege
          FROM users
          WHERE user_id= :idVal
          ;");
        $stmt->execute([
            'idVal' => $id
        ]);
        $result = $stmt->fetch();

        //Database::disconnect();
        $output = (object) [
            'user' => $result
        ];
        return $output;
    }


    /**
     * Get all users with pagination 
     * 
     * @return array Return an array of associative arrays containing all users used with pagination
     * 
     */
    public function getAllUsers(int $page, int $perPage)
    {

        // Since MySQL pages start at 0, we deduct 1 of the value and add it back after the query
        $page = $page - 1;

        $offset = $page * $perPage;
        // Run the query and get users limited with pages
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT users.user_id, users.first_name, users.last_name, users.email, users.phone, users.privilege
         FROM users
         WHERE active = 1
         LIMIT :offsetVal,:perPageVal;
         ");
        $stmt->execute([
            'perPageVal' => $perPage,
            'offsetVal' => $offset
        ]);
        $result = $stmt->fetchAll();
        Database::disconnect();

        $this->getTotalAmountActiveUsersAmount();
        // Get max pages possible with pagination limits
        $outOfPages = ceil($this->totalAmountActiveUsers / $perPage);

        // Create output object
        $output = (object) [
            'page' => $page + 1, // add 1 back to the page value
            'per_page' => $perPage,
            'out_of_pages' => $outOfPages,
            'users' => $result
        ];
        return $output;
    }

    /**
     * Get the value of totalUsersAmount
     * 
     *  
     * @return int The amount of active users 
     */

    public function getTotalAmountActiveUsersAmount()
    {
        // Create the WHERE statement to either get past or upcoming trips
        //$pastStatement = $past ? "trips.date < subdate(current_date, 1)" : "trips.date > subdate(current_date, 1)";

        $conn = Database::connect();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM users 
        WHERE active = 1;");

        $stmt->execute();
        $result = $stmt->fetch();
        Database::disconnect();
        $count = $result['COUNT(*)'];
        $this->totalAmountActiveUsers = $count;
        return $count;
    }




    /**
     * Creates new user 
     * 
     * 
     */
    public function createNewUser($firstName, $lastName, $email, $password, $phone, int $privilege)
    {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $recoveryLink = uniqid();
        $active = 1;
        $conn = Database::connect();
        $stmt = $conn->prepare("INSERT INTO users
        (first_name, last_name, email, phone, password, recovery_link, active, privilege)
        VALUES
        (:firstNameVal, :lastNameVal, :emailVal, :phoneVal, :passwordVal, :recoveryLinkVal, :activeVal, :privilegeVal );");
        $stmtEx = $stmt->execute([
            'firstNameVal' => $firstName,
            'lastNameVal' => $lastName,
            'emailVal' => $email,
            'phoneVal' => $phone,
            'passwordVal' => $passwordHash,
            'recoveryLinkVal' => $recoveryLink,
            'activeVal' => $active,
            'privilegeVal' => $privilege
        ]);



        if ($stmtEx) {
            //statement was successful
            $lastId = $conn->lastInsertId();
            return $this->getUserById($lastId);
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'insert faild'
            ];
        }
        //Database::disconnect();
    }

    /**
     * Login user, and if true, set session cookie
     * 
     * @param string $email Email of the user who wants to log in
     * @param string $password Password of the user who wants to log in
     * 
     * @return boolean 
     */
    public function login($email, $password)
    {

        // Sanitize password
        $password = sanitizeFormPassword($password);

        // Check first if email valid & exists
        $emailStatusArray = $this->emailExists($email);
        if ($emailStatusArray['status'] == "invalid") {
            //email exist
            return [
                'status' => 0,
                'message' => 'Invalid email'
            ];
        }

        if ($emailStatusArray['status'] == "nonexistant") {
            //email exist
            return [
                'status' => 0,
                'message' => 'Email / password do not match'
            ];
        }

        // Check password for the valid&existing email
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT * FROM users 
        WHERE email = :emailVal;");
        $stmt->execute([
            'emailVal' => $email
        ]);
        $user = $stmt->fetch();
        $hashedPassword = $user['password'];
        Database::disconnect();
        if (password_verify($password, $hashedPassword)) {
            Session::write('user', [
                'id' => $user['user_id'],
                'privilege' => $user['privilege'],
                'name' => "{$user['first_name']} {$user['last_name']}"
            ]);

            //Password matches
            return $output = [
                "status" => 1,
                "message" => "User authenticated"
            ];
        } else {
            //Password does not match
            return $output = [
                "status" => 0,
                "message" => "Password does not match"
            ];
        }
    }

    /**
     * Logout the user
     * 
     * @return bool Status of the logout
     */
    public function logout()
    {
        Session::destroy();
    }


    /**
     * Authenticate if the user with privilege X is logged in
     * 
     * @param int $privilege The privilege level of the user to check. 0 = admin, 1 = employee
     * @return array    [
     *                      'logged_ind' => 0 = not logged in, 1 = logged in
     *                      'privilege' => 0 = not high enough privilege levels, 1 = privilige level is high enough
     *                  ]
     */
    public function auth($privilege = 0)
    {
        //if ($this->authActive) {
        //Check if a user is logged in
        $userExists = Session::exist('user');
        if (!$userExists) {
            //user is not logged in -> return false
            return [
                'logged_in' => 0,
                'privilege' => 0
            ];
        }

        // Check if the logged in user has high enough privileges
        $user = Session::read('user');
        if ($privilege != $user['privilege']) {
            // User has not the according priviliges
            return [
                'logged_in' => 1,
                'privilege' => 0
            ];
        }

        // Else return success array for logged in and high enough privilege user
        return [
            'logged_in' => 1,
            'privilege' => 1
        ];
        // } else {
        //     return [
        //         'logged_in' => 1,
        //         'privilege' => 1
        //     ];
        // }
    }

    public function isLoggedIn()
    {
        $result = $this->auth();
        if ($result["logged_in"]) {
            //is logged in
            //Redirect::page(__DIR__ . '/../../public/dashboard/trips.php');
            return true;
        } else {
            //not logged in
            //Redirect::page(__DIR__ . '/../../public/dashboard/index.php');
            return false;
        }
    }

    public function hasPrivilege($privilege = 0)
    {
        $result = $this->auth($privilege);
        if ($result["privilege"] != 0) {
            //has privilege
            return true;
        } else {
            //doesnt have privilege
            return false;
            //Redirect::page(__DIR__ . '/../../public/dashboard/trips.php');
        }
    }

    /**
     * Check if email exists
     * 
     * @param string $email Check the email
     */
    public function emailExists($email)
    {

        // Check if valid email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // email invalid
            return $output = [
                "status" => "invalid",
                "message" => "Invalid email"
            ];
        }

        // Check if email exists
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT * FROM users 
        WHERE email = :emailVal;");
        $stmt->execute([
            'emailVal' => $email
        ]);
        $result = $stmt->rowCount();
        Database::disconnect();
        if ($result > 0) {
            // email exists
            return $output = [
                "status" => "exists",
                "message" => "Email exists"
            ];
        } else {
            // email does not exist
            return $output = [
                "status" => "nonexistant",
                "message" => "Email does not exist"
            ];
        }
    }




    /**
     * Get all users with pagination 
     * 
     * 
     * 
     */
    public function updateUserById(int $id, $firstName, $lastName, $email, $phone, int $privilege)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("UPDATE users 
        SET
            first_name = :firstNameVal,
            last_name = :lastNameVal,
            email = :emailVal,
            phone = :phoneVal,
            privilege = :privilegeVal
        WHERE
            user_id = :idVal;");
        $stmtUp = $stmt->execute([
            'idVal' => $id,
            'firstNameVal' => $firstName,
            'lastNameVal' => $lastName,
            'emailVal' => $email,
            'phoneVal' => $phone,
            'privilegeVal' => $privilege
        ]);

        if ($stmtUp) {
            //statement was successful
            return $this->getUserById($id);
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'update failed'
            ];
        }
    }


    /**
     * Delete user by Id
     * 
     * @param $id = id of user to delete
     * 
     */

    public function deleteUserById($id)
    {

        $conn = Database::connect();

        try {
            // Start transaction - only commit a delete if every step is successfull
            $conn->beginTransaction();
            // Get customer_id of the trip
            $stmtCId = $conn->prepare("SELECT assigned_captain_user_id FROM trips
            WHERE assigned_captain_user_id=:idVal;
            ");
            $result = $stmtCId->execute([
                'idVal' => $id
            ]);

            if (!$result) {
                throw new Exception("Could not run query");
            }
            $stmtCUp = $conn->prepare("UPDATE trips 
            SET assigned_captain_user_id=1
            WHERE assigned_captain_user_id=:idVal;
            ");
            $result = $stmtCUp->execute([
                'idVal' => $id
            ]);
            if (!$result) {
                throw new Exception("Could not run query");
            }

            $stmtCUpE = $conn->prepare("UPDATE events 
            SET assigned_captain_user_id=1
            WHERE assigned_captain_user_id=:idVal;
            ");
            $result = $stmtCUpE->execute([
                'idVal' => $id
            ]);
            if (!$result) {
                throw new Exception("Could not run query");
            }

            // Delete the trip
            $stmtDT = $conn->prepare("DELETE FROM users
            WHERE user_id = :idVal;");
            $stmtDT->execute([
                'idVal' => $id
            ]);
            $tResult = $stmtDT->rowCount(); // count the amount of affected rows by the previous SQL query. If successful, the rowCount() returns 1, if failed, it returns 0

            if (!$tResult) {
                // Couldn't delete trip
                throw new Exception("Couldn't delete user");
            }

            $conn->commit(); // commit the transaction
            Database::disconnect();

            $output = (object) [
                'message' => "The user with the user_id = {$id} have successfully been deleted.",
                'user_id' => $id,
                'status' => 1
            ];
            return $output;
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'status' => 0
            ];
            return $output;
        }
    }

    /**
     * Get recovery link 
     * 
     * @param $email = email of user to get recovery_link
     * 
     * @return object result => [
     *          'id'=> id of user
     *          'recovery_link' => recovery link
     *                           
     * ]
     */
    public function getRecoveryLink($email)
    {
        //check is email exists/valid
        //get recoverylink and id
        $emailVerified = $this->emailExists($email);
        if ($emailVerified['status'] == 'invalid') {
            return $emailVerified;
        }

        if ($emailVerified['status'] == "nonexistant") {
            return $emailVerified;
        }

        if ($emailVerified['status'] == "exists") {


            $conn = Database::connect();

            $stmtRL = $conn->prepare("SELECT users.user_id, users.recovery_link 
            FROM users
            WHERE email= :emailVal;");

            $stmtRL->execute([
                'emailVal' => $email
            ]);

            $result = $stmtRL->fetch();
            Database::disconnect();

            // Create output object
            $output = [
                'status' => 0,
                'recovery' => $result
            ];
            return $output;
        }
    }

    public function setActive($id)
    {
        $conn = Database::connect();
        $stmtAct = $conn->prepare("UPDATE users
            SET active=1
            WHERE user_id=:idVal;
            ");
        $result = $stmtAct->execute([
            'idVal' => $id
        ]);
        Database::disconnect();
        if (!$result) {
            return 0;
        } else {
            return 1;
        }
    }
    public function setUnactive($id)
    {
        $conn = Database::connect();
        $stmtUAct = $conn->prepare("UPDATE users
            SET active=0
            WHERE user_id=:idVal;
            ");
        $result = $stmtUAct->execute([
            'idVal' => $id
        ]);
        Database::disconnect();
        if (!$result) {
            return 0;
        } else {
            return 1;
        }
    }


    /**
     * Reset password
     * 
     * @param int $id = id of the user to change password
     * @param string $recoveryLink = recovery link from page
     * @param string $password = new password 
     * 
     * 
     * @return bool result
     */
    public function changePassord($id, $recoveryLink, $password)
    {
        //check is recovery link matches with user 

        $conn = Database::connect();

        try {
            // Start transaction - only commit a delete if every step is successfull
            $conn->beginTransaction();
            // Get customer_id of the trip
            $stmtRLM = $conn->prepare("SELECT users.recovery_link FROM users
            WHERE user_id=:idVal;
            ");
            $stmtRLM->execute([
                'idVal' => $id
            ]);

            $result = $stmtRLM->fetch();

            if ($result["recovery_link"] != $recoveryLink) {
                throw new Exception("Recovery Link does not match");
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmtRP = $conn->prepare("UPDATE users 
            SET password=:passwordVal
            WHERE user_id=:idVal;
            ");
            $result = $stmtRP->execute([
                'idVal' => $id,
                'passwordVal' => $passwordHash
            ]);
            if (!$result) {
                throw new Exception("Could not update password");
            }
            $newRecoveryLink = uniqid();
            $stmtRP = $conn->prepare("UPDATE users 
            SET recovery_link=:newRecoveryLinkVal
            WHERE user_id=:idVal;
            ");
            $result = $stmtRP->execute([
                'idVal' => $id,
                'newRecoveryLinkVal' => $newRecoveryLink
            ]);
            if (!$result) {
                throw new Exception("Could not update password");
            }
            $stmtUAct = $conn->prepare("UPDATE users
            SET active=1
            WHERE user_id=:idVal;
            ");
            $result = $stmtUAct->execute([
                'idVal' => $id
            ]);
            if (!$result) {
                throw new Exception("Could not update password");
            }
            $conn->commit(); // commit the transaction
            Database::disconnect();

            return $output = (object) [
                'message' => "password changed successfuly",
                'user_id' => $id,
                'status' => 1
            ];
        } catch (Exception $e) {
            $conn->rollback();
            // Create output object
            $output = (object) [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'status' => 0
            ];
            return $output;
        }
    }
}
