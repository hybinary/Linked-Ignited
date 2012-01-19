<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

        private $linkedInData;
    
	public function __construct()
	{
            parent::__construct();
            
            $this->load->model('Users_model', 'users');
            $this->load->library('Simple_Xml_Handler');
	}

	public function index($type="")
	{
            //get the loggedin user if from session, may be like
            $userId = $this->session->userdata("user_id");
            
            if ($userId != false) {
                // check if the user is logged in
                $linkedinCreds = $this->users->get_linkedin_user($userId);
            } 
            
            // set the flash vars
            if(!empty($linkedinCreds)) {
                $this->set("auth", 1);
            } else {
                $this->set("auth", 0);
            }
            
            $this->set("appLoad", "loader_intro");
            $this->render();
	}
}
?>