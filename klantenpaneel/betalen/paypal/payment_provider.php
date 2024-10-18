<?php
class paypal extends Payment_Provider_Base
{
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'paypal';
		$this->conf['PaymentMethod'] 	= 'paypal';
		
		// Load parent constructor
		parent::__construct();
		
		// Load configuration
		$this->loadConf();
		
		//$this->conf['paypal_url']	= "https://www.sandbox.paypal.com/cgi-bin/webscr";
		$this->conf['paypal_url']	= "https://www.paypal.com/cgi-bin/webscr";
		
		$this->conf['return_url'] 	= IDEAL_EMAIL . 'paypal/return.php';
		$this->conf['cancel_url'] 	= IDEAL_EMAIL . 'paypal/cancel.php';
		$this->conf['ipn_url'] 		= IDEAL_EMAIL . 'paypal/ipn.php';
	}
	
	
	public function startTransaction()
	{		

		if($this->Type == 'invoice')
		{
			$orderID		= $this->InvoiceCode;
			$description	= __('description prefix invoice').' '.$this->InvoiceCode; 
			
			$_SESSION['paypal_session']['id'] 	= $this->InvoiceID;
			$_SESSION['paypal_session']['type'] = $this->Type;
		}
		else
		{
			$orderID		= $this->OrderCode;
			$description	= __('description prefix order').' '.$this->OrderCode; 
			
			$_SESSION['paypal_session']['id'] 	= $this->OrderID;
			$_SESSION['paypal_session']['type'] = $this->Type;
		}
		
		
		$amount					= number_format($this->Amount,2,'.','');
		$temp_transaction_id 	= md5($orderID);
		
		// Update database
        $this->updateTransactionID($temp_transaction_id);
        
		
		// Get customer data
		$customer_data = $this->getCustomerData();
		
		// Create script
		?><body>
		<form name="form" action="<?php echo $this->conf['paypal_url']; ?>" method="post">
			<input type="hidden" name="charset" value="utf-8" />
			<input type="hidden" name="cmd" value="_cart" />
  			<input type="hidden" name="upload" value="1" />
  			<input type="hidden" name="notify_url" value="<?php echo $this->conf['ipn_url']; ?>" />
  			<input type="hidden" name="return" value="<?php echo $this->conf['return_url']; ?>" />
  			<input type="hidden" name="rm" value="2" />
  			<input type="hidden" name="cancel_return" value="<?php echo $this->conf['cancel_url']; ?>" />
  			
  			<input type="hidden" name="currency_code" value="<?php echo CURRENCY_CODE; ?>" />
  			<input type="hidden" name="invoice" value="<?php echo $orderID; ?>" />
  			<input type="hidden" name="amount_1" value="<?php echo $amount; ?>" />
  			<input type="hidden" name="item_name_1" value="<?php echo $description; ?>" />
  			
  			<input type="hidden" name="business" value="<?php echo $this->conf['MerchantID']; ?>" />
  			
  			<input type="hidden" name="address1" value="<?php echo $customer_data->Address; ?>" />
			<input type="hidden" name="city" value="<?php echo $customer_data->City; ?>" />
			<input type="hidden" name="country" value="<?php echo $customer_data->Country; ?>" />
			<input type="hidden" name="email" value="<?php echo getFirstMailAddress($customer_data->EmailAddress); ?>" />
			<input type="hidden" name="first_name" value="<?php echo $customer_data->Initials; ?>" />
			<input type="hidden" name="last_name" value="<?php echo $customer_data->SurName; ?>" />
			<input type="hidden" name="zip" value="<?php echo $customer_data->ZipCode; ?>" />
		</form>
		<script type="text/javascript">
			document.form.submit();
		</script></body>
		<?php
		exit;
	}
	
	public function validateTransaction($transactionID)
	{
		
		if($this->isNotificationScript === true)
		{
			// 1. Verify emailaddress
			if($_POST['receiver_email'] != $this->conf['MerchantID'])
			{
				return false;
			}
			
			// 2. Verify PayPal as Sender
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->conf['paypal_url'].'?cmd=_notify-validate&'.http_build_query($_POST));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);
						
			if(strtolower($result) != 'verified')
			{
				return false;
			}
		 	
			if($this->getType(md5($_POST['invoice'])))
			{
				// First response of paypal IPN, update transaction ID
				$this->updateTransactionID($transactionID);
			}
			
			// Try to get transaction based on transactionID
			$this->getType($transactionID);
			
			// 3. Verify that the outstanding amount is at least paid, else skip this transaction validation.
			if($_POST['mc_gross'] < $this->Amount)
			{
				return false;
			}
			
			// IPN scenarios
			switch(strtolower($_POST['payment_status']))
			{
				case 'pending':
					// Update invoice
					if($this->Type == 'invoice' && isset($this->InvoiceID) && $this->InvoiceID > 0 && $this->InvoiceCode)
					{
						// Add line to logile
						$pdo_statement = $this->db->prepare("INSERT INTO `HostFact_Log` (`Date`, `Type`, `Reference`, `Who`, `Action`, `Values`, `Translate`, `Page`) VALUES (NOW(), 'invoice', :invoice_id, 0, :action, '', 'no', '')");
						$pdo_statement->bindValue(':invoice_id',	$this->InvoiceID);	
						$pdo_statement->bindValue(':action',		'PayPal transaction pending: ' . strtolower($_POST['pending_reason']));
						$pdo_statement->execute();
						exit;							
					}
					elseif($this->Type == 'order' && isset($this->OrderID) && $this->OrderID > 0 && $this->OrderCode)
					{
						// We can't add information to order	
					}
					break;
				case 'processed':
				case 'completed':
					// Update database for successfull transaction
	        		$this->paymentProcessed($transactionID);
					break;
				case 'denied':
				case 'expired':
				case 'failed':
				case 'reversed':
					// Update database for failed transaction
					$this->paymentFailed($transactionID);
					break;
			}
		}
		else
		{
			// For consumer
			
			// Get transaction object if transaction ID is given
			if($transactionID){
				$this->getType($transactionID);	
			}
			
			if($this->Paid > 0)
			{
				if($this->Type 	== 'invoice')
				{
					$_SESSION['payment']['type'] 			= 'invoice';
					$_SESSION['payment']['id'] 				= $this->InvoiceID;
				}
				elseif($this->Type 	== 'order')
				{
					$_SESSION['payment']['type'] 			= 'order';
					$_SESSION['payment']['id'] 				= $this->OrderID;
				}
					
				// Because type is found, we know it is paid
				$_SESSION['payment']['status'] 			= 'paid';
				$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
				$_SESSION['payment']['transactionid'] 	= $transactionID;
				$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');				
			}
			else
			{
				$_SESSION['payment']['status'] 			= 'pending';
				$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
				$_SESSION['payment']['transactionid'] 	= $transactionID;
				$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');		
			}					
			
			
			header("Location: ".IDEAL_EMAIL);
			exit;
			
		}
		
	}
	
	public static function getBackofficeSettings()
	{
		$settings = array();
		
		$settings['MerchantID']['Title'] = "E-mailadres";
		$settings['MerchantID']['Value'] = "";
		
		$settings['Advanced']['Title'] = "PayPal";
		$settings['Advanced']['Image'] = "paypal.jpg";
		$settings['Advanced']['Description'] = "Met PayPal betaalt u snel en veilig online.";
		
		$settings['Advanced']['FeeType'] = "";
		$settings['Advanced']['FeeAmount'] = "0";
		$settings['Advanced']['FeeDesc'] = "Transactiekosten";
		
		$settings['Advanced']['Testmode'] = "0";
		
		return $settings;
	}
}