<?php

class Base_Controller
{
	public $lang = array();	
	
	private $_vars = array();


	public function __construct()
	{					
		// Error messages
		$this->Warning = array();
		$this->Error = array();
	}

	public function set($index, $value)
	{
		$this->_vars[$index] = $value;
	}
	
	public function get($index)
	{
		return $this->_vars[$index];
	}
			
	function display($view, $view_dir = 'views')
	{
		
		global $_LANG;

		// Load variables
		foreach($this->_vars as $key => $value)
		{
			${$key} = $value;
		}
			
		include "views/header.phtml";

		// Load view
		if(file_exists($view_dir."/".$view))
		{
			include $view_dir."/".$view;
		}
		else
		{
			fatal_error('Een bestand ontbreekt', 'Kan het benodigde bestand voor de view "'.$view.'" niet vinden.');
		}
		
		
		include "views/footer.phtml";	
	}
}

