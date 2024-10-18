<?php
class ideal_abn_idealonly extends Payment_Provider_Base
{
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'ideal.abn.idealonly';
		$this->conf['PaymentMethod'] 	= 'ideal';
		
		// Load parent constructor
		parent::__construct();
	
		// Load configuration
		$this->loadConf();

		$this->conf['MerchantReturnURL'] 		= IDEAL_EMAIL . 'ideal.abn.idealonly/StatReq.php';
	}

	public function startTransaction()
	{		
		// Build data string
		$data_array = array();
		
		if($this->Type == 'invoice')
		{
			$orderID		= $this->InvoiceCode;
			$description	= __('description prefix invoice').' '.$this->InvoiceCode; 
		}
		else
		{
			$orderID 		= $this->OrderCode;
			$description	= __('description prefix order').' '.$this->OrderCode; 
		}
		
		$amount	= number_format($this->Amount,2,'.','');
		
		$customer_data = $this->getCustomerData();
		
		// Update database
        $this->updateTransactionID($orderID);
		
		$string_to_hash = 'ACCEPTURL='.$this->conf['MerchantReturnURL'].$this->conf['Password']
				. 'AMOUNT='.number_format($amount*100,0,'.','').$this->conf['Password']
				. 'CANCELURL='.$this->conf['MerchantReturnURL'].$this->conf['Password']
				. (($customer_data->Initials . $customer_data->SurName) ? 'CN='.($customer_data->Initials . " " . $customer_data->SurName).$this->conf['Password'] : '')
				. 'COM='.$description.$this->conf['Password']
				. 'CURRENCY=EUR'.$this->conf['Password']
				. 'DECLINEURL='.$this->conf['MerchantReturnURL'].$this->conf['Password']
				. (($customer_data->EmailAddress) ? 'EMAIL='.getFirstMailAddress($customer_data->EmailAddress).$this->conf['Password'] : '')
				. 'EXCEPTIONURL='.$this->conf['MerchantReturnURL'].$this->conf['Password']
				. 'LANGUAGE=NL_NL'.$this->conf['Password']
				. 'ORDERID='.$orderID.$this->conf['Password']
				. (($customer_data->Address) ? 'OWNERADDRESS='.$customer_data->Address.$this->conf['Password'] : '')
				. ((str_replace("EU-","",$customer_data->Country)) ? 'OWNERCTY='.str_replace("EU-","",$customer_data->Country).$this->conf['Password'] : '')
				. (($customer_data->City) ? 'OWNERTOWN='.$customer_data->City.$this->conf['Password'] : '')
				. (($customer_data->ZipCode) ? 'OWNERZIP='.$customer_data->ZipCode.$this->conf['Password'] : '')
				. 'PM=iDEAL'.$this->conf['Password']
				. 'PSPID='.$this->conf['MerchantID'].$this->conf['Password'];

		//$url = 'https://internetkassa.abnamro.nl/ncol/test/orderstandard.asp';
		$url = 'https://internetkassa.abnamro.nl/ncol/prod/orderstandard.asp';
		?>
		<body>
		<form method="post" action="<?php echo $url; ?>" name="form">
		<input type="hidden" name="PSPID" value="<?php echo $this->conf['MerchantID']; ?>" />
		<input type="hidden" name="ORDERID" value="<?php echo $orderID; ?>" />
		<input type="hidden" name="AMOUNT" value="<?php echo number_format($amount*100,0,'.',''); ?>" />
		<input type="hidden" name="CURRENCY" value="EUR" />
		<input type="hidden" name="LANGUAGE" value="NL_NL" />
		
		<input type="hidden" name="CN" value="<?php echo $customer_data->Initials . " " . $customer_data->SurName; ?>" maxlength="35" />
		<input type="hidden" name="EMAIL" value="<?php echo getFirstMailAddress($customer_data->EmailAddress); ?>" maxlength="50" />
		<input type="hidden" name="OWNERZIP" value="<?php echo $customer_data->ZipCode; ?>" maxlength="10" />
		<input type="hidden" name="OWNERADDRESS" value="<?php echo $customer_data->Address; ?>" maxlength="255" />
		<input type="hidden" name="OWNERCTY" value="<?php echo str_replace("EU-","",$customer_data->Country); ?>"/>
		<input type="hidden" name="OWNERTOWN" value="<?php echo $customer_data->City; ?>" maxlength="25" />
		<input type="hidden" name="COM" value="<?php echo $description; ?>" />
		<input type="hidden" name="SHASIGN" value="<?php echo strtoupper(sha1($string_to_hash)); ?>" />
		
		<input type="hidden" name="PM" value="iDEAL" />
		
		<input type="hidden" name="accepturl" value="<?php echo $this->conf['MerchantReturnURL']; ?>" />
		<input type="hidden" name="declineurl" value="<?php echo $this->conf['MerchantReturnURL']; ?>" />
		<input type="hidden" name="exceptionurl" value="<?php echo $this->conf['MerchantReturnURL']; ?>" />
		<input type="hidden" name="cancelurl" value="<?php echo $this->conf['MerchantReturnURL']; ?>" />
		
		<script language="javascript" type="text/javascript">
			document.form.submit();
		</script>
		</form>
		</body><?php
		exit;
	}
	
	public function validateTransaction()
	{
		// Check SHA1-out
		$get_hash = '';
		$get_array = array();
		
		// Strtoupper keys
		foreach($_GET as $key=>$value){
			$get_array[strtoupper($key)] = $value;
		}
		// Sort keys
		ksort($get_array);
		
		// Create hash
		foreach($get_array as $key=>$value){
			if($key != "SHASIGN" && $value != ''){
				$get_hash .= strtoupper($key).'='.$value.$this->conf['Password'];
			}
		}

		// If hash isn't correct, redirect user
		if(strtoupper(sha1($get_hash)) != $get_array['SHASIGN']){
			$this->paymentStatusUnknown('We konden uw betaling nog niet verifieren. We zullen later uw betaling handmatig controleren.');
			exit;
		}
		
		// Get object
		if(!$this->getType($get_array['ORDERID']))
		{
			// No object found
			$this->paymentStatusUnknown('We konden uw betaling nog niet verifieren. We zullen later uw betaling handmatig controleren.');			
		}
		
		
		// Update database
		$this->updateTransactionID($get_array['PAYID']);
		
		if(isset($get_array['STATUS']) && in_array($get_array['STATUS'], array(5, 9)) && (!isset($get_array['NCERROR']) || !$get_array['NCERROR']))
		{
			// Update database for successfull transaction
        	$this->paymentProcessed($get_array['PAYID']);
		}
		else
		{
			$this->paymentFailed($get_array['PAYID']);
		}
	}
	
	public static function getBackofficeSettings()
	{
		$settings = array();
		
		$settings['InternalName'] = 'ABN iDEAL Only';
		
		// Partner ID
		$settings['MerchantID']['Title'] = "PSPID";
		$settings['MerchantID']['Value'] = "";
		
		$settings['Password']['Title'] = "SHA-1-IN en SHA-1-OUT";
		$settings['Password']['Value'] = "";
		
		$settings['Advanced']['Title'] = "iDEAL";
		$settings['Advanced']['Image'] = "ideal.jpg";
		$settings['Advanced']['Description'] = "Met iDEAL kunt u vertrouwd, veilig en gemakkelijk uw online aankopen afrekenen.";
		
		$settings['Advanced']['FeeType'] = "";
		$settings['Advanced']['FeeAmount'] = "0";
		$settings['Advanced']['FeeDesc'] = "Transactiekosten";
		
		$settings['Advanced']['Testmode'] = "0";
		
		return $settings;
	}
}
