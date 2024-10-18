<?php
class Customer_Model extends Base_Model{
	
	public $CompanyName, $CompanyNumber, $TaxNumber, $LegalForm;
	public $Sex, $Initials, $SurName, $Address, $Address2, $ZipCode, $City, $State, $Country;
	public $EmailAddress, $PhoneNumber, $MobileNumber, $FaxNumber;
	public $Comment, $InvoiceMethod, $InvoiceAuthorisation;
	public $InvoiceCompanyName, $InvoiceSex, $InvoiceInitials, $InvoiceSurName, $InvoiceAddress, $InvoiceAddress2, $InvoiceZipCode, $InvoiceCity, $InvoiceState, $InvoiceCountry, $InvoiceEmailAddress;
	public $InvoiceTemplate, $PriceQuoteTemplate;
	public $AccountNumber, $AccountBIC, $AccountName, $AccountBank, $AccountCity;
	public $DefaultLanguage;
	public $Username, $Password;
	
	public $CustomInvoiceAddress;
	
	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();
		
		// Default values
		$this->Sex					= 'm';
		$this->Country				= $this->_settings->get('company_country');
		$this->InvoiceMethod 		= $this->_settings->get('STANDARD_INVOICEMETHOD');
		$this->InvoiceAuthorisation = 'no';
		$this->InvoiceTemplate 		= 0;
		$this->PriceQuoteTemplate 	= 0;
		
		$this->CustomInvoiceAddress	= 'no';
		$this->DefaultLanguage		= '';
		
		$this->Password				= generatePassword();
		
		// Load session data in model
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Customer_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Customer_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Customer_Model'] as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}
		
		$array_states = $this->_settings->get('array_states');
		$this->StateName 			= (isset($array_states[$this->Country][$this->State])) ? $array_states[$this->Country][$this->State] : $this->State;
		$this->InvoiceStateName 	= (isset($array_states[$this->InvoiceCountry][$this->InvoiceState])) ? $array_states[$this->InvoiceCountry][$this->InvoiceState] : $this->InvoiceState;
	}
	
	/**
	 * Customer_Model::set()
	 * Save value in session and model
	 * 
	 * @param mixed $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{
		$_SESSION['OrderForm'.ORDERFORM_ID]['Customer_Model'][$index] = trim($value);
		$this->{$index} = trim($value);
	}
	
	/**
	 * Customer_Model::get()
	 * Get htmlspecialchars-save value
	 * 
	 * @param mixed $index
	 * @return string
	 */
	public function get($index)
	{
		return htmlspecialchars($this->{$index});
	}
	
	/**
	 * Customer_Model::show()
	 * Get array with customer data for save displaying
	 * 
	 * @return array 
	 */
	public function show()
	{
		$info = array();
		
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
		$info['CustomInvoiceAddress'] 	= htmlspecialchars($this->CustomInvoiceAddress);
		$info['InvoiceCompanyName'] 	= htmlspecialchars($this->InvoiceCompanyName);
        $info['InvoiceSex']      		= htmlspecialchars($this->InvoiceSex);
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
		
		// Account data
		$info['AccountNumber'] 		= htmlspecialchars($this->AccountNumber);
		$info['AccountBIC'] 		= htmlspecialchars($this->AccountBIC);
		$info['AccountName'] 		= htmlspecialchars($this->AccountName);
		$info['AccountBank'] 		= htmlspecialchars($this->AccountBank);
		$info['AccountCity'] 		= htmlspecialchars($this->AccountCity);
		
		// Future login credentials
		$info['Username'] 			= htmlspecialchars($this->Username);
		$info['Password'] 			= htmlspecialchars($this->Password);
		
		// Return customer info
		return $info;
	}
	
	function add()
	{
		// First validate customer data
		if(!$this->validate())
		{
			return false;
		}

		// Should we set a default language to the newcustomer?
		if($this->_settings->get('CLIENTAREA_DEFAULT_LANG') != LANG)
		{
			$this->DefaultLanguage = LANG;
		}

		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_NewCustomers` (`Username`, `Password`, `CompanyName`, `CompanyNumber`, `TaxNumber`, `LegalForm`, `Sex`, `Initials`, `SurName`, `Address`, `Address2`, `ZipCode`, `City`, `State`, `Country`, `EmailAddress`, `PhoneNumber`, `MobileNumber`, `FaxNumber`, `Comment`, `InvoiceMethod`, `InvoiceAuthorisation`, `InvoiceTemplate`, `PriceQuoteTemplate`, `InvoiceCompanyName`, `InvoiceSex`, `InvoiceInitials`, `InvoiceSurName`, `InvoiceAddress`, `InvoiceAddress2`, `InvoiceZipCode`, `InvoiceCity`, `InvoiceState`, `InvoiceCountry`, `InvoiceEmailAddress`, `AccountNumber`, `AccountBIC`, `AccountName`, `AccountBank`, `AccountCity`,`DefaultLanguage`,`Created`, `Modified`) VALUES (:username, :password, :companyname, :companynumber, :taxnumber, :legalform, :sex, :initials, :surname, :address, :address2, :zipcode, :city, :state, :country, :emailaddress, :phonenumber, :mobilenumber, :faxnumber, :comment, :invoicemethod, :invoiceauthorisation, :invoicetemplate, :pricequotetemplate, :invoice_companyname, :invoice_sex, :invoice_initials, :invoice_surname, :invoice_address, :invoice_address2, :invoice_zipcode, :invoice_city, :invoice_state, :invoice_country, :invoice_emailaddress, :accountnumber, :accountbic, :accountname, :accountbank, :accountcity, :default_language, NOW(), NOW())");
		
		// Future login credentials
		$pdo_statement->bindValue(':username',		($this->Username) ? $this->Username : '[DebtorCode]');
		$pdo_statement->bindValue(':password',		passcrypt($this->Password));
		
		// Company
		$pdo_statement->bindValue(':companyname', 	$this->CompanyName);
		$pdo_statement->bindValue(':companynumber', $this->CompanyNumber);
		$pdo_statement->bindValue(':taxnumber', 	$this->TaxNumber);
		$pdo_statement->bindValue(':legalform', 	$this->LegalForm);
		
		// Contact person
		$pdo_statement->bindValue(':sex', 			$this->Sex);
		$pdo_statement->bindValue(':initials', 		$this->Initials);
		$pdo_statement->bindValue(':surname', 		$this->SurName);
		$pdo_statement->bindValue(':address', 		$this->Address);
		$pdo_statement->bindValue(':address2', 		$this->Address2);
		$pdo_statement->bindValue(':zipcode', 		$this->ZipCode);
		$pdo_statement->bindValue(':city', 			$this->City);
		$pdo_statement->bindValue(':state', 		$this->State);
		$pdo_statement->bindValue(':country', 		$this->Country);
		
		// Contact data
		$pdo_statement->bindValue(':emailaddress', 	check_email_address($this->EmailAddress, 'convert'));
		$pdo_statement->bindValue(':phonenumber', 	$this->PhoneNumber);
		$pdo_statement->bindValue(':mobilenumber', 	$this->MobileNumber);
		$pdo_statement->bindValue(':faxnumber', 	$this->FaxNumber);
		
		// Other properties
		$pdo_statement->bindValue(':comment', 				$this->Comment);
		$pdo_statement->bindValue(':invoicemethod', 		$this->InvoiceMethod);
		$pdo_statement->bindValue(':invoiceauthorisation', 	$this->InvoiceAuthorisation);
		$pdo_statement->bindValue(':invoicetemplate', 		$this->InvoiceTemplate);
		$pdo_statement->bindValue(':pricequotetemplate', 	$this->PriceQuoteTemplate);
		
		// Custom invoice data
		if($this->CustomInvoiceAddress == 'yes')
		{
			$pdo_statement->bindValue(':invoice_companyname', 	$this->InvoiceCompanyName);
            $pdo_statement->bindValue(':invoice_sex',    		$this->InvoiceSex);
			$pdo_statement->bindValue(':invoice_initials', 		$this->InvoiceInitials);
			$pdo_statement->bindValue(':invoice_surname', 		$this->InvoiceSurName);
			$pdo_statement->bindValue(':invoice_address', 		$this->InvoiceAddress);
			$pdo_statement->bindValue(':invoice_address2', 		$this->InvoiceAddress2);
			$pdo_statement->bindValue(':invoice_zipcode', 		$this->InvoiceZipCode);
			$pdo_statement->bindValue(':invoice_city', 			$this->InvoiceCity);
			$pdo_statement->bindValue(':invoice_state', 		$this->InvoiceState);
			$pdo_statement->bindValue(':invoice_country', 		$this->InvoiceCountry);
			$pdo_statement->bindValue(':invoice_emailaddress', 	check_email_address($this->InvoiceEmailAddress, 'convert'));
		}
		else
		{
			$pdo_statement->bindValue(':invoice_companyname', 	'');
            $pdo_statement->bindValue(':invoice_sex', 		    $this->Sex);
			$pdo_statement->bindValue(':invoice_initials', 		'');
			$pdo_statement->bindValue(':invoice_surname', 		'');
			$pdo_statement->bindValue(':invoice_address', 		'');
			$pdo_statement->bindValue(':invoice_address2', 		'');
			$pdo_statement->bindValue(':invoice_zipcode', 		'');
			$pdo_statement->bindValue(':invoice_city', 			'');
			$pdo_statement->bindValue(':invoice_state', 		'');
			$pdo_statement->bindValue(':invoice_country', 		$this->Country);
			$pdo_statement->bindValue(':invoice_emailaddress', 	'');
		}
		
		
		// Account data
		$pdo_statement->bindValue(':accountnumber', 	$this->AccountNumber);
		$pdo_statement->bindValue(':accountbic', 		$this->AccountBIC);
		$pdo_statement->bindValue(':accountname', 		$this->AccountName);
		$pdo_statement->bindValue(':accountbank', 		$this->AccountBank);
		$pdo_statement->bindValue(':accountcity', 		$this->AccountCity);

		// Set default language
		$pdo_statement->bindValue(':default_language',	$this->DefaultLanguage);

		// Execute statement
		$result = $pdo_statement->execute();		
									
		if($result)
		{
			$this->Identifier = $this->_db->lastInsertId();
			
			// Should we also add custom fields?
			CustomClientFields_Model::addCustomFields('customer', $this->Identifier);
			
			return true;			
		}
		else
		{
			return false;
		}		
	}
	
	/**
	 * Customer_Model::validate()
	 * Checks if customer data is valid
	 * 
	 * @return boolean
	 */
	function validate()
	{
		
		// Check if username is valid
		if(!is_null($this->Username) && (strlen($this->Username) > 100 || !is_string($this->Username)))
		{
			$this->Error[] = __('invalid username');
		}
		elseif(!is_null($this->Username) && strlen($this->Username) > 0 && !$this->is_free_username($this->Username))
		{
			$this->Error[] = __('the username already exists');
		}
		
		// If username is not empty, password should also not be empty
		if(!is_null($this->Username) && strlen($this->Username) > 0 && strlen($this->Password) === 0)
		{
			$this->Error[] = __('invalid password');
		}
		
		if(!$this->CompanyName && !$this->SurName)
		{
			$this->Error[] = __('no companyname and no surname');
		}
		
		// Company
		if(!is_null($this->CompanyName) && (strlen($this->CompanyName) > 100 || !is_string($this->CompanyName)))
		{
			$this->Error[] = __('invalid companyname');
		}
		
		if(!is_null($this->CompanyNumber) && (strlen($this->CompanyNumber) > 100 || !is_string($this->CompanyNumber)))
		{
			$this->Error[] = __('invalid companynumber');
		}
		
		if(!is_null($this->TaxNumber) && (strlen($this->TaxNumber) > 20 || !is_string($this->TaxNumber)))
		{
			$this->Error[] = __('invalid taxnumber');
		}
		elseif(function_exists("checkTaxNumber") && !is_null($this->TaxNumber) && strlen($this->TaxNumber) > 0  && !checkTaxNumber($this->TaxNumber))
		{
			$this->Error[] = __('invalid taxnumber');
		}
		
		// Contact person
		if(!in_array($this->Sex, array('m','f','d', 'u')))
		{
			$this->Error[] = __('invalid gender');
		}
		
		if(!is_null($this->Initials) && (strlen($this->Initials) > 25 || !is_string($this->Initials)))
		{
			$this->Error[] = __('invalid initials');
		}
		
		if(!is_null($this->SurName) && (strlen($this->SurName) > 100 || !is_string($this->SurName)))
		{
			$this->Error[] = __('invalid surname');
		}
		
		if(!$this->Address || strlen($this->Address) > 100 || !is_string($this->Address))
		{
			$this->Error[] = __('invalid address');
		}
		elseif(!is_null($this->Address2) && (strlen($this->Address2) > 100 || !is_string($this->Address2)))
		{
			$this->Error[] = __('invalid address');
		}
		
		if(!$this->ZipCode || strlen($this->ZipCode) > 10 || !is_string($this->ZipCode))
		{
			$this->Error[] = __('invalid zipcode');
		}
		
		if(!$this->City || strlen($this->City) > 100 || !is_string($this->City))
		{
			$this->Error[] = __('invalid city');
		}
		
		if(!is_null($this->State) && (strlen($this->State) > 100 || !is_string($this->State)))
		{
			$this->Error[] = __('invalid state');
		}
		
		if(strlen($this->Country) > 10 || !is_string($this->Country))
		{
			$this->Error[] = __('invalid country');
		}
		
		// Contact data
		if(!$this->EmailAddress || !check_email_address($this->EmailAddress) || (function_exists("checkEmailAddress") && !checkEmailAddress($this->EmailAddress)))
		{
			$this->Error[] = __('invalid emailaddress');
		}
        
        // if you want to make the phonenumber optional, the next check can be removed.
        if(!$this->PhoneNumber)
        {
            $this->Error[] = __('no phonenumber given');
        }
		
		if(!is_null($this->PhoneNumber) && (strlen($this->PhoneNumber) > 25 || !is_string($this->PhoneNumber)))
		{
			$this->Error[] = __('invalid phonenumber');
		}
		
		if(!is_null($this->MobileNumber) && (strlen($this->MobileNumber) > 25 || !is_string($this->MobileNumber)))
		{
			$this->Error[] = __('invalid mobile number');
		}
		
		if(!is_null($this->FaxNumber) && (strlen($this->FaxNumber) > 25 || !is_string($this->FaxNumber)))
		{
			$this->Error[] = __('invalid faxnumber');
		}
		
		// Other properties
		if(!in_array($this->InvoiceMethod, array(0,1,3)))
		{
			$this->Error[] = __('invalid invoicemethod');
		}
		
		if($this->InvoiceAuthorisation != 'yes' && $this->InvoiceAuthorisation != 'no')
		{
			$this->Error[] = __('invalid authorization value');
		}
		
		if(!is_numeric($this->InvoiceTemplate))
		{
			$this->Error[] = __('invalid custom invoice template');
		}
		
		if(!is_numeric($this->PriceQuoteTemplate))
		{
			$this->Error[] = __('invalid custom pricequote template');
		}

		// Custom invoice data
		if($this->CustomInvoiceAddress == 'yes')
		{
    		
            if($this->InvoiceSex && !in_array($this->InvoiceSex, array('m','f','d','u')))
    		{
    			$this->Error[] = __('invalid invoice sex');
    		}
    		elseif(!$this->InvoiceSex)
    		{
    			$this->InvoiceSex = 'm';	
    		}
			if(!is_null($this->InvoiceInitials) && (strlen($this->InvoiceInitials) > 25 || !is_string($this->InvoiceInitials)))
			{
				$this->Error[] = __('invalid invoice initials');
			}
			
			if(!is_null($this->InvoiceSurName) && (strlen($this->InvoiceSurName) > 100 || !is_string($this->InvoiceSurName)))
			{
				$this->Error[] = __('invalid invoice surname');
			}
			
			if(!is_null($this->InvoiceAddress) && (strlen($this->InvoiceAddress) > 100 || !is_string($this->InvoiceAddress)))
			{
				$this->Error[] = __('invalid invoice address');
			}
			elseif(!is_null($this->InvoiceAddress2) && (strlen($this->InvoiceAddress2) > 100 || !is_string($this->InvoiceAddress2)))
			{
				$this->Error[] = __('invalid invoice address');
			}
			
			if(!is_null($this->InvoiceZipCode) && (strlen($this->InvoiceZipCode) > 10 || !is_string($this->InvoiceZipCode)))
			{
				$this->Error[] = __('invalid invoice zipcode');
			}
			
			if(!is_null($this->InvoiceCity) && (strlen($this->InvoiceCity) > 100 || !is_string($this->InvoiceCity)))
			{
				$this->Error[] = __('invalid invoice city');
			}
			
			if(!is_null($this->InvoiceState) && (strlen($this->InvoiceState) > 100 || !is_string($this->InvoiceState)))
			{
				$this->Error[] = __('invalid invoice state');
			}
			
			if(strlen($this->InvoiceCountry) > 0 && (strlen($this->InvoiceCountry) > 10 || !is_string($this->InvoiceCountry)))
			{
				$this->Error[] = __('invalid invoice country');
			}
	
			if(!is_null($this->InvoiceEmailAddress) && strlen($this->InvoiceEmailAddress) > 0 && !check_email_address($this->InvoiceEmailAddress))
			{
				$this->Error[] = __('invalid invoice emailaddress');
			}
			elseif(!is_null($this->InvoiceEmailAddress) && strlen($this->InvoiceEmailAddress) > 0 && check_email_address($this->InvoiceEmailAddress) && function_exists("checkEmailAddress") && !checkEmailAddress($this->InvoiceEmailAddress))
			{
				$this->Error[] = __('invalid invoice emailaddress');
			}
		}
		
		// Account data
		if(!is_null($this->AccountNumber) && (strlen($this->AccountNumber) > 50 || !is_string($this->AccountNumber)))
		{
			$this->Error[] = __('invalid accountnumber');
		}
				
		if(!is_null($this->AccountBIC) && (strlen($this->AccountBIC) > 50 || !is_string($this->AccountBIC)))
		{
			$this->Error[] = __('invalid bic');
		}
		
		if(!is_null($this->AccountName) && (strlen($this->AccountName) > 100 || !is_string($this->AccountName)))
		{
			$this->Error[] = __('invalid accountname');
		}
		
		if(!is_null($this->AccountBank) && (strlen($this->AccountBank) > 100 || !is_string($this->AccountBank)))
		{
			$this->Error[] = __('invalid bank');
		}
		
		if(!is_null($this->AccountCity) && (strlen($this->AccountCity) > 100 || !is_string($this->AccountCity)))
		{
			$this->Error[] = __('invalid account city');
		}
		
		// Validate custom fields if any
		$this->Error = array_merge($this->Error, CustomClientFields_Model::validateCustomFields('customer'));
		
		// Are there any errors?
		return empty($this->Error) ? true : false;
		
	}
	
	/**
	 * Customer_Model::is_free_username()
	 * Checks if a username is still free to use
	 * 
	 * @param mixed $username
	 * @return boolean
	 */
	function is_free_username($username)
	{
		if(!trim($username))
		{
			// Empty username, so we don't need to check
			return true;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_Debtors` WHERE `Username`=:username LIMIT 1");
		$pdo_statement->bindValue(':username', $this->Username);
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();	
		
		if(isset($result->id) && $result->id > 0)
		{
			// Username is already in use
			return false;
		}
		
		// If username is not in use for debtors, check new customers for samen username
		if($username == '[DebtorCode]')
		{
			// We always accept the [DebtorCode] value
			return true;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_NewCustomers` WHERE `Username`=:username LIMIT 1");
		$pdo_statement->bindValue(':username', $this->Username);
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();	
		
		if(isset($result->id) && $result->id > 0)
		{
			// Username is already in use
			return false;
		}
		
		// Not in use
		return true;
	}
}