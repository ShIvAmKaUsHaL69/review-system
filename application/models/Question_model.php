<?php
class Question_model extends CI_Model
{
    public function get_by_role($role_id)
    {
        return $this->db->get_where('questions', ['for_role' => $role_id])->result();
    }

    public function create($text, $for_role)
    {
        $this->db->insert('questions', ['text' => $text, 'for_role' => $for_role]);
    }

    public function update($id, $text)
    {
        return $this->db->update('questions', ['text' => $text], ['id' => $id]);
    }
}
