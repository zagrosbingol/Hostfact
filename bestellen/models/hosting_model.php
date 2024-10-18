<?php
class Hosting_Model extends OrderElement_Model{
	
	public $_packages;
	
	function __construct()
	{
		// Load parent constructor
		parent::__construct();		
		
		$this->_packages = array();
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT p.`id`, p.`PriceExcl`, p.`PricePeriod`, p.`ProductCode`, p.`ProductName`, p.`ProductKeyPhrase`, p.`TaxPercentage`, (p.`PriceExcl` * (1+p.`TaxPercentage`)) as `PriceIncl` 
											 FROM `HostFact_Products` p, `HostFact_GroupRelations` g 
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`Status`='1'
											 ORDER BY `ProductCode` ASC");
		$pdo_statement->bindValue(':group_id', GROUP_HOSTING);


		// Execute statement
		$pdo_statement->execute();	
		$packages = $pdo_statement->fetchAll();

		foreach($packages as $tmp_package){
			$this->_packages[$tmp_package->id] = $tmp_package;
		}

	}
	
	function getHostingProducts(){
		return $this->_packages;
	}
	
	function addToDatabase($cart_item){
		
		$order = new Order_Model();
		
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
		$this->Status		= -1;
		$this->ProductID 	= $cart_item['ProductID'];
		$this->Username 	= (isset($cart_item['Username'])) ? strtolower($cart_item['Username']) : $this->generateNewAccountname($this->_settings->get('ACCOUNT_GENERATION'), $customer_data['CompanyName'], $customer_data['SurName'], $customer_data['Initials'], (isset($cart_item['Domain'])) ? $cart_item['Domain'] : '');
		$this->Password 	= (isset($cart_item['Password'])) ? $cart_item['Password'] : $this->generateHostingPassword();
		$this->Domain 		= (isset($cart_item['Domain'])) ? $cart_item['Domain'] : '';
		
		// Determine package
		if($this->Debtor > 0 && $debtor->Server > 0){
			// If debtor has a custom server, try to find a package on this server
			$pdo_statement = $this->_db->prepare("SELECT package.`id`, package.`Server` FROM `HostFact_Packages` as package, `HostFact_Products` as prod WHERE package.`Server`=:server_id AND package.`Product`=prod.`id` AND prod.`ProductCode`=:product_code LIMIT 1");
			$pdo_statement->bindValue(':server_id', $debtor->Server);
			$pdo_statement->bindValue(':product_code', $cart_item['ProductCode']);
			$pdo_statement->execute();	
			$package_info = $pdo_statement->fetch(); 
			
		}		
		// If no custom package is found, use default
		if(empty($package_info->id)){
			$pdo_statement = $this->_db->prepare("SELECT package.`id`, package.`Server` FROM `HostFact_Packages` as package, `HostFact_Products` as prod WHERE prod.`PackageID`=package.`id` AND prod.`ProductCode`=:product_code LIMIT 1");
			$pdo_statement->bindValue(':product_code', $cart_item['ProductCode']);
			$pdo_statement->execute();	
			$package_info = $pdo_statement->fetch(); 
		}
		
		$this->Package = (isset($package_info->id) && $package_info->id > 0) ? $package_info->id : 0;

		// Determine server
		if(isset($cart_item['Server']) && $cart_item['Server'] > 0)
		{
			// If server is given from orderform
			$this->Server = $cart_item['Server'];
		}
		elseif(isset($debtor->Server) && $debtor->Server > 0)
		{
			// Customer has his own server
			$this->Server = $debtor->Server;
		}
		elseif(isset($package_info->Server))
		{
			// Use server from package
			$this->Server = $package_info->Server;
		}
		else
		{
			$this->Server = 0;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_Hosting` (`Debtor`, `Product`, `Package`, `Username`, `Password`, `Domain`, `Status`, `Server`) VALUES (:debtor_id, :product_id, :package_id, :username, :password, :domain, :status, :server_id)");
		
		$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
		$pdo_statement->bindValue(':product_id', 			$this->ProductID);
		$pdo_statement->bindValue(':package_id', 			$this->Package);
		$pdo_statement->bindValue(':username', 				$this->Username);
		$pdo_statement->bindValue(':password', 				passcrypt($this->Password));
		$pdo_statement->bindValue(':domain', 				$this->Domain);
		$pdo_statement->bindValue(':status', 				$this->Status);
		$pdo_statement->bindValue(':server_id', 			$this->Server);
		
		// Execute statement
		$result = $pdo_statement->execute();	

		if($result)
		{
			// Store hosting ID in case we need to delete them again
			$hosting_id = $this->_db->lastInsertId();
			$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHosting'] = $hosting_id;
						
			// Set producttype	
			$cart_item['ProductType'] = 'hosting';
			$cart_item['Reference'] = $hosting_id;
			
			
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
		// Delete hosting
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHosting']))
		{
			$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_Hosting` WHERE `id`=:hosting_id AND `Status`=-1");
			$pdo_statement->bindValue(':hosting_id', $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedHosting']);
			$pdo_statement->execute();
		}
		
		return parent::removeFromDatabase($cart_item);	
	}
	
	
	/**
     * Hosting_Model::generateNewAccountname()
     * 
     * Generate new accountname
     * @param int $method Method for accountname. 1: autonummering, 2: name, 3: domain
     * @param string $companyname String for company name
     * @param string $surname String for surname
     * @param string $initials String for initials
     * @param string $domain String for domainname
     * @return string|bool New accountname, or false if no accountname is generated
     */   
    function generateNewAccountname($method = 1, $companyname = '', $surname = '', $initials='', $domain=''){
        $maxlength = '8';
        $maxtries = 50;

        //1. autonummering
        //2. bedrijfsnaam/achternaam + evt cijfer
        //3. domeinnaam + evt cijfer
        switch($method){
            default:
                //1. autonummering
                $prefix = $this->_settings->get('ACCOUNTCODE_PREFIX');
				$number = $this->_settings->get('ACCOUNTCODE_NUMBER');
				
				// Replace variables from prefix ([yyyy], [yy] and [mm])
				$prefix = parsePrefixVariables($prefix);
				
				// Determine total length of accountnumber
				$length = strlen($prefix.$number);
				
				// Then get last accountnumber from database
				$pdo_statement = $this->_db->prepare("SELECT `Username` FROM `HostFact_Hosting` WHERE `Username` LIKE :prefix AND LENGTH(`Username`)>=:length AND (SUBSTR(`Username`,:prefix_offset)*1) > 0 AND SUBSTR(`Username`,:prefix_offset) REGEXP '^[0-9]+$' ORDER BY (SUBSTR(`Username`,:prefix_offset)*1) DESC LIMIT 1");
				$pdo_statement->bindValue(':prefix', $prefix.'%%');
				$pdo_statement->bindValue(':length', $length);
				$pdo_statement->bindValue(':prefix_offset', strlen($prefix)+1);
				
				// Execute statement
				$pdo_statement->execute();	
				$result = $pdo_statement->fetch();		
				        
				// Calculate the new Username
				if(isset($result->Username) && $result->Username && is_numeric(substr($result->Username,strlen($prefix))))
				{
					$code = substr($result->Username,strlen($prefix));
					$code = $prefix . @str_repeat('0', max(strlen($number) - strlen(max($code + 1,$number)),0)) . (max($code + 1,$number));
				}
				else
				{
					$code = $prefix . $number;
				}

                $cur_try = 0;
                while($cur_try < $maxtries){
    
                    if($this->is_free($code)){
                        $account = $code;
                        break;
                    }
                    
                    $code = substr($code,strlen($prefix));
					$code = $prefix.@str_repeat('0', max(strlen($number) - strlen(max($code + 1,$number)),0)).(max($code + 1,$number));
                    $account = $code;
    
                    $cur_try++;                    
                }
                break;
            case '2':
                //2. bedrijfsnaam/achternaam + evt cijfer
                if($companyname){
                    // Remove forbidden tokens
					$companyname = preg_replace("/[^a-z0-9]/i", "", htmlspecialchars_decode($companyname));
					// Always start with a-z
					if(preg_match('/([a-z][a-z0-9]*)/i',$companyname, $matches))
					{
						$companyname = $matches[1];
					}
                    
                    $cur_length = $maxlength;
                    $cur_try = 0;
                    $account = null;
                    while($cur_try < $maxtries){
                        $check_name = substr($companyname,0,$cur_length);
                        if($cur_try > 0)
                            $check_name .= $cur_try;
                        
                        if($this->is_free($check_name)){
                            $account = $check_name;
                            break;
                        }

                        if($cur_length >= $maxlength)
                            $cur_length--;
                        
                        $cur_try++;                    
                    }
                    
                    if($account == null){
                        $this->Error[] = __('could not generate new accountname based on company name');
                        return false;
                    }
                    $account = strtolower($account);
                }elseif($surname){
                    // Remove forbidden tokens
                    $name = preg_replace("/[^A-Za-z0-9]/i", "", htmlspecialchars_decode($surname.$initials));
                    
                    $cur_length = $maxlength;
                    $cur_try = 0;
                    $account = null;
                    while($cur_try < $maxtries){
                        $check_name = substr($name,0,$cur_length);
                        if($cur_try > 0)
                            $check_name .= $cur_try;
                        
                        if($this->is_free($check_name)){
                            $account = $check_name;
                            break;
                        }

                        if($cur_length >= $maxlength)
                            $cur_length--;
                        
                        $cur_try++;                    
                    }
                    
                    if($account == null){
                        $this->Error[] = __('could not generate new accountname based on debtor name');
                        return false;
                    }
                    $account = strtolower($account);
                }else{
                    $this->Error[] = __('could not generate new accountname based on debtor');
                    return false;
                }
                break;
            case '3':
                //3. domeinnaam + evt cijfer
                if($domain){
                    // Remove forbidden tokens
                    $domain = preg_replace("/[^a-z0-9]/i", "", $domain);

					// Always start with a-z
					if(preg_match('/([a-z][a-z0-9]*)/i',$domain, $matches))
					{
						$domain = $matches[1];
					}
                    
                    $cur_length = $maxlength;
                    $cur_try = 0;
                    $account = null;
                    while($cur_try < $maxtries){
                        $check_name = substr($domain,0,$cur_length);
                        if($cur_try > 0)
                            $check_name .= $cur_try;
                        
                        if($this->is_free($check_name)){
                            $account = $check_name;
                            break;
                        }
                        
                        if($cur_length >= $maxlength)
                            $cur_length--;
                        
                        $cur_try++;                    
                    }
                  
                    if($account == null){
                        $this->Error[] = __('could not generate new accountname based on domain');
                        return false;
                    }
                    $account = strtolower($account);
                }else{
                    return $this->generateNewAccountname('1', $companyname, $surname, $initials, $domain);
                }
                break;
        }

        if(!$this->is_free($account))
        {
            $this->Error[] = __('could not generate new accountname');
		}
		return empty($this->Error) ? $account : false;
        
    }
    
    function is_free($username, $id = null)
    {
        if(!trim($username))
		{
			// Empty username, so we don't need to check
			return false;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_Hosting` WHERE `Username`=:username AND `Status`!='9'  LIMIT 1");
		$pdo_statement->bindValue(':username', $username);
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();	
		
		if(isset($result->id) && $result->id > 0)
		{
			// Username is already in use
			return false;
		}
		
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_NewOrderElements` WHERE `Description` LIKE :username LIMIT 1");
		$pdo_statement->bindValue(':username', '%[h:'.$username.']%');
		
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
    
    function generateHostingPassword($length = false)
    {
        return generatePassword($length);
    }

}
