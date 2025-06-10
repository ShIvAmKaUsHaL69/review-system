<?php
class Question_model extends CI_Model
{
    /**
     * Fetch questions for a particular role.
     *
     * @param int  $role_id                 Role identifier (2 = TL→TM, 3 = TM→TL, 4 = TM→TM)
     * @param bool $apply_quarter_filter    When TRUE, only return questions that are relevant for the
     *                                      current month based on their `quater` value. When FALSE the
     *                                      filter is skipped and all questions for the role are returned.
     *                                      Default is FALSE so that admin pages still see the full list.
     * @param string $specific_quater       Optional specific quarter filter
     * @return array                        List of question rows as objects
     */
    public function get_by_role($role_id, $apply_quarter_filter = false, $specific_quater = null)
    {
        // If a specific quarter filter is provided (from admin filter), use it directly
        if ($specific_quater !== null && $specific_quater !== '') {
            return $this->db
                        ->where('for_role', $role_id)
                        ->where('quater', (int)$specific_quater)
                        ->get('questions')
                        ->result();
        }

        if ($apply_quarter_filter) {
            // Determine which `quater` values are allowed for the current month.
            // 0  = show every month
            // 1  = March  (month 3)
            // 2  = June   (month 6)
            // 3  = Sept   (month 9)
            // 4  = Dec    (month 12)

            $month = (int) date('n'); // 1-12
            $allowed = [0]; // always include monthly questions

            switch ($month) {
                case 3:
                    $allowed[] = 1;
                    break;
                case 6:
                    $allowed[] = 2;
                    break;
                case 9:
                    $allowed[] = 3;
                    break;
                case 12:
                    $allowed[] = 4;
                    break;
            }

            return $this->db
                        ->where('for_role', $role_id)
                        ->where_in('quater', $allowed)
                        ->get('questions')
                        ->result();
        }

        // No quarter filter – return all questions for the role
        return $this->db->get_where('questions', ['for_role' => $role_id])->result();
    }

    /**
     * Insert a new question
     *
     * @param string $text       The question text
     * @param int    $for_role   Role for which the question is applicable
     * @param int    $quater     Visibility rule (0-4) as described above
     */
    public function create($text, $for_role, $quater = 0)
    {
        $this->db->insert('questions', [
            'text'      => $text,
            'for_role'  => $for_role,
            'quater'    => $quater,
        ]);
    }

    /**
     * Update a question's text (and optionally its quarter value).
     * Only the text is currently updated by the UI but we keep the method extensible.
     */
    public function update($id, $text, $quater = null)
    {
        $data = ['text' => $text];
        if ($quater !== null) {
            $data['quater'] = $quater;
        }
        return $this->db->update('questions', $data, ['id' => $id]);
    }
}
