<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Email Configuration
|--------------------------------------------------------------------------
|
| Configure your email settings. You should move the sensitive data
| (like passwords) to a separate .env file in production.
|  
| 
| 
| 
| 
| 
| 
| 
| # Run at 10:00 AM every day
0 10 * * * php /path/to/your/project/index.php cron send_rating_reminders
*/



$config = [
    'protocol'  => 'smtp',
    'smtp_host' => 'smtp.gmail.com',    // Change this based on your email provider
    'smtp_port' => 587,
    'smtp_user' => '',                  // Add your email here
    'smtp_pass' => '',                  // Add your password/app password here
    'mailtype'  => 'html',
    'charset'   => 'utf-8',
    'newline'   => "\r\n",
    'wordwrap'  => TRUE,
    'validate'  => TRUE
]; 