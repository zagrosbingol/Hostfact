<?php
class wfPDOStatement extends PDOStatement
{

	function bindValue($parameter, $value, $data_type = PDO::PARAM_STR): bool
	{
		// In case of a NULL value, make it an empty string
		if($value === null)
		{
			$value = '';
		}
		
		// Use PDOStatement method for further processing
		return parent::bindValue($parameter, $value, $data_type);
	}
	
	function execute($parameters = array()): bool
	{
		
		try {
			if(!empty($parameters))
			{
				return parent::execute($parameters);
			}
			else
			{
				return parent::execute();
			}
		} catch (PDOException $e) {
		    if(stripos($e->getMessage(), 'Base table or view not found'))
            {
                fatal_error('Your database connection is not configured yet', 'If you copy the connect.php file from the backoffice to the order form folder, the database connection is restored.');
            }
			fatal_error(__('error in mysql query'), $e->getMessage());
		}
	}	
}

class Database_Model extends PDO{
	
	public static $instance;
	
	static function getInstance(){
		if(!self::$instance)
		{
      		self::$instance = new Database_Model();
    	}
	
		return self::$instance;
	}
	
	function __construct(){
		self::$instance = $this;
		
		// Error messages
		$this->Error = array();
		
		// Make connection
		if(!@file_exists('connect.php')){
			fatal_error('Your database connection is not configured yet', 'If you copy the connect.php file from the backoffice to the order form folder, the database connection is restored.');
		}
	
		// Require DB credentials
		require_once 'connect.php';
		
		// Set headers
		header("Content-type: text/html; charset=utf-8");
		mb_internal_encoding('UTF-8');
		
		if(defined("DB_CRYPT") && DB_CRYPT){
		    $db_host 		= (defined("DB_HOST")) ? db_decrypt(DB_HOST)  : "" ;
			$db_user 		= (defined("DB_USERNAME")) ? db_decrypt(DB_USERNAME) : "" ;
			$db_password 	= (defined("DB_PASSWORD")) ? db_decrypt(DB_PASSWORD) : "" ;
			$db_name 		= (defined("DB_NAME")) ? db_decrypt(DB_NAME) : "" ;
		}elseif(defined("DB_HOST")){
			$db_host 		= (defined("DB_HOST")) ? DB_HOST : "" ;
			$db_user 		= (defined("DB_USERNAME")) ? DB_USERNAME : "" ;
			$db_password 	= (defined("DB_PASSWORD")) ? DB_PASSWORD : "" ;
			$db_name 		= (defined("DB_NAME")) ? DB_NAME : "" ;
		}else{
			fatal_error('Your database connection is not configured yet', 'If you copy the connect.php file from the backoffice to the order form folder, the database connection is restored.');
		}
		
		try {
		    parent::__construct('mysql:dbname=' . $db_name . ';host=' . $db_host, $db_user, $db_password);
		    $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('wfPDOStatement'));
		    
		    $this->query("SET collation_connection = 'utf8_general_ci', CHARACTER SET 'utf8', NAMES 'utf8', collation_server = 'utf8_general_ci', character_set_server = 'utf8', character_set_results = 'utf8', character_set_connection = 'utf8', character_set_client = 'utf8', collation_database = 'utf8_general_ci'");

			// Handle strict modus
			$strict_check = $this->query("SELECT @@SESSION.sql_mode as Modus");
			if($strict_check)
			{
				$strict_check = $strict_check->fetch();

				if(strpos(strtolower($strict_check->Modus), 'strict') !== FALSE || strpos(strtolower($strict_check->Modus), 'traditional') !== FALSE || strpos(strtolower($strict_check->Modus), 'no_zero_date') !== FALSE || strpos(strtolower($strict_check->Modus), 'only_full_group_by') !== FALSE)
				{
					$this->query("SET SESSION sql_mode ='NO_ENGINE_SUBSTITUTION'");
				}
			}
		    
		} catch (PDOException $e) {
			fatal_error('Your database connection is not configured yet', 'If you copy the connect.php file from the backoffice to the order form folder, the database connection is restored.');
		}
		
	}
}