<?php

/**
 * @property User_model       $User_model
 * @property Complaint_model  $Complaint_model
 * @property CI_Input         $input
 * @property CI_Session       $session
 */
class Complain extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Complaint_model','User_model']);
    }

    /**
     * Show and handle complaint submission form.
     */
    public function submit()
    {
        // Must be logged-in employee or TL
        $this->require_role(['employee','tl']);

        if ($this->input->method() === 'post') {
            $against_id    = $this->input->post('against_id');
            $complain_text = trim($this->input->post('complain'));

            // Basic validation
            if (!$against_id || !$complain_text) {
                $this->session->set_flashdata('error', 'Please fill in all required fields.');
                redirect('submit-complain');
            }

            $this->Complaint_model->create($against_id, $complain_text);
            $this->session->set_flashdata('success', 'Complaint submitted successfully.');
        }

        // GET: render form
        $data['users'] = $this->db->select('id, name')->from('users')->order_by('name')->get()->result();
        $this->load->view('complain/form', $data);
    }
} 