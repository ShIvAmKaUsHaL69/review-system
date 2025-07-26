<?php
class Submission_model extends CI_Model
{
    public function can_submit($submitter_id, $target_id, $period_id)
    {
        $this->db->where(compact('submitter_id','target_id','period_id'));
        return $this->db->count_all_results('submissions') === 0;
    }

    public function save($submitter_id, $target_id, $period_id, $answers)
    {
        $this->db->trans_start();

        $this->db->insert('submissions', compact('submitter_id','target_id','period_id'));
        $submission_id = $this->db->insert_id();

        foreach ($answers as $q_id => $payload) {
            $this->db->insert('submission_answers', [
                'submission_id' => $submission_id,
                'question_id'   => $q_id,
                'rating'        => $payload['rating'],
                'comment'       => $payload['comment'] ?? null
            ]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function list_all()
    {
        return $this->db
            ->select('s.*, u1.name AS submitter, u2.name AS target, p.yearmonth, r1.role_name AS submitter_role, r2.role_name AS target_role')
            ->from('submissions s')
            ->join('users u1','u1.id=s.submitter_id')
            ->join('users u2','u2.id=s.target_id')
            ->join('periods p','p.id=s.period_id')
            ->join('roles r1','r1.id=u1.role_id')
            ->join('roles r2','r2.id=u2.role_id')
            ->order_by('s.created_at','DESC')
            ->get()->result();
    }

    public function get_answers($submission_id)
    {
        return $this->db->select('q.text, sa.rating, sa.comment')
                        ->from('submission_answers sa')
                        ->join('questions q','q.id = sa.question_id')
                        ->where('sa.submission_id', $submission_id)
                        ->get()
                        ->result();
    }

    public function get_submitted_targets($submitter_id, $period_id)
    {
        return $this->db->select('target_id')
                        ->from('submissions')
                        ->where(compact('submitter_id','period_id'))
                        ->get()
                        ->result_array();
    }

    public function find($id)
    {
        return $this->db
            ->select('s.*, u1.name AS submitter, u2.name AS target, p.yearmonth, r1.role_name AS submitter_role, r2.role_name AS target_role')
            ->from('submissions s')
            ->join('users u1','u1.id=s.submitter_id')
            ->join('users u2','u2.id=s.target_id')
            ->join('periods p','p.id=s.period_id')
            ->join('roles r1','r1.id=u1.role_id')
            ->join('roles r2','r2.id=u2.role_id')
            ->where('s.id',$id)
            ->get()
            ->row();
    }

    public function get_period_id($yearmonth)
    {
        $row = $this->db->get_where('periods',['yearmonth'=>$yearmonth])->row();
        return $row ? $row->id : null;
    }

    public function can_submit_current($submitter_id,$target_id)
    {
        $period_id = $this->get_period_id(date('Y-m'));
        if(!$period_id) return true;
        return $this->can_submit($submitter_id,$target_id,$period_id);
    }

    public function get_all_periods()
    {
        return $this->db->select('id, yearmonth')
                       ->from('periods')
                       ->order_by('yearmonth', 'DESC')
                       ->get()
                       ->result();
    }
    
    public function list_filtered($tl_id = null, $period_id = null, $filter_type = null)
    {
        $this->db->select('s.*, u1.name AS submitter, u2.name AS target, p.yearmonth, r1.role_name AS submitter_role, r2.role_name AS target_role')
                ->from('submissions s')
                ->join('users u1','u1.id=s.submitter_id')
                ->join('users u2','u2.id=s.target_id')
                ->join('periods p','p.id=s.period_id')
                ->join('roles r1','r1.id=u1.role_id')
                ->join('roles r2','r2.id=u2.role_id');
        
        // Apply filters
        if ($period_id) {
            $this->db->where('s.period_id', $period_id);
        }
        
        if ($filter_type) {
            switch ($filter_type) {
                case 'tl_emp':
                    // TL rating employees
                    $this->db->where('r1.role_name', 'tl')
                            ->where('r2.role_name', 'employee');
                    break;
                    
                case 'emp_tl':
                    // Employee rating TL
                    $this->db->where('r1.role_name', 'employee')
                            ->where('r2.role_name', 'tl');
                    break;
                    
                case 'emp_emp':
                    // Employee rating employee
                    $this->db->where('r1.role_name', 'employee')
                            ->where('r2.role_name', 'employee');
                    break;
            }
        }
        
        // Filter by specific TL's team
        if ($tl_id) {
            $this->db->where("(u1.id = $tl_id OR u2.id = $tl_id OR u1.tl_id = $tl_id OR u2.tl_id = $tl_id)");
        }
        
        return $this->db->order_by('s.created_at','DESC')
                        ->get()
                        ->result();
    }

    /**
     * Check if a user has submitted any ratings in a specific month
     */
    public function has_rated_in_month($user_id, $yearmonth)
    {
        $period_id = $this->get_period_id($yearmonth);
        if (!$period_id) return false;

        return $this->db->where(['submitter_id' => $user_id, 'period_id' => $period_id])
                       ->count_all_results('submissions') > 0;
    }
}
