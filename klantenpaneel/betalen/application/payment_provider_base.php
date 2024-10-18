<?php
class Payment_Provider_Base
{
	// Public variables
	public $Error;
	public $isNotificationScript;
	
	// Protected variables
	protected $db;
	protected $conf = array();
	
	protected $Type, $InvoiceCode, $OrderCode, $Amount;
	
	
	function __construct()
	{
		$this->db = Database_Model::getInstance();
		$this->Error = '';
		$this->isNotificationScript = false;
	}

	public function setInvoice($invoice_id)
	{
		// Lookup order
		$pdo_statement = $this->db->prepare("SELECT `AmountIncl`, `AmountPaid`, `InvoiceCode`, `Debtor`, `Status`, `Paid`, `TransactionID` FROM `HostFact_Invoice` WHERE `id`=:invoice_id");
		$pdo_statement->bindValue(':invoice_id', 	$invoice_id);	
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if(!$result){
			$this->InvoiceID	= null;
			return false;
		}
		
		// Set properties
		$this->InvoiceID	= $invoice_id;
		$this->Type 		= 'invoice';
		$this->InvoiceCode 	= $result->InvoiceCode;
		
		$this->Paid			   = ($result->Status == 4 || $result->Paid > 0) ? true : false;
        $this->TransactionID   = $result->TransactionID;
		$this->Amount		   = number_format(($result->AmountIncl - $result->AmountPaid), 2, '.', '');	
		$this->Debtor		   = $result->Debtor;	
		
		return true;
	}
	
	public function setOrder($order_id)
	{
		// Lookup order
		$pdo_statement = $this->db->prepare("SELECT `AmountIncl`, `OrderCode`, `Paid`, `TransactionID` FROM `HostFact_NewOrder` WHERE `id`=:order_id");
		$pdo_statement->bindValue(':order_id', 	$order_id);	
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if(!$result){
			$this->OrderID	= null;
			return false;
		}
		
		// Set properties
		$this->OrderID		= $order_id;
		$this->Type 		= 'order';
		$this->OrderCode 	= $result->OrderCode;
		
		$this->Paid			   = ($result->Paid > 0) ? true : false;
        $this->TransactionID   = $result->TransactionID;
		$this->Amount		   = number_format(($result->AmountIncl), 2, '.', '');	
		
		return true;
	}
	
	public function getCustomerData()
	{
		if($this->Type == 'invoice' && isset($this->InvoiceID) && $this->InvoiceID > 0 && $this->InvoiceCode)
		{
			// Lookup invoice
			$pdo_statement = $this->db->prepare("SELECT `CompanyName`, `Initials`, `SurName`, `Address`, `ZipCode`, `City`, `Country`, `EmailAddress` FROM `HostFact_Invoice` WHERE `id`=:invoice_id");
			$pdo_statement->bindValue(':invoice_id', 	$this->InvoiceID);	
			
			// Execute statement
			$pdo_statement->execute();
			$result = $pdo_statement->fetch();
			
			foreach($result as $k=>$v)
			{
				$result->{$k} = htmlspecialchars($v);
			}
			
			return $result;
		}
		elseif($this->Type == 'order' && isset($this->OrderID) && $this->OrderID > 0 && $this->OrderCode)
		{
			// Lookup order
			$pdo_statement = $this->db->prepare("SELECT `CompanyName`, `Initials`, `SurName`, `Address`, `ZipCode`, `City`, `Country`, `EmailAddress` FROM `HostFact_NewOrder` WHERE `id`=:order_id");
			$pdo_statement->bindValue(':order_id', 	$this->OrderID);	
			
			// Execute statement
			$pdo_statement->execute();
			$result = $pdo_statement->fetch();
			
			foreach($result as $k=>$v)
			{
				$result->{$k} = htmlspecialchars($v);
			}
			
			return $result;
		}
		else
		{
			// If no customer data is found, return false
			return false;
		}	
	}
	
	public function choosePaymentMethod()
	{
		// Normally we don't have to choose payment methods upfront
		return false;
	}
	
	public function validateChosenPaymentMethod()
	{
		// By default, we don't have to choose payment methods upfront
		return true;
	}
	
	protected function updateTransactionID($transactionID)
	{
		if($this->Type == 'invoice' && isset($this->InvoiceID) && $this->InvoiceID > 0 && $this->InvoiceCode)
		{
			// Prepare statement
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_Invoice` SET `PaymentMethod`=:payment_method, `PaymentMethodID`=:payment_method_id, `TransactionID`=:transaction_id, `Modified`=NOW() WHERE `id`=:invoice_id AND `InvoiceCode`=:invoice_code");
			$pdo_statement->bindValue(':payment_method', 	$this->conf['PaymentMethod']);	
			$pdo_statement->bindValue(':payment_method_id', $this->conf['PaymentMethodID']);
			$pdo_statement->bindValue(':transaction_id', 	$transactionID);	
			$pdo_statement->bindValue(':invoice_id', 		$this->InvoiceID);	
			$pdo_statement->bindValue(':invoice_code', 		$this->InvoiceCode);	
			
			// Execute statement
			return $pdo_statement->execute();			
		}
		elseif($this->Type == 'order' && isset($this->OrderID) && $this->OrderID > 0 && $this->OrderCode)
		{
			// Prepare statement
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_NewOrder` SET `PaymentMethod`=:payment_method, `PaymentMethodID`=:payment_method_id, `TransactionID`=:transaction_id, `Modified`=NOW() WHERE `id`=:order_id AND `OrderCode`=:order_code");
			$pdo_statement->bindValue(':payment_method', 	$this->conf['PaymentMethod']);	
			$pdo_statement->bindValue(':payment_method_id', $this->conf['PaymentMethodID']);
			$pdo_statement->bindValue(':transaction_id', 	$transactionID);	
			$pdo_statement->bindValue(':order_id', 			$this->OrderID);	
			$pdo_statement->bindValue(':order_code', 		$this->OrderCode);	
			
			// Execute statement
			return $pdo_statement->execute();	
		}
		else
		{
			// Could not update
			return false;
		}
	}
	
	protected function paymentProcessed($transactionID)
	{
		// If type is not set, lookup payment object
		if(!isset($this->Type))
		{
			$this->getType($transactionID);
		}
		
		// Update invoice
		if($this->Type == 'invoice' && isset($this->InvoiceID) && $this->InvoiceID > 0 && $this->InvoiceCode)
		{
			// Prepare statement which only updates the invoice if not already marked as paid (otherwise paid=2 will cause problems)
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_Invoice` SET `Status`='4', `Paid`='2', `PaymentMethod`=:payment_method, `PaymentMethodID`=:payment_method_id, `PayDate`=CURDATE(), `SubStatus`='', `Modified`=NOW() WHERE `id`=:invoice_id AND `InvoiceCode`=:invoice_code AND `Status` < 4");
			$pdo_statement->bindValue(':payment_method', 	$this->conf['PaymentMethod']);
			$pdo_statement->bindValue(':payment_method_id', $this->conf['PaymentMethodID']);
			$pdo_statement->bindValue(':invoice_id', 		$this->InvoiceID);	
			$pdo_statement->bindValue(':invoice_code', 		$this->InvoiceCode);	
			
			// Execute statement
			if($pdo_statement->execute())
			{
				// Add line to logile
				$pdo_statement = $this->db->prepare("INSERT INTO `HostFact_Log` (`Date`, `Type`, `Reference`, `Who`, `Action`, `Values`, `Translate`, `Page`) VALUES (NOW(), 'invoice', :invoice_id, 0, :action, :values, 'yes', :page)");
				$pdo_statement->bindValue(':invoice_id',	$this->InvoiceID);	
				$pdo_statement->bindValue(':action',		'log online payment succeeded');
				$pdo_statement->bindValue(':values',		$transactionID);		
				$pdo_statement->bindValue(':page',			'');
				$pdo_statement->execute();
				
				// Set redirect location
				if(!$this->isNotificationScript)
				{
					$_SESSION['payment']['status'] 			= 'paid';
					$_SESSION['payment']['type'] 			= 'invoice';
					$_SESSION['payment']['id'] 				= $this->InvoiceID;
					$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
					$_SESSION['payment']['transactionid'] 	= $transactionID;
					$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');
					
					header("Location: ".IDEAL_EMAIL);
				}
				exit;				
			}			
		}
		elseif($this->Type == 'order' && isset($this->OrderID) && $this->OrderID > 0 && $this->OrderCode)
		{
			// Prepare statement which only updates the neworder if not already marked as paid (otherwise paid=2 will cause problems)
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_NewOrder` SET `Paid`='1', `PaymentMethod`=:payment_method, `PaymentMethodID`=:payment_method_id, `Modified`=NOW()  WHERE `id`=:order_id AND `OrderCode`=:order_code AND `Status` < 8");
			$pdo_statement->bindValue(':payment_method', 	$this->conf['PaymentMethod']);
			$pdo_statement->bindValue(':payment_method_id', $this->conf['PaymentMethodID']);
			$pdo_statement->bindValue(':order_id', 		$this->OrderID);	
			$pdo_statement->bindValue(':order_code', 	$this->OrderCode);	
			
			// Execute statement
			if($pdo_statement->execute())
			{
				if(!$this->isNotificationScript)
				{
					// Set redirect location
					if(DEFAULT_ORDERFORM > 0)
					{
						// We have a new orderform, redirect to this one
						header("Location: ".ORDERFORM_URL . "?step=onlinepayment".(($transactionID) ? "&trxid=".$transactionID : ""));
						exit;
					}
					elseif(defined('ORDERFORM_ENABLED') && ORDERFORM_ENABLED == 'yes')
					{
						// Redirect to older orderform
						header("Location: ".CLIENTAREA_URL . "bestellen.status.php?step=onlinepayment".(($transactionID) ? "&trxid=".$transactionID : ""));
						exit;
					}
					
					// Set redirect location if none of above
					$_SESSION['payment']['status'] 			= 'paid';
					$_SESSION['payment']['type'] 			= 'order';
					$_SESSION['payment']['id'] 				= $this->OrderID;
					$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
					$_SESSION['payment']['transactionid'] 	= $transactionID;
					$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');
					
					
					header("Location: ".IDEAL_EMAIL);
				}
				exit;				
			}		
		}
		
		if(!$this->isNotificationScript)
		{
			// Could not update
			$_SESSION['payment']['status'] 			= 'paid';
			$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
			$_SESSION['payment']['transactionid'] 	= $transactionID;
			$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');
			header("Location: ".IDEAL_EMAIL);
		}
		exit;		

	}
	
	protected function paymentFailed($transactionID)
	{
		// If type is not set, lookup payment object
		if(!isset($this->Type))
		{
			$this->getType($transactionID);
		}
		
		// Update invoice
		if($this->Type == 'invoice' && isset($this->InvoiceID) && $this->InvoiceID > 0 && $this->InvoiceCode)
		{
			// Prepare statement which only updates the invoice if not already marked as paid (otherwise paid=2 will cause problems)
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_Invoice` SET `PaymentMethod`='', `PaymentMethodID`='', `TransactionID`='', `Paid`='0', `Modified`=NOW() WHERE `id`=:invoice_id AND `InvoiceCode`=:invoice_code AND `Status` < 4");
			$pdo_statement->bindValue(':invoice_id', 		$this->InvoiceID);	
			$pdo_statement->bindValue(':invoice_code', 		$this->InvoiceCode);	
			
			// Execute statement
			if($pdo_statement->execute())
			{
				// Add line to logile
				$pdo_statement = $this->db->prepare("INSERT INTO `HostFact_Log` (`Date`, `Type`, `Reference`, `Who`, `Action`, `Values`, `Translate`, `Page`) VALUES (NOW(), 'invoice', :invoice_id, 0, :action, :values, 'yes', :page)");
				$pdo_statement->bindValue(':invoice_id',	$this->InvoiceID);	
				$pdo_statement->bindValue(':action',		'log online payment failed');
				$pdo_statement->bindValue(':values',		$transactionID);			
				$pdo_statement->bindValue(':page',			'');
				$pdo_statement->execute();
				
				// Set redirect location
				if(!$this->isNotificationScript)
				{
					$_SESSION['payment']['status'] 			= 'failed';
					$_SESSION['payment']['type'] 			= 'invoice';
					$_SESSION['payment']['id'] 				= $this->InvoiceID;
					$_SESSION['payment']['transactionid'] 	= $transactionID;
					
					header("Location: ".IDEAL_EMAIL);
				}
				exit;	
			}			
		}
		elseif($this->Type == 'order' && isset($this->OrderID) && $this->OrderID > 0 && $this->OrderCode)
		{
			// Prepare statement which only updates the order if not already marked as paid (otherwise paid=2 will cause problems)
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_NewOrder` SET `PaymentMethod`='', `PaymentMethodID`='', `TransactionID`='', `Paid`='0', `Modified`=NOW() WHERE `id`=:order_id AND `OrderCode`=:order_code AND `Status` < 8");
			$pdo_statement->bindValue(':order_id', 		$this->OrderID);	
			$pdo_statement->bindValue(':order_code', 	$this->OrderCode);	
			
			// Execute statement
			if($pdo_statement->execute())
			{				
				// Set redirect location
				if(!$this->isNotificationScript)
				{
					$_SESSION['payment']['status'] 			= 'failed';
					$_SESSION['payment']['type'] 			= 'order';
					$_SESSION['payment']['id'] 				= $this->OrderID;
					$_SESSION['payment']['transactionid'] 	= $transactionID;
					
					header("Location: ".IDEAL_EMAIL);
				}
				exit;	
			}
		}
		else
		{
			// Could not update
			$this->paymentStatusUnknown();
			return false;
		}
	}
	
	public function paymentStatusUnknown($message = '')
	{
		if(!$this->isNotificationScript)
		{				
			// Set redirect location
			$_SESSION['payment']['status'] 			= 'failed';
			if($this->Type)
			{
				$_SESSION['payment']['type'] 			= $this->Type;
				$_SESSION['payment']['id'] 				= ($this->Type == 'order') ? $this->OrderID : $this->InvoiceID;
			}
			
			// Set message, if given
			if($message)
			{
				$_SESSION['payment']['error_message'] = $message;
			}
			
			header("Location: ".IDEAL_EMAIL);
		}
		exit;	
		
	}
	
	protected function getType($transactionID)
	{
		if(!trim($transactionID))
		{
			return false;
		}
		
		// Lookup payment object, first look for invoice
		$pdo_statement = $this->db->prepare("SELECT * FROM `HostFact_Invoice` WHERE `TransactionID`=:transaction_id");
		$pdo_statement->bindValue(':transaction_id', 	$transactionID);	
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if(isset($result->id) && $result->id > 0)
		{
			// Set properties
			$this->InvoiceID	= $result->id;
			$this->Type 		= 'invoice';
			$this->InvoiceCode 	= $result->InvoiceCode;
			
			$this->Paid			= ($result->Status == 4 || $result->Paid > 0) ? true : false;
			$this->Amount		= number_format(($result->AmountIncl - $result->AmountPaid), 2, '.', '');
			return true;
		}
		else
		{
			// Lookup payment object, look for order
			$pdo_statement = $this->db->prepare("SELECT * FROM `HostFact_NewOrder` WHERE `TransactionID`=:transaction_id");
			$pdo_statement->bindValue(':transaction_id', 	$transactionID);	
			
			// Execute statement
			$pdo_statement->execute();
			$result = $pdo_statement->fetch();
			
			if(isset($result->id) && $result->id > 0)
			{
				// Set properties
				$this->OrderID		= $result->id;
				$this->Type 		= 'order';
				$this->OrderCode 	= $result->OrderCode;
				
				$this->Paid			= ($result->Paid > 0) ? true : false;
				$this->Amount		= number_format($result->AmountIncl, 2, '.', '');
				return true;
			}			
		}
		
		// If we don't find the right object, return false
		return false;	
	}
	
	protected function loadConf()
	{
		// Load settings from database		
		$pdo_statement = $this->db->prepare("SELECT * FROM `HostFact_PaymentMethods` WHERE `Directory`=:payment_path");
		$pdo_statement->bindValue(':payment_path', $this->conf['PaymentDirectory']);	
			    	
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if(!$result)
		{
			// Directory not found
			fatal_error('error loading configuration','payment provider configuration not found in database');
		}
		
		$this->conf['PaymentMethodID'] 	= $result->id;
		$this->conf['MerchantID'] 		= $result->MerchantID;
		$this->conf['Password'] 		= $result->Password;
	}
	
	public static function getBackofficeSettings(){
		
		$settings = array();
		
		$settings['InternalName'] = '';
		
		$settings['MerchantID']['Title'] = "";
		$settings['MerchantID']['Value'] = "";
		
		$settings['Password']['Title'] = "";
		$settings['Password']['Value'] = "";
		
		$settings['Advanced']['Title'] = "";
		$settings['Advanced']['Image'] = "";
		$settings['Advanced']['Description'] = "";
		
		$settings['Advanced']['FeeType'] = "";
		$settings['Advanced']['FeeAmount'] = "0";
		$settings['Advanced']['FeeDesc'] = "";
		
		$settings['Advanced']['Testmode'] = "0";
		$settings['Advanced']['Extra'] = "";
		
		$settings['Hint'] = "";
		
		return $settings;		
	}
}
