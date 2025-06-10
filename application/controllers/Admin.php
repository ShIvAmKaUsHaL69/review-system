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
        // Handle new question creation
        if ($this->input->method() === 'post') {
            $this->Question_model->create(
                $this->input->post('text'),
                $this->input->post('for_role'),
                $this->input->post('quater')
            );
            redirect('admin/questions');
        }

        // Optional quarter filter (GET)
        $filter_quater = $this->input->get('filter_quater');

        // Fetch questions with optional filter
        $data['questions_tl']        = $this->Question_model->get_by_role(2, false, $filter_quater);
        $data['questions_emp']       = $this->Question_model->get_by_role(3, false, $filter_quater);
        $data['questions_emp_to_emp'] = $this->Question_model->get_by_role(4, false, $filter_quater);

        $data['filter_quater'] = $filter_quater; // pass to view so dropdown remembers

        $this->load->view('admin/questions', $data);
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

    public function performance()
    {
        // Load dependencies
        $this->load->model(['Submission_model','Question_model']);

        // Read filters from query string
        $filter_tl      = $this->input->get('tl_id');
        $filter_period  = $this->input->get('period_id');
        $filter_level   = $this->input->get('level'); // outstanding|good|average|bad|null
        $filter_type    = $this->input->get('review_type'); // tl_emp|emp_emp|emp_tl|null

        // Base data for dropdowns / filters
        $data['tls']           = $this->User_model->all_tl();
        $data['periods']       = $this->Submission_model->get_all_periods();
        $data['filter_tl']     = $filter_tl;
        $data['filter_period'] = $filter_period;
        $data['filter_level']  = $filter_level;
        $data['filter_type']   = $filter_type;

        // Fetch submissions using helper with direction filter
        $submissions = $this->Submission_model->list_filtered($filter_tl, $filter_period, $filter_type);

        // Pre-compute answers, average rating, and collect common question list
        $common_questions = null; // intersection set
        foreach ($submissions as $idx => $s) {
            $answers = $this->Submission_model->get_answers($s->id);
            $total   = 0;
            $count   = 0;
            $answers_map = [];
            foreach ($answers as $a) {
                $answers_map[$a->text] = $a;
                $total += (int)$a->rating;
                $count++;
            }
            $avg = $count ? ($total / $count) : null;
            // Add new properties onto submission object so the view can access directly
            $s->answers     = $answers_map;
            $s->avg_rating  = $avg !== null ? round($avg, 2) : null;

            // Build intersection of answered questions (to avoid blanks)
            $answered_qs = array_keys($answers_map);
            if ($common_questions === null) {
                $common_questions = $answered_qs;
            } else {
                $common_questions = array_intersect($common_questions, $answered_qs);
            }
        }

        // If performance filter selected, narrow submissions based on average rating
        if ($filter_level) {
            $filtered = [];
            foreach ($submissions as $s) {
                if ($s->avg_rating === null) continue; // skip incomplete rows
                $avg = $s->avg_rating;
                $keep = false;
                switch ($filter_level) {
                    case 'outstanding':
                        if ($avg >= 8) $keep = true;
                        break;
                    case 'good':
                        if ($avg >= 5 && $avg < 8) $keep = true;
                        break;
                    case 'average':
                        if ($avg >= 3 && $avg < 5) $keep = true;
                        break;
                    case 'bad':
                        if ($avg < 3) $keep = true;
                        break;
                }
                if ($keep) $filtered[] = $s;
            }
            $submissions = $filtered;
        }

        // Prepare ordered list of question objects (retain original order from DB if possible)
        $question_objs = [];
        if ($common_questions) {
            foreach ($common_questions as $q_text) {
                $obj       = new stdClass();
                $obj->text = $q_text;
                $question_objs[] = $obj;
            }
        }

        $data['submissions'] = $submissions;
        $data['questions']   = $question_objs;

        $this->load->view('admin/performance', $data);
    }

    public function update_question()
    {
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            $this->output->set_status_header(405);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Method not allowed']));
            return;
        }
        
        $id = $this->input->post('id');
        $text = $this->input->post('text');
        
        if (!$id || !$text) {
            $this->output->set_status_header(400);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Missing required fields']));
            return;
        }
        
        // Update the question
        $result = $this->Question_model->update($id, $text);
        
        $this->output->set_content_type('application/json');
        if ($result) {
            $this->output->set_output(json_encode(['success' => true]));
        } else {
            $this->output->set_status_header(500);
            $this->output->set_output(json_encode(['error' => 'Failed to update question']));
        }
    }

    public function update_user()
    {
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            $this->output->set_status_header(405);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Method not allowed']));
            return;
        }
        
        $id = $this->input->post('id');
        if (!$id) {
            $this->output->set_status_header(400);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Missing user ID']));
            return;
        }
        
        // Get data to update
        $updateData = [];
        
        // Check which fields are being updated
        if ($this->input->post('name')) {
            $updateData['name'] = $this->input->post('name');
        }
        
        if ($this->input->post('email')) {
            $updateData['email'] = $this->input->post('email');
        }
        if ($this->input->post('password')) {
            $updateData['password'] = $this->input->post('password');
        }
        
        if ($this->input->post('tl_id') !== null) {
            $updateData['tl_id'] = $this->input->post('tl_id') ?: null;
        }
        
        if ($this->input->post('role_id')) {
            $updateData['role_id'] = $this->input->post('role_id');
        }

        if ($this->input->post('designation')) {
            $updateData['designation'] = $this->input->post('designation');
        }
        
        // Update the user
        $result = $this->User_model->update($id, $updateData);
        
        $this->output->set_content_type('application/json');
        if ($result) {
            // If role or TL changed, we need to send back additional data
            $data = ['success' => true];
            
            // If TL selection changed, provide TL name
            if (isset($updateData['tl_id']) && $updateData['tl_id']) {
                $tl = $this->User_model->find($updateData['tl_id']);
                if ($tl) {
                    $data['tl_name'] = $tl->name;
                }
            }
            
            // If role changed, provide role name
            if (isset($updateData['role_id'])) {
                $data['role_name'] = $updateData['role_id'] == 2 ? 'TL' : 'Employee';
            }
            
            $this->output->set_output(json_encode($data));
        } else {
            $this->output->set_status_header(500);
            $this->output->set_output(json_encode(['error' => 'Failed to update user']));
        }
    }

    public function charts()
    {
        // Load required models
        $this->load->model(['Submission_model','Question_model']);

        // Fetch filter values (GET)
        $target_id        = $this->input->get('target_id');
        $start_period_id  = $this->input->get('start_period');
        $end_period_id    = $this->input->get('end_period');

        // Data for dropdowns
        // All TL + Employees in one list for easy selection
        $data['users']    = $this->db->select('id,name,role_id')->from('users')->order_by('name','ASC')->get()->result();
        $data['periods']  = $this->Submission_model->get_all_periods();

        $data['target_id']       = $target_id;
        $data['start_period_id'] = $start_period_id;
        $data['end_period_id']   = $end_period_id;

        $data['charts'] = [];
        $data['months_range'] = [];

        // Only build charts if all filters are present
        if ($target_id && $start_period_id && $end_period_id) {
            // Resolve start / end yearmonth strings for easier comparison
            $start_period = $this->db->get_where('periods',['id'=>$start_period_id])->row();
            $end_period   = $this->db->get_where('periods',['id'=>$end_period_id])->row();
            if($start_period && $end_period) {
                $startYM = $start_period->yearmonth;
                $endYM   = $end_period->yearmonth;

                // Generate list of months between start and end inclusive
                $months = [];
                $d = new DateTime($startYM.'-01');
                $endD = new DateTime($endYM.'-01');
                while ($d <= $endD) {
                    $months[] = $d->format('Y-m');
                    $d->modify('+1 month');
                }
                $data['months_range'] = $months;

                // Build query to fetch answers within range for target user
                $rows = $this->db->select('s.submitter_id, u.name AS submitter_name, r.role_name AS submitter_role, p.yearmonth, q.text AS question, sa.rating')
                                  ->from('submissions s')
                                  ->join('submission_answers sa','sa.submission_id = s.id')
                                  ->join('questions q','q.id = sa.question_id')
                                  ->join('periods p','p.id = s.period_id')
                                  ->join('users u','u.id = s.submitter_id')
                                  ->join('roles r','r.id = u.role_id')
                                  ->where('s.target_id', $target_id)
                                  ->where('p.yearmonth >=', $startYM)
                                  ->where('p.yearmonth <=', $endYM)
                                  ->order_by('u.role_id','ASC') // TL first
                                  ->order_by('u.name','ASC')
                                  ->order_by('p.yearmonth','ASC')
                                  ->get()->result();

                // Organise rows per submitter
                $chartData = [];
                foreach ($rows as $row) {
                    if(!isset($chartData[$row->submitter_id])) {
                        $chartData[$row->submitter_id] = [
                            'submitter_name' => $row->submitter_name,
                            'submitter_role' => $row->submitter_role,
                            'ratings'        => [] // [question][yearmonth] => rating
                        ];
                    }
                    $chartData[$row->submitter_id]['ratings'][$row->question][$row->yearmonth] = $row->rating;
                }
                $data['charts'] = $chartData;
            }
        }

        $this->load->view('admin/charts', $data);
    }
}
