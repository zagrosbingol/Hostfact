<?php
class SSL_Model extends OrderElement_Model{
	
	public $id, $Debtor, $SSLTypeID, $Registrar, $Status;
	public $CommonName, $Type, $Wildcard, $MultiDomain, $MultiDomainRecords, $ApproverEmail, $CSR;
	public $Period, $ownerHandle, $adminHandle, $techHandle;
	
	function __construct()
	{
		
		// Load parent constructor
		parent::__construct();
		
		// Default variables
		$this->Wildcard = 'no';
		$this->MultiDomain = 'no';
		$this->MultiDomainRecords = array();
		$this->Period = 1;
		
		
	}
	
	function getSSLTypeInfo($productcode)
	{
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT t.*  
											 FROM (`HostFact_Products` p, `HostFact_SSL_Types` t) 
											 WHERE p.`ProductCode`=:productcode AND p.`Status`='1' AND p.`ProductType`='ssl' AND p.`PackageID`=t.`id` AND t.`Status`='active'
											 LIMIT 1");
		$pdo_statement->bindValue(':productcode', $productcode);
		$pdo_statement->execute();	
		$ssltype = $pdo_statement->fetch();
		
		
		$ssltype->PeriodPrices = $this->listCustomProductPrices($productcode, 'productcode');
	
		
		return $ssltype;
	}
	
	function getProductsFromGroup($group_id)
	{
		$tmp_products = array();
		
		// Prepare query		
		$pdo_statement = $this->_db->prepare("SELECT p.`id`, p.`PriceExcl`, p.`PricePeriod`, p.`ProductCode`, p.`ProductName`, p.`ProductKeyPhrase`, p.`TaxPercentage`, (p.`PriceExcl` * (1+p.`TaxPercentage`)) as `PriceIncl`, t.`Type`, t.`Name`, t.`Brand`, t.`Wildcard`, t.`MultiDomain`  
											 FROM (`HostFact_Products` p, `HostFact_GroupRelations` g, `HostFact_SSL_Types` t)
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`Status`='1'
											 AND p.`ProductType`='ssl' AND p.`PackageID`=t.`id` AND t.`Status`='active'
											 ORDER BY p.`ProductName` ASC");
		$pdo_statement->bindValue(':group_id', $group_id);

		// Execute statement
		$pdo_statement->execute();	
		$result = $pdo_statement->fetchAll();

		foreach($result as $tmp_product){
			$tmp_products[$tmp_product->id] = $tmp_product;
		}
		
		// Sort it
		global $orderform_settings;
		$ssl_sorted_list = (isset($orderform_settings->OtherSettings->ssl->SortedList)) ? explode("|",$orderform_settings->OtherSettings->ssl->SortedList) : array();
		$products = array();
		foreach($ssl_sorted_list as $_product_id)
		{
			if(isset($tmp_products[$_product_id]))
			{
				$products[$_product_id] = $tmp_products[$_product_id];
				unset($tmp_products[$_product_id]);
			}
		}
		$products = array_merge($products, $tmp_products);
	
		
		return $products;
	}
	
	function getCartItem($index, $escaped = true)
	{

		// Get cart item with help of parent function
		$item = parent::getCartItem($index, false);

		// Extend with some extra values		
		$item['Description'] 	= (!is_null($this->getAttribute('Description'))) ? $this->getAttribute('Description') : $item['Description'] . ' - ' . $this->getAttribute('CommonName');

		// Should we escape output?
		if($escaped)
		{
			$item = escapeArray($item);
		}
				
		return $item;
		
	}
	
	function addToDatabase($cart_item){

		$order = new Order_Model();

		if(!$ssltype_info = $this->getSSLTypeInfo($cart_item['ProductCode']))
		{
			// The parent will add the order element to the database
			return parent::addToDatabase($cart_item);	
		}
	
		$this->SSLTypeID = $ssltype_info->id;
		
		// Get registrar info
		$pdo_statement = $this->_db->prepare("SELECT reg.`id`, reg.`AdminCustomer`, reg.`AdminHandle`, reg.`TechCustomer`, reg.`TechHandle` FROM `HostFact_Registrar` as reg, `HostFact_SSL_Types` as types WHERE types.`id`=:type_id AND types.`Registrar`=reg.`id` LIMIT 1");
		$pdo_statement->bindValue(':type_id', $this->SSLTypeID);
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
		$this->Registrar 	= (isset($registrar_info->id)) ? $registrar_info->id : 0;
		$this->Status		= 'inorder';
		$this->CommonName	= (isset($cart_item['CommonName'])) ? $cart_item['CommonName'] : '';
	
		// Certificate type properties
		$this->Type			= $ssltype_info->Type;
		$this->Wildcard		= $ssltype_info->Wildcard;
		$this->MultiDomain	= $ssltype_info->MultiDomain;
		$this->MultiDomainRecords = (isset($cart_item['MultiDomainRecords'])) ? $cart_item['MultiDomainRecords'] : '';
		
		$this->ApproverEmail = (isset($cart_item['ApproverEmail'])) ? $cart_item['ApproverEmail'] : '';
		$this->CSR			 = (isset($cart_item['CSR'])) ? $cart_item['CSR'] : '';

		$available_periods = ($ssltype_info->Periods) ? explode(',', $ssltype_info->Periods) : array('1');
		$selected_period = (isset($cart_item['Period']) && in_array($cart_item['Period'], $available_periods)) ? $cart_item['Period'] : current($available_periods);
		$this->Period		 = $selected_period;

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
				
				if(isset($order->CustomWHOIS) && $order->CustomWHOIS == 'yes')
				{
					// From session data, so already in $handle
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
				
				$handle->Debtor 			= $this->Debtor;
				$handle->Registrar 			= $this->Registrar;
				$handle->RegistrarHandle 	= '';
				$handle->HandleType 		= '';
				$handle->Status 			= 1;

				$lookup = $handle->lookupHandle($handle->Registrar);
			    if($lookup === false)
				{
			        $handle->Handle 		= $handle->nextInternalHandle('debtor',$this->Debtor);
			        
					if($handle->add())
					{
			            $this->{$handle_type.'Handle'} = $handle->Identifier;
			            
						// Store handle ID's in case we need to delete them again
						$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHandles'][] = $handle->Identifier;
			        }
					else
					{
			        	$this->{$handle_type.'Handle'} = 0;
			        }
			    }else{
			        $this->{$handle_type.'Handle'} = $lookup; unset($lookup);
			    }
			}
		}
		

		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_SSL_Certificates` (`Debtor`, `SSLTypeID`, `Registrar`, `Status`, `CommonName`, `Type`, `Wildcard`, `MultiDomain`, `MultiDomainRecords`, `ApproverEmail`, `CSR`, `Period`, `ownerHandle`, `adminHandle`, `techHandle`) VALUES (:debtor_id, :ssltype_id, :registrar_id, :status, :commonname, :type, :wildcard, :multidomain, :multidomainrecords, :approveremail, :csr, :period, :ownerhandle, :adminhandle, :techhandle)");
		
		$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
		$pdo_statement->bindValue(':ssltype_id', 			$this->SSLTypeID);
		$pdo_statement->bindValue(':registrar_id', 			$this->Registrar);
		$pdo_statement->bindValue(':status', 				$this->Status);
		$pdo_statement->bindValue(':commonname', 			$this->CommonName);
		$pdo_statement->bindValue(':type', 					$this->Type);
		$pdo_statement->bindValue(':wildcard', 				$this->Wildcard);
		$pdo_statement->bindValue(':multidomain', 			$this->MultiDomain);
		$pdo_statement->bindValue(':multidomainrecords', 	json_encode($this->MultiDomainRecords));
		$pdo_statement->bindValue(':approveremail', 		$this->ApproverEmail);
		$pdo_statement->bindValue(':csr', 					$this->CSR);
		$pdo_statement->bindValue(':period', 				$this->Period);
		$pdo_statement->bindValue(':ownerhandle', 			$this->ownerHandle);
		$pdo_statement->bindValue(':adminhandle', 			$this->adminHandle);
		$pdo_statement->bindValue(':techhandle', 			$this->techHandle);
		
		// Execute statement
		$result = $pdo_statement->execute();	

		if($result)
		{
			// Store SSL ID in case we need to delete them again
			$ssl_id = $this->_db->lastInsertId();
			$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedSSL'] = $ssl_id;
			
			$cart_item['ProductType'] = 'ssl';
			$cart_item['Reference'] = $ssl_id;
						
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
		
		// Delete ssl
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedSSL']))
		{
			$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_SSL_Certificates` WHERE `id`=:ssl_id AND `Status`='inorder'");
			$pdo_statement->bindValue(':ssl_id', $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedSSL']);
			$pdo_statement->execute();
		}
		
		return parent::removeFromDatabase($cart_item);	
	}	
}