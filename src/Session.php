<?php

class Session
{

    public static function init()
    {
        //start session
        if (session_id() == '' || !isset($_SESSION)) {
            session_start();
        }
    }

    public static function write($key, $value)
    {
        self::init();
        //writing the session
        $_SESSION[$key] = $value;
    }

    public static function read($key)
    {
        self::init();
        return $_SESSION[$key];
    }

    /**
     * Check if session exists, and if argument is passed, check for speicific key existence inside the session
     * 
     * @param string $key Key of session key to check
     * 
     * @return bool
     *  */
    public static function exist($key = "")
    {
        self::init();
        // Check if session exists
        if (!isset($_SESSION)) {
            return false; // session does not exist
        }

        if (!empty($key)) {
            // key argument has been passed
            if (array_key_exists($key, $_SESSION)) {
                return true; //key exists in session
            } else {
                return false; //key does not exist in session
            }
        } else {
            return true; // key has not been passed as an argument and session exists in general
        }
    }

    public static function destroy()
    {
        self::init();
        //destry session
        session_destroy();
    }
}
