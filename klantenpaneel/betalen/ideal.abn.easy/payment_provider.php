<?php
class ideal_abn_easy extends Payment_Provider_Base
{
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'ideal.abn.easy';
		$this->conf['PaymentMethod'] 	= 'ideal';
		
		// Load parent constructor
		parent::__construct();
	
		// Load configuration
		$this->loadConf();
	}

	public function startTransaction()
	{				
		if($this->Type == 'invoice')
		{
			$orderID		= $this->InvoiceCode;
			$description	= __('description prefix invoice').' '.$this->InvoiceCode; 
		}
		else
		{
			$orderID		= $this->OrderCode;
			$description	= __('description prefix order').' '.$this->OrderCode; 
		}
		
		$customer_data = $this->getCustomerData();
		
		$amount	= number_format($this->Amount,2,'.','');
				
		//$url = 'https://internetkassa.abnamro.nl/ncol/test/orderstandard.asp';
		$url = 'https://internetkassa.abnamro.nl/ncol/prod/orderstandard.asp';
		?><body>
		<form method="post" action="<?php echo $url; ?>" name="form">
		<input type="hidden" name="PSPID" value="<?php echo $this->conf['MerchantID'] ; ?>" />
		<input type="hidden" name="orderID" value="<?php echo $orderID; ?>" />
		<input type="hidden" name="amount" value="<?php echo number_format($amount*100,0,'.',''); ?>" />
		<input type="hidden" name="COM" value="<?php echo $description; ?>" />
		<input type="hidden" name="currency" value="EUR" />
		<input type="hidden" name="language" value="<?php echo strtoupper(LANGUAGE); ?>" />
		<input type="hidden" name="PM" value="iDEAL" />
		<input type="hidden" name="CN" value="<?php echo $customer_data->Initials . " " . $customer_data->SurName; ?>" maxlength="35" />
		<input type="hidden" name="EMAIL" value="<?php echo getFirstMailAddress($customer_data->EmailAddress); ?>" maxlength="50" />
		<input type="hidden" name="owneraddress" value="<?php echo $customer_data->Address; ?>" maxlength="255" />
		<input type="hidden" name="ownertown" value="<?php echo $customer_data->City; ?>" maxlength="25" />
		<input type="hidden" name="ownerzip" value="<?php echo $customer_data->ZipCode; ?>" maxlength="10" />
		<input type="hidden" name="ownercty" value="<?php echo $customer_data->Country; ?>"/>
		<script language="javascript" type="text/javascript">
			document.form.submit();
		</script>
		</form></body>
		<?php
		exit;
	}
		
	public static function getBackofficeSettings()
	{
		$settings = array();
		
		$settings['InternalName'] = 'ABN iDEAL Easy';
		
		// Partner ID
		$settings['MerchantID']['Title'] = "PSPID";
		$settings['MerchantID']['Value'] = "";
				
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
