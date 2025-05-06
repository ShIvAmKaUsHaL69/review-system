<?php

/**
 * @property User_model        $User_model
 * @property Question_model    $Question_model
 * @property Submission_model  $Submission_model
 * @property CI_Input          $input
 * @property CI_Session        $session
 */
class Rating extends MY_Controller
{
    public function submit($target_user_id = null)
    {
        // Allowed for TLs or employees only
        $this->require_role(['tl','employee']);

        // Determine target list
        if (
            $this->user->role_name === 'tl'
        ) {
            $targets = $this->User_model->all_employees($this->user->id);
        } else {
            $targets = $this->User_model->all_employees($this->user->tl_id);
            $tl      = $this->User_model->find($this->user->tl_id);
            if($tl) array_unshift($targets, $tl); // employee can also rate his TL
        }

        // will filter after we detect current period

        // If explicit target ID is provided in query string, keep it for view
        $data_preselect_target = $this->input->get('target_id') ?? $target_user_id;

        $this->load->model(['Question_model','Submission_model']);

        // Create (or find) current period row
        $ym = date('Y-m');
        $period = $this->db->get_where('periods',['yearmonth'=>$ym])->row();
        if (!$period) {
            $this->db->insert('periods',['yearmonth'=>$ym]);
            $period = (object)['id'=>$this->db->insert_id()];
        }

        // Remove self from targets list for employees
        $targets = array_filter($targets, fn($t)=>$t->id!=$this->user->id);

        // Exclude targets already submitted this month
        $submittedIds = array_column($this->Submission_model->get_submitted_targets($this->user->id,$period->id),'target_id');
        $targets = array_values(array_filter($targets, fn($t)=> !in_array($t->id,$submittedIds)));

        // On form submit:
        if ($this->input->method() === 'post') {
            $target_id = $this->input->post('target_id');
            if (!$this->Submission_model->can_submit($this->user->id,$target_id,$period->id)) {
                $this->session->set_flashdata('error','Already submitted for this target in '.$ym);
                redirect('dashboard');
            }

            $answers = [];
            foreach ($this->input->post('rating') as $q_id => $rating) {
                $answers[$q_id] = [
                    'rating'  => $rating,
                    'comment' => $this->input->post('comment')[$q_id] ?? ''
                ];
            }
            $this->Submission_model->save($this->user->id,$target_id,$period->id,$answers);
            $this->session->set_flashdata('success','Thank you!');
            redirect('dashboard');
        }

        // GET – render form
        $role_for_questions = $this->user->role_name === 'tl' ? 2 : 3;
        $data['questions']  = $this->Question_model->get_by_role($role_for_questions);
        $data['targets']    = $targets;
        $data['preselect']  = $data_preselect_target;
        $this->load->view('rating/form', $data);
    }
}
