<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Input     $input
 * @property CI_Session   $session
 * @property User_model   $User_model
 */
class Profile extends MY_Controller
{
    /**
     * Allow TL and Employee to change their password
     */
    public function change_password()
    {
        // Ensure only logged-in TLs & Employees can access
        $this->require_role(['tl','employee']);

        // Handle form submission
        if ($this->input->method() === 'post') {
            $new      = trim($this->input->post('new_password'));
            $confirm  = trim($this->input->post('confirm_password'));

            if ($new === '') {
                $this->session->set_flashdata('error', 'Please enter a new password.');
            } elseif ($new !== $confirm) {
                $this->session->set_flashdata('error', 'Passwords do not match.');
            } else {
                $this->User_model->update($this->user->id, ['password' => $new]);
                $this->session->set_flashdata('success', 'Password updated successfully.');
            }
            redirect('change-password');
            return;
        }

        // Decide which view to render depending on role
        $view = ($this->user->role_name === 'tl') ? 'tl/change_password' : 'employee/change_password';
        $this->load->view($view);
    }
} 