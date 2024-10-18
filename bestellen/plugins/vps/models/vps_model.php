<?php
class VPS_Model extends OrderElement_Model{
	
	public $id, $Debtor, $Status;
	public $Hostname, $Password, $Node, $Package, $Image;
	public $CustomFields;
	//TODO: add customfields...
	function __construct()
	{
		
		// Load parent constructor
		parent::__construct();
		
		// Default variables
		$this->Status = 'inorder';
		
		
	}
	
	function getProductsFromGroup($group_id)
	{
		$products = array();
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT p.`id`, p.`PriceExcl`, p.`PricePeriod`, p.`ProductCode`, p.`ProductName`, p.`ProductKeyPhrase`, p.`TaxPercentage`, (p.`PriceExcl` * (1+p.`TaxPercentage`)) as `PriceIncl`  
											 FROM `HostFact_Products` p, `HostFact_GroupRelations` g 
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`Status`='1' AND p.`ProductType`='vps'
											 ORDER BY `ProductCode` ASC");
		$pdo_statement->bindValue(':group_id', $group_id);

		// Execute statement
		$pdo_statement->execute();	
		$result = $pdo_statement->fetchAll();

		foreach($result as $tmp_product){
			$products[$tmp_product->id] = $tmp_product;
		}
		
		return $products;
	}
	
	function getCartItem($index, $escaped = true)
	{

		// Get cart item with help of parent function
		$item = parent::getCartItem($index, false);

		// Extend with some extra values		
		$item['Description'] 	= (!is_null($this->getAttribute('Description'))) ? $this->getAttribute('Description') : $item['Description'] . ' - ' . $this->getAttribute('Hostname');

		// Should we escape output?
		if($escaped)
		{
			$item = escapeArray($item);
		}
				
		return $item;
		
	}
	
	function getPackageInfo($productcode)
	{
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT t.*  
											 FROM (`HostFact_Products` p, `HostFact_VPS_Packages` t)
											 WHERE p.`ProductCode`=:productcode AND p.`Status`='1' AND p.`ProductType`='vps' AND p.`PackageID`=t.`id` AND t.`Status`='active'
											 LIMIT 1");
		$pdo_statement->bindValue(':productcode', $productcode);
		$pdo_statement->execute();	
		$package = $pdo_statement->fetch();

		if($package)
		{
			$package->PeriodPrices = $this->listCustomProductPrices($productcode, 'productcode');
			
			// Get images
			$pdo_statement = $this->_db->prepare("SELECT `Key`, `ImageName` FROM `HostFact_VPS_Images` WHERE `Node`=:node_id AND `Status`='active'");
			$pdo_statement->bindValue(':node_id', $package->Node);
			$pdo_statement->execute();	
			$package->ImageList = $pdo_statement->fetchAll();
			
			return $package;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function validate()
	{
		if(strlen(trim($this->Hostname)) === 0)
        {
            $this->Error[] = __('please enter a hostname', 'vps');
        }        

		return empty($this->Error) ? true : false;
	}
	
	function addToDatabase($cart_item)
	{
		$order = new Order_Model();

		if(!$package_info = $this->getPackageInfo($cart_item['ProductCode']))
		{
			// The parent will add the order element to the database
			return parent::addToDatabase($cart_item);	
		}
				
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
		
		
		// Prepare adding vps into database
		$this->Package 		= $package_info->id;
		$this->Node 		= $package_info->Node;
		$this->Hostname		= (isset($cart_item['Hostname'])) ? $cart_item['Hostname'] : '';
		$this->Password 	= (isset($cart_item['Password'])) ? $cart_item['Password'] : generatePassword();
		$this->Status		= 'inorder';
		$this->Image		= (isset($cart_item['Image'])) ? $cart_item['Image'] : '';

//TODO: customfields


		// First check values
		if(!$this->validate())
		{
			return FALSE;
		}

		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_VPS_Services` (`Debtor`, `Package`, `Node`, `Hostname`, `Password`, `Status`, `Image`) VALUES (:debtor_id, :package_id, :node_id, :hostname, :password, :status, :image)");
		
		$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
		$pdo_statement->bindValue(':package_id', 			$this->Package);
		$pdo_statement->bindValue(':node_id', 				$this->Node);
		$pdo_statement->bindValue(':hostname', 				$this->Hostname);
		$pdo_statement->bindValue(':password', 				passcrypt($this->Password));
		$pdo_statement->bindValue(':status', 				$this->Status);
		$pdo_statement->bindValue(':image', 				$this->Image);
	
		// Execute statement
		$result = $pdo_statement->execute();	

		if($result)
		{
			// Store VPS ID in case we need to delete them again
			$vps_id = $this->_db->lastInsertId();
			$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedVPS'] = $vps_id;
			
			$cart_item['ProductType'] = 'vps';
			$cart_item['Reference'] = $vps_id;
						
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
		// Delete vps
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedVPS']))
		{
			$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_VPS_Services` WHERE `id`=:vps_id AND `Status`='inorder'");
			$pdo_statement->bindValue(':vps_id', $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$cart_item['Index']]['CreatedVPS']);
			$pdo_statement->execute();
		}
		
		return parent::removeFromDatabase($cart_item);	
	}	
}