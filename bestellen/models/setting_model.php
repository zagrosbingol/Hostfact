<?php
class Setting_Model{
	
	public $_db;
	private $_settings = array();
	
	public static $instance;

    const GENDER_SHOW_IF_IN_ARRAY = ['m','f'];
	
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
		
		// Get global, whitelisted settings (some must be defined, others not)
	    $define_settings_whitelist = array('IS_INTERNATIONAL', 'CLIENTAREA_LOGO_URL', 'LANGUAGE','DEFAULT_ORDERFORM','ORDERFORM_ENABLED','CLIENTAREA_URL', 'DATE_FORMAT', 'BACKOFFICE_URL','CURRENCY_CODE','CURRENCY_SIGN_LEFT', 'CURRENCY_SIGN_RIGHT', 'AMOUNT_DEC_PLACES', 'AMOUNT_DEC_SEPERATOR', 'AMOUNT_THOU_SEPERATOR','SDD_ID','VAT_CALC_METHOD','PASSWORD_GENERATION', 'DKIM_DOMAINS');
		$settings_whitelist = array_merge($define_settings_whitelist , array('COMPANY_NAME', 'COMPANY_EMAIL', 'ORDERCODE_PREFIX','ORDERCODE_NUMBER','INVOICE_TERM','LICENSE','DEBTORCODE_PREFIX','ACCOUNT_GENERATION','ACCOUNTCODE_PREFIX','ACCOUNTCODE_NUMBER','SMTP_ON','SMTP_HOST','SMTP_AUTH','SMTP_USERNAME','SMTP_PASSWORD','COMPANY_AV_PDF','ORDERMAIL_SENT','ORDERMAIL_SENT_BCC','ORDERFORM_CSS_COLOR','DOMAIN_AUTH_KEY_REQUIRED','ORDERFORM_TO_PAYMENTDIR','STANDARD_INVOICEMETHOD', 'CLIENTAREA_DEFAULT_LANG'));

	    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings` WHERE `Variable` IN ('".implode("','",$settings_whitelist)."')");

		// Execute statement
		$pdo_statement->execute();
		$settings = $pdo_statement->fetchAll();

		// Cache settings
		foreach($settings as $tmp_setting)
		{
			$this->_settings[$tmp_setting->Variable] = $tmp_setting->Value;
		}

		// Define some variables, which we use in multiple files
		foreach($define_settings_whitelist as $tmp_setting_name)
		{
			if(isset($this->_settings[$tmp_setting_name]))
			{
				define($tmp_setting_name, $this->_settings[$tmp_setting_name]);
			}
		}
	}
	
	function get($index)
	{	
		// If setting is known, return
		if(isset($this->_settings[$index]))
		{
			return $this->_settings[$index];
		}
		
		//Otherwise, check what to get
		switch($index){
			case 'company_data':
				// Get country of company
				$pdo_statement = $this->_db->prepare("SELECT `CompanyName`, `EmailAddress` FROM `HostFact_Company` LIMIT 1");
				$pdo_statement->execute();
				$result = $pdo_statement->fetch();

				$this->_settings['company_data'] = $result;
			break;

			case 'array_legaltype':
				// Get legalforms
				$this->_settings['array_legaltype'] = array();
			    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_LegalForms` ORDER BY `OrderID` ASC, `Title` ASC");
		
				// Execute statement
				$pdo_statement->execute();	
				$legalforms = $pdo_statement->fetchAll();
				
				// Cache settings
				foreach($legalforms as $tmp_legalform)
				{
					$this->_settings['array_legaltype'][$tmp_legalform->LegalForm] = $tmp_legalform->Title;
				}
				break;
			case 'array_paymentmethods':
				// Get payment methods
				$this->_settings['array_paymentmethods'] = array();
			    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_PaymentMethods` WHERE `Availability` = 1 OR `Availability` = 3 ORDER BY `Ordering` ASC");
			    
			    // Execute statement
				$pdo_statement->execute();	
				$paymentmethods = $pdo_statement->fetchAll();
				
			    $payment_method = array();
			    foreach($paymentmethods as $key => $method){
					if(isset($payment_method[$method->PaymentType])){
						$method->PaymentType = $method->PaymentType.$method->id;	
					}
					$payment_method[$method->PaymentType]['Enabled']		= true;
					$payment_method[$method->PaymentType]['PriceExcl'] 		= ($method->FeeType == 'EUR') ? $method->FeeAmount : "";
					$payment_method[$method->PaymentType]['PriceIncl'] 		= ($method->FeeType == 'EUR') ? $method->FeeAmount * (1+$this->get('STANDARD_TAX')) : "";
					$payment_method[$method->PaymentType]['Percentage'] 	= ($method->FeeType == 'PROC') ? $method->FeeAmount : "";
					$payment_method[$method->PaymentType]['Title'] 			= $method->Title;
					$payment_method[$method->PaymentType]['Description'] 	= $method->Description;
					$payment_method[$method->PaymentType]['FeeDesc'] 		= $method->FeeDesc;
					$payment_method[$method->PaymentType]['Image'] 			= $method->Image;
					$payment_method[$method->PaymentType]['Directory'] 		= $method->Directory;
					$payment_method[$method->PaymentType]['Class'] 			= strtolower(str_replace('.','_',$method->Directory));
					
					if(is_numeric(substr($payment_method[$method->PaymentType]['Class'],0,1))){
						$payment_method[$method->PaymentType]['Class']	= str_replace(array(1,2,3,4,5,6,7,8,9,0),array('one_','two_','three_','four_','five_','six_','seven_','eight_','nine_','zero_'),substr($payment_method[$method->PaymentType]['Class'],0,1)) . substr($payment_method[$method->PaymentType]['Class'],1);	
					}
					
					$payment_method[$method->PaymentType]['Extra'] 			= $method->Extra;
					$payment_method[$method->PaymentType]['PaymentType'] 	= $method->PaymentType;
				}	
				
				$this->_settings['array_paymentmethods'] = $payment_method;
				
				// Help settings
				define("IDEAL_EMAIL", $this->get('IDEAL_EMAIL'));
				define("ORDERFORM_TO_PAYMENTDIR", $this->get('ORDERFORM_TO_PAYMENTDIR'));
				break;
			case 'array_sex':
				$this->_settings['array_sex']['m'] = __('gender male'); 
				$this->_settings['array_sex']['f'] = __('gender female');
                $this->_settings['array_sex']['d'] = __('gender department');
                $this->_settings['array_sex']['u'] = __('gender unknown');
				break;
			case 'array_country':
                $lang_code = (LANG == 'nl_NL') ? 'nl_NL' : 'en_EN';
				$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_Countries` WHERE `Visible`='yes' ORDER BY `OrderID` ASC, `".$lang_code."` ASC");

			    // Execute statement
				$pdo_statement->execute();	
				$countries = $pdo_statement->fetchAll();
                	
			    $array_country = array();
			    foreach($countries as $tmp_country){
			    	$array_country[$tmp_country->CountryCode] = $tmp_country->{$lang_code};	
		    	}
				
				$this->_settings['array_country'] = $array_country;
				break;
			case 'array_states':
				$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_States` ORDER BY `State` ASC");
			    
			    // Execute statement
				$pdo_statement->execute();	
				$states = $pdo_statement->fetchAll();
				
			    $array_states = array();
			    foreach($states as $tmp_state){
			    	$array_states[$tmp_state->CountryCode][$tmp_state->StateCode] = $tmp_state->State;	
		    	}
				
				$this->_settings['array_states'] = $array_states;
				break;
			case 'company_country':
				// Get country of company
			    $pdo_statement = $this->_db->prepare("SELECT `Country` FROM `HostFact_Company`");
				$pdo_statement->execute();	
				$result = $pdo_statement->fetch();	
				
				$this->_settings['company_country'] = $result->Country;
				break;
			case 'STANDARD_TAX':
			case 'array_taxpercentages':
			case 'array_taxpercentages_info':	
				// Get taxrates
				$this->_settings['array_taxpercentages'] = $this->_settings['array_taxpercentages_info'] = array();
			    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_Taxrates` WHERE `TaxType`='line' ORDER BY `Rate` DESC");
		
				// Execute statement
				$pdo_statement->execute();	
				$taxrates = $pdo_statement->fetchAll();
	
				// Cache settings
				foreach($taxrates as $tmp_taxrate)
				{
					$this->_settings['array_taxpercentages'][''.(float)$tmp_taxrate->Rate] = 100 * (float)$tmp_taxrate->Rate;
					
					$this->_settings['array_taxpercentages_info'][''.(float)$tmp_taxrate->Rate] = array('label' => htmlspecialchars($tmp_taxrate->Label));
					
					if($tmp_taxrate->Default == 'yes')
					{
						$this->_settings['STANDARD_TAX'] = (float)$tmp_taxrate->Rate;
					}
				}
				break;
			case 'STANDARD_TOTAL_TAX':
			case 'array_total_taxpercentages':
			case 'array_total_taxpercentages_info':	
				// Get taxrates
				$this->_settings['array_total_taxpercentages'] = $this->_settings['array_total_taxpercentages_info'] = array();
			    $pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_Taxrates` WHERE `TaxType`='total' ORDER BY `Rate` DESC");
		
				// Execute statement
				$pdo_statement->execute();	
				$taxrates = $pdo_statement->fetchAll();
	
				// Cache settings
				foreach($taxrates as $tmp_taxrate)
				{
					$this->_settings['array_total_taxpercentages'][''.(float)$tmp_taxrate->Rate] = 100 * (float)$tmp_taxrate->Rate;
					
					$this->_settings['array_total_taxpercentages_info'][''.(float)$tmp_taxrate->Rate] = array('label' => htmlspecialchars($tmp_taxrate->Label), 'compound' => $tmp_taxrate->Compound);
					
					if($tmp_taxrate->Default == 'yes')
					{
						$this->_settings['STANDARD_TOTAL_TAX'] = (float)$tmp_taxrate->Rate;
					}
				}
				break;
			// Else
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
	
	function loadOrderForm($orderform_id){
		
		$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_OrderForms` WHERE `id` = :orderform_id AND `Available`='yes' LIMIT 1");
  		$pdo_statement->bindValue(':orderform_id', $orderform_id);
			    
	    // Execute statement
		$pdo_statement->execute();	
		$result = $pdo_statement->fetch();
		
		if($result === false)
		{
			// Form does not exists or is inactive
			return false;
		}
		else
		{
			// Parse settings
			$result->ProductGroups 			= json_decode($result->ProductGroups);
			$result->OtherSettings 			= json_decode($result->OtherSettings);
			$result->PeriodChoiceOptions 	= json_decode($result->PeriodChoiceOptions);
			
			return $result;
		}		
	}

    /**
     * Only show the gender name if it's a male or female.
     *
     * @param string $gender - Value for the gender.
     * @param bool $always - If true, always show the gender name.
     *
     * @return string - Display name.
     */
    public static function getGenderTranslation(string $gender, bool $always = false): string
    {

        if ($always === true || in_array($gender, self::GENDER_SHOW_IF_IN_ARRAY)) {
            $array_sex = self::getInstance()->get('array_sex');
            return $array_sex[$gender];
        }

        return '';
    }
}