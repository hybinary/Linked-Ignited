<?php

class MY_Controller extends CI_Controller {

	private $_data = array();
	private $return_only_view = false;
	
	function __construct()
	{
		parent::__construct();
		//$this->load_language();
		$this->set('module', $this->_get_module());
		$this->set('view', $this->router->method.EXT);
		
		//$this->_initialize_meta();
	}
	        
    public function return_view_only()
    {
    	$this->return_only_view = true;
    }
	
	public function set($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	public function get($key)
	{
		return (isset($this->_data[$key])) ? $this->_data[$key] : NULL;
	}
	
	protected function meta($key, $value)
	{
		$this->_data['meta'][$key] = $value;
	}
	
	public function render()
	{
		$this->load->vars($this->_data);
	
        if($this->return_only_view == true)
        {
        	$this->load->view('modules/'.$this->_data['module'].'/'.$this->_data['view']);
        }
        else
        {
        	$this->load->view('loader');
		}
	}
	
	private function _get_module()
	{
		$module = '';
	
		if ( !empty($this->router->directory) ) $module .= $this->router->directory.'/';
		$module .= $this->router->class;
	
		return $module;
	}

	private function _initialize_meta()
	{
		$this->load->config('meta');
	
		$meta = $this->config->item('meta');
	
		foreach ( $meta as $key => $value )
		{
			$this->meta($key, $value);
		}
	}
	
	public function error($code)
	{
		$method = '_error_'.$code;
		return $this->$method();
	}
	
	private function _error_404()
	{
		$this->set('module', 'error');
	
		$this->output->set_status_header('404');
		$this->set('view', '404.'.$this->router->rest_format().EXT);
		
		$this->render();
	}


    /**
	* Load up the language files
	* If in admin uses the user language settings, otherwise uses the
	* config item language as set in the config file based on subdomains
	*/
	function load_language()
	{
		// load language as set in Config file
        $this->lang->load('loader', $this->config->item('language') );

        // set config item for the language as a country code from the DB
        if(isset($language) )
			$this->config->set_item('language_code', $this->countries_model->get_country_code_from_name($language));

	}
}