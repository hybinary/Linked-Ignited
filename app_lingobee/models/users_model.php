<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model 
{
	
    function __construct()
    {
	parent::__construct();
        $this->load->database();
    }

    function get_linkedin_user($id)
    {
        $query = $this->db->get_where('users', array('user_id' => $id));
        return $query->result();
    }
    
    function save_linkedin_user($data)
    {
        $this->db->insert('users', $data);
        $result = $this->db->insert_id();
        return $result;
    }
	
}