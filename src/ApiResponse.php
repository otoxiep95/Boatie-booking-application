<?php

class ApiResponse
{
    /**
     * Success message response
     * 
     * @param array/object $successData An array or an object of additional data you want to send as a reply. E.g. an array of events.
     */
    public static function success($successData = null)
    {
        $status = (object) [
            "status" => "success",
            "statusCode" => 200
        ];

        //If no successdata has been passed, return an empty class
        if ($successData == null) {
            $successData = new stdClass;
        }

        // Merge the $status and $successData object/array together. Doing it inside a foreach is 400% faster. Source: https://stackoverflow.com/a/455736/3673659
        foreach ($successData as $k => $v) $status->$k = $v;
        $response = json_encode($status);

        // setcookie('cross-site-cookie', 'name', ['samesite' => 'None', 'secure' => true]);
        echo  $response;

        exit;
    }

    /**
     * Send error response
     * 
     * @param string $errorMessage The error text to respond with.
     * @param integer $errorStatus The error status code, by default 0 to indicate FALSE
     */
    public static function error($errorMessage = "error", $errorStatus = 0)
    {
        //Instead of using __LINE__ to get the current line, the three following lines are returning the line where the function is fired from
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        $response = '{"status": "error",
        "statusCode": "' . $errorStatus . '",
        "message": "' . $errorMessage . '",
       "line": ' . $lineNumber . '}';

        // setcookie('cross-site-cookie', 'name', ['samesite' => 'None', 'secure' => true]);
        echo $response;
        exit;
    }
}
