<?php
class Setting_Model{
	
	public $_db;
	private $_settings = array();
	
	public static $instance;
	
	static function getInstance(){
		if(!self::$instance)
		{
      		self::$instance = new Setting_Model();
    	}
	
		return self::$instance;
	}
	
	function __construct(){

		self::$instance = $this;

		// Load parent constructor
		$this->_db = Database_Model::getInstance();
		
		// Get global, whitelisted settings
	    $settings_whitelist = array('COMPANY_NAME', 'COMPANY_EMAIL', 'CLIENTAREA_LOGO_URL', 'IDEAL_EMAIL','LANGUAGE','DEFAULT_ORDERFORM','ORDERFORM_ENABLED','ORDERFORM_URL','CLIENTAREA_URL', 'DATE_FORMAT', 'BACKOFFICE_URL','CURRENCY_CODE','CURRENCY_SIGN_LEFT', 'CURRENCY_SIGN_RIGHT', 'AMOUNT_DEC_PLACES', 'AMOUNT_DEC_SEPERATOR', 'AMOUNT_THOU_SEPERATOR','SDD_ID', 'DKIM_DOMAINS');
	    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings` WHERE `Variable` IN ('".implode("','",$settings_whitelist)."')");

		// Execute statement
		$pdo_statement->execute();
		$settings = $pdo_statement->fetchAll();

		// Cache settings
		foreach($settings as $tmp_setting)
		{
			$this->_settings[$tmp_setting->Variable] = $tmp_setting->Value;
			if(!defined($tmp_setting->Variable)){ define($tmp_setting->Variable, $tmp_setting->Value); }
		}
	}
	
	function get($index)
	{	
		// If setting is known, return
		if(isset($this->_settings[$index]))
		{
			return $this->_settings[$index];
		}
		
        switch($index)
        {
            case 'company_data':
    			// Get country of company
    		    $pdo_statement = $this->_db->prepare("SELECT `CompanyName`, `EmailAddress` FROM `HostFact_Company` LIMIT 1");
    			$pdo_statement->execute();	
    			$result = $pdo_statement->fetch();	
    			
    			$this->_settings['company_data'] = $result;
            break;
            
            default:
                // If not already retrieved, get it anyway
        		$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings` WHERE `Variable`=:variable");
        		$pdo_statement->bindValue(':variable', $index);	
        
        		// Execute statement
        		$pdo_statement->execute();
        		$tmp_setting = $pdo_statement->fetch();
        
        		// Cache settings
        		if(isset($tmp_setting->Variable) && $tmp_setting->Variable == $index)
        		{
        			$this->_settings[$tmp_setting->Variable] = $tmp_setting->Value;
        		}            
            break;
        }
	
		return (isset($this->_settings[$index])) ? $this->_settings[$index] : null;
	}

}