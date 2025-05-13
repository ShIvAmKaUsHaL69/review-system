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
        // Load required models
        $this->load->model(['Submission_model','Question_model']);
        
        // Get filter values from GET params
        $filter_tl = $this->input->get('tl_id');
        $filter_period = $this->input->get('period_id');
        $filter_type = $this->input->get('type');
        $filter_month = $this->input->get('month'); // New filter for month
        
        // Load data for filters
        $data['tls'] = $this->User_model->all_tl();
        $data['periods'] = $this->Submission_model->get_all_periods();
        $data['filter_tl'] = $filter_tl;
        $data['filter_period'] = $filter_period;
        $data['filter_type'] = $filter_type;
        $data['filter_month'] = $filter_month;
        $data['current_month'] = date('Y-m');
        
        // Apply filters to data
        $data['submissions'] = $this->Submission_model->list_filtered($filter_tl, $filter_period, $filter_type);
        $data['employees'] = $this->User_model->all_employees();
        
        // Pass all questions to the view so we can show them as columns
        $data['questions'] = $this->db->get('questions')->result();
        
        // Performance summary stats for selected or current month
        $selectedYM = $filter_month ?: date('Y-m');
        
        // Build average rating per employee for selected month
        $avgRows = $this->db->select('u.id, u.name, AVG(sa.rating) as avg_rating')
                           ->from('submissions s')
                           ->join('submission_answers sa','sa.submission_id = s.id')
                           ->join('periods p','p.id = s.period_id')
                           ->join('users u','u.id = s.target_id')
                           ->where('p.yearmonth', $selectedYM)
                           ->group_by(['u.id','u.name'])
                           ->get()->result();

        $outstanding = [];
        $low = [];
        foreach ($avgRows as $row) {
            $avg = (float)$row->avg_rating;
            if ($avg >= 8) {
                $outstanding[] = $row;
            } elseif ($avg <= 3) {
                $low[] = $row;
            }
        }

        $data['outstanding']    = $outstanding;
        $data['low_performers'] = $low;
        $data['selected_month'] = $selectedYM;
        
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
        
        
        // Load the correct TL dashboard view
        $this->load->view('tl/dashboard', $data);
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
        
        // Load the correct employee dashboard view
        $this->load->view('employee/dashboard', $data);
    }
    
}
