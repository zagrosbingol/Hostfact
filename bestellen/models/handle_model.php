<?php
class Handle_Model extends Base_Model{
	
	public $Debtor, $Handle, $Registrar, $RegistrarHandle;
	public $CompanyName, $CompanyNumber, $TaxNumber, $LegalForm;
	public $Sex, $Initials, $SurName, $Address, $Address2, $ZipCode, $City, $State, $Country;
	public $EmailAddress, $PhoneNumber, $FaxNumber;
	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();
		
		// Default values
		$this->Debtor				= 0;
		$this->Sex					= 'm';
		$this->Country				= $this->_settings->get('company_country');
		
		// Load session data in model
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model'] as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}
	}
	
	/**
	 * Handle_Model::set()
	 * Save value in session and model
	 * 
	 * @param mixed $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{
		$_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model'][$index] = trim($value);
		$this->{$index} = trim($value);
	}
	
	/**
	 * Handle_Model::get()
	 * Get htmlspecialchars-save value
	 * 
	 * @param mixed $index
	 * @return string
	 */
	public function get($index)
	{
		return htmlspecialchars($this->{$index});
	}
	
	public function show()
	{
		$info = array();
		
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model'] as $key=>$value)
			{
				if(is_string($value))
				{
					$info[$key] = htmlspecialchars($value);
				}
			}
		}
		
		$array_states = $this->_settings->get('array_states');
		$info['StateName'] 			= (isset($info['State']) && isset($info['Country']) && isset($array_states[$info['Country']][$info['State']])) ? $array_states[$info['Country']][$info['State']] : ((isset($info['State'])) ? $info['State'] : '');
		
		// Return handle info
		return $info;
	}
	
	function lookupHandle($registrar_id){

		$candidates = array();

		$pdo_statement = $this->_db->prepare("SELECT `id`, `Handle`, `Debtor` FROM `HostFact_Handles` WHERE (`Registrar`=:registrar_id OR `Registrar`='') AND `Initials`=:initials AND `SurName`=:surname AND `Address`=:address AND `Address2`=:address2 AND `ZipCode`=:zipcode AND `City`=:city AND `State`=:state AND `Country`=:country AND `PhoneNumber`=:phonenumber AND `FaxNumber`=:faxnumber AND `EmailAddress`=:emailaddress AND `Sex`=:sex AND `CompanyName`=:companyname AND `TaxNumber`=:taxnumber AND `Status`!=9 ORDER BY `Debtor` DESC");
		$pdo_statement->bindValue(':registrar_id', 	$registrar_id);
		$pdo_statement->bindValue(':sex', 			$this->Sex);
        $pdo_statement->bindValue(':initials', 		$this->Initials);
        $pdo_statement->bindValue(':surname', 		$this->SurName);
        $pdo_statement->bindValue(':address', 		$this->Address);
        $pdo_statement->bindValue(':address2', 		$this->Address2);
        $pdo_statement->bindValue(':zipcode', 		$this->ZipCode);
        $pdo_statement->bindValue(':city', 			$this->City);
        $pdo_statement->bindValue(':state', 		$this->State);
        $pdo_statement->bindValue(':country', 		$this->Country);
        $pdo_statement->bindValue(':phonenumber', 	$this->PhoneNumber);
        $pdo_statement->bindValue(':faxnumber', 	$this->FaxNumber);
        $pdo_statement->bindValue(':emailaddress', 	$this->EmailAddress);
        $pdo_statement->bindValue(':companyname', 	$this->CompanyName);
        $pdo_statement->bindValue(':taxnumber', 	$this->TaxNumber);
		
		$pdo_statement->execute();	
		$handle_list = $pdo_statement->fetchAll();

		foreach($handle_list as $tmp_handle){
        	if(isset($tmp_handle->Debtor))
			{
        		$candidates[$tmp_handle->Debtor] = array('id' => $tmp_handle->id, 'handle' => $tmp_handle->Handle);
       		}
        }
        
        // Check candidates
        if(isset($candidates[$this->Debtor]))
		{
			// Found an exact match on same debtor, use it
        	return $candidates[$this->Debtor]['id'];
        }
		elseif(count($candidates) == 1 && isset($candidates[0]))
		{
			// Found an exact match which is general, use it
        	return $candidates[0]['id'];
        }
		elseif(count($candidates) == 1 || count($candidates) > 1)
		{
			// Found one handle, which is from another debtor, change it to general
			// Or if we found multiple matches, but none of them is a general match or from same debtor
        	foreach($candidates as $debtor_id => $handle_id)
			{
        		// Update Debtor AND Handle column
				$handle_tmp = (strpos($handle_id['handle'],parsePrefixVariables($this->_settings->get('DEBTORCODE_PREFIX'))) !== false && strpos($handle_id['handle'],parsePrefixVariables($this->_settings->get('DEBTORCODE_PREFIX'))) == 0) ? $this->nextInternalHandle('general') : $handle_id['handle'];
        		
				$pdo_statement = $this->_db->prepare("UPDATE `HostFact_Handles` SET `Debtor`='0', `Handle`=:new_handle WHERE `id`=:handle_id AND `Debtor`=:debtor_id");
				$pdo_statement->bindValue(':new_handle', 	$handle_tmp);
				$pdo_statement->bindValue(':handle_id', 	$handle_id['id']);
		        $pdo_statement->bindValue(':debtor_id', 	$debtor_id);
				
        		if($pdo_statement->execute()){
        			return $handle_id['id'];
        		}else{
        			return false;
        		}
       		}
        }

        // No match is found
        return false;
    }
    
    function nextInternalHandle($handle_type, $debtor_id = false){
    	$handle = '';
		
		switch($handle_type){
    		case 'debtor':
    		
    			// Get debtorcode
    			if($debtor_id == -1)
    			{
    				$debtorcode = "NIEUW";
    			}
    			else
    			{
			    	$pdo_statement = $this->_db->prepare("SELECT `DebtorCode` FROM `HostFact_Debtors` WHERE `id`=:debtor_id");
					$pdo_statement->bindValue(':debtor_id', 	$debtor_id);
					$pdo_statement->execute();	
					$debtor_info = $pdo_statement->fetch();
					$debtorcode = $debtor_info->DebtorCode;
				}
    		
    			// Get latest debtor-handle
    			$pdo_statement = $this->_db->prepare("SELECT `Handle` FROM `HostFact_Handles` WHERE `Handle` LIKE :debtorcode AND SUBSTRING(`Handle`,:debtorcode_length) REGEXP '^[0-9]*$' ORDER BY LENGTH(`Handle`) DESC, `Handle` DESC LIMIT 1");
				$pdo_statement->bindValue(':debtorcode', $debtorcode.'-%');
				$pdo_statement->bindValue(':debtorcode_length', strlen($debtorcode)+2);
				$pdo_statement->execute();	
				$last_handle = $pdo_statement->fetch();
     		
				// Calculate new handle  
                if($last_handle)
                {
            		$last_handle->Handle = str_replace($debtorcode,"",$last_handle->Handle);
            		$last_handle->Handle = substr($last_handle->Handle,strpos($last_handle->Handle,"-")+1);
            		$last_handle->Handle++;
                
                    $handle = $debtorcode."-".str_pad(max(1,$last_handle->Handle), 3, '0', STR_PAD_LEFT);
                }
                else
                {
                    $handle = $debtorcode."-".str_pad(1, 3, '0', STR_PAD_LEFT);
                }
    			break;
   			default:
   				$general_prefix = 'ALG';
   				
   				// Get latest debtor-handle
    			$pdo_statement = $this->_db->prepare("SELECT `Handle` FROM `HostFact_Handles` WHERE `Handle` LIKE :general_prefix AND SUBSTRING(`Handle`,:general_prefix_length) REGEXP '^[0-9]*$' ORDER BY LENGTH(`Handle`) DESC, `Handle` DESC LIMIT 1");
				$pdo_statement->bindValue(':general_prefix', $general_prefix.'-%');
				$pdo_statement->bindValue(':general_prefix_length', strlen($general_prefix)+2);
				$pdo_statement->execute();	
				$last_handle = $pdo_statement->fetch();
     		
				// Calculate new handle
                if($last_handle)
                { 
            		$last_handle->Handle = str_replace($general_prefix,"",$last_handle->Handle);
            		$last_handle->Handle = substr($last_handle->Handle,strpos($last_handle->Handle,"-")+1);
            		$last_handle->Handle++;
   
                    $handle = $general_prefix."-".str_pad(max(1,$last_handle->Handle), 3, '0', STR_PAD_LEFT);
                }
                else
                {
                    $handle = $general_prefix."-".str_pad(1, 3, '0', STR_PAD_LEFT);
                }
   				break;
    	}

		return $handle;    	
    }
    
    function add()
    {
   		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_Handles` (`Debtor`,`Handle`,`Registrar`,`RegistrarHandle`,`Initials`,`SurName`,`Address`,`Address2`,`ZipCode`,`City`,`State`,`Country`,`PhoneNumber`,`FaxNumber`,`EmailAddress`,`Sex`,`CompanyName`,`LegalForm`,`CompanyNumber`,`TaxNumber`,`HandleType`,`Status`) VALUES (:debtor_id, :handle, :registrar_id, '', :initials, :surname, :address, :address2, :zipcode, :city, :state, :country, :phonenumber, :faxnumber, :emailaddress, :sex, :companyname, :legalform, :companynumber, :taxnumber, '', 1)");
		
		$pdo_statement->bindValue(':debtor_id', 		$this->Debtor);
		$pdo_statement->bindValue(':handle', 			$this->Handle);
		$pdo_statement->bindValue(':registrar_id', 		$this->Registrar);
		$pdo_statement->bindValue(':sex', 				$this->Sex);
		$pdo_statement->bindValue(':initials', 			$this->Initials);
		$pdo_statement->bindValue(':surname', 			$this->SurName);
		$pdo_statement->bindValue(':address', 			$this->Address);
		$pdo_statement->bindValue(':address2', 			$this->Address2);
		$pdo_statement->bindValue(':zipcode', 			$this->ZipCode);
		$pdo_statement->bindValue(':city', 				$this->City);
		$pdo_statement->bindValue(':state', 			$this->State);
		$pdo_statement->bindValue(':country', 			$this->Country);
		$pdo_statement->bindValue(':companyname', 		$this->CompanyName);
		$pdo_statement->bindValue(':companynumber', 	$this->CompanyNumber);
		$pdo_statement->bindValue(':taxnumber', 		$this->TaxNumber);
		$pdo_statement->bindValue(':legalform', 		$this->LegalForm);
		$pdo_statement->bindValue(':emailaddress', 		getFirstMailAddress($this->EmailAddress));
		$pdo_statement->bindValue(':phonenumber', 		$this->PhoneNumber);
		$pdo_statement->bindValue(':faxnumber', 		$this->FaxNumber);
		
		// Execute statement
		$result = $pdo_statement->execute();	

		if($result)
		{
        	$this->Identifier = $this->_db->lastInsertId();
        	
        	// Should we also add custom fields?
			CustomClientFields_Model::addCustomFields('handle', $this->Identifier);
			
            return true;
        }
		else
		{
            return false;
        }	
    }
    
    function validate()
    {
    	if(!$this->CompanyName && !$this->SurName)
		{
			$this->Error[] = __('no companyname and no surname');
		}
		
		// Validate custom fields if any
		$this->Error = array_merge($this->Error, CustomClientFields_Model::validateCustomFields('handle'));
		
		// Are there any errors?
		return empty($this->Error) ? true : false;
    }

	static function listDebtorHandles()
	{
		// Get debtor information
		$debtor 	= new Debtor_Model();
		if(!$debtor->checkLogin())
		{
			return false;
		}
		$debtor->show();

		// List all handles
		$pdo_statement = Database_Model::getInstance()->prepare("SELECT `id`, `Handle`, `Registrar`, `Initials`,`SurName`,`Address`,`Address2`,`ZipCode`,`City`,`State`,`Country`,`PhoneNumber`,`FaxNumber`,`EmailAddress`,`Sex`,`CompanyName`,`LegalForm`,`CompanyNumber`,`TaxNumber` FROM `HostFact_Handles` WHERE `Debtor`=:debtor_id AND `Status`!=9 ORDER BY CONCAT(`CompanyName`,`Initials`,`SurName`) ASC");
		$pdo_statement->bindValue(':debtor_id', 	$debtor->Identifier);
		$pdo_statement->execute();
		$handle_list = $pdo_statement->fetchAll();

		return $handle_list;
	}
}