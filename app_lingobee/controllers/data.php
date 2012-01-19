<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data extends MY_Controller {
	
        private $linkedInData;


        public function __construct()
	{
            parent::__construct();
            
            parse_str($_SERVER['QUERY_STRING'],$_GET);
            
            $this->linkedinData['consumer_key'] = $this->config->item('linkedin_apikey');
            $this->linkedinData['consumer_secret'] = $this->config->item('linkedin_secret');
            $this->linkedinData['callback_url'] = base_url() . "data/user";
            
            $this->load->model('Users_model', 'users');
            $this->load->library('Simple_Xml_Handler');
	}

	public function index($type="")
	{
            //get the loggedin user if from session, may be like
            $userId = $this->session->userdata("user_id");
            
            // check if the user is logged in
            $linkedinCreds = $this->users->get_linkedin_user($userId);
            
            
            if(!empty($linkedinCreds)) {     
                $this->linkedinData['oauth_token'] = $linkedinCreds[0]->token;
                $this->linkedinData['oauth_token_secret'] = $linkedinCreds[0]->oauth_secret;
                $this->load->library('linkedin', $this->linkedinData);
                
                $profileResponse = $this->linkedin->getUserProfile($this->linkedin->token);
                $profileResponse = simplexml_load_string($profileResponse);
                $userId = $profileResponse->xpath("//person");
                
                $this->set("profile", $profileResponse->id);
                
                $this->render();
            } else {
                $this->load->library('linkedin', $this->linkedinData);
                $token = $this->linkedin->get_request_token();
                
                //store the tokens in session
                $tokenArray = array(
                                    "oauth_request_token"           =>  $token['oauth_token'],
                                    "oauth_request_token_secret"    =>  $token['oauth_token_secret']
                );
                
                $this->session->set_userdata($tokenArray);
                
                $request_link = $this->linkedin->get_authorize_URL($token);
                redirect($request_link);
            }
	}
        
        public function user()
        {
            //get request tokens from session
            $this->linkedinData['oauth_token'] = $this->session->userdata('oauth_request_token');
            $this->linkedinData['oauth_token_secret'] = $this->session->userdata('oauth_request_token_secret');
            
            $this->load->library('linkedin', $this->linkedinData);
            $tokenArray = array("oauth_verifier" => $_GET['oauth_verifier']);
            $this->session->set_userdata($tokenArray);
            
            $tokens = $this->linkedin->get_access_token($this->session->userdata('oauth_verifier'));
                        
            $profileResponse = $this->linkedin->getUserProfileId($this->linkedin->token);
            $profileResponse = simplexml_load_string($profileResponse);
            $userId = $profileResponse->xpath("//id");
            
            foreach ($userId as $key => $value)
            {
                $userId = $value;
            }
            
            $userId = (string)$userId;
            $userArray = array( "user_id" => $userId);
            $this->session->set_userdata($userArray);
            
            $linkedinData = array(
                'user_id'       =>   $userId,
                'token'         =>   $tokens['oauth_token'],
                'oauth_secret'  =>   $tokens['oauth_token_secret']
            );
            
            $insertLinkedinData = $this->users->save_linkedin_user($linkedinData, $userId);
            //now redirect to linkedin function
            redirect(base_url() . "data");
        }
}
