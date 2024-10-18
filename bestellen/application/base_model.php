<?php
class Base_Model{
	
	public $_db;

	function __construct()
	{
				
		// Error messages
		$this->Warning = array();
		$this->Error = array();
		
		// Make database connection
		$this->_db = Database_Model::getInstance();
		
		// Get settings
		$this->_settings = Setting_Model::getInstance();
				
	}
}
