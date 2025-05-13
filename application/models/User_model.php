<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'users';

    /**
     * Get single user by id including role name.
     */
    public function find($id)
    {
        return $this->db->select('u.*, r.role_name')
                        ->from("{$this->table} u")
                        ->join('roles r', 'r.id = u.role_id', 'left')
                        ->where('u.id', $id)
                        ->get()
                        ->row();
    }

    /**
     * Get user by email (login purpose)
     */
    public function find_by_email($email)
    {
        return $this->db->select('u.*, r.role_name')
                        ->from("{$this->table} u")
                        ->join('roles r', 'r.id = u.role_id', 'left')
                        ->where('u.email', $email)
                        ->get()
                        ->row();
    }

    /**
     * Return all team leads.
     */
    public function all_tl()
    {
        return $this->db->select('*')
                        ->from($this->table)
                        ->where('role_id', 2) // 2 = TL
                        ->order_by('name')
                        ->get()
                        ->result();
    }

    /**
     * Return all employees, optionally filtered by tl_id.
     */
    public function all_employees($tl_id = null)
    {
        $this->db->select('*')->from($this->table)->where('role_id', 3); // 3 = employee
        if ($tl_id !== null) {
            $this->db->where('tl_id', $tl_id);
        }
        return $this->db->order_by('name')->get()->result();
    }

    /**
     * Insert new user record. Accepts associative array with name, email, password, role_id, tl_id.
     */
    public function create(array $data)
    {
        // Password is stored as plaintext as requested
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        // Filter valid fields to update
        $validFields = ['name', 'email','password', 'tl_id', 'role_id'];
        $updateData = array_intersect_key($data, array_flip($validFields));
        
        // Only proceed if we have valid data to update
        if (!empty($updateData)) {
            return $this->db->update('users', $updateData, ['id' => $id]);
        }
        return false;
    }
}