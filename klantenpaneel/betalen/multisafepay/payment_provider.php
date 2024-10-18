<?php
class multisafepay extends Payment_Provider_Base
{
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'multisafepay';
		$this->conf['PaymentMethod'] 	= 'other';
		
		// Load parent constructor
		parent::__construct();
	
		// Load configuration
		$this->loadConf();
		
		//$this->conf['api_url']	= "https://testapi.multisafepay.com/ewx/";
		$this->conf['api_url']	= "https://api.multisafepay.com/ewx/";
		
		$this->conf['notify_url'] 	= IDEAL_EMAIL . 'multisafepay/notify.php';
		$this->conf['redirect_url'] 	= IDEAL_EMAIL . 'multisafepay/return.php';		

		// Specific configuration for this integration
		$user_info = explode(";",$this->conf['MerchantID'],2);
		$this->conf['merchant_account'] 	= (isset($user_info[1])) ? $user_info[0] : 1;
		$this->conf['site_id'] 				= (isset($user_info[1])) ? $user_info[1] : $user_info[0];
		
		// Specific configuration for this integration
		$this->chosen_payment_method = (isset($_SESSION['multisafepay_payment_method'])) ? $_SESSION['multisafepay_payment_method'] : 0;
	

	}
	
	public function choosePaymentMethod()
	{
		
		// generate request
		$request  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$request .= "<gateways ua=\"example-php-1.1\">\n";
		$request .= "  <merchant>\n";
		$request .= "    <account>" .          $this->xml_escape($this->conf['merchant_account']) . "</account>\n";
		$request .= "    <site_id>" .          $this->xml_escape($this->conf['site_id']) . "</site_id>\n";
		$request .= "    <site_secure_code>" . $this->xml_escape($this->conf['Password']) . "</site_secure_code>\n";
		$request .= "  </merchant>\n";
		$request .= "  <customer>\n";
		$request .= "    <country></country>\n";
		$request .= "  </customer>\n";
		$request .= "</gateways>\n";
		
		$gateways = array();
		
		if ($reply = $this->xml_post_curl($this->conf['api_url'], $request, $error)) {
			$matches = array();
			
			// check transaction status
			preg_match('/\<gateways result="(.*)"\>/U', $reply, $matches);
			
			if (!empty($matches) && $matches[1] == 'ok') {
				$matches = array();
				
				// get redirect URL
				$gatewayCount = preg_match_all('/\<gateway\>\s*\<id\>(.*)\<\/id\>\s*\<description\>(.*)\<\/description\>\s*\<\/gateway\>/U', $reply, $matches);
		
				for ($i = 0; $i < $gatewayCount; $i++) {
					$gateways[$matches[1][$i]] = $matches[2][$i];
				}
			} else {
				// get error message
				$matches = array();
				preg_match('/\<error\>.*\<description\>(.*)\<\/description\>.*\<\/error\>/U', $reply, $matches);
				
				if ($matches > 0) {
					$error = $this->xml_unescape($matches[1]);
				}
			}
		}
		
		$html  = "<select name=\"multisafepay_payment_method\" class=\"issuerID\">";
		$html  .= "<option value=\"0\">Kies een betaalmethode...</option>";
		
		foreach ($gateways as $id => $description){
			$html  .= "<option value=\"".htmlspecialchars($id)."\">".htmlspecialchars($description)."</option>";
		}
		
		$html  .= "</select>";

		return $html;
	}
	
	public function validateChosenPaymentMethod()
	{
		if(isset($_POST['multisafepay_payment_method']) && $_POST['multisafepay_payment_method'])
		{
			$this->chosen_payment_method = $_POST['multisafepay_payment_method'];
			$_SESSION['multisafepay_payment_method'] = $this->chosen_payment_method;
			return true;
		}
		elseif(!isset($_POST['multisafepay_payment_method']) && isset($_SESSION['multisafepay_payment_method']) && $_SESSION['multisafepay_payment_method'])
		{
			$this->chosen_payment_method = $_SESSION['multisafepay_payment_method'];
			return true;
		}
		else
		{
			$this->Error = 'U heeft geen betaalmethode gekozen.';
		}
		
		return false;
		
	}

	public function startTransaction()
	{		
		// Build data string
		$data_array = array();
		
		if($this->Type == 'invoice')
		{
			$data_array['transaction']['id']			= $this->InvoiceCode . '-'. substr(md5(date('YmdHis')),0,6);
			$data_array['transaction']['description'] 	= __('description prefix invoice').' '.$this->InvoiceCode; 
		}
		else
		{
			$data_array['transaction']['id']			= $this->OrderCode . '-'. substr(md5(date('YmdHis')),0,6);
			$data_array['transaction']['description'] 	= __('description prefix order').' '.$this->OrderCode; 
		}
		
		$amount	= number_format($this->Amount,2,'.','');
		
		$data_array['transaction']['currency']    = CURRENCY_CODE;
		$data_array['transaction']['gateway']     = $this->chosen_payment_method;
		$data_array['transaction']['amount']      = $amount*100;
		
		// calculate signature
		$signature = md5($data_array['transaction']['amount'].$data_array['transaction']['currency'].$this->conf['merchant_account'].$this->conf['site_id'].$data_array['transaction']['id']);
		
		
		// merchant details (private)
		$data_array['merchant']['account']          = $this->conf['merchant_account'];
		$data_array['merchant']['site_id']          = $this->conf['site_id'];
		$data_array['merchant']['site_secure_code'] = $this->conf['Password'];
		$data_array['merchant']['notification_url'] = $this->conf['notify_url'];
		$data_array['merchant']['redirect_url']	  	= $this->conf['redirect_url'];
		$data_array['merchant']['close_window']	  	= false;
		
		// Update database
        $this->updateTransactionID($data_array['transaction']['id']);
		
		// Get customer data
		$customer_data = $this->getCustomerData();
		
		$data_array['customer']['locale']      = 'nl_NL';
		$data_array['customer']['ipaddress']   = $_SERVER['REMOTE_ADDR'];
		$data_array['customer']['forwardedip'] = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
		$data_array['customer']['firstname']   = $customer_data->Initials;
		$data_array['customer']['lastname']    = $customer_data->SurName;
		$data_array['customer']['address1']    = $customer_data->Address;
		$data_array['customer']['address2']    = '';
		$data_array['customer']['housenumber'] = '';
		$data_array['customer']['zipcode']     = $customer_data->ZipCode;
		$data_array['customer']['city']        = $customer_data->City;
		$data_array['customer']['state']       = '';
		$data_array['customer']['country']     = $customer_data->Country;
		$data_array['customer']['phone']       = '';
		$data_array['customer']['email']       = getFirstMailAddress($customer_data->EmailAddress);

		
		// generate request
		$request  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$request .= "<redirecttransaction ua=\"custom-1.2\">\n";
		$request .= $this->xml_array_to_xml($data_array['merchant'], 'merchant');
		$request .= $this->xml_array_to_xml($data_array['customer'], 'customer');
		$request .= $this->xml_array_to_xml($data_array['transaction'], 'transaction');
		$request .= "<signature>".$signature."</signature>\n";
		$request .= "</redirecttransaction>\n";
		
		// start transaction
		$error = '';
		
		if ($reply = $this->xml_post_curl($this->conf['api_url'], $request, $error)) {
			
			$matches = array();
		
			// check transaction status
			preg_match('/\<redirecttransaction result="(.*)"\>/U', $reply, $matches);
		
			if (!empty($matches) && $matches[1] == 'ok') {
				$matches = array();
		
				// get redirect URL
				preg_match('/\<payment_url\>(.*)\<\/payment_url\>/U', $reply, $matches);
				
				if (!empty($matches)) {
					header('Location: ' . $this->xml_unescape($matches[1]));
					exit;
				} else {
					$error = 'Unable to redirect user.';
				}
			}
		
			// get error message
			$matches = array();
			preg_match('/\<error\>.*\<description\>(.*)\<\/description\>.*\<\/error\>/U', $reply, $matches);
		
			if ($matches > 0) {
				$error = $this->xml_unescape($matches[1]);
				$this->paymentStatusUnknown('Er is helaas iets misgegaan. Foutmelding van MultiSafepay: '.$error);
				exit;
			}
			exit;
		}

	}
	
	public function validateTransaction($transactionID)
	{
		// Try to get transaction based on transactionID
		$this->getType($transactionID);
			
		if($this->isNotificationScript === true)
		{
			$data_array = array();
			$data_array['merchant']['account']          = $this->conf['merchant_account'];
			$data_array['merchant']['site_id']          = $this->conf['site_id'];
			$data_array['merchant']['site_secure_code'] = $this->conf['Password'];
	
			$data_array['transaction']['id'] 			= $this->xml_escape($transactionID);
			
			// generate request
			$request  = "<?xml version=\"1.2\" encoding=\"UTF-8\"?>\n";
			$request .= "<status ua=\"example-1.1\">\n";
			$request .= $this->xml_array_to_xml($data_array['merchant'], 'merchant');
			$request .= $this->xml_array_to_xml($data_array['transaction'], 'transaction');
			$request .= "</status>\n";
			
			// get transaction status
			$error = '';
			
			if ($reply = $this->xml_post_curl($this->conf['api_url'], $request, $error)) {
				$dom = new domDocument;
				$dom->loadXML($reply);
				if (!$dom) {
				    echo 'Error while parsing the document';
				    exit;
				}
				$s = simplexml_import_dom($dom);
			} else {
				echo 'Error: '.$error;
				exit;
			}
			
			switch($s->ewallet[0]->status)
			{
				case 'completed':
					// Update database for successfull transaction
		        	$this->paymentProcessed($transactionID);
					break;
				case 'void':
				case 'declined':
				case 'expired':
					// Update database for failed transaction
					$this->paymentFailed($transactionID);
					break;
			}
		}
		else
		{
			// For consumer			
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
		
		$settings['InternalName'] = 'MultiSafePay';
		
		// Partner ID
		$settings['MerchantID']['Title'] = "Merchant account / Site ID";
		$settings['MerchantID']['Value'] = "account;site_id";
		
		// Site secure code
		$settings['Password']['Title'] = "Site secure code";
		$settings['Password']['Value'] = "";
		
		$settings['Advanced']['Title'] = "MultiSafepay";
		$settings['Advanced']['Image'] = "multisafepay.jpg";
		$settings['Advanced']['Description'] = "Met MultiSafepay kunt u vertrouwd, veilig en gemakkelijk uw online aankopen afrekenen.";
		
		$settings['Advanced']['FeeType'] = "";
		$settings['Advanced']['FeeAmount'] = "0";
		$settings['Advanced']['FeeDesc'] = "Transactiekosten";
		
		$settings['Advanced']['Testmode'] = "0";
		$settings['Advanced']['Extra'] = "Kies een betaalmethode: ";
		
		$settings['Hint'] = "In het controlepaneel bij MultiSafePay dient u eerst een site aan te maken onder 'Settings' > 'Sites'.<br /><br />Vul in het veld 'Merchant account / Site ID' beide waarden in gescheiden door een ; ";
		
		return $settings;
	}
	
	function xml_escape($str)
	{
		return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
	}
	
	function xml_unescape($str)
	{
		return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
	}
	
	function xml_array_to_xml($arr, $name)
	{
		$data = "<{$name}>\n";
	
		foreach ($arr as $key => $value) {
			$value = $this->xml_escape($value);
			$data .= "<{$key}>{$value}</{$key}>\n";
		}
	
		$data .= "</{$name}>\n";
	
		return $data;
	}
	
	function xml_post_curl($url, $request_data, &$error_message, $verify_peer = false)
	{
		$parsed_url = parse_url($url);
	
		if (empty($parsed_url['port'])) {
			$parsed_url['port'] = strtolower($parsed_url['scheme']) == 'https' ? 443 : 80;
		}
	
		$real_url = $parsed_url['scheme'] . "://" . $parsed_url['host'] . ":" . $parsed_url['port'] . "/";
	
		// generate request
		$header  = "POST " . $parsed_url['path'] ." HTTP/1.1\r\n";
		$header .= "Host: " . $parsed_url['host'] . "\r\n";
		$header .= "Content-Type: text/xml\r\n";
		$header .= "Content-Length: " . strlen($request_data) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $request_data;
	
		// issue request
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_peer);
		curl_setopt($ch, CURLOPT_URL,            $real_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,        30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $request);
	
		$reply_data = curl_exec($ch);
	
		// check response
		if (curl_errno($ch)) {
			$error_message = curl_error($ch);
			return false;
		}
	
		$reply_info = curl_getinfo($ch);
		curl_close($ch);
	
		if ($reply_info['http_code'] != 200) {
			$error_message = 'HTTP code is ' . $reply_info['http_code'] . ', expected 200';
			return false;
		}
	
		if (strstr($reply_info['content_type'], "/xml") === false) {
			$error_message = 'Content type is ' . $reply_info['content_type'] . ', expected */xml';
			return false;
		}
	
		return $reply_data;
	}
}