<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Event extends REST_Controller
{
	
    function invite_post()
    {		
        if(!$this->post('facebookIdStr'))
        {
            $this->response(NULL, 400);
        } 
        else
        {
            $facebookUserIds = explode(",", $this->post('facebookIdStr'));
            $eventId = $this->post('eventId');

            //$eventInvite = $this->fb_ignited->api('Events.invite', array( 'personal_message' => "personal message" ,'eid' => $eventId, 'uids' => $facebookUserIds));

            $eventInvite = $this->fb_ignited->api(array(
                    'method' => 'events.invite',
                    'eid' => $eventId,
                    'uids' => $facebookUserIds,
                    'personal_message' => "Enjoy some breakfast rugby with your mates!",
                ));

                    $data = array('returned: '. $eventInvite);  
            $this->response($data, 200);
        }
    }
	
    function create_post()
    {
        $this->fb_ignited->setFileUploadSupport(true); 

        if(!$this->post('eventName'))
        {
            $this->response(NULL, 400);
        } 
        else
        {
            $event_param = array(
                    "name" 		=>	$this->post('eventName'),
                    "description"	=>	$this->post('eventDesc'),
                    "start_time" 	=>	$this->post('eventStart'),
                    "location"		=>	$this->post('eventLocation'),
                    "privacy_type"	=>	"CLOSED",
                    "picture"		=>	'@' . $_SERVER['DOCUMENT_ROOT'] . "/static/images/global/event-image.jpg"
            );

                    $event_id = $this->fb_ignited->api("/me/events", "POST", $event_param);

                    $data = array("event" => $event_id);  
            $this->response($data, 200);
        }
    }
}