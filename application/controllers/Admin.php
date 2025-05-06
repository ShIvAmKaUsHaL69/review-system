<?php

/**
 * @property CI_Input        $input
 * @property User_model      $User_model
 * @property Question_model  $Question_model
 * @property CI_Output       $output
 */
class Admin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role(['admin']);
        $this->load->model(['Question_model']);
    }

    /* Users page */
    public function users()
    {
        if ($this->input->method() === 'post') {
            $this->User_model->create($this->input->post());
            redirect('admin/users');
        }
        $data['tls']       = $this->User_model->all_tl();
        $data['employees'] = $this->User_model->all_employees();
        $this->load->view('admin/users',$data);
    }

    /* Questions page */
    public function questions()
    {
        if ($this->input->method() === 'post') {
            $this->Question_model->create(
                $this->input->post('text'),
                $this->input->post('for_role')
            );
            redirect('admin/questions');
        }
        $data['questions_tl']  = $this->Question_model->get_by_role(2);
        $data['questions_emp'] = $this->Question_model->get_by_role(3);
        $this->load->view('admin/questions',$data);
    }

    public function reviews($id = null)
    {
        $this->load->model('Submission_model');
        if ($id) {
            $data['submission'] = $this->Submission_model->find($id);
            if(!$data['submission']) show_404();
            $data['answers']    = $this->Submission_model->get_answers($id);
            $this->load->view('admin/review_detail',$data);
        } else {
            $data['submissions'] = $this->Submission_model->list_all();
            $this->load->view('admin/reviews',$data);
        }
    }

    public function get_review_json($id)
    {
        $this->load->model('Submission_model');
        $submission = $this->Submission_model->find($id);
        
        if (!$submission) {
            $this->output->set_status_header(404);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Not found']));
            return;
        }
        
        $answers = $this->Submission_model->get_answers($id);
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'submission' => $submission,
            'answers' => $answers
        ]));
    }
}
