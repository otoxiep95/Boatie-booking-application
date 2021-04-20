<?php
define('WEB_PATH', 'https://boatie.dk/public/');
/**
 * Init of the application 
 */

// Import core files
require_once(__DIR__ . '/utilities/functions.php');
require_once(__DIR__ . '/Logger.php');
require_once(__DIR__ . '/Session.php');
require_once(__DIR__ . '/Redirect.php');
require_once(__DIR__ . '/Database.php');
require_once(__DIR__ . '/Mail.php');
require_once(__DIR__ . '/ApiResponse.php');


// Import classes
require_once(__DIR__ . '/Classes/Trips.php');
require_once(__DIR__ . '/Classes/Users.php');
require_once(__DIR__ . '/Classes/Events.php');
require_once(__DIR__ . '/Classes/Customers.php');
require_once(__DIR__ . '/Classes/Unavailabilities.php');



/**
 * Custom site wide error handler
 */

// These values below are used for a prod server -> hide all error messages
// error_reporting(0);
// ini_set('display_errors', 0);

set_exception_handler(function ($e) {
    $severity = -1;
    //Check if severity level is available
    if ($e instanceof ErrorException) {
        $severity = $e->getSeverity();
    } else {
        // no getSeverity() available
    }

    //Assign severity level an error level
    //Levels definition: https://www.tutorialrepublic.com/php-reference/php-error-levels.php
    if ($severity == 1 || $severity == 16 || $severity == 64 || $severity == 256 || $severity == 4096) {
        //fatal error
        Logger::fatal($e->getMessage(), [$e->__toString()]);
    } else if ($severity == 4 || $severity == 32767) {
        // error
        Logger::error($e->getMessage(), [$e->__toString()]);
    } else if ($severity == 2 || $severity == 32 || $severity == 128 || $severity == 512 || $severity == 2048 || $severity == 8192 || $severity == 16384) {
        //warning
        Logger::warning($e->getMessage(), [$e->__toString()]);
    } else if ($severity == 8 || $severity == 1024) {
        //notice
        Logger::notice($e->getMessage(), [$e->__toString()]);
    } else if ($severity == -1) {
        //severity level doesn't exist, use as default error status instead
        Logger::error($e->getMessage(), [$e->__toString()]);
    } else {
        //couldn't assign severity level to any error level, use fatal as default instead
        Logger::fatal($e->getMessage(), [$e->__toString()]);
    }
    http_response_code(500);
    if (ini_get('display_errors')) {
        //Dev server
        echo $e;
    } else {
        //Prod server
        Redirect::errorPage($e->getMessage());
    }
});
require_once(__DIR__ . '/Classes/Unavailabilities.php');
require_once(__DIR__ . '/Classes/Settings.php');
