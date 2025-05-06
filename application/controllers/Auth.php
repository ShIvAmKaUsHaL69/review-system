<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Input     $input
 * @property CI_Session   $session
 * @property User_model   $User_model
 */
class Auth extends MY_Controller
{
    public function login()
    {
        if ($this->input->method() === 'post') {
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->User_model->find_by_email($email);

            if ($user && $password === $user->password) {
                $this->session->set_userdata('user_id', $user->id);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error','Invalid credentials');
            }
        }
        $this->load->view('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
