<?php
class OrderElement_Model extends Base_Model{

	public $Type, $Index;
	public $OrderCode, $Debtor, $Date, $Number, $ProductCode, $Description, $PriceExcl, $TaxPercentage, $DiscountPercentage, $DiscountPercentageType, $Periods, $Periodic;
	public $ProductType, $Reference;
	
	public $has_mimimum_period = false;
	public $_elementlist = array();
	public $_attributes = array();
	
	function __construct()
	{
		// Default values
		$this->Type = get_class($this);
		
		// Load parent constructor
		parent::__construct();
		
		// Load list of elements of this type
		$this->_elementlist = (isset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'])) ? $_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'] : array();
		
	}
	
	function newItem($index)
	{
		// Reset attributes
		$this->_attributes = array();
		
		// Set index
		$this->Index = $index;
		
		return true;
	}
	
	function saveItem()
	{
		// Check if an index is given
		if(is_null($this->Index))
		{
			return false;
		}
	
		// Push attributes into list		
		$_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$this->Index] = $this->_attributes;
		$this->_elementlist[$this->Index] = $this->_attributes;

		return true;
	}
	
	function removeItem($index)
	{				
		// Remove item from list
		unset($_SESSION['OrderForm'.ORDERFORM_ID][$this->Type]['Elements'][$index]);
		unset($this->_elementlist[$index]);
		
		return true;
	}
	
	function getItem($index)
	{
		// Reset attributes
		$this->_attributes = array();
		
		// Set index
		$this->Index = $index;
		
		// Check if item exists
		if(isset($this->_elementlist[$index]))
		{
			foreach($this->_elementlist[$index] as $key=>$value)
			{
				$this->setAttribute($key, $value);
			}
			
			return true;	
		}
		
		return false;
	}
	
	function setAttribute($key, $value){
		// Store attribute
		$this->{$key} = $value;
		$this->_attributes[$key] = $value;

		return true;
	}
	
	function getAttribute($key){
		
		// Get attribute
		if(isset($this->_attributes[$key]))
		{
			return $this->_attributes[$key];
		}
		else
		{
			return null;
		}
	}
	
	function removeAttribute($key){
		// Remove attribute
		if(isset($this->_attributes[$key]))
		{
			$this->{$key} = null;
			unset($this->_attributes[$key]);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function listItems($escaped = true)
	{
		$list = array();
		
		if(empty($this->_elementlist))
		{
			return $list;
		}
		else
		{
			foreach($this->_elementlist as $index => $tmp)
			{
				$list[$index] = $this->getCartItem($index, $escaped);
			}
			
			
			return $list;
		}
	}
	
	function getCartItem($index, $escaped = true)
	{
		// Build array for cart	
		$this->getDefaultValues();
		
		// Build array
		$item = array();
		$item['Index'] = $index;
		
		// Get item properties
		$this->getItem($index);
		foreach($this->_attributes as $key=>$value)
		{
			$item[$key] = $value;
		}	
		
		// If item is a product, get product information		
		$this->getProductDetails($this->ProductCode);
		
		$this->PriceIncl 			= (float)$this->PriceExcl * (1 + $this->btwcheck($this->TaxPercentage));
	
		$item['Type'] 				= $this->Type;
		$item['Number'] 			= $this->Number;
		$item['ProductID'] 			= $this->ProductID;
		$item['ProductCode'] 		= $this->ProductCode;
		$item['Description'] 		= $this->Description;
		$item['PriceExcl'] 			= $this->PriceExcl;
		$item['TaxPercentage'] 		= $this->btwcheck($this->TaxPercentage);
		$item['PriceIncl'] 			= $this->PriceIncl;
		$item['DiscountPercentage'] = $this->DiscountPercentage;
		$item['DiscountPercentageType'] = $this->DiscountPercentageType;
		$item['Periods'] 			= $this->Periods;
		$item['Periodic'] 			= $this->Periodic;
		
		$item['ProductType'] 		= $this->ProductType;
		$item['Reference'] 			= $this->Reference;
		
		// Should we escape output?
		if($escaped)
		{
			$item = escapeArray($item);
		}
		
		
		return $item;
		
	}
	
	function getDefaultValues()
	{
		$this->Number = 1;
		$this->ProductID = 0;
		$this->ProductCode = '';
		$this->Description = '';
		$this->PriceExcl = 0;
		$this->TaxPercentage = $this->_settings->get('STANDARD_TAX');
		$this->DiscountPercentage = 0;
		$this->DiscountPercentageType = 'line';
		$this->Periods = 1;
		$this->Periodic = '';
		
		$this->ProductType = '';
		$this->Reference = 0;
	}
	
	function getProductsFromGroup($group_id)
	{
		$products = array();
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT p.`id`, p.`PriceExcl`, p.`PricePeriod`, p.`ProductCode`, p.`ProductName`, p.`ProductKeyPhrase`, p.`TaxPercentage`, (p.`PriceExcl` * (1+p.`TaxPercentage`)) as `PriceIncl`  
											 FROM `HostFact_Products` p, `HostFact_GroupRelations` g 
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`Status`='1'
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
	
	function getProductDetails($productcode)
	{
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id`, `PriceExcl`, `PricePeriod`, `ProductKeyPhrase`, `TaxPercentage`, `HasCustomPrice` 
											 FROM `HostFact_Products` p
											 WHERE p.`ProductCode`=:product_code
											 LIMIT 1");
		$pdo_statement->bindValue(':product_code', $productcode);


		// Execute statement
		$pdo_statement->execute();	
		$product_info = $pdo_statement->fetch();
		
		if($product_info === false){
			return false;
		}
									
		// Copy data if not yet filled
		$this->ProductID		= $product_info->id;
		$this->Description 		= (!is_null($this->getAttribute('Description'))) 	? $this->getAttribute('Description') 	: $product_info->ProductKeyPhrase;
		$this->PriceExcl 		= (!is_null($this->getAttribute('PriceExcl'))) 		? $this->getAttribute('PriceExcl') 		: $product_info->PriceExcl;
		
		$taxrates = $this->_settings->get('array_taxpercentages');
		$this->TaxPercentage	= (!is_null($this->getAttribute('TaxPercentage'))) 	? $this->getAttribute('TaxPercentage') 	: ((isset($taxrates[(string)$product_info->TaxPercentage])) ? $product_info->TaxPercentage : $this->_settings->get('STANDARD_TAX'));

		// Period
		$converter = array('t' => 0.5, 'j' => 1, 'h' => 2, 'k' => 4, 'm' => 12, 'w' => 52, 'd' => 365);

		if(!is_null($this->getAttribute('Periodic')))
		{
			$this->Periodic 		= $this->getAttribute('Periodic');
		}
		else
		{
			// First check for custom period of element
			$priceperiod = $this->getAttribute('Periodic');
			$period_may_be_changed = true;

			if(is_null($priceperiod))
			{
				// Check for order priceperiod
				$order = new Order_Model();
				$priceperiod = $order->get('PricePeriod');
			}

			// If we have a custom price period and product is periodic..
			if(!is_null($priceperiod) && $product_info->PricePeriod)
			{
				// Extract periods
				preg_match_all('/[0-9]+/',$priceperiod,$matches);
				$tmp_periods = (!empty($matches[0])) ? $matches[0][0] : $this->Periods;

				// Extract periodic
				preg_match_all('/[a-z]+/',$priceperiod,$matches);
				$tmp_periodic = (!empty($matches[0])) ? $matches[0][0] : $product_info->PricePeriod;

				// For some elements we need a minimal period
				if(isset($this->has_mimimum_period) && $this->has_mimimum_period === true)
				{
					$period_may_be_changed = ((1 / $converter[$product_info->PricePeriod]) <= ($tmp_periods / $converter[$tmp_periodic]));
				}
				
				$this->Periods = ($period_may_be_changed) ? $tmp_periods : $this->Periods;
				$this->Periodic = ($period_may_be_changed) ? $tmp_periodic : $product_info->PricePeriod;

			}
			else
			{
				$this->Periodic = $product_info->PricePeriod;
			}
		}
		
		// Does product has custom period prices?
		if($product_info->HasCustomPrice == 'period')
		{
			$price_array = $this->listCustomProductPrices($this->ProductID);

			if(isset($price_array['period'][$this->Periods.'-'.$this->Periodic]['PriceExcl']))
			{
				$this->PriceExcl = $price_array['period'][$this->Periods.'-'.$this->Periodic]['PriceExcl'];
				
				// Don't convert the amount
				return true;
			}
			else
			{
				$this->PriceExcl = $price_array['period']['default']['PriceExcl'];
			}
		}
		
		// If period from element is different than product price period, but price is the same, we want to calculate the correct price
		if($this->Periodic != $product_info->PricePeriod && $this->PriceExcl == $product_info->PriceExcl){
			
			// Calculate new price if both periods exist in converter-array
			if(isset($converter[$product_info->PricePeriod]) && isset($converter[$this->Periodic]))
			{
				$this->PriceExcl = $this->PriceExcl * ( $converter[$product_info->PricePeriod] / $converter[$this->Periodic] );
			}			
		}
		
		return true;
	}

	function listCustomProductPrices($product_id, $id_type = 'id')
	{
		// Get custom prices
		if($id_type == 'productcode')
		{
			$pdo_statement = $this->_db->prepare("SELECT pcp.* FROM `HostFact_Product_Custom_Prices` pcp, `HostFact_Products` prod WHERE pcp.`ProductID`=prod.`id` AND prod.`ProductCode`=:product_code AND prod.`Status`!=9");
			$pdo_statement->bindValue(':product_code', $product_id);
		}
		else
		{
			$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Product_Custom_Prices` WHERE `ProductID`=:product_id");
			$pdo_statement->bindValue(':product_id', $product_id);
		}		
		$pdo_statement->execute();
		
		if(!$result = $pdo_statement->fetchAll())
		{
			return false;
		}
		
		// Get default price	
		if($id_type == 'productcode')
		{
			$pdo_statement = $this->_db->prepare("SELECT `id`, `PriceExcl`, `PricePeriod`, `TaxPercentage` FROM `HostFact_Products` WHERE `ProductCode`=:product_id AND `Status`!=9 LIMIT 1");
		}
		else
		{
			$pdo_statement = $this->_db->prepare("SELECT `id`, `PriceExcl`, `PricePeriod`, `TaxPercentage` FROM `HostFact_Products` WHERE `id`=:product_id LIMIT 1");
		}
		$pdo_statement->bindValue(':product_id', $product_id);

		// Execute statement
		$pdo_statement->execute();	
		$product_info = $pdo_statement->fetch();
		
		// Loop custom prices
		$price_array = array();
		foreach($result as $tmp_price)
		{
			$price_array[$tmp_price->PriceType][$tmp_price->Periods.'-'.$tmp_price->Periodic]['PriceExcl'] = $tmp_price->PriceExcl;
			$price_array[$tmp_price->PriceType][$tmp_price->Periods.'-'.$tmp_price->Periodic]['PriceIncl'] = (defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl") ? round($tmp_price->PriceExcl * (1+$product_info->TaxPercentage), 5) : round(round((float)$tmp_price->PriceExcl, 2) * (1+$product_info->TaxPercentage), 2);
		}

		if(isset($price_array['period']))
		{
			$price_array['period']['default']['PriceExcl'] = $product_info->PriceExcl;
			$price_array['period']['default']['PriceIncl'] = $product_info->PriceExcl * (1 + $this->btwcheck($product_info->TaxPercentage));
		}
		
		return $price_array;
	}
	
	function btwcheck($tax, $taxtype = 'line')
	{
		// Get company country
		$company_country = $this->_settings->get('company_country');
		$company_state = $this->_settings->get('company_state');

		$order = new Order_Model();
		if($order->get('ExistingCustomer') == 'yes')
		{	
	    	$debtor = new Debtor_Model();
	    	$debtor->checkLogin();
	    	$debtor->show();

	    	if($debtor->Taxable == 'no'){
	    		return 0;
	    	}elseif($debtor->Taxable == 'yes'){
	    		return $tax;
	    	}
		}else{
			$debtor = new Customer_Model();
		}
		
		// Use taxrules
		$debtor_country = ($debtor->InvoiceCountry && $debtor->InvoiceAddress) ? $debtor->InvoiceCountry : $debtor->Country;
		
		// Try to match a rule on CountryCode
		$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_TaxRules` WHERE `CountryCode`=:country_code");
		$pdo_statement->bindValue(':country_code', $debtor_country);
		$pdo_statement->execute();	
		$result = $pdo_statement->fetchAll();
		
		if(!empty($result))
		{
			foreach($result as $rule)
			{				
				// Check if rule matches
				if($rule->StateCode == 'all' || ($rule->StateCode == 'same' && $debtor->State == $company->State) || ($rule->StateCode == 'other' && $debtor->State != $company->State))
				{
					// Check if restriction matches
					if($rule->Restriction == 'all' || ($rule->Restriction == 'company' && $debtor->CompanyName) || ($rule->Restriction == 'company_vat' && $debtor->CompanyName && $debtor->TaxNumber) || ($rule->Restriction == 'individual' && (!$debtor->CompanyName || !$debtor->TaxNumber)))
					{
						if($taxtype == 'line' && !is_null($rule->TaxLevel1)){
							return (float)$rule->TaxLevel1;
						}elseif($taxtype == 'total' && !is_null($rule->TaxLevel2)){
							return (float)$rule->TaxLevel2;
						} 
					}
				}
			}
			
		}
		
		// Else try to match other CC-methodes all/otherEU/nonEU
		$pdo_statement = $this->_db->prepare("SELECT `CountryCode` FROM `HostFact_Settings_Countries` WHERE `EUCountry`='yes'");
		$pdo_statement->execute();	
		$result = $pdo_statement->fetchAll();
		
		$eu_countries = array();
		if(!empty($result))
		{
			foreach($result as $tmp_cc)
			{
				$eu_countries[] = $tmp_cc->CountryCode;
			}
		}
		
		
		$pdo_statement = $this->_db->prepare("SELECT * FROM `HostFact_Settings_TaxRules` WHERE `CountryCode`='all' OR `CountryCode`='other' OR `CountryCode`='otherEU' OR `CountryCode`='nonEU'");
		$pdo_statement->execute();	
		$result = $pdo_statement->fetchAll();
		
		if(!empty($result))
		{
			foreach($result as $rule)
			{
				// Check if rule matches
				if($rule->CountryCode == 'all' || ($rule->CountryCode == 'other' && $company_country != $debtor_country) || ($rule->CountryCode == 'otherEU' && in_array($debtor_country,$eu_countries) && $company_country != $debtor_country) || ($rule->CountryCode == 'nonEU' && !in_array($debtor_country,$eu_countries)))
				{		
					if($rule->StateCode == 'all' || ($rule->StateCode == 'same' && $debtor->State == $company_state) || ($rule->StateCode == 'other' && $debtor->State != $company_state))
					{
						// Check if restriction matches
						if($rule->Restriction == 'all' || ($rule->Restriction == 'company' && $debtor->CompanyName) || ($rule->Restriction == 'company_vat' && $debtor->CompanyName && $debtor->TaxNumber) || ($rule->Restriction == 'individual' && (!$debtor->CompanyName || !$debtor->TaxNumber)))
						{
							if($taxtype == 'line' && !is_null($rule->TaxLevel1)){
								return (float)$rule->TaxLevel1;
							}elseif($taxtype == 'total' && !is_null($rule->TaxLevel2)){
								return (float)$rule->TaxLevel2;
							} 
						}
					}
				}
			}
		}
		
		return $tax;
	}	
	

	function addToDatabase($cart_item)
	{
		$line_amount = getLineAmount($this->VatCalcMethod,$cart_item['PriceExcl'], $cart_item['Periods'], $cart_item['Number'], $cart_item['TaxPercentage'], ($cart_item['DiscountPercentage'] / 100));

		// Prepare query
		$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_NewOrderElements` (`OrderCode`, `Debtor`,`Date`, `Number`, `ProductCode`, `Description`, `PriceExcl`, `TaxPercentage`, `DiscountPercentage`, `DiscountPercentageType`, `Periods`, `Periodic`, `ProductType`, `Reference`, `LineAmountExcl`, `LineAmountIncl`) VALUES (:order_code, :debtor_id, CURDATE(), :number, :product_code, :description, :price_excl, :tax_percentage, :discount_percentage, :discount_percentage_type, :periods, :periodic, :producttype, :reference, :lineamountexcl, :lineamountincl)");
		
		$pdo_statement->bindValue(':order_code', 			$this->OrderCode);
		$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
		$pdo_statement->bindValue(':number', 				$cart_item['Number']);
		$pdo_statement->bindValue(':product_code', 			$cart_item['ProductCode']);
		$pdo_statement->bindValue(':description', 			$cart_item['Description']);
		$pdo_statement->bindValue(':price_excl', 			$cart_item['PriceExcl']);
		$pdo_statement->bindValue(':tax_percentage', 		$cart_item['TaxPercentage']);
		$pdo_statement->bindValue(':discount_percentage', 	$cart_item['DiscountPercentage'] / 100);
		$pdo_statement->bindValue(':discount_percentage_type', 	($cart_item['DiscountPercentageType'] == 'subscription' && $cart_item['Periodic']) ? 'subscription' : 'line');
		$pdo_statement->bindValue(':periods', 				$cart_item['Periods']);
		$pdo_statement->bindValue(':periodic', 				$cart_item['Periodic']);
		$pdo_statement->bindValue(':producttype', 			$cart_item['ProductType']);
		$pdo_statement->bindValue(':reference', 			$cart_item['Reference']);
		$pdo_statement->bindValue(':lineamountexcl', 		$line_amount['excl']);
		$pdo_statement->bindValue(':lineamountincl', 		$line_amount['incl']);
	
		// Execute statement		
		if($pdo_statement->execute())
		{
			return true;
		}
		else
		{
			// Fix already created stuff
			$this->removeFromDatabase($cart_item);
			return false;
		}		
	}

	function all($OrderCode){

		$pdo_statement = $this->_db->prepare("SELECT *, `LineAmountExcl` as AmountExcl, `LineAmountIncl` as AmountIncl FROM HostFact_NewOrderElements WHERE `OrderCode`=:order_code");
		$pdo_statement->bindValue(':order_code', $OrderCode);
		$pdo_statement->execute();
		$element_list = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);

		return $element_list;
	}
	
	function removeFromDatabase($cart_item)
	{
		// Order elements are deleted automatically if needed 		
	}
}