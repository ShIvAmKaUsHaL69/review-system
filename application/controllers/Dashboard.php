<?php

/**
 * @property User_model        $User_model
 * @property Submission_model  $Submission_model
 */
class Dashboard extends MY_Controller
{
    public function index()
    {
        if (!$this->user) redirect('auth/login');
        switch ($this->user->role_name) {
            case 'admin':   $this->_admin();   break;
            case 'tl':      $this->_tl();      break;
            case 'employee':$this->_employee();break;
        }
    }

    /* ─────────── Super-admin pages ─────────── */
    private function _admin()
    {
        // Use the Admin model directly 
        $this->load->model(['Submission_model']);
        
        // Get filter values from GET params
        $filter_tl = $this->input->get('tl_id');
        $filter_period = $this->input->get('period_id');
        $filter_type = $this->input->get('type');
        
        // Load data for filters
        $data['tls'] = $this->User_model->all_tl();
        $data['periods'] = $this->Submission_model->get_all_periods();
        $data['filter_tl'] = $filter_tl;
        $data['filter_period'] = $filter_period;
        $data['filter_type'] = $filter_type;
        
        // Apply filters to data
        $data['submissions'] = $this->Submission_model->list_filtered($filter_tl, $filter_period, $filter_type);
        $data['employees'] = $this->User_model->all_employees();
        
        $this->load->view('admin/dashboard',$data);
    }

    /* ─────────── TL dashboard ─────────── */
    private function _tl()
    {
        // List employees assigned
        $this->load->model('Submission_model');
        $data['employees'] = $this->User_model->all_employees($this->user->id);
        
        // Check if they can be submitted for current month
        $can_submit = [];
        foreach ($data['employees'] as $emp) {
            $can_submit[$emp->id] = $this->Submission_model->can_submit_current($this->user->id, $emp->id);
        }
        $data['can_submit'] = $can_submit;
        
        $this->load->view('tl/dashboard',$data);
    }

    /* ─────────── Employee dashboard ─────────── */
    private function _employee()
    {
        $this->load->model(['User_model','Submission_model']);
        $data['fellow_employees'] = $this->User_model->all_employees($this->user->tl_id);
        $data['tl']              = $this->User_model->find($this->user->tl_id);
        
        // Check if they can be submitted for current month
        $can_submit = [];
        foreach ($data['fellow_employees'] as $emp) {
            $can_submit[$emp->id] = $this->Submission_model->can_submit_current($this->user->id, $emp->id);
        }
        if ($data['tl']) {
            $can_submit[$data['tl']->id] = $this->Submission_model->can_submit_current($this->user->id, $data['tl']->id);
        }
        $data['can_submit'] = $can_submit;
        
        $this->load->view('employee/dashboard',$data);
    }
}
