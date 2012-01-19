<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Linkedindata extends REST_Controller
{
    private $linkedinData;
    private $xmlData;
    private $workIds = "";
    private $eduIds = "";
    private $typeMatch = false;
    
    public function __construct()
    {        
        parent::__construct();
        
        $this->linkedinData['appKey'] = $this->config->item('linkedin_apikey');
        $this->linkedinData['appSecret'] = $this->config->item('linkedin_secret');
        $this->linkedinData['callbackUrl'] = base_url() . "app/login";

        $this->load->model('Users_model', 'users');
        $this->load->library('Simple_Xml_Handler');
    }
    
    // useful function when working with a flash application 
    // that needs to the the avatar images as LinkedIn still
    // doesn't want to provide a crossdomain.xml files
    public function avatar_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        } else {
            //get the loggedin user if from session, may be like
            $userId = $this->session->userdata("user_id");

            // check if the user is logged in
            $linkedinCreds = $this->users->get_linkedin_user($userId);

            if(!empty($linkedinCreds)) {
                // set the auth token and secret
                $this->authTokens($linkedinCreds);

                $friendImage = $this->linkedin->profile('id=' . $this->get('id') . ':(picture-url)');
                                
                $friendImage = simplexml_load_string($friendImage['linkedin']);
                $profileImage = $friendImage->{'picture-url'};
                
                header('Content-Type: image/jpeg');
                readfile($profileImage);
            }
        }
    }
    
    
    public function profile_get()
    {	
        //get the loggedin user if from session, may be like
        $userId = $this->session->userdata("user_id");
                
        // check if the user is logged in
        $linkedinCreds = $this->users->get_linkedin_user($userId);

        if(!empty($linkedinCreds)) {
            // set the auth token and secret
            $this->authTokens($linkedinCreds);
            
            // get the users profile
            $userProfile = $this->linkedin->profile('~:(id,first-name,last-name,picture-url,num-connections,num-connections-capped,num-recommenders,positions,educations)');
            $userProfile = simplexml_load_string($userProfile['linkedin']);
        } 
        else 
        {
            $this->response("session data not set, user not authenticated", 400);
        }
    }

    
    private function authTokens($linkedinCreds)
    {
        $this->load->library('linkedin', $this->linkedinData);
        $access_token = array('oauth_token' => $linkedinCreds[0]->token, 'oauth_token_secret' => $linkedinCreds[0]->oauth_secret, 'oauth_expires_in' => $linkedinCreds[0]->oauth_expires_in, 'oauth_authorization_expires_in' => $linkedinCreds[0]->oauth_authorization_expires_in);
        $response = $this->linkedin->setTokenAccess($access_token);
    }
    
    private function setupXML($string)
    {
        $data = simplexml_load_string($string);
        return $data;
        
    }
    
    private function getUserProfile()
    {
        $response = $this->linkedin->getUserProfile($this->linkedin->token);
        $response = simplexml_load_string($response);
        return $response;
    }
    
    private function getFriendProfile($memberId)
    {
        $response = $this->linkedin->getFriendProfile($this->linkedin->token, $memberId);
        $response = simplexml_load_string($response);
        return $response;
    }
    
    private function getFriendImage($memberId)
    {
        $response = $this->linkedin->getFriendImage($this->linkedin->token, $memberId);
        $response = simplexml_load_string($response);
        return $response;
    }
    
    private function getConnections()
    {
        $response = $this->linkedin->getUserConnections($this->linkedin->token);
        $response = simplexml_load_string($response);
        return $response;
    }
}