<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Only allow this controller to be called via CLI
        if (!is_cli()) {
            show_error('This controller can only be accessed via command line.');
            exit;
        }
        
        $this->load->model(['User_model', 'Submission_model']);
        $this->load->library('email');
    }
    
    /**
     * Send rating reminder emails to users who haven't rated yet
     * This should be called daily via cron after 25th of each month
     */
    public function send_rating_reminders()
    {
        // Only proceed if it's after the 25th of the month
        if (date('d') < 25) {
            echo "Not sending reminders - it's before the 25th.\n";
            return;
        }
        
        // Get current month
        $current_month = date('Y-m');
        
        // Get all users (both TLs and employees)
        $users = array_merge(
            $this->User_model->all_tl(),
            $this->User_model->all_employees()
        );
        
        // Filter users who haven't rated yet
        $pending_users = array_filter($users, function($user) use ($current_month) {
            return !$this->Submission_model->has_rated_in_month($user->id, $current_month);
        });
        
        if (empty($pending_users)) {
            echo "No pending ratings found.\n";
            return;
        }
        
        // Configure email settings
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com',  // Change this based on your email provider
            'smtp_port' => 587,
            'smtp_user' => 'your-email@gmail.com',  // Change this
            'smtp_pass' => 'your-password',         // Change this
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];
        $this->email->initialize($config);
        
        $sent_count = 0;
        $failed_count = 0;
        
        foreach ($pending_users as $user) {
            // Load email template
            $email_content = $this->load->view('emails/rating_reminder', [
                'user_name' => $user->name
            ], TRUE);
            
            $this->email->clear();
            $this->email->from('noreply@yourcompany.com', 'Rating System');
            $this->email->to($user->email);
            $this->email->subject('Reminder: Submit Your Monthly Ratings');
            $this->email->message($email_content);
            
            if ($this->email->send()) {
                $sent_count++;
                echo "Sent reminder to {$user->email}\n";
            } else {
                $failed_count++;
                echo "Failed to send reminder to {$user->email}\n";
                echo $this->email->print_debugger();
            }
            
            // Sleep briefly to prevent overwhelming the mail server
            usleep(100000); // 0.1 second
        }
        
        echo "\nSummary:\n";
        echo "Total pending users: " . count($pending_users) . "\n";
        echo "Successfully sent: {$sent_count}\n";
        echo "Failed to send: {$failed_count}\n";
    }
} 