<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_model extends CI_Model
{
    private $table = 'complains';

    /**
     * Insert a new complaint row.
     *
     * @param int $submitted_against    ID of the user the complaint is against
     * @param string $text              Complaint text
     * @return int                      Insert ID
     */
    public function create($submitted_against, $text)
    {
        $data = [
            'submitted_against' => $submitted_against,
            'complain'          => $text,
            'created_at'        => date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Fetch all complaints with helper join to get user names.
     * @return array
     */
    public function list_all()
    {
        return $this->db
            ->select('c.*, sa.name AS against_name')
            ->from($this->table . ' c')
            ->join('users sa', 'sa.id = c.submitted_against', 'left')
            ->order_by('c.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Fetch complaints with optional filters.
     *
     * @param int|null    $against_id   Filter by user ID the complaint is against
     * @param string|null $yearmonth    Filter by YYYY-MM string (e.g., 2024-05)
     * @return array
     */
    public function list_filtered($against_id = null, $yearmonth = null)
    {
        $this->db->select('c.*, sa.name AS against_name')
                 ->from($this->table . ' c')
                 ->join('users sa', 'sa.id = c.submitted_against', 'left');

        if ($against_id) {
            $this->db->where('c.submitted_against', $against_id);
        }

        if ($yearmonth) {
            // Compare year & month part only
            $this->db->where('DATE_FORMAT(c.created_at, "%Y-%m") =', $yearmonth);
        }

        return $this->db->order_by('c.created_at', 'DESC')->get()->result();
    }

    /**
     * Get distinct year-month strings (YYYY-MM) that have complaints.
     *
     * @return array Array of strings ordered desc, e.g. ['2024-05','2024-04']
     */
    public function get_available_months()
    {
        return array_column(
            $this->db->select('DISTINCT DATE_FORMAT(created_at, "%Y-%m") AS ym')
                      ->from($this->table)
                      ->order_by('ym', 'DESC')
                      ->get()
                      ->result_array(),
            'ym'
        );
    }

    /**
     * Get list of users who have complaints against them (id, name).
     *
     * @return array
     */
    public function get_against_users()
    {
        return $this->db->select('u.id, u.name')
                        ->from($this->table . ' c')
                        ->join('users u', 'u.id = c.submitted_against', 'left')
                        ->group_by('u.id')
                        ->order_by('u.name', 'ASC')
                        ->get()
                        ->result();
    }
} 