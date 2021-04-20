<?php


/**
 * Url encode a string (converts "spaces" to "+" characters)
 * 
 * Use for:
 *      - for single query values
 * 
 * @param string of the value to encode
 * @return string of the encoded value
 */
function u($string = "")
{
    return urlencode($string);
}

/**
 * Raw url encode a string (converts "spaces" to "%20")
 * 
 * Use for:
 *      - for mailto: links. Spaces inside mailto: links must be percent-encoded
 *      - if the link has to be decoded in JavaScript
 * 
 * @param string of the value to encode
 * @return string of the encoded value
 */
function raw_u($string = "")
{
    return rawurlencode($string);
}

/**
 * Sanitize text for HTML use
 * 
 * Not suitable if HTML elements such as <a href="">Visit facebook</a> are needed inside a text.
 * htmlspecialchars will escape html elements and some special characters
 * 
 * @param string of the value to sanitize
 * @return string of the sanitized value
 */
function h($string = "")
{
    return htmlspecialchars($string);
}

/**
 * Sanitize normal user input where not HTML is needed & and remove space in the beginning and end
 * E.g. name, address, title,...
 * 
 * @param string $string The text to sanitize
 * @return string Sanitized value
 */
function ht($string = "")
{
    return trim(htmlspecialchars($string));
}

/**
 * Check if the current page received a POST request
 * 
 * @return boolean value 
 */
function is_post_request()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * Check if the current page received a GET request
 * 
 * @return boolean value 
 */
function is_get_request()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * Sanitize passwords by removing HTML tags
 * 
 * @param string of the password
 * @return string of sanitized password
 */
function sanitizeFormPassword($inputText)
{
    $inputText = strip_tags($inputText);
    return $inputText;
}


/**
 * Convert date from format given format to "D., Month Year"
 * 
 * @param string $dateString The string of the date
 * @param string $initFormat The initial format of the string
 * 
 * @return string Human friendly date
 */
function convertDateToFriendly($dateString, $initFormat = 'Y-d-m')
{
    $date = DateTime::createFromFormat($initFormat, $dateString)->format('l, jS F Y');
    return $date;
}


/**
 * Check for the existence of a value.
 * 
 * This setup is mainly aimed to be used within API's
 * where sometimes you have to use false or zero's
 * and need to check for their existence.
 * Why not use alone empty() or isset()?
 *      - empty() will fail with 0 and false.
 *      - isset() returns true with an empty string (""), empty array [] or empty object {}
 * 
 * 
 * Returns true in these cases:
 *      1               (integer)
 *      0               (integer with 0 value)
 *      1.2             (float)
 *      0.0             (float with 0.0 value)
 *      true            (boolean)
 *      false           (boolean)
 *      "Hello world"   (string)
 *      ['a','b']       (array)
 *      {a : 'b'}       (object)
 * 
 * 
 * Returns false in these cases:
 *      ""              (empty string)
 *      []              (empty array)
 *      {}              (empty object)
 *      $variable;      (declared variable without value)
 *      NULL
 *      non declared variables
 * 
 * In the case of non declared variables, use the function like: val_exists(@$var); which will turn off error noticing for this function
 * 
 * @param array|object|integer|float|string|boolean|null $var The variable to check
 * @return boolean Retuns true or false
 */
function val_exists($var)
{
    return is_array($var) || is_object($var) ? !empty((array) $var) : (is_numeric($var) ? true : (!empty($var) && strlen($var)) || is_bool($var));
}
/**
 * The val_exists function (above) in a expended version for better understanding
 * 
 *    function valExistExpanded($var)
 *      {
 *         if (is_array($var) || is_object($var)) {
 *              return !empty((array) $var);
 *         }
 *   
 *        if (is_numeric($var)) {
 *             return true;
 *         }
 *   
 *         return !empty($var) && strlen($var) || is_bool($var);
 *      }
 */



/**
 * Replace text between two strings and remove the needles
 * 
 * @param string $str The whole text
 * @param string $needle_start The start string to check for
 * @param string $needle_end The end string to check for
 * @param string $replacement The text to replace inbetween the two string
 * 
 * @return string
 */
function replace_between($str, $needle_start, $needle_end, $replacement)
{
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    $textWithNeedles = substr_replace($str, $replacement, $start, $end - $start);
    $textWithout = str_replace([$needle_start, $needle_end], ['', ''], $textWithNeedles);
    return $textWithout;
}
