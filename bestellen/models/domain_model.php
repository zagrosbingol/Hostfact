<?php
class Domain_Model extends OrderElement_Model{
	
	function __construct()
	{
		
		// Load parent constructor
		parent::__construct();
		
		// Set minimum period for domains
		$this->has_mimimum_period = true;
		
		$this->_tlds = array();
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT p.`ProductTLD` as `TLD`, p.`PriceExcl`, p.`PricePeriod`, p.`ProductCode`, t.`AskForAuthKey`, t.`Registrar`, p.`TaxPercentage`, (p.`PriceExcl` * (1+p.`TaxPercentage`)) as `PriceIncl`  
											 FROM (`HostFact_Products` p, `HostFact_GroupRelations` g) 
											 LEFT JOIN `HostFact_TopLevelDomain` t ON (t.`Tld`=p.`ProductTLD`)
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`ProductType`='domain' AND p.`ProductTLD`<>'' AND p.`Status`='1'
											 ORDER BY `ProductTLD` ASC");
		$pdo_statement->bindValue(':group_id', GROUP_DOMAIN);


		// Execute statement
		$pdo_statement->execute();	
		$tlds = $pdo_statement->fetchAll();
		
		// Split popular
		foreach($tlds as $tmp_tld){
			$tmp_tld->TLD = strtolower($tmp_tld->TLD);
			
			$this->_tlds[$tmp_tld->TLD] = $tmp_tld;
		}
	}
	
	function getCartItem($index, $escaped = true)
	{

		// Get cart item with help of parent function
		$item = parent::getCartItem($index, false);

		// Extend with some extra values
		$item['Description'] = (!is_null($this->getAttribute('Description'))) ? $this->getAttribute('Description') : $this->getDomainDescription();
		$item['Domain']		 = $this->Domain;
		$item['SLD']		 = $this->SLD;
		$item['TLD']		 = $this->TLD;
		
		$item['AuthKey']	 = $this->AuthKey;	
		$item['AskForAuthKey'] = (isset($this->_tlds[$this->TLD]->AskForAuthKey) && $this->_tlds[$this->TLD]->AskForAuthKey == 'yes') ? true : false;
		
		$item['Availability'] = (isset($_SESSION['whois_results'][$this->Domain]) && $_SESSION['whois_results'][$this->Domain]) ? $_SESSION['whois_results'][$this->Domain] : '';		
		
		// Should we escape output?
		if($escaped)
		{
			$item = escapeArray($item);
		}
				
		return $item;
		
	}
	
	function getDefaultValues()
	{
		parent::getDefaultValues();
		
		$this->AuthKey = '';
	}
	
	function getDomainDescription()
	{
		if(strpos($this->Description, ' .' . $this->TLD) !== false)
		{
			$lastIndex = strrpos($this->Description, ' .' . $this->TLD);	
			$description = substr($this->Description, 0,$lastIndex) . str_replace(' .' . $this->TLD, ' '.$this->SLD . '.' . $this->TLD,substr($this->Description, $lastIndex));
		}
		else
		{
			$lastIndex = strrpos($this->Description, '-');	
			
			if($lastIndex !== false){ 
				$description = substr($this->Description, 0,$lastIndex) . '- ' . $this->SLD . '.' . $this->TLD;
			}else{
				$description = $this->Description . ' - ' . $this->SLD . '.' . $this->TLD;
			}
		} 
		
		return $description;
	}
	
	function saveItem()
	{
		// Check if an index is given
		if(is_null($this->Index))
		{
			return false;
		}
		
		// Check if domain is valid and store SLD and TLD
		if($this->parseDomain($this->getAttribute('Domain')))
		{
			// Do whois check if not already done...
			if(!$this->getAttribute('Availability') && (!isset($_SESSION['whois_results'][$this->getAttribute('Domain')]) || !$_SESSION['whois_results'][$this->getAttribute('Domain')]))
			{		
				// Check domain
				$whois = new Whois_Model();	
				$result = $whois->checkDomain($this->SLD, $this->TLD);
				
				// Store result
				$_SESSION['whois_results'][$this->Domain] = $result;
			}	
			
			// Push attributes into list		
			$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$this->Index] = $this->_attributes;
			$this->_elementlist[$this->Index] = $this->_attributes;
		}

		return true;
	}
	
	function parseDomain($domain){
		
		// Escape domain
		$domain = strtolower(stripslashes(htmlspecialchars(trim($domain))));
		
		// Strip www., http:// and https://
        $domain = preg_replace('/^(http(s)?:\/\/)?(www\.)?/i', '', $domain);
		
		// Explode domain on dot-notation
		$exploded_domain = explode('.',$domain, 2);

		// Check SLD
		$this->setAttribute('SLD', $exploded_domain[0]);
			// Check if SLD is not empty
			if(!$this->SLD){
				$this->Error[] = __('no domain entered');
				return false;
			}
			
			// Check if SLD is between 2 and 63 characters
			if(strlen($this->SLD) < 2 || strlen($this->SLD) > 63){
				$this->Error[] = __('sld must be between 2 and 63 characters');
				return false;
			}
		
			// Check if SLD do not contain dots
			if(strpos($this->SLD,'.') !== false){
				$this->Error[] = __('sld should not contain dots');
				return false;
			}
		
		// Check TLD
		if(isset($exploded_domain[1])){
			// Get all tlds and check if tld exists
			$all_tlds = $this->getTopLevelDomains('all');
			
			$tmp_all_tlds = array();
			foreach($all_tlds as $tmp_tld){
				$tmp_all_tlds[] = $tmp_tld->TLD;
			}
			
			// If not in available domains, give error
			if(!in_array($exploded_domain[1], $tmp_all_tlds)){
				$this->Error[] = __('tld not available');
				return false;
			}
			
			// Preg-match for valid signs
			$idn = $this->getAllowedIDNCharacters($exploded_domain[1]);
			if(preg_match("/^[a-z".$idn."0-9-]+(\.[a-z".$idn."0-9-]+)*$/i", $this->SLD) == 0){
				$this->Error[] = __('sld contains invalid characters');
				return false;
			}
			
			$this->setAttribute('TLD', $exploded_domain[1]);
		}
		else
		{
			// We do need a TLD
			$this->Error[] = __('tld not available');
			return false;
		}
		
		// Domain correctly parsed
		return true;
	}
	
	function getAllowedIDNCharacters($tld = '')
	{
		if($tld)
		{
			// Get specific characters for this tld
			$pdo_statement = $this->_db->prepare("SELECT `AllowedIDNCharacters` FROM `HostFact_TopLevelDomain` WHERE `Tld`=:tld");
			$pdo_statement->bindValue(':tld', $tld);
		}
		else
		{
			// Get IDN characters from all tlds
			$pdo_statement = $this->_db->prepare("SELECT `AllowedIDNCharacters` FROM `HostFact_TopLevelDomain`");
		}
										 
		$pdo_statement->execute();	
		$idn_per_tld = $pdo_statement->fetchAll();
				
		$idn = '';
		foreach($idn_per_tld as $idn_tld)
		{
			$idn .= $idn_tld->AllowedIDNCharacters;
		}

		return $idn;
	}
	
	private $_tlds;

	function getTopLevelDomains(){
		
		$topleveldomains = array();
		
		foreach($this->_tlds as $k=>$tld)
		{
			$topleveldomains[] = $tld;		
		}
		return $topleveldomains;
		
	}
	
	function getProductDetails($productcode){
		
		foreach($this->_tlds as $k=>$tld)
		{
			if($tld->TLD == $this->TLD)
			{
				$this->ProductCode = $tld->ProductCode;
				parent::getProductDetails($this->ProductCode);
				
				return true;
			}		
		}
		
		return false;
	}
	
	function addToDatabase($cart_item){

		$order = new Order_Model();
		
		// Get registrar info
		$pdo_statement = $this->_db->prepare("SELECT reg.`id`, reg.`DNS1`, reg.`DNS2`, reg.`DNS3`, reg.`AdminCustomer`, reg.`AdminHandle`, reg.`TechCustomer`, reg.`TechHandle` FROM `HostFact_Registrar` as reg, `HostFact_TopLevelDomain` as tld WHERE tld.`Tld`=:tld AND tld.`Registrar`=reg.`id` LIMIT 1");
		$pdo_statement->bindValue(':tld', $cart_item['TLD']);
		$pdo_statement->execute();	
		$registrar_info = $pdo_statement->fetch();
		
		// Get debtor
		if($order->get('ExistingCustomer') == 'yes')
		{
			$debtor = new Debtor_Model();
			if($debtor->checkLogin())
			{
				$customer_data = $debtor->show();
				$this->Debtor = $debtor->Identifier;
			}
			else
			{
				// Copy error
				$this->Error = $debtor->Error;
				return false;
			}
		}
		else
		{
			$customer = new Customer_Model();
			$customer_data = $customer->show();
			$this->Debtor = -1;
		}
		
		
		// Prepare adding domain into database
		$this->SLD			= $cart_item['SLD'];
		$this->TLD			= $cart_item['TLD'];
		$this->Registrar 	= (isset($registrar_info->id)) ? $registrar_info->id : 0;
		$this->Status		= -1;
		$this->AuthKey 		= (isset($cart_item['AuthKey'])) ? $cart_item['AuthKey'] : '';
		$this->RegType 		= (isset($cart_item['Availability']) && $cart_item['Availability'] == 'available') ? 'register' : ((isset($cart_item['Availability']) && $cart_item['Availability'] == 'unavailable') ? 'transfer' : '');

		
		// Determine nameservers
		if(isset($order->CustomNameServers) && $order->CustomNameServers == 'yes')
		{
			// Custom nameservers from order
			$this->DNS1 = $order->NS1;
			$this->DNS2 = (isset($order->NS2)) ? $order->NS2 : '';
			$this->DNS3 = (isset($order->NS2)) ? $order->NS3 : '';
		}
		elseif(isset($debtor->DNS1) && $debtor->DNS1)
		{
			// Custom nameservers for debtor
			$this->DNS1 = $debtor->DNS1;
			$this->DNS2 = (isset($debtor->DNS2)) ? $debtor->DNS2 : '';
			$this->DNS3 = (isset($debtor->DNS3)) ? $debtor->DNS3 : '';
		}
		else
		{
			// Because we don't know which server, we will use registrar DNS. In backoffice we will look for possible server-DNS
			$this->DNS1 = (isset($registrar_info->DNS1)) ? $registrar_info->DNS1 : "";
			$this->DNS2 = (isset($registrar_info->DNS2)) ? $registrar_info->DNS2 : "";
			$this->DNS3 = (isset($registrar_info->DNS3)) ? $registrar_info->DNS3 : "";
		}
		
		// Determine handles
		$handle_types = array('owner','admin','tech');
		foreach($handle_types as $handle_type)
		{
		
			if(isset($cart_item[$handle_type.'Handle']) && $cart_item[$handle_type.'Handle'] > 0)
			{
				// If a handle identifier is already given
				$this->{$handle_type.'Handle'} = $cart_item[$handle_type.'Handle'];
			}
			elseif(isset($registrar_info->{ucfirst($handle_type).'Customer'}) && $registrar_info->{ucfirst($handle_type).'Customer'} == 'no' && $registrar_info->{ucfirst($handle_type).'Handle'} > 0)
			{
				// Use default contacts from registrar
				$this->{$handle_type.'Handle'} = $registrar_info->{ucfirst($handle_type).'Handle'};
			}
			else
			{
				// Use debtor data
				$handle = new Handle_Model();
				$do_lookup = true;
				
				if(isset($order->CustomWHOIS) && $order->CustomWHOIS == 'yes')
				{
					// From session data, so already in $handle
					if(isset($handle->id) && $handle->id > 0)
					{
						// We use an existing handle, so we need to check if it has the same registrar
						$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Handles` WHERE `id`=:handle_id AND `Debtor`=:debtor_id LIMIT 1");
						$pdo_statement->bindValue(':handle_id', $handle->id);
						$pdo_statement->bindValue(':debtor_id', $this->Debtor);
						$pdo_statement->execute();
						$handle_info = $pdo_statement->fetch();

						// If handle has same registrar, we can use it
						if($handle_info->Registrar && $handle_info->Registrar == $this->Registrar)
						{
							$this->{$handle_type.'Handle'} = $handle->id;
							$do_lookup = false;
						}
						else
						{
							// Copy info to $handle object
							foreach($handle_info as $_key => $_value)
							{
								$handle->{$_key} = $_value;
							}

							// Also copy custom fields
							CustomClientFields_Model::syncCustomFields('handle', $handle->id);
						}
					}

				}
				else
				{
					// Use data from debtor or new customer
					$handle->Sex 			= (isset($customer_data['Sex'])) 			? htmlspecialchars_decode($customer_data['Sex']) : 'm';
					$handle->Initials 		= (isset($customer_data['Initials'])) 		? htmlspecialchars_decode($customer_data['Initials']) : '';
					$handle->SurName 		= (isset($customer_data['SurName'])) 		? htmlspecialchars_decode($customer_data['SurName']) : '';
					$handle->Address 		= (isset($customer_data['Address'])) 		? htmlspecialchars_decode($customer_data['Address']) : '';
					$handle->Address2 		= (isset($customer_data['Address2'])) 		? htmlspecialchars_decode($customer_data['Address2']) : '';
					$handle->ZipCode 		= (isset($customer_data['ZipCode'])) 		? htmlspecialchars_decode($customer_data['ZipCode']) : '';
					$handle->City 			= (isset($customer_data['City'])) 			? htmlspecialchars_decode($customer_data['City']) : '';
					$handle->State 			= (isset($customer_data['State'])) 			? htmlspecialchars_decode($customer_data['State']) : '';
					$handle->Country 		= (isset($customer_data['Country'])) 		? htmlspecialchars_decode($customer_data['Country']) : '';
					$handle->PhoneNumber 	= (isset($customer_data['PhoneNumber']) && $customer_data['PhoneNumber']) ? htmlspecialchars_decode($customer_data['PhoneNumber']) : ((isset($customer_data['MobileNumber']) && $customer_data['MobileNumber']) ? htmlspecialchars_decode($customer_data['MobileNumber']) : '');
					$handle->FaxNumber 		= (isset($customer_data['FaxNumber'])) 		? htmlspecialchars_decode($customer_data['FaxNumber']) : '';
					$handle->EmailAddress 	= (isset($customer_data['EmailAddress'])) 	? getFirstMailAddress(htmlspecialchars_decode($customer_data['EmailAddress'])) : '';
					
					$handle->CompanyName 	= (isset($customer_data['CompanyName'])) 	? htmlspecialchars_decode($customer_data['CompanyName']) : '';
					$handle->LegalForm 		= (isset($customer_data['LegalForm'])) 		? htmlspecialchars_decode($customer_data['LegalForm']) : '';
					$handle->CompanyNumber 	= (isset($customer_data['CompanyNumber'])) 	? htmlspecialchars_decode($customer_data['CompanyNumber']) : '';
					$handle->TaxNumber 		= (isset($customer_data['TaxNumber'])) 		? htmlspecialchars_decode($customer_data['TaxNumber']) : '';
				}

				if($do_lookup)
				{
					$handle->Debtor          = $this->Debtor;
					$handle->Registrar       = $this->Registrar;
					$handle->RegistrarHandle = '';
					$handle->HandleType      = '';
					$handle->Status          = 1;

					$lookup = $handle->lookupHandle($handle->Registrar);
					if($lookup === FALSE)
					{
						$handle->Handle = $handle->nextInternalHandle('debtor', $this->Debtor);

						// Sync custom fields (if necessary)
						CustomClientFields_Model::syncCustomFields(($order->get('ExistingCustomer') == 'yes') ? 'debtor' : 'customer', $this->Debtor);

						if($handle->add())
						{
							$this->{$handle_type . 'Handle'} = $handle->Identifier;

							// Store handle ID's in case we need to delete them again
							$_SESSION['OrderForm' . ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHandles'][] = $handle->Identifier;
						}
						else
						{
							$this->{$handle_type . 'Handle'} = 0;
						}
					}
					else
					{
						$this->{$handle_type . 'Handle'} = $lookup;
						unset($lookup);
					}
				}
			}
		}
		
		// If domain should be connected to existing hosting account, lookup hosting ID
		if($order->get('HostingType') == 'existing' && (!isset($cart_item['HostingID']) || !$cart_item['HostingID']) && $order->get('Domain') && $order->get('ExistingCustomer') == 'yes' && $this->Debtor > 0)
		{
			$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_Hosting` WHERE `Domain`=:domain AND `Debtor`=:debtor_id AND `Status` != 9");
			$pdo_statement->bindValue(':domain', 	$order->get('Domain'));
			$pdo_statement->bindValue(':debtor_id', $this->Debtor);
			if($pdo_statement->execute())
			{
				$result = $pdo_statement->fetch();
				if($result && $result->id > 0)
				{
					$cart_item['HostingID'] = $result->id;
				}
			}
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_Domains` (`Debtor`, `Product`, `Domain`, `Tld`, `Registrar`, `Status`, `DNS1`, `DNS2`, `DNS3`, `HostingID`, `ownerHandle`, `adminHandle`, `techHandle`, `Authkey`, `Type`) VALUES (:debtor_id, :product_id, :domain, :tld, :registrar_id, :status, :dns1, :dns2, :dns3, :hosting_id, :ownerhandle, :adminhandle, :techhandle, :authkey, :type)");
		
		$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
		$pdo_statement->bindValue(':product_id', 			$cart_item['ProductID']);
		$pdo_statement->bindValue(':domain', 				$this->SLD);
		$pdo_statement->bindValue(':tld', 					$this->TLD);
		$pdo_statement->bindValue(':registrar_id', 			$this->Registrar);
		$pdo_statement->bindValue(':status', 				$this->Status);
		$pdo_statement->bindValue(':dns1', 					$this->DNS1);
		$pdo_statement->bindValue(':dns2', 					$this->DNS2);
		$pdo_statement->bindValue(':dns3', 					$this->DNS3);
		$pdo_statement->bindValue(':hosting_id', 			(isset($cart_item['HostingID'])) ? $cart_item['HostingID'] : 0); 
		$pdo_statement->bindValue(':ownerhandle', 			$this->ownerHandle);
		$pdo_statement->bindValue(':adminhandle', 			$this->adminHandle);
		$pdo_statement->bindValue(':techhandle', 			$this->techHandle);
		$pdo_statement->bindValue(':authkey', 				$this->AuthKey);
		$pdo_statement->bindValue(':type', 					$this->RegType);
		
		// Execute statement
		$result = $pdo_statement->execute();	

		if($result)
		{
			// Store domain ID in case we need to delete them again
			$domain_id = $this->_db->lastInsertId();
			$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedDomain'] = $domain_id;
			
			$cart_item['ProductType'] = 'domain';
			$cart_item['Reference'] = $domain_id;
			
			if(isset($cart_item['ExtraFields']) && is_array($cart_item['ExtraFields']))
			{
				$available_fields = array();
				
				$pdo_statement = $this->_db->prepare("SELECT `id`, `RegistrarField` FROM `HostFact_Domain_Extra_Fields` WHERE `Registrar`=:registrar_id AND (`Tld`=:tld OR `Tld`='all')");
				$pdo_statement->bindValue(':registrar_id', 	$this->Registrar);
				$pdo_statement->bindValue(':tld', 			$this->TLD);
				$pdo_statement->execute();
				$result = $pdo_statement->fetchAll();
				
				
				foreach($result as $tmp_result){
					$available_fields[$tmp_result->RegistrarField] = $tmp_result->id;
				}

				foreach($cart_item['ExtraFields'] as $key=>$value){
					if(isset($available_fields[$key]) && $available_fields[$key] > 0)
					{
						$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_Domain_Extra_Values` (`Value`, `DomainID`, `FieldID`) VALUES (:value,:domain_id,:field_id)");
						$pdo_statement->bindValue(':value', 	$value);
						$pdo_statement->bindValue(':domain_id', $domain_id);
						$pdo_statement->bindValue(':field_id', 	$available_fields[$key]);
						
						$pdo_statement->execute();
					}
				}
				
			}
			
			// The parent will add the order element to the database
			return parent::addToDatabase($cart_item);	
		}
		else
		{
			// Fix created stuff
			$this->removeFromDatabase($cart_item);
			return false;
		}		
	}
	
	function removeFromDatabase($cart_item)
	{
		// Delete handles?
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHandles']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHandles']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHandles'] as $tmp_handle_id)
			{
				$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_Handles` WHERE `id`=:handle_id");
				$pdo_statement->bindValue(':handle_id', $tmp_handle_id);
				$pdo_statement->execute();
			}
		}
		
		// Delete domain
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedDomain']))
		{
			$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_Domains` WHERE `id`=:domain_id AND `Status`=-1");
			$pdo_statement->bindValue(':domain_id', $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedDomain']);
			$pdo_statement->execute();
		}
		
		// Delete extra fields (if any)
		$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_Domain_Extra_Values` WHERE `DomainID`=:domain_id");
		$pdo_statement->bindValue(':domain_id', $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedDomain']);
		$pdo_statement->execute();
		
		return parent::removeFromDatabase($cart_item);	
	}
	
}
