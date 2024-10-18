<?php
class Order_Model extends Base_Model{
	
	public $OrderCode, $Debtor, $Customer, $Type, $Date;
	public $Term, $Discount, $IgnoreDiscount, $Coupon;
	public $CompanyName, $Initials, $SurName, $Address, $ZipCode, $City, $Country, $EmailAddress;
	public $Authorisation, $InvoiceMethod, $Template, $TaxRate, $Compound, $AmountExcl, $AmountIncl, $VatCalcMethod;
	public $PaymentMethod, $Paid, $TransactionID, $Comment, $IPAddress;
	
	public $elements, $AuthAgree;
	public $highestElementNumber;
	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();
		
		// Default values
		$this->Type 			= 'new';
		$this->Term 			= $this->_settings->get('INVOICE_TERM');
		$this->Discount 		= 0;
		$this->IgnoreDiscount 	= 0;
		$this->InvoiceMethod 	= 0;
		$this->Authorisation 	= 'no';
		$this->Template 		= 0;
		$this->IPAddress 		= $_SERVER['REMOTE_ADDR'];
		
		$this->elements 		= array();
		$this->highestElementNumber = 1;
		
		$this->TaxRate			= $this->_settings->get('STANDARD_TOTAL_TAX');
		$this->VatCalcMethod	= (SHOW_VAT_INCLUDED == true) ? 'incl' : 'excl';
		
		// Load session data in model
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model'] as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}
	}
	
	/**
	 * Order_Model::set()
	 * Save value in session and model
	 * 
	 * @param mixed $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{
		$_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model'][$index] = trim($value);
		$this->{$index} = trim($value);
	}
	
	/**
	 * Order_Model::get()
	 * Get htmlspecialchars-save value
	 * 
	 * @param mixed $index
	 * @return string
	 */
	public function get($index)
	{
		return (isset($this->{$index})) ? htmlspecialchars($this->{$index}) : null;
	}
	
	public function show()
	{
		$info = array();
		
		if(isset($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model']))
		{
			foreach($_SESSION['OrderForm'.ORDERFORM_ID]['Order_Model'] as $key=>$value)
			{
				$info[$key] = htmlspecialchars($value);
			}
		}
		
		// Calculate discount
		if($this->IgnoreDiscount == 0)
		{
			$discount = new Discount_Model();
			$this->elements = $discount->check($this);
			
			// Take the max value of percentage discount
			$this->Discount = max($this->Discount, $discount->DiscountPercentage);
		}
		
		// Do we have transaction costs
		$this->checkTransactionCosts();
		
		// Calculate totals
		$this->calculateTotals();
		
		// Other properties
		$info['elements'] 	= $this->elements;
		$info['AmountExcl'] = $this->AmountExcl;
		$info['AmountIncl'] = $this->AmountIncl;
		
		$info['UsedTaxRates']		= $this->UsedTaxRates;
		$info['TaxRateAmount'] 		= $this->TaxRateAmount;
		$info['TaxRateLabel'] 		= $this->TaxRateLabel;
		
		$info['Discount']	= $this->Discount;
		$info['DiscountAmountExcl']	= (isset($this->DiscountAmountExcl)) ? $this->DiscountAmountExcl : 0;
		$info['DiscountAmountIncl']	= (isset($this->DiscountAmountIncl)) ? $this->DiscountAmountIncl : 0;
		
		$info['highestElementNumber'] = $this->highestElementNumber;
		
		// Return order info
		return $info;
	}
	
	function calculateTotals()
	{
		$this->AmountExcl = $this->AmountIncl = 0;
		foreach($this->elements as $k=>$element){
			
			$line_discount_percentage_factor = ($element['DiscountPercentage'] / 100);
            $element['PriceExcl'] = (float)$element['PriceExcl'];
			
			// Format different totals, which can be used for view
			$this->elements[$k]['PeriodPriceExcl'] 		= $element['Periods'] * $element['PriceExcl'];
			$this->elements[$k]['DiscountDescription'] 	= sprintf(__('discountpercentage on product'), $element['DiscountPercentage']);
			$this->elements[$k]['DiscountAmountExcl'] 	= $element['Number'] * $element['Periods'] * $element['PriceExcl'] * -$line_discount_percentage_factor;
			$this->elements[$k]['AmountExcl'] 			= $element['Number'] * $element['Periods'] * $element['PriceExcl'];
			
			// Depending on vat calc method prices including vat must be calculated
			if($this->VatCalcMethod == 'incl')
			{
				// Incl vat
				$this->elements[$k]['PeriodPriceIncl'] 		= $element['Periods'] * $element['PriceExcl'] * (1+$element['TaxPercentage']);
				$this->elements[$k]['PriceIncl'] 			= $element['PriceExcl'] * (1+$element['TaxPercentage']);
				$this->elements[$k]['DiscountAmountIncl'] 	= $element['Number'] * $element['Periods'] * $element['PriceExcl'] * -$line_discount_percentage_factor * (1+$element['TaxPercentage']);
				$this->elements[$k]['AmountIncl'] 			= $element['Number'] * $element['Periods'] * $element['PriceExcl'] * (1+$element['TaxPercentage']);
			}
			else
			{
				// Excl vat
				$this->elements[$k]['PeriodPriceIncl'] 		= round($element['Periods'] * $element['PriceExcl'],2) * (1+$element['TaxPercentage']);
				$this->elements[$k]['PriceIncl'] 			= round((float)$element['PriceExcl'],2) * (1+$element['TaxPercentage']);
				$this->elements[$k]['DiscountAmountIncl'] 	= round($element['Number'] * $element['Periods'] * $element['PriceExcl'] * -$line_discount_percentage_factor,2) * (1+$element['TaxPercentage']);
				$this->elements[$k]['AmountIncl'] 			= round($element['Number'] * $element['Periods'] * $element['PriceExcl'],2) * (1+$element['TaxPercentage']);
			}
						
			$this->elements[$k]['AmountTax']  = $this->elements[$k]['AmountIncl'] - $this->elements[$k]['AmountExcl'];
		
			$this->AmountExcl += $this->elements[$k]['AmountExcl'] * (1-$line_discount_percentage_factor);
			$this->AmountIncl += $this->elements[$k]['AmountIncl'] * (1-$line_discount_percentage_factor);		
		}

		$this->AmountExcl = round($this->AmountExcl,2);
		$this->AmountIncl = round($this->AmountIncl,2);
		
		// Calculate discount percentage
		if(isset($this->Discount) && $this->Discount > 0 && $this->Discount <= 100)
		{
			$DiscountPercentage = round((float)$this->Discount,2) / 100;
			$tmp_AmountExcl = $this->AmountExcl;
			$tmp_AmountIncl = $this->AmountIncl;

			$this->AmountExcl = round(($this->AmountExcl) - (round($this->AmountExcl * $DiscountPercentage, 3)),2);
			$this->AmountIncl = round(($this->AmountIncl) - (round($this->AmountIncl * $DiscountPercentage, 3)),2);
			
			$this->DiscountAmountExcl = $this->AmountExcl - $tmp_AmountExcl;
			$this->DiscountAmountIncl = $this->AmountIncl - $tmp_AmountIncl;
		}
		
		// Total tax rate
		$tmp_element = new OrderElement_Model();
		$this->TaxRate = $tmp_element->btwcheck($this->TaxRate, 'total');
		unset($tmp_element);
		
		$this->TaxRateAmount = 0;
		$this->TaxRateLabel = '';
		if($this->TaxRate > 0)
		{
			$array_total_taxpercentages_info = $this->_settings->get('array_total_taxpercentages_info');
			$this->Compound = (isset($array_total_taxpercentages_info[(string)(float)$this->TaxRate]['compound'])) ? $array_total_taxpercentages_info[(string)(float)$this->TaxRate]['compound'] : '';

			if($this->Compound == 'yes'){
				$this->TaxRateAmount 	= round(($this->AmountIncl * $this->TaxRate),2);
			}else{
				$this->TaxRateAmount 	= round(($this->AmountExcl * $this->TaxRate),2);
			}
			$this->TaxRateLabel 	= (isset($array_total_taxpercentages_info[(string)(float)$this->TaxRate]['label'])) ? $array_total_taxpercentages_info[(string)(float)$this->TaxRate]['label'] : '';
			
			$this->AmountIncl += $this->TaxRateAmount;
		}
		
		// Determine taxrates
		$used_rates = array();
		$excl_tax_rounder = ($this->VatCalcMethod == 'incl') ? 9 : 2;
		if(is_array($this->elements)){
			$disc_percentage = ($this->Discount == 100) ? 0 : ((100-$this->Discount)/100);
			
			foreach($this->elements as $k=>$element){
    			if(is_numeric($k)){
					if(!isset($used_rates[(string)$element['TaxPercentage']])){
						$used_rates[(string)$element['TaxPercentage']]['AmountExcl'] = 0;
					}
					
					$used_rates[(string)$element['TaxPercentage']]['AmountExcl'] += round($element['AmountExcl'] * (1-($element['DiscountPercentage'] / 100)), $excl_tax_rounder) * $disc_percentage;
				}
			}
			
			foreach($used_rates as $k=>$v)
			{
				$used_rates[$k]['AmountTax'] = round($v['AmountExcl'] * $k,2);
				$used_rates[$k]['AmountExcl'] = round($v['AmountExcl'],2);
			}
		}
		$this->UsedTaxRates = $used_rates;
	}
	
	function checkTransactionCosts()
	{
		if(!isset($this->PaymentMethod) || !$this->PaymentMethod)
		{
			return false;
		}
		
		// Get paymentmethods		
		$paymentmethods = $this->_settings->get('array_paymentmethods');
		
		// Are there costs involved?
		if($paymentmethods[$this->PaymentMethod]['PriceExcl'] !== '' && $paymentmethods[$this->PaymentMethod]['PriceExcl'] != 0)
		{
			// Create a temporary object
            $order_element = new OrderElement_Model();
            $order_element->newItem('TransactionCosts');
			$order_element->setAttribute('Description', 		$paymentmethods[$this->PaymentMethod]['FeeDesc']);
			$order_element->setAttribute('isCompactView',		true);
			$order_element->setAttribute('PriceExcl', 			$paymentmethods[$this->PaymentMethod]['PriceExcl']);
			$order_element->saveItem();
			
			// Add element
			$this->elements[] = $order_element->getCartItem('TransactionCosts');
			
			// Delete temporary transaction costs
			$order_element->removeItem('TransactionCosts');
		}
		elseif($paymentmethods[$this->PaymentMethod]['Percentage'] !== '' && $paymentmethods[$this->PaymentMethod]['Percentage'] != 0)
		{
			$this->calculateTotals();
			
			// Create a temporary object
            $order_element = new OrderElement_Model();
            $order_element->newItem('TransactionCosts');
			$order_element->setAttribute('Description', 		$paymentmethods[$this->PaymentMethod]['FeeDesc']);
			$order_element->setAttribute('isCompactView',		true);
			$order_element->setAttribute('PriceExcl', 			round($this->AmountExcl * $paymentmethods[$this->PaymentMethod]['Percentage'] / 100,2) );
			
			$order_element->saveItem();
			
			// Add element
			$this->elements[] = $order_element->getCartItem('TransactionCosts');
			
			// Delete temporary transaction costs
			$order_element->removeItem('TransactionCosts');
		}
	}
	
	function addElement($element)
	{
		if($element['Description'])
		{
			$this->elements[] = $element;
			$this->highestElementNumber = max($this->highestElementNumber, $element['Number']);
		}
		
	}
	
	function add()
	{
		$this->set('OrderCode', $this->newOrderCode());
		
		// If we have a new customer
		if($this->Type == 'new')
		{
			// Load debtor for customer data
			$debtor = new Customer_Model;
		}
		else
		{
			$debtor = new Debtor_Model;
			$debtor->Identifier = $this->Debtor;
			$debtor->show();
		}	
		$this->set('CompanyName',		($debtor->InvoiceCompanyName) ? $debtor->InvoiceCompanyName : $debtor->CompanyName);
        $this->set('Sex', 			    ($debtor->InvoiceSurName) ? $debtor->InvoiceSex : $debtor->Sex);
		$this->set('Initials', 			($debtor->InvoiceInitials) ? $debtor->InvoiceInitials : $debtor->Initials);
		$this->set('SurName', 			($debtor->InvoiceSurName) ? $debtor->InvoiceSurName : $debtor->SurName);
		$this->set('Address', 			($debtor->InvoiceAddress) ? $debtor->InvoiceAddress : $debtor->Address);
		$this->set('Address2', 			($debtor->InvoiceAddress2) ? $debtor->InvoiceAddress2 : $debtor->Address2);
		$this->set('ZipCode', 			($debtor->InvoiceZipCode) ? $debtor->InvoiceZipCode : $debtor->ZipCode);
		$this->set('City', 				($debtor->InvoiceCity) ? $debtor->InvoiceCity : $debtor->City);
		$this->set('State', 			($debtor->InvoiceState) ? $debtor->InvoiceState : $debtor->State);
		$this->set('Country', 			($debtor->InvoiceCountry && $debtor->InvoiceAddress) ? $debtor->InvoiceCountry : $debtor->Country);
		$this->set('EmailAddress', 		($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress);
		$this->set('InvoiceMethod',		$debtor->InvoiceMethod);
		$this->set('Term',			 	(isset($debtor->InvoiceTerm) && $debtor->InvoiceTerm > 0) ? $debtor->InvoiceTerm : $this->Term);
		
		// validate order data
		if(!$this->validate())
		{
			// Delete customer
			if($this->Type == 'new' && $this->Debtor > 0)
			{
				$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_NewCustomers` WHERE `id`=:customer_id");
				$pdo_statement->bindValue(':customer_id', $this->Debtor);
				$pdo_statement->execute();
			}
		
			return false;
		}
		
		// Calculate discount
		if($this->IgnoreDiscount == 0)
		{
			$discount = new Discount_Model();
			$discount->updateDiscountCounter = true;
			$this->elements = $discount->check($this);
			
			// Take the max value of percentage discount
			$this->Discount = max($this->Discount, $discount->DiscountPercentage);
		}
			
		// Do we have transaction costs
		$this->checkTransactionCosts();
		
		//Set PaymentMethod
		$this->OriginalPaymentMethod = $this->PaymentMethod;
		if($this->PaymentMethod == 'auth')
		{
			$this->set('Authorisation', 'yes');
		}
		elseif(substr($this->PaymentMethod,0,5) == 'other')
		{
			$this->PaymentMethod = 'other';
		}
		elseif(substr($this->PaymentMethod,0,5) == 'ideal')
		{
			$this->PaymentMethod = 'ideal';
		
		}elseif(substr($this->PaymentMethod,0,6) == 'paypal')
		{
			$this->PaymentMethod = 'paypal';
		}
		
		// Add elements into database
		foreach($this->elements as $tmp_element)
		{		
			// Create instance of order element
			$element = new $tmp_element['Type']();
			$element->VatCalcMethod = $this->VatCalcMethod;
			
			// Copy some important data
			$element->OrderCode = $this->OrderCode;
			$element->Debtor 	= $this->Debtor;
			$element->DebtorType = $this->Type;
			
			// Add element into database
			$result = $element->addToDatabase($tmp_element);
			
			if(!$result)
			{
				// Merge errors
				$this->Error = array_merge($this->Error, $element->Error);
			}
		
		}
		
		if(empty($this->Error))
		{
			// Process discount on total amounts
	 		$this->DiscountPercentage = round((float)$this->Discount,2) / 100;

			// Calculate financial totals
			$tmp_element = new OrderElement_Model();
			$tmp_element->VatCalcMethod = $this->VatCalcMethod;
			$elements = $tmp_element->all($this->OrderCode);
			
			$financial_totals = calculateFinancialTotals($this->VatCalcMethod, $elements, $this->DiscountPercentage);
			$this->AmountExcl = $financial_totals['totals']['AmountExcl'];
			$this->AmountIncl = $financial_totals['totals']['AmountIncl'];

			// Determine total tax + compound
			$tmp_element = new OrderElement_Model();
			$this->TaxRate = $tmp_element->btwcheck($this->TaxRate, 'total');
			unset($tmp_element);
			
			if($this->TaxRate > 0)
			{
				$array_total_taxpercentages_info = $this->_settings->get('array_total_taxpercentages_info');
				
				$this->Compound = (isset($array_total_taxpercentages_info[(string)(float)$this->TaxRate]['compound'])) ? $array_total_taxpercentages_info[(string)(float)$this->TaxRate]['compound'] : 'no';
				
				if($this->Compound == 'yes')
				{
					$this->TaxRate_Amount = round($this->AmountIncl * $this->TaxRate,2);
					$this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
				}
				else
				{
					$this->TaxRate_Amount = round($this->AmountExcl * $this->TaxRate,2);
					$this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
				}
			}

			// Php-fix for rounding amount with e.g. 1.235 -> 1.24 instead of PHP-default 1.23
			if($this->AmountIncl > 0){
				$this->AmountIncl = number_format($this->AmountIncl+0.00000001,2,'.','');
			}elseif($this->AmountIncl < 0){
				$this->AmountIncl = number_format($this->AmountIncl-0.00000001,2,'.','');
			}

			// If there is nothing to pay, mark as paid
			if($this->AmountIncl <= 0)
			{
				$this->Paid = 1;
			}

			// Prepare query
			$pdo_statement = $this->_db->prepare("INSERT INTO `HostFact_NewOrder` (`OrderCode`, `Debtor`, `Type`, `Date`, `Term`, `Discount`, `IgnoreDiscount`, `Coupon`, `CompanyName`, `Sex`, `Initials`, `SurName`, `Address`, `Address2`, `ZipCode`, `City`, `State`, `Country`, `EmailAddress`, `Authorisation`, `InvoiceMethod`, `Template`, `TaxRate`, `Compound`, `AmountExcl`, `AmountIncl`, `VatCalcMethod`, `PaymentMethod`, `Paid`, `TransactionID`, `Comment`, `IPAddress`, `Employee`, `Created`, `Modified`) VALUES (:order_code, :debtor_id, :debtor_type, NOW(), :term, :discount, :ignore_discount, :coupon, :companyname, :sex, :initials, :surname, :address, :address2, :zipcode, :city, :state, :country, :emailaddress, :authorisation, :invoicemethod, :template_id, :taxrate, :compound, :amount_excl, :amount_incl, :vatcalcmethod, :paymentmethod, :paid, :transactionid, :comment, :ipaddress, 0, NOW(), NOW())");
			
			$pdo_statement->bindValue(':order_code', 			$this->OrderCode);
			$pdo_statement->bindValue(':debtor_id', 			$this->Debtor);
			$pdo_statement->bindValue(':debtor_type', 			$this->Type);
			$pdo_statement->bindValue(':term', 					$this->Term);
			$pdo_statement->bindValue(':discount', 				$this->DiscountPercentage);
			$pdo_statement->bindValue(':ignore_discount', 		$this->IgnoreDiscount);
			$pdo_statement->bindValue(':coupon', 				$this->Coupon);
			
			$pdo_statement->bindValue(':companyname', 			$this->CompanyName);
            $pdo_statement->bindValue(':sex', 			       	$this->Sex);
			$pdo_statement->bindValue(':initials', 				$this->Initials);
			$pdo_statement->bindValue(':surname', 				$this->SurName);
			$pdo_statement->bindValue(':address', 				$this->Address);
			$pdo_statement->bindValue(':address2', 				$this->Address2);
			$pdo_statement->bindValue(':zipcode', 				$this->ZipCode);
			$pdo_statement->bindValue(':city', 					$this->City);
			$pdo_statement->bindValue(':state', 				$this->State);
			$pdo_statement->bindValue(':country', 				$this->Country);
			$pdo_statement->bindValue(':emailaddress', 			check_email_address($this->EmailAddress, 'convert'));
			
			$pdo_statement->bindValue(':authorisation', 		$this->Authorisation);
			$pdo_statement->bindValue(':invoicemethod', 		$this->InvoiceMethod);
			$pdo_statement->bindValue(':template_id', 			$this->Template);
			
			$pdo_statement->bindValue(':taxrate', 				$this->TaxRate);
			$pdo_statement->bindValue(':compound', 				$this->Compound);
			
			$pdo_statement->bindValue(':amount_excl', 			round($this->AmountExcl,2));
			$pdo_statement->bindValue(':amount_incl', 			round($this->AmountIncl,2));
			$pdo_statement->bindValue(':vatcalcmethod', 		$this->VatCalcMethod);
			
			$pdo_statement->bindValue(':paymentmethod', 		$this->PaymentMethod);
			$pdo_statement->bindValue(':paid', 					$this->Paid);
			$pdo_statement->bindValue(':transactionid', 		$this->TransactionID);
			
			$pdo_statement->bindValue(':comment', 				$this->Comment);
			$pdo_statement->bindValue(':ipaddress', 			$this->IPAddress);
			
			// Execute statement
			if($pdo_statement->execute())
			{
				$this->Identifier = $this->_db->lastInsertId();
				
				// If existing debtor, update auth information
				if($this->Authorisation == 'yes' && $this->Type == 'debtor' && $this->AccountNumber)
				{
						$pdo_statement = $this->_db->prepare("UPDATE `HostFact_Debtors` SET `InvoiceAuthorisation`='yes', `AccountNumber`=:accountnumber, `AccountBIC`=:accountbic, `AccountName`=:accountname, `AccountCity`=:accountcity WHERE `id`=:debtor_id");
						
						$pdo_statement->bindValue(':accountnumber', 	$this->AccountNumber);
						$pdo_statement->bindValue(':accountbic', 		$this->AccountBIC);
						$pdo_statement->bindValue(':accountname', 		$this->AccountName);
						$pdo_statement->bindValue(':accountcity', 		$this->AccountCity);
						$pdo_statement->bindValue(':debtor_id', 		$this->Debtor);
						
						$pdo_statement->execute();
			
				}
				return true;
			}
		}
		
		//If failed and not returned, cleanup
		
		// Call remove functions, to undo actions
		foreach($this->elements as $tmp_element)
		{
			// Create instance of order element
			$element = new $tmp_element['Type']();
							
			// Remove element from database
			$result = $element->removeFromDatabase($tmp_element);
		}
		
		// Delete order elements
		$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_NewOrderElements` WHERE `OrderCode`=:order_code");
		$pdo_statement->bindValue(':order_code', $this->OrderCode);
		$pdo_statement->execute();
		
		// Delete customer
		if($this->Type == 'new')
		{
			$pdo_statement = $this->_db->prepare("DELETE FROM `HostFact_NewCustomers` WHERE `id`=:customer_id");
			$pdo_statement->bindValue(':customer_id', $this->Debtor);
			$pdo_statement->execute();
		}
		
		return false;		
	}
	
	function validate()
	{
		
		
		// Check if ordercode is valid
		if(!$this->OrderCode || !$this->is_free($this->OrderCode))
		{
			$this->Error[] = __('ordercode already in use');
		}
		
		// Check if debtor is valid
		if($this->Debtor <= 0)
		{
			$this->Error[] = __('could not found debtor data');
		}
		
		// Company
		if(!is_null($this->CompanyName) && (strlen($this->CompanyName) > 100 || !is_string($this->CompanyName)))
		{
			$this->Error[] = __('invalid companyname');
		}
				
		// Contact person		
		if(!is_null($this->Initials) && (strlen($this->Initials) > 25 || !is_string($this->Initials)))
		{
			$this->Error[] = __('invalid initials');
		}
		
		if(!is_null($this->SurName) && (strlen($this->SurName) > 100 || !is_string($this->SurName)))
		{
			$this->Error[] = __('invalid surname');
		}
		
		if(!is_null($this->Address) && (strlen($this->Address) > 100 || !is_string($this->Address)))
		{
			$this->Error[] = __('invalid address');
		}
		elseif(!is_null($this->Address2) && (strlen($this->Address2) > 100 || !is_string($this->Address2)))
		{
			$this->Error[] = __('invalid address');
		}
		
		if(!is_null($this->ZipCode) && (strlen($this->ZipCode) > 10 || !is_string($this->ZipCode)))
		{
			$this->Error[] = __('invalid zipcode');
		}
		
		if(!is_null($this->City) && (strlen($this->City) > 100 || !is_string($this->City)))
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
		if(!is_null($this->EmailAddress) && strlen($this->EmailAddress) > 0 && !check_email_address($this->EmailAddress))
		{
			$this->Error[] = __('invalid emailaddress');
		}
		elseif(!is_null($this->EmailAddress) && strlen($this->EmailAddress) > 0 && check_email_address($this->EmailAddress) && function_exists("checkEmailAddress") && !checkEmailAddress($this->EmailAddress))
		{
			$this->Error[] = __('invalid emailaddress');
		}
		
		if(empty($this->elements))
		{
			$this->Error[] = __('no products in order');
		}
		
		// Are there any errors?
		return empty($this->Error) ? true : false;
	}
	
	/**
	 * Order_Model::newOrderCode()
	 * Gets new ordercode. Return false if no ordercode could be generated
	 * 
	 * @return mixed
	 */
	function newOrderCode()
	{
		// Get prefix and number from settings
		$prefix = $this->_settings->get('ORDERCODE_PREFIX');
		$number = $this->_settings->get('ORDERCODE_NUMBER');
		
		// Replace variables from prefix ([yyyy], [yy] and [mm])
		$prefix = parsePrefixVariables($prefix);
		
		// Determine total length of ordercode
		$length = strlen($prefix.$number);
		
		// Then get last ordercode from database
		$pdo_statement = $this->_db->prepare("SELECT `OrderCode` FROM `HostFact_NewOrder` WHERE `OrderCode` LIKE :prefix AND LENGTH(`OrderCode`)>=:length AND (SUBSTR(`OrderCode`,:prefix_offset)*1) > 0 AND SUBSTR(`OrderCode`,:prefix_offset) REGEXP '^[0-9]+$' ORDER BY (SUBSTR(`OrderCode`,:prefix_offset)*1) DESC LIMIT 1");
		$pdo_statement->bindValue(':prefix', $prefix.'%%');
		$pdo_statement->bindValue(':length', $length);
		$pdo_statement->bindValue(':prefix_offset', strlen($prefix)+1);

		// Execute statement
		$pdo_statement->execute();	
		$result = $pdo_statement->fetch();		
		
		// Calculate the new ordercode
		if(isset($result->OrderCode) && $result->OrderCode && is_numeric(substr($result->OrderCode,strlen($prefix))))
		{
			$code = substr($result->OrderCode,strlen($prefix));
			$code = $prefix . @str_repeat('0', max(strlen($number) - strlen(max($code + 1,$number)),0)) . (max($code + 1,$number));
		}
		else
		{
			$code = $prefix . $number;
		}
		
		// Check again if ordercode is indeed free
		if(!$this->is_free($code))
		{
			$this->Error[] = __('could not generate ordercode');
			return false;
		}
		else
		{
			return $code;
		}
	}

	/**
	 * Order_Model::is_free()
	 * Check if ordercode is still free
	 * 
	 * @param mixed $order_code
	 * @return boolean
	 */
	function is_free($order_code)
	{
		if(!trim($order_code))
		{
			// Empty ordercode, so we don't need to check
			return false;
		}
		
		// Prepare query
		$pdo_statement = $this->_db->prepare("SELECT `id` FROM `HostFact_NewOrder` WHERE `OrderCode`=:order_code LIMIT 1");
		$pdo_statement->bindValue(':order_code', $order_code);
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();	
		
		if(isset($result->id) && $result->id > 0)
		{
			// Ordercode is already in use
			return false;
		}
				
		// Not in use
		return true;
	}
	
}