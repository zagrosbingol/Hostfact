<?php


class PaymentForm_Controller extends Base_Controller
{
	private $payment_method;
	private $payment_object;

	public function __construct()
	{	
		$this->payment_method 	= new PaymentMethod_Model;
	}
	
	public function index()
	{
		// Get payment methods
		$payment_method_list = $this->payment_method->listPaymentMethods();

		// Get payment objects
		$this->payment_object = (isset($_GET['payment']) && isset($_GET['key'])) ? $this->payment_method->getInvoice(strtolower($_GET['payment']), $_GET['key']) : FALSE;

		// If no open invoice is found, return error
		if($this->payment_object === FALSE)
		{
			if(isset($_GET['payment']))
			{
				$this->set('error_message', sprintf(__('cannot find open invoice with number'), htmlspecialchars($_GET['payment'])));
			}
			else
			{
				$this->set('error_message', __('cannot find open invoice'));
			}

			// Load template
			$this->display('global.phtml');
			exit;

			// If the invoice status is 4, return Invoice already paid
		}
		elseif($this->payment_object->Status == 4 || ($this->payment_object->AmountIncl - $this->payment_object->AmountPaid) < 0)
		{
			$this->set('error_message', __('error invoice already paid'));

			// Load template
			$this->display('global.phtml');
			exit;
		}
		// If the invoice has direct debit and batch is already downloaded, do not allow payment
		elseif($this->payment_object->Authorisation == 'yes' && $this->payment_object->TransactionID)
		{
			$this->set('error_message', __('error invoice already in direct debit batch'));

			// Load template
			$this->display('global.phtml');
			exit;
		}

		// Start payment
		if(isset($_POST['start_payment']) && $_POST['start_payment'] == 'yes')
		{
			$this->start_payment();
		}
		elseif(version_compare(PHP_VERSION, '7.0.0', '<') && function_exists('mysql_connect'))
		{
			// Backward compatible with older versions
			// Temp MySQL fix, because of integration with older payment gateway
			if(defined("DB_CRYPT") && DB_CRYPT){
		        @mysql_connect(db_decrypt(DB_HOST), db_decrypt(DB_USERNAME),db_decrypt(DB_PASSWORD));
		        @mysql_select_db(db_decrypt(DB_NAME));
		    }else{
		        @mysql_connect(DB_HOST, DB_USERNAME,DB_PASSWORD);
		        @mysql_select_db(DB_NAME);
		    }
		}
        
		// Show payment methods
		$this->set('payment_object',  		$this->payment_object);	
		$this->set('payment_method_list',  	$payment_method_list);
		$this->set('form_action', '?payment='.htmlspecialchars($_GET['payment']).'&amp;key='.htmlspecialchars($_GET['key']));
        
        $this->set('company_data',  		Setting_Model::getInstance()->get('company_data'));
		
		// Load template	
		$this->display('paymentform.phtml');
	}
	
	private function start_payment()
	{
		$payment_method_list = $this->payment_method->listPaymentMethods();
		
		// Start payment
		if(isset($_POST['payment_method']) && isset($payment_method_list[$_POST['payment_method']]))
		{
			
			$method = $payment_method_list[$_POST['payment_method']];
	
			// If payment method has payment provider class, initiate
			if(file_exists($method['DIRECTORY']."/payment_provider.php"))
			{
				include_once $method['DIRECTORY']."/payment_provider.php";
				
				$tmp_payment_provider = new $method['CLASS'];
				
				// Set invoice
				$tmp_payment_provider->setInvoice($this->payment_object->id);
				
				// Validate the chosen paymentmethod (like selecting a bank)
				if($tmp_payment_provider->validateChosenPaymentMethod())
				{
					// On success, start transaction and redirect
					$result = $tmp_payment_provider->startTransaction();
					exit;
				}
				else
				{
					// If validate returns false, return error message
					$this->set('error_message', $tmp_payment_provider->Error);
					return false;
				}
			}
			
			// Backward compatible with older versions
			// Copy object to $invoice
			$invoice = $this->payment_object;
			$invoice->Identifier = $invoice->id;
			
			// Temp MySQL fix, because of integration with older payment gateway
			if(version_compare(PHP_VERSION, '7.0.0', '<') && function_exists('mysql_connect'))
			{
				if (defined("DB_CRYPT") && DB_CRYPT) {
					@mysql_connect(db_decrypt(DB_HOST), db_decrypt(DB_USERNAME), db_decrypt(DB_PASSWORD));
					@mysql_select_db(db_decrypt(DB_NAME));
				} else {
					@mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
					@mysql_select_db(DB_NAME);
				}
			}

			switch(substr($_POST['payment_method'],0,4)){
				case 'idea':
					// Do whe need issuer ID?
					if((file_exists($method['DIRECTORY']."/DirReq.php") && ((isset($_POST['issuerID']) && !$_POST['issuerID']) || (isset($_POST['issuerID-'.$_POST['payment_method']]) && !$_POST['issuerID-'.$_POST['payment_method']]))))
					{
						$this->set('error_message', __('please select a bank'));	
					}
					else
					{
						if(!isset($_POST['issuerID']) || !$_POST['issuerID']){
							if(isset($_POST['issuerID-'.$_POST['payment_method']])){
								$_POST['issuerID'] = $_POST['issuerID-'.$_POST['payment_method']];
							}else{
								$_POST['issuerID'] ='';
							}
						}

						require_once $method['DIRECTORY']."/TransReq2.php";
						exit;				
					}			
					break;
			}
		}
		elseif(isset($_POST['start_payment']) && $_POST['start_payment'] == 'yes')
		{
			// No payment method selected
			$this->set('error_message', __('please select a payment method'));
			return false;
		}
	}
	
	/**
	 * PaymentForm_Controller::paid()
	 * Show succesfull payment message
	 * @return void
	 */
	public function paid()
	{
		// Get payment object
		if(isset($_SESSION['payment']['type']) && $_SESSION['payment']['type'] == 'invoice')
		{
			$payment_object 		= $this->payment_method->getInvoiceByID($_SESSION['payment']['id']);
			
			// Parse object to template
			$this->set('payment_object',  $payment_object);
		}
		elseif(isset($_SESSION['payment']['type']) && $_SESSION['payment']['type'] == 'order')
		{
			$payment_object 		= $this->payment_method->getOrderByID($_SESSION['payment']['id']);
			
			// Parse object to template
			$this->set('payment_object',  $payment_object);
		}
		
		// Get payment method name
		$paymentmethod_name = false;
		if($payment_object->PaymentMethodID > 0)
		{
			$paymentmethod_name = $this->payment_method->getPaymentMethodByID($payment_object->PaymentMethodID);
		}
		
		if($paymentmethod_name)
		{
			$this->set('paymentmethod',  	$paymentmethod_name);
		}
		else
		{
			$array_paymentmethod = array(	"auth" => __('paymentmethod type auth'),
											"paypal" => __('paymentmethod type paypal'),
											"ideal" => __('paymentmethod type ideal'),
										 	"other" => __('paymentmethod type other'));
 			$this->set('paymentmethod',  	(isset($_SESSION['payment']['paymentmethod']) && isset($array_paymentmethod[$_SESSION['payment']['paymentmethod']])) ? $array_paymentmethod[$_SESSION['payment']['paymentmethod']] : $array_paymentmethod['other']);
		}
 		
 		// Set variables
 		$date_format = DATE_FORMAT . ' ' . __('at') . ' %H:%i';
 		
 		$this->set('paymentdate',  		(isset($_SESSION['payment']['date'])) ? rewrite_date_db2site($_SESSION['payment']['date'], $date_format) : '');
 		$this->set('transactionid',  	(isset($_SESSION['payment']['transactionid'])) ? htmlspecialchars($_SESSION['payment']['transactionid']) : '');
		
		// Load template
		if(isset($_SESSION['payment']['paymentmethod']) && $_SESSION['payment']['paymentmethod'] == 'auth')
		{
			$this->display('paid.phtml', 'payment.auth');
		}
		else
		{
			$this->display('paid.phtml');
		}
	}
	
	/**
	 * PaymentForm_Controller::pending()
	 * Show pending payment message
	 * @return void
	 */
	public function pending()
	{
		// Get payment object
		if(isset($_SESSION['payment']['type']) && $_SESSION['payment']['type'] == 'invoice')
		{
			$payment_object 		= $this->payment_method->getInvoiceByID($_SESSION['payment']['id']);
			
			// Parse object to template
			$this->set('payment_object',  $payment_object);
		}
		elseif(isset($_SESSION['payment']['type']) && $_SESSION['payment']['type'] == 'order')
		{
			$payment_object 		= $this->payment_method->getOrderByID($_SESSION['payment']['id']);
			
			// Parse object to template
			$this->set('payment_object',  $payment_object);
		}
		
		// Get payment method name
		$paymentmethod_name = false;
		if($payment_object->PaymentMethodID > 0)
		{
			$paymentmethod_name = $this->payment_method->getPaymentMethodByID($payment_object->PaymentMethodID);
		}
		
		if($paymentmethod_name)
		{
			$this->set('paymentmethod',  	$paymentmethod_name);
		}
		else
		{
			$array_paymentmethod = array(	"auth" => __('paymentmethod type auth'),
											"paypal" => __('paymentmethod type paypal'),
											"ideal" => __('paymentmethod type ideal'),
										 	"other" => __('paymentmethod type other'));
 			$this->set('paymentmethod',  	(isset($_SESSION['payment']['paymentmethod']) && isset($array_paymentmethod[$_SESSION['payment']['paymentmethod']])) ? $array_paymentmethod[$_SESSION['payment']['paymentmethod']] : $array_paymentmethod['other']);
		}
			
 		
 		// Set variables
 		$date_format = DATE_FORMAT . ' ' . __('at') . ' %H:%i';
	
 		$this->set('paymentdate',  		(isset($_SESSION['payment']['date'])) ? rewrite_date_db2site($_SESSION['payment']['date'], $date_format) : '');
 		$this->set('transactionid',  	(isset($_SESSION['payment']['transactionid'])) ? htmlspecialchars($_SESSION['payment']['transactionid']) : '');
		
		// Load template
		$this->display('pending.phtml');
	}
	
	/**
	 * PaymentForm_Controller::failed()
	 * Show failed payment message
	 * @return void
	 */
	public function failed()
	{
		
		$error_message  = '<strong>'.__('your payment has failed').'</strong><br />';
		
		// If we have an error from payment provider, use this one
		if(isset($_SESSION['payment']['error_message']))
		{
			$error_message .= '<p>'.$_SESSION['payment']['error_message'].'</p>';
			unset($_SESSION['payment']['error_message']);
		}

        $company_data    = Setting_Model::getInstance()->get('company_data');
		$error_message  .= '<p>'.sprintf(__('try again or contact us'),'<a href="mailto:'.htmlspecialchars($company_data->EmailAddress).'">'.htmlspecialchars($company_data->EmailAddress).'</a>').'</p>';
		
		// Parse message to template
		$this->set('error_message', $error_message);
		
		// Load object
		if(isset($_SESSION['payment']['type']) && $_SESSION['payment']['type'] == 'invoice')
		{
			$this->payment_object			= (isset($_SESSION['payment']['id'])) ? $this->payment_method->getInvoiceByID($_SESSION['payment']['id']) : false;
		
			// Get payment methods
			$payment_method_list 			= $this->payment_method->listPaymentMethods();
			
			
			// Start payment
			if(isset($_POST['payment_method']) && isset($payment_method_list[$_POST['payment_method']]))
			{
				$this->start_payment();
			}
		}
		
		if(isset($this->payment_object) && $this->payment_object !== false)
		{
			// Parse object to template
			$this->set('payment_object',  		$this->payment_object);
			$this->set('payment_method_list',  	$payment_method_list);
			$this->set('form_action', 			'?');
            
            $this->set('company_data',  		Setting_Model::getInstance()->get('company_data'));
			
			// Load template
			$this->display('paymentform.phtml');
		}
		else
		{
			// Load template
			$this->display('global.phtml');
		}
	}
}