<?php
class Debtor_Model extends Base_Model{
	
	public $Identifier, $DebtorCode;
	public $CompanyName, $CompanyNumber, $TaxNumber, $LegalForm;
	public $Sex, $Initials, $SurName, $Address, $Address2, $ZipCode, $City, $State, $Country;
	public $EmailAddress, $PhoneNumber, $MobileNumber, $FaxNumber;
	public $Comment, $InvoiceMethod, $InvoiceAuthorisation;
	public $InvoiceCompanyName, $InvoiceSex, $InvoiceInitials, $InvoiceSurName, $InvoiceAddress, $InvoiceAddress2, $InvoiceZipCode, $InvoiceCity, $InvoiceState, $InvoiceCountry, $InvoiceEmailAddress;
	public $InvoiceTemplate, $PriceQuoteTemplate;
	public $AccountNumber, $AccountBIC, $AccountName, $AccountBank, $AccountCity;
	public $Taxable; 
	public $Username, $Password, $SecurePassword;
	
	public $CustomInvoiceAddress;
	
	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();
		
		// Default values
		$this->Sex					= 'm';
		$this->Country				= 'NL';
		$this->InvoiceMethod 		= 0;
		$this->InvoiceAuthorisation = 'no';
		$this->InvoiceTemplate 		= 0;
		$this->PriceQuoteTemplate 	= 0;
		
		$this->CustomInvoiceAddress	= 'no';
		
		// Load session data in model
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Debtor_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Debtor_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Debtor_Model'] as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}
	}
	
	/**
	 * Debtor_Model::set()
	 * Save value in session and model
	 * 
	 * @param mixed $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{		
		$_SESSION['OrderForm'.ORDERFORM_ID]['Debtor_Model'][$index] = trim($value);
		$this->{$index} = trim($value);
	}
	
	/**
	 * Debtor_Model::get()
	 * Get htmlspecialchars-save value
	 * 
	 * @param mixed $index
	 * @return string
	 */
	public function get($index)
	{
		return htmlspecialchars($this->{$index});
	}
	
	public function checkLogin()
	{
		// Do we have a username and password
		if(strlen($this->Username) === 0 || (isset($this->PostPassword) && $this->PostPassword === TRUE && strlen($this->Password) === 0))
		{
			$this->Error[] = __('invalid login credentials');
			return false;
		}
  
		// Prepare query
        if(isset($this->PostPassword) && $this->PostPassword === TRUE)
        {
            $pdo_statement = $this->_db->prepare("SELECT `id`, `Password`, `SecurePassword`, `OneTimePasswordValidTill` FROM `HostFact_Debtors` WHERE `Username`=:username AND `Status`!='9' AND `ActiveLogin`='yes' LIMIT 1");
    		$pdo_statement->bindValue(':username', $this->Username);
    		$pdo_statement->execute();
    		$result = $pdo_statement->fetch();            
            
            if($result && $result->SecurePassword && wf_password_verify($this->Password, $result->SecurePassword))
            {
                $this->set('SecurePassword', $result->SecurePassword);    
            }
            elseif($result && $result->Password && $result->Password == passcrypt($this->Password) && $result->OneTimePasswordValidTill > date('Y-m-d H:i:s'))
            {
                
            }
            else
            {
                $result = FALSE;
            }
        }
        else
        {
            if($this->SecurePassword != '')
            {
                $pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_Debtors` WHERE `Username`=:username AND `SecurePassword`=:securepassword AND `Status`!='9' AND `ActiveLogin`='yes' LIMIT 1");
        		$pdo_statement->bindValue(':username', $this->Username);
                $pdo_statement->bindValue(':securepassword', $this->SecurePassword);
                $pdo_statement->execute();
  		        $result = $pdo_statement->fetch();
            }
            elseif($this->Password != '')
            {
                $pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_Debtors` WHERE `Username`=:username AND `Password`=:password AND `OneTimePasswordValidTill` > NOW() AND `Status`!='9' AND `ActiveLogin`='yes' LIMIT 1");
        		$pdo_statement->bindValue(':username', $this->Username);
                $pdo_statement->bindValue(':password', passcrypt($this->Password));
                $pdo_statement->execute();
  		        $result = $pdo_statement->fetch();                    
            }
            else
            {
                $result = FALSE; 
            }
        }
		
		if(isset($result->id) && $result->id > 0)
		{
			// Set identifier
			$this->Identifier = $result->id;
			return true;
		}
		
		// Debtor not found
		$this->Error[] = __('invalid login credentials');
		return false;
	}
	
	public function show()
	{
		if(!$this->Identifier)
		{
			$this->Error[] = __('invalid debtor id');
			return false;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Debtors` WHERE `id`=:debtor_id AND `Status`!='9' AND `ActiveLogin`='yes' LIMIT 1");
		$pdo_statement->bindValue(':debtor_id', $this->Identifier);
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();	

		foreach($result as $key => $value)
		{
			if($key != 'Password')
			{
				$this->{$key} = $value;
			}
		}
		
		$array_states = $this->_settings->get('array_states');
		$this->StateName 			= (isset($array_states[$this->Country][$this->State])) ? $array_states[$this->Country][$this->State] : $this->State;
		$this->InvoiceStateName 	= (isset($array_states[$this->InvoiceCountry][$this->InvoiceState])) ? $array_states[$this->InvoiceCountry][$this->InvoiceState] : $this->InvoiceState;
		
		
		// Build the same array as the show-array from Customer_Model
		$info = array();
		
		$info['DebtorCode'] 		= htmlspecialchars($this->DebtorCode);
		
		// Company
		$info['CompanyName'] 		= htmlspecialchars($this->CompanyName);
		$info['CompanyNumber'] 		= htmlspecialchars($this->CompanyNumber);
		$info['TaxNumber'] 			= htmlspecialchars($this->TaxNumber);
		$info['LegalForm'] 			= htmlspecialchars($this->LegalForm);
		
		// Contact person
		$info['Sex'] 				= htmlspecialchars($this->Sex);
		$info['Initials'] 			= htmlspecialchars($this->Initials);
		$info['SurName'] 			= htmlspecialchars($this->SurName);
		$info['Address'] 			= htmlspecialchars($this->Address);
		$info['Address2'] 			= htmlspecialchars($this->Address2);
		$info['ZipCode'] 			= htmlspecialchars($this->ZipCode);
		$info['City'] 				= htmlspecialchars($this->City);
		$info['State'] 				= htmlspecialchars($this->State);
		$info['StateName'] 			= htmlspecialchars($this->StateName);
		$info['Country'] 			= htmlspecialchars($this->Country);
		
		// Contact data
		$info['EmailAddress'] 		= htmlspecialchars($this->EmailAddress);
		$info['PhoneNumber'] 		= htmlspecialchars($this->PhoneNumber);
		$info['MobileNumber'] 		= htmlspecialchars($this->MobileNumber);
		$info['FaxNumber'] 			= htmlspecialchars($this->FaxNumber);
		
		// Other properties
		$info['Comment'] 				= htmlspecialchars($this->Comment);
		$info['InvoiceMethod'] 			= htmlspecialchars($this->InvoiceMethod);
		$info['InvoiceAuthorisation'] 	= htmlspecialchars($this->InvoiceAuthorisation);
		$info['InvoiceTemplate'] 		= htmlspecialchars($this->InvoiceTemplate);
		$info['PriceQuoteTemplate'] 	= htmlspecialchars($this->PriceQuoteTemplate);
		
		// Custom invoice data
		if($this->InvoiceCompanyName || $this->InvoiceInitials || $this->InvoiceSurName || $this->InvoiceAddress || $this->InvoiceAddress2 || $this->InvoiceZipCode || $this->InvoiceCity || ($this->InvoiceCountry && $this->InvoiceCountry != $this->Country) || $this->InvoiceEmailAddress)
		{
			$info['CustomInvoiceAddress'] 	= 'yes';
			$info['InvoiceCompanyName'] 	= htmlspecialchars($this->InvoiceCompanyName);
            $info['InvoiceSex'] 		    = htmlspecialchars($this->InvoiceSex);
			$info['InvoiceInitials'] 		= htmlspecialchars($this->InvoiceInitials);
			$info['InvoiceSurName'] 		= htmlspecialchars($this->InvoiceSurName);
			$info['InvoiceAddress'] 		= htmlspecialchars($this->InvoiceAddress);
			$info['InvoiceAddress2'] 		= htmlspecialchars($this->InvoiceAddress2);
			$info['InvoiceZipCode'] 		= htmlspecialchars($this->InvoiceZipCode);
			$info['InvoiceCity'] 			= htmlspecialchars($this->InvoiceCity);
			$info['InvoiceState'] 			= htmlspecialchars($this->InvoiceState);
			$info['InvoiceStateName'] 		= htmlspecialchars($this->InvoiceStateName);
			$info['InvoiceCountry'] 		= htmlspecialchars($this->InvoiceCountry);
			$info['InvoiceEmailAddress'] 	= htmlspecialchars($this->InvoiceEmailAddress);
		}
		else
		{
			$info['CustomInvoiceAddress'] 	= 'no';
			$info['InvoiceCompanyName'] 	= '';
            $info['InvoiceSex']      		= $this->Sex;
			$info['InvoiceInitials'] 		= '';
			$info['InvoiceSurName'] 		= '';
			$info['InvoiceAddress'] 		= '';
			$info['InvoiceAddress2'] 		= '';
			$info['InvoiceZipCode'] 		= '';
			$info['InvoiceCity'] 			= '';
			$info['InvoiceState'] 			= '';
			$info['InvoiceStateName']		= '';
			$info['InvoiceCountry'] 		= $this->Country;
			$info['InvoiceEmailAddress'] 	= '';
		}
		
		// Account data
		$info['AccountNumber'] 		= htmlspecialchars($this->AccountNumber);
		$info['AccountBIC'] 		= htmlspecialchars($this->AccountBIC);
		$info['AccountName'] 		= htmlspecialchars($this->AccountName);
		$info['AccountBank'] 		= htmlspecialchars($this->AccountBank);
		$info['AccountCity'] 		= htmlspecialchars($this->AccountCity);
		
		// login credentials
		$info['Username'] 			= htmlspecialchars($this->Username);

		return $info;
	}
	
}