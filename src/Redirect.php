<?php

/**
 * Class to redirect the user to a specific page, all going from the public folder.
 * 
 * Example: 
 * - Redirect to index page             ->  Redirect::page('index.php');
 * - Redirect to dashboard trips page   ->  Redirect::page('dashboard/trips.php');
 * 
 * !!! IMPORTANT !!!
 * If this file is moved into another directory, update the $baseDIR path to point to the public/ folder!
 * 
 */
class Redirect
{

    /**
     * Redirect to a page using relative paths from the requesting file
     * 
     * E.g: 
     * Redirect::page(__DIR__ . '/../src/utilities/test2.php');
     * 
     * @param string $message Text of the error message displayed to the user
     */
    public static function page($dest)
    {
        $realpath    = str_replace('\\', '/', $dest);
        $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $realpath);
        // Redirect
        header('Location: ' . $file . '');
        exit();
    }

    /**
     * Redirect to the error page with a custom message
     * 
     * @param string $message Text of the error message displayed to the user. Message is accessible via $_SESSION['userError']
     */
    public static function errorPage($message)
    {
        // Store error message for error.php
        session_start();
        $_SESSION['userError'] = $message;

        $dest = __DIR__ . '/../public/error.php';
        $realpath    = str_replace('\\', '/', $dest);
        $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $realpath);
        // Redirect
        header('Location: ' . $file . '');
        exit();
    }
}
