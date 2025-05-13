<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class MY_Controller
 *
 * @property CI_Input              $input
 * @property CI_Session            $session
 * @property CI_DB_query_builder   $db
 * @property CI_DB_driver         $db
 * @property CI_Loader             $load
 * @property User_model            $User_model
 * @property Submission_model      $Submission_model
 * @property Question_model        $Question_model
 * @method int insert_id()
 */

class MY_Controller extends CI_Controller
{
    protected $user;

    public function __construct()
    {
        parent::__construct();
        // Load common models
        $this->load->model('User_model');

        // If logged in, pull the user object for views
        if ($this->session->userdata('user_id')) {
            $this->user = $this->User_model->find($this->session->userdata('user_id'));
            $this->load->vars(['auth_user' => $this->user]);
        }
    }

    protected function require_role($roles = [])
    {
        if (!$this->user || !in_array($this->user->role_name, $roles)) {
            redirect('auth/login');
            exit;
        }
    }
}
