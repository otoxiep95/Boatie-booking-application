<?php

require_once(__DIR__ . '/init.php');


// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/vendors/PHPMailer/PHPMailer.php');
require_once(__DIR__ . '/vendors/PHPMailer/Exception.php');
require_once(__DIR__ . '/vendors/PHPMailer/SMTP.php');



class Mail
{

    private static $fromEmail = 'info@boatie.dk';

    /**
     * Send email with custom HTML body
     * 
     * @param array $args = [
     *         'recipient_mail' =>  Send to email address
     *         'recipient_name' =>  Name of the email receiver
     *         'subject' => Subject to display in the email
     *         'from_name' => From Name to display in the email
     *         'body' => custom HTML body
     *     ]
     * 
     * @return array With success/failure messages
     */
    public static function send(array $args = [])
    {
        // Check recipient mail
        if (!filter_var($args['recipient_mail'], FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => 0,
                "message" => "Recipient mail is invalid"
            ];
        }

        //Check for existing subject
        if (!isset($args['subject'])) {
            return [
                "status" => 0,
                "message" => "Subject is missing"
            ];
        }

        //Check for existing body
        if (!isset($args['body'])) {
            return [
                "status" => 0,
                "message" => "Body is missing"
            ];
        }

        $fromEmail = self::$fromEmail; // The "Sent from" email to display
        $fromName = isset($args['from_name']) ? $args['from_name'] : 'Boatie Info'; // The "From" to display
        $recipientMail = $args['recipient_mail']; // The receiver of the mail
        $recipientName = isset($args['recipient_name']) ? ht($args['recipient_name']) : null; // The name of the receiver
        $subject = ht($args['subject']); // The Subject to display
        $body = $args['body']; // Since email body can/should contain HTML, we will not sanitize this string
        $conf = require(__DIR__ . '/../config/mail-config.php');
        // Try to send the mail
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = $conf->host;                            // Specify main and backup SMTP servers
            $mail->SMTPAuth   = $conf->smtp_auth;                                   // Enable SMTP authentication
            $mail->Username   = $conf->username;                        // SMTP username
            $mail->Password   = $conf->password;                        // SMTP password
            $mail->SMTPSecure = $conf->smtp_encryption_type;                        // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $conf->port;                            // TCP port to connect to

            //Recipients
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($recipientMail, $recipientName);          // Add a recipient

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;


            $mail->send();
            return [
                "status" => 1,
                "message" => "Email successfully sent"
            ];
        } catch (Exception $e) {
            return [
                "status" => 0,
                "message" => "Email delivery failed",
                "errorInfo" => $mail->ErrorInfo
            ];
        }
    }


    /**
     * Send email using booking-confirmation.html email template
     * 
     * @param array $args = [
     *          'recipient_mail' => (string)   Send to email address
     *          'recipient_name' => (string)   Name of the email receiver
     *          'subject' => (string)  Subject to display in the email
     *          'from_name' => (string)  From Name to display in the email
     *          'title' => (string)  Title of the mail to display inside the body, will be overwritten if is_event = true
     *          'pickup' => (string)  Time + location to pickup. Example: "12.00 -  Slusholmen"
     *          'dropoff' => (string) Time + location to dropoff. Example: "14.00 -  Slusholmen"
     *          'price' => (int) The price for a single trip, or if an event, the price for the whole group
     *          'date' => (string)  Date of the event/trip
     *          'is_event' => (boolean)  Flag used to change the layout from trip to event setup, default value = false
     *          'event_name' => (string)  If is_event = true, the event name to display in the body title
     *          'group_size' => (int) If is_event = true, the booked size of the group for the event
     *     ]
     * 
     * @return array With success/failure messages
     */
    public static function sendBookingConfirmation(array $args = [])
    {
        // Check recipient mail
        if (!filter_var($args['recipient_mail'], FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => 0,
                "message" => "Recipient mail is invalid"
            ];
        }

        //Check for existing body
        if (!isset($args['pickup'])) {
            return [
                "status" => 0,
                "message" => "Pickup info is missing"
            ];
        }

        //Check for existing body
        if (!isset($args['dropoff'])) {
            return [
                "status" => 0,
                "message" => "Dropoff info is missing"
            ];
        }

        //Check for existing body
        if (!isset($args['price'])) {
            return [
                "status" => 0,
                "message" => "Price info is missing"
            ];
        }

        //Check for existing body
        if (!isset($args['date'])) {
            return [
                "status" => 0,
                "message" => "Date info is missing"
            ];
        }

        $isEvent = false;
        //Check for is event
        if (is_bool($args['is_event'])) {
            $isEvent = $args['is_event'];
        }

        $fromEmail = self::$fromEmail; // The "Sent from" email to display
        $fromName = isset($args['from_name']) ? $args['from_name'] : 'Boatie Booking'; // The "From" to display
        $recipientMail = $args['recipient_mail']; // The receiver of the mail
        $recipientName = isset($args['recipient_name']) ? ht($args['recipient_name']) : null; // The name of the receiver
        $subject = isset($args['subject']) ? ht($args['subject']) : "Booking confirmation"; // The Subject to display
        $title = isset($args['title']) ?  ht($args['title']) : 'Booking confirmation';
        $date = ht($args['date']);
        $pickup = ht($args['pickup']);
        $dropoff = ht($args['dropoff']);
        $price = ht($args['price']);

        $groupSizeText = $isEvent ? ht($args['group_size']) : '';

        $template = file_get_contents(__DIR__ . '/email-templates/booking.html');
        $body = str_replace([
            '{{TITLE}}',
            '{{DATE}}',
            '{{PICKUP}}',
            '{{DROPOFF}}',
            '{{GROUP_SIZE}}',
            '{{PRICE}}'
        ], [
            $title,
            $date,
            $pickup,
            $dropoff,
            $groupSizeText,
            $price
        ], $template);


        //Remove group size element from template if is_event=false
        if (!$isEvent) {
            $body = replace_between($body, "<!-- {{GROUP_START}} -->", "<!-- {{GROUP_END}} -->", "");
        }


        $conf = require(__DIR__ . '/../config/mail-config.php');


        // Try to send the mail
        return static::sendMail([
            'username' =>  $conf->username_booking,
            'password' => $conf->password_booking,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'recipient_email' => $recipientMail,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'body' => $body
        ]);
    }
    /**
     * Send email with custom HTML body
     * 
     * @param array $args = [
     *         'recovery-link' => 
     *         'recipient_mail' =>  Send to email address
     *         'recipient_name' =>  Name of the email receiver
     *         'subject' => Subject to display in the email
     *         'from_name' => From Name to display in the email
     *         'recovery_link' => recovery link for reseting the password
     *         'body' => custom HTML body
     *     ]
     * 
     * @return array With success/failure messages
     */
    public static function sendRecoveryLink(array $args = [])
    {
        // Check recipient mail
        if (!filter_var($args['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => 0,
                "message" => "Recipient mail is invalid"
            ];
        }

        //Check for existing subject
        // if (!isset($args['subject'])) {
        //     return [
        //         "status" => 0,
        //         "message" => "Subject is missing"
        //     ];
        // }

        //Check for existing body
        if (!isset($args['recovery-link'])) {
            return [
                "status" => 0,
                "message" => "Recovery link is missing"
            ];
        }



        //Check for existing id
        if (!isset($args['id'])) {
            return [
                "status" => 0,
                "message" => "Id is missing"
            ];
        }

        $fromEmail = self::$fromEmail; // The "Sent from" email to display
        $fromName = isset($args['from_name']) ? $args['from_name'] : 'Boatie'; // The "From" to display
        $recipientMail = $args['recipient_email']; // The receiver of the mail

        $recipientName = isset($args['recipient_name']) ? ht($args['recipient_name']) : 'Recovery setup'; // The name of the receiver
        $subject = isset($args['subject']) ? ht($args['subject']) : "Password reset"; // The Subject to display
        $title = isset($args['title']) ?  ht($args['title']) : 'Password reset';
        $id = $args['id'];
        $recoveryLink = $args['recovery-link'];


        // $wholeWebPath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        // $currentWebDirectory = substr($wholeWebPath, 0, strrpos($wholeWebPath, '/'));
        // $publicWebDirectory = substr($currentWebDirectory, 0, strrpos($currentWebDirectory, '/') + 1) + 'public/';


        $sKeyPath = WEB_PATH . 'dashboard/reset-password.php' . '?id=' . $id . '&key=' . $recoveryLink; // since we use .htaccess in this folder, .php extension does not have to be specified


        $body = '<div>
                    <h3>To change your password please click bellow:</h3>
                    <a href=" ' . $sKeyPath . '">Click here</a>
                </div>';



        $conf = require(__DIR__ . '/../config/mail-config.php');



        return static::sendMail([
            'username' =>  $conf->username,
            'password' => $conf->password,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'recipient_email' => $recipientMail,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'body' => $body
        ]);
    }


    /**
     * Send the mail using PHPMailer using one of the methods inside the Mail class
     * 
     * @param array $args = [
     *                      'host' => (int) The host, default value is the one specified inside the config file
     *                      'smtp_auth' => (string)The auth method, default value is the one specified inside the config file
     *                      'username' => (string) The username to log in with, default value is the one specified inside the config file
     *                      'password' => (string) The password to login with, default value is the one specified inside the config file
     *                      'port' => (int) The port of SMTP, default value is the one specified inside the config file
     *                      'from_email' =>(string) The "Sent from" email, REQUIRED
     *                      'from_name' =>(string) The "From name" , REQUIRED
     *                      'recipient_email' => (string)Send email to, REQUIRED
     *                      'recipient_name' => (string)The name of the recipient, REQUIRED
     *                      'subject' => (string)The email subject, REQUIRED
     *                      'body' => (string)The email body, REQUIRED
     *              ]
     */
    private function sendMail($args = [])
    {

        if (!isset($args['recipient_email'])) {
            return [
                "status" => 0,
                "message" => "Recipient mail is missing",
                "errorInfo" => "None"
            ];
        }
        if (!isset($args['recipient_name'])) {
            return [
                "status" => 0,
                "message" => "Recipient name is missing",
                "errorInfo" => "None"
            ];
        }
        // Check recipient mail
        if (!filter_var($args['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => 0,
                "message" => "Recipient mail is invalid",
                "errorInfo" => "None"
            ];
        }

        //Check for existing subject
        if (!isset($args['subject'])) {
            return [
                "status" => 0,
                "message" => "Subject is missing",
                "errorInfo" => "None"
            ];
        }
        //Check for existing subject
        if (!isset($args['body'])) {
            return [
                "status" => 0,
                "message" => "Body is missing",
                "errorInfo" => "None"
            ];
        }

        $conf = require(__DIR__ . '/../config/mail-config.php');

        // Set default values if no arguments are passed
        $host = isset($args['host']) ? ht($args['host']) : $conf->host;
        $smtp_auth = isset($args['smtp_auth']) ? ht($args['smtp_auth']) : $conf->smtp_auth;
        $username = isset($args['username']) ? ht($args['username']) : $conf->username;
        $password = isset($args['password']) ? ht($args['password']) : $conf->password;
        $port =  isset($args['port']) ? ht($args['port']) : $conf->port;

        $from_email = isset($args['from_email']) ? ht($args['from_email']) : $conf->username;
        $from_name = isset($args['from_name']) ? ht($args['from_name']) : "Boatie info";


        $recipient_email = ht($args['recipient_email']);
        $recipient_name = ht($args['recipient_name']);

        $subject = ht($args['subject']);
        $body = $args['body'];

        // Try to send the mail
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = $host;                        // Specify main and backup SMTP servers
            $mail->SMTPAuth   = $smtp_auth;                              // Enable SMTP authentication
            $mail->Username   = $username;                // SMTP username
            $mail->Password   = $password;                        // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                        // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $port;                            // TCP port to connect to

            //Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($recipient_email, $recipient_name);          // Add a recipient

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;


            $mail->send();
            return [
                "status" => 1,
                "message" => "Email successfully sent",
            ];
        } catch (Exception $e) {
            return [
                "status" => 0,
                "message" => "Email delivery failed",
                "errorInfo" => $mail->ErrorInfo
            ];
        }
    }
}
