<?php

class OrderForm_Controller extends Base_Controller
{
	public $lang = array();
	
	private $_vars = array();

	public function __construct()
	{		
			
		// Load parent constructor
		parent::__construct();	
			
		// Create instances of uses objects	
		$this->order = new Order_Model();
		$this->element 	= new OrderElement_Model();		
		
		if(PERIOD_CHOICE == 'default')
		{
			$this->order->set('PricePeriod', PERIOD_DEFAULT_PERIODS . PERIOD_DEFAULT_PERIODIC);
		}
		elseif(PERIOD_CHOICE == 'yes' && !$this->order->get('PricePeriod'))
		{
			$this->order->set('PricePeriod', PERIOD_DEFAULT_PERIODS . PERIOD_DEFAULT_PERIODIC);
		}
		
		// If user is already logged in in customer panel, also login into orderform
		if(isset($_SESSION['User']) && isset($_SESSION['Username']) && $_SESSION['User'] > 0 && is_null($this->order->get('ExistingCustomer'))){
		
			$this->debtor 	= new Debtor_Model();
			$this->debtor->set('Username', (isset($_SESSION['Username'])) ? $_SESSION['Username'] : '');
			$this->debtor->set('Password', (isset($_SESSION['Password'])) ? $_SESSION['Password'] : '');
            $this->debtor->set('SecurePassword', (isset($_SESSION['SecurePassword'])) ? $_SESSION['SecurePassword'] : '');
			
			// Check username and password
			if($this->debtor->checkLogin())
			{
                // Regenerate session id for security.
                session_regenerate_id(true);

				$this->order->set('ExistingCustomer', 'yes');

				// Activate direct debit for debtors with authorisation (if paymentmethod is visible)
				$payment_methods = $this->order->_settings->get('array_paymentmethods');
					if(isset($payment_methods['auth']))
					{
					$this->debtor->show();
					if($this->debtor->InvoiceAuthorisation == 'yes')
					{
						$this->order->set('PaymentMethod', 'auth');
						$this->order->set('AccountNumber', $this->debtor->AccountNumber);
						$this->order->set('AccountBIC', $this->debtor->AccountBIC);
						$this->order->set('AccountName', $this->debtor->AccountName);
						$this->order->set('AccountCity', $this->debtor->AccountCity);
					}
				}
			}
			else
			{
				$this->debtor->set('Username','');
				$this->debtor->set('Password','');
				$this->debtor->set('SecurePassword','');
				$this->order->set('ExistingCustomer', 'no');
			}
		}		
	}
	
	function start()
	{	
		
		// Check also for GET variables
		if(isset($_GET['product']))
		{
			$this->element->newItem('Product');
			$this->element->setAttribute('ProductCode', $_GET['product']);
			$this->element->saveItem();	
		}
		
		
		// Default orderform doesn't have an extra step
		return $this->details();
	}
	
	
	function details()
	{
		// Get products and pass to view
		$list_products = $this->element->getProductsFromGroup(GROUP_PRODUCTS);
		$this->set('list_products', $list_products);
		
		// Handle other POST data from domainform
		if(isset($_POST['step']))
		{
		
			// Set period
			global $period_choice_options;
			if(PERIOD_CHOICE == 'yes' && isset($_POST['BillingPeriod']) && in_array($_POST['BillingPeriod'], $period_choice_options))
			{
				$this->order->set('PricePeriod', $_POST['BillingPeriod']);
			}
		
			// Store product in session
			$this->element->newItem('Product');
			$this->element->setAttribute('ProductCode', $_POST['Product']);
			$this->element->saveItem();
			
			// We do ofcourse need a product code to be selected
			if(!isset($_POST['Product']) || !$_POST['Product'])
			{
				$this->Error[] = __('you need to select a product');
			}
			
			// Handling options
			$this->__handleOptions();	
							
			// Depending on errors, go to next step or display same page again
			if(empty($this->Error))
			{
				// If we don't have any errors, go to the next step
				unset($_POST['step']);
				return $this->customer();
			}
			else
			{
				// Display same page again, but pass errors to view
				$this->set('errors', $this->Error);	
			}
		}
		else
		{
			// Handling options
			$this->__handleOptions();
		}
		
		// Get elements in session and pass to view
		$element_list = $this->element->listItems();
		$this->set('element_list', $element_list);
		
		// Pass order instance to view
		$this->set('order',  $this->order->show());	
		
		// Load view
		$this->display('details.phtml');	
	}
	
	/**
	 * OrderForm_Controller::customer()
	 * Process customer data
	 */
	function customer()
	{
		// Handling customer data
		$this->__handleCustomerData();

		// Handling payment data
		$this->__handlePaymentData();
		
		// If form is posted, we need to process the data		
		if(isset($_POST['step']))
		{			
			// Store other properties
			$this->order->set('Comment', $_POST['Comment']);

			// Do we have any errors?
			if(empty($this->Error) && (!isset($_POST['action']) || !$_POST['action']))
			{
				// If success, go to overview
				unset($_POST['action']);
				return $this->overview();
			}
		}
		
		// Only pass customer error to view  if we check for existing customer
		if(isset($_POST['action']) && $_POST['action'] && $_POST['action'] == 'check_existing_customer')
		{	
			// We only want errors if we are logged in
			if($this->debtor->get('Username'))
			{
				$this->set('errors', $this->debtor->Error);
			}
		}
		else
		{
			//Pass error to view
			$this->set('errors', $this->Error);
		}

		// Load all items into the order object
		$this->getCart();

		// Pass order to view
		$this->set('order', $this->order->show());
		
		// Show customer information
		$this->display('customer.phtml');
	}
	
	
	function overview()
	{
		// Check for actions on page
		if(isset($_POST['action']) && $_POST['action'])
		{	
			// If action is process discount coupon, store the coupon
			if($_POST['action'] == 'discount')
			{
				$this->order->set('Coupon', $_POST['Coupon']);
			}	
		}
		// If customer accepts agreement, we know the order must be placed
		elseif(isset($_POST['agree']) && $_POST['agree'] == 'yes')
		{
			unset($_POST['agree'], $_POST['action']);
			return $this->placeOrder();		
		}
		elseif(isset($_POST['action']) && (!isset($_POST['agree']) || $_POST['agree'] != 'yes'))
		{
			$this->Error[] = __('you must agree to the terms and conditions');
		}
		
		// Get customer data
		if($this->order->get('ExistingCustomer') == 'yes')
		{
			$this->debtor = new Debtor_Model();
			if(!$this->debtor->checkLogin())
			{
				// Return to customer step, because login has become invalid
				return $this->customer();	
			}
			
			// Pass debtor data to template
			$this->set('customer_data', $this->debtor->show());
		}
		else
		{
			$this->customer = new Customer_Model();
			
			// Pass customer data to template
			$this->set('customer_data', $this->customer->show());
		}
			
		// Load all items into the order object
		$this->getCart();
	
		//Pass error to view
		$this->set('errors', $this->Error);
		
		// Pass order object to view
		$this->set('order', $this->order->show());
		
		// Load arrays
		$this->set('array_legaltype', 			$this->order->_settings->get('array_legaltype'));
		$this->set('array_sex', 				$this->order->_settings->get('array_sex'));
		$this->set('array_country', 			$this->order->_settings->get('array_country'));
		$this->set('array_paymentmethods',  	$this->order->_settings->get('array_paymentmethods'));
		
		// Load view
		$this->display('overview.phtml');
	}
	
	function completed()
	{
		// Check if we need to send a mail
		$this->__sendMail();

		// Load all items again in order object
		$this->getCart(false);

		// Pass order to view
		$order = $this->order->show();
		$this->set('order', $order);

		// Only Prepare session for online payment when there is something to pay
		if(isset($order['AmountIncl']) && $order['AmountIncl'] > 0)
		{
			if ($this->order->get('PaymentMethod') == "ideal" || $this->order->get('PaymentMethod') == "other")
			{
				$_SESSION['issuerID'] = $this->order->get('issuerID');
				$_SESSION['OrderCode'] = passcrypt($this->order->get('OrderCode'));
				$_SESSION['OrderCode2'] = $this->order->get('Identifier');
				$_SESSION['PaymentMethod'] = $this->order->OriginalPaymentMethod;
				$payment_methods = $this->order->_settings->get('array_paymentmethods');

				$this->set('online_payment_method', $payment_methods[$this->order->OriginalPaymentMethod]);
				$this->set('online_payment_link', '?step=start_onlinepayment');

			}
			elseif ($this->order->get('PaymentMethod') == "paypal")
			{
				$_SESSION['OrderCode'] = passcrypt($this->order->get('OrderCode'));
				$_SESSION['OrderCode2'] = $this->order->get('Identifier');
				$_SESSION['PaymentMethod'] = $this->order->OriginalPaymentMethod;
				$payment_methods = $this->order->_settings->get('array_paymentmethods');

				$this->set('online_payment_method', $payment_methods[$this->order->OriginalPaymentMethod]);
				$this->set('online_payment_link', '?step=start_onlinepayment');
			}
		}
		
		// Unset session
		$_SESSION['OrderForm'.ORDERFORM_ID] = array();
		unset($_SESSION['OrderForm'.ORDERFORM_ID]);
		unset($_SESSION['whois_'.ORDERFORM_ID.'_domain']);	
	
		// Load completed view
		$this->display('completed.phtml');	
	}
	
	function start_onlinepayment(){
		$payment_methods = $this->order->_settings->get('array_paymentmethods');
		
		$method = $payment_methods[$_SESSION['PaymentMethod']];
		
		if(file_exists(ORDERFORM_TO_PAYMENTDIR.$method['Directory']."/payment_provider.php"))
		{
			$working_dir = getcwd();
			chdir(ORDERFORM_TO_PAYMENTDIR);
			include_once "application/payment_provider_base.php";
			include_once $method['Directory']."/payment_provider.php";
			$tmp_payment_provider = new $method['Class'];
	 				
			// Set order
			$tmp_payment_provider->setOrder($_SESSION['OrderCode2']);

			// Validate the chosen paymentmethod (like selecting a bank)
			if($tmp_payment_provider->validateChosenPaymentMethod())
			{
				$result = $tmp_payment_provider->startTransaction(); 
			}
			else
			{
				$error_message = $tmp_payment_provider->Error;
				fatal_error('', $error_message);
			}
			exit;
		}
		elseif(!is_dir(ORDERFORM_TO_PAYMENTDIR))
        {
            fatal_error('Configuration error', 'If you are the administrator of this order form, please fix the setting for the relative path between order form and payment folders.');
        }
		else
		{
			// Temp MySQL fix, because of integration with older payment gateway
			if(version_compare(PHP_VERSION, '7.0.0', '<') && function_exists('mysql_connect')) {
				if (defined("DB_CRYPT") && DB_CRYPT) {
					@mysql_connect(db_decrypt(DB_HOST), db_decrypt(DB_USERNAME), db_decrypt(DB_PASSWORD));
					@mysql_select_db(db_decrypt(DB_NAME));
				} else {
					@mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
					@mysql_select_db(DB_NAME);
				}
			}

			if(substr($_SESSION['PaymentMethod'],0,5) == "ideal" || substr($_SESSION['PaymentMethod'],0,5) == "other")
			{
				require_once ORDERFORM_TO_PAYMENTDIR.$payment_methods[$_SESSION['PaymentMethod']]['Directory'].'/TransReq.php';
				exit;			
			}
			elseif(substr($_SESSION['PaymentMethod'],0,6) == "paypal")
			{
				require_once ORDERFORM_TO_PAYMENTDIR.$payment_methods[$_SESSION['PaymentMethod']]['Directory'].'/orderform.php';
				exit;			
			}
		}
		
		
	}
	
	function onlinepayment()
	{
		$payment_methods = $this->order->_settings->get('array_paymentmethods');
		
		// Set payment method (if known)
		if(isset($_SESSION['PaymentMethod']) && $_SESSION['PaymentMethod'] && isset($payment_methods[$_SESSION['PaymentMethod']]))
		{
			$this->set('online_payment_method', $payment_methods[$_SESSION['PaymentMethod']]);
		}

		// Set transaction ID
		$this->set('transaction_id', (isset($_GET['trxid'])) ? htmlspecialchars($_GET['trxid']) : '');
		
		// Load view
		$this->display('onlinepayment.phtml');		
	}
	
	function getCart($escaped = true)
	{
		// Add all different elements to order object		
		$this->order->elements = array();
				
		// Get other items
		$element_list = $this->element->listItems($escaped);
		foreach($element_list as $element => $element_info)
		{
			// Add element into order
			$this->order->addElement($element_info);
		}	
	}
	
	function placeOrder()
	{
		// Load all items in order object
		$this->getCart(false);
		
		// Are there items in the cart?
		if(empty($this->order->elements))
		{
			// Redirect to start
			unset($_POST);
			return $this->start();
		}
		
		if($this->order->get('ExistingCustomer') == 'yes')
		{
			$this->debtor = new Debtor_Model();
			if($this->debtor->checkLogin())
			{
				$this->order->Debtor = $this->debtor->Identifier;
				$this->order->Type = 'debtor';
			}
			else
			{
				// Copy error
				$this->Error = $this->debtor->Error;
				return false;
			}
		}
		else
		{
			// Add new customer to database
			$this->customer = new Customer_Model();
			if(!$this->customer->add())
			{
				// Copy error
				$this->Error = $this->customer->Error;
				return false;
			}	
				
			$this->order->Debtor = $this->customer->Identifier;
			$this->order->Type = 'new';
		}

		// Add order into database
		if($this->order->add())
		{
			// Order succesfully added
			return $this->completed();
		}
		else
		{
			// Copy error
			$this->Error = $this->order->Error;
			
			// Reset element-array
			$this->order->elements = array();
				
			// Go back to overview
			return $this->overview();
		}
		
	}
	
	function router()
	{
		$step = 'start';
		
		// If we have a GET value for the step, use it
		if(isset($_GET['step']) && $_GET['step'])
		{
			// Only valid steps will return the corresponding action name
			switch($_GET['step'])
			{
				case 'start': $step = 'start'; break;
				case 'details': $step = 'details'; break;
				case 'customer': $step = 'customer'; break;
				case 'overview': $step = 'overview'; break;
				case 'completed': $step = 'completed'; break;
				case 'onlinepayment': $step = 'onlinepayment'; break;
				case 'start_onlinepayment': $step = 'start_onlinepayment'; break;
			}
		}
		
		// If we have no (valid) GET value, use POST value
		if(isset($_POST['step']) && $_POST['step'])
		{
			// Only valid steps will return the corresponding action name
			switch($_POST['step'])
			{
				case 'start': $step = 'start'; break;
				case 'details': $step = 'details'; break;
				case 'customer': $step = 'customer'; break;
				case 'overview': $step = 'overview'; break;
				case 'get_states': $step = '__getStates'; break;
				case 'get_handledetail': $step = '__getHandleDetails'; break;
			}
		}

		// Go to given step
		return $this->{$step}();
		
	}
	
	function __handleOptions()
	{
		// Load product groups and pass to view
		$option_products = $this->element->getProductsFromGroup(GROUP_OPTIONS);
		$this->set('option_products', $option_products);
		
		// Process POST data
		if(isset($_POST['step']))
		{
			// Update selected options
			if(isset($option_products) && is_array($option_products))
			{
				// Loop through all option products and create or remove this option
				foreach($option_products as $prod_id => $tmp_prod)
				{
					if(isset($_POST['Options'][$prod_id]) && $_POST['Options'][$prod_id])
					{
						// Store option in session
						$this->element->newItem('Option'.$prod_id);
						$this->element->setAttribute('ProductCode', $_POST['Options'][$prod_id]);
						$this->element->saveItem();
					}
					else
					{
						// Remove option from session
						$this->element->removeItem('Option'.$prod_id);
					}
				}
			}
		}		
	}
	
	function __handleCustomerData()
	{
		// Get customer objects
		$this->debtor 	= new Debtor_Model();
		$this->customer = new Customer_Model();
        
		// If form is posted, we need to process the data		
		if(isset($_POST['CompanyName']) || isset($_POST['SurName']))
		{			
			// If existing customer, validate username and password
			if(isset($_POST['ExistingCustomer']) && $_POST['ExistingCustomer'] == 'yes')
			{
				// Store the choice for existing customer
				$this->order->set('ExistingCustomer', 'yes');
				
				// Store username and password
				if(isset($_POST['action']) && $_POST['action'] && $_POST['action'] == 'check_existing_customer')
				{
					$this->debtor->set('Username', (isset($_POST['ex_Username'])) ? $_POST['ex_Username'] : '');
					$this->debtor->set('Password', (isset($_POST['ex_Username'])) ? $_POST['ex_Password'] : '');
                    $this->debtor->PostPassword = (isset($_POST['ex_Username'])) ? TRUE : FALSE;
					
					// Also logout from customer panel
					if(!isset($_POST['ex_Username']))
					{
					   $this->debtor->set('SecurePassword', '');
						unset($_SESSION['User'], $_SESSION['Username'], $_SESSION['Password'], $_SESSION['SecurePassword'], $_SESSION['LoggedIn'], $_SESSION['ca_api_hash']);
					}
				}
				else
				{
					$this->debtor->set('Username', (isset($_POST['ex_Username'])) ? $_POST['ex_Username'] : $this->debtor->get('Username'));
					$this->debtor->set('Password', (isset($_POST['ex_Username'])) ? $_POST['ex_Password'] : $this->debtor->get('Password'));
                    $this->debtor->PostPassword = (isset($_POST['ex_Username'])) ? TRUE : FALSE;
				}
		
				// Check username and password
				if(!$this->debtor->checkLogin())
				{
					// If there is no match, merge error
					$this->Error = array_merge($this->Error, $this->debtor->Error);
				}
				else
				{
				    // Regenerate session id for security.
                    session_regenerate_id(true);

					$debtor_info = $this->debtor->show();
					
					// Should we use auth information
					if((!isset($_POST['PaymentMethod']) || $_POST['PaymentMethod'] == 'auth') && isset($_POST['action']) && $_POST['action'] && $_POST['action'] == 'check_existing_customer')
					{
						$payment_methods = $this->order->_settings->get('array_paymentmethods');
						if(isset($payment_methods['auth']) && $debtor_info['InvoiceAuthorisation'] == 'yes')
						{
							
							$_POST['PaymentMethod'] = 'auth';
							$this->order->set('AccountNumber', 	$this->debtor->AccountNumber);
							$this->order->set('AccountBIC', 	$this->debtor->AccountBIC);
							$this->order->set('AccountName', 	$this->debtor->AccountName);
							$this->order->set('AccountCity', 	$this->debtor->AccountCity);
						}
					}
				}
			}
			else
			{
				// Store the choice for new customer
				$this->order->set('ExistingCustomer', 'no');
				
				// If new customer is a company, save data
				if(isset($_POST['CompanyName']) && $_POST['CompanyName'])
				{
					$this->customer->set('CompanyName', 	$_POST['CompanyName']);
					$this->customer->set('CompanyNumber', 	$_POST['CompanyNumber']);
					$this->customer->set('TaxNumber', 		$_POST['TaxNumber']);
					$this->customer->set('LegalForm', 		(isset($_POST['LegalForm'])) ? $_POST['LegalForm'] : '');
				}
				else
				{
					$this->customer->set('CompanyName', 	'');
					$this->customer->set('CompanyNumber', 	'');
					$this->customer->set('TaxNumber', 		'');
					$this->customer->set('LegalForm', 		'');
				}
				
				// Contact person
				$this->customer->set('Sex', 		$_POST['Sex']);
				$this->customer->set('Initials', 	$_POST['Initials']);
				$this->customer->set('SurName', 	$_POST['SurName']);
				$this->customer->set('Address', 	$_POST['Address']);
				$this->customer->set('ZipCode', 	$_POST['ZipCode']);
				$this->customer->set('City', 		$_POST['City']);
				$this->customer->set('Country', 	$_POST['Country']);
				
				if(IS_INTERNATIONAL){
					$this->customer->set('Address2', 	$_POST['Address2']);
					$this->customer->set('State', 		(isset($_POST['StateCode']) && $_POST['StateCode']) ? $_POST['StateCode'] : $_POST['State']);
				}
				
				// Contact data
				$this->customer->set('EmailAddress', 	$_POST['EmailAddress']);
				$this->customer->set('PhoneNumber', 	$_POST['PhoneNumber']);
			
				// If customer has chosen for payment method authorisation, store account data
				if(isset($_POST['PaymentMethod']) && $_POST['PaymentMethod'] == 'auth')
				{
					// Activate authorisation
					$this->customer->set('InvoiceAuthorisation', 	'yes');
					
					// Store account-data
					$this->customer->set('AccountNumber', 	$_POST['AccountNumber']);
					$this->customer->set('AccountBIC', 		$_POST['AccountBIC']);
					$this->customer->set('AccountName', 	$_POST['AccountName']);
					$this->customer->set('AccountCity', 	$_POST['AccountCity']);
				}
				else
				{
					// Dectivate authorisation, but don't reset account-data
					$this->customer->set('InvoiceAuthorisation', 	'no');
				}
				
				// Custom invoice data
				if(isset($_POST['CustomInvoiceAddress']) && $_POST['CustomInvoiceAddress'] == 'yes')
				{
					// We do have custom invoice data, store this information
					$this->customer->set('CustomInvoiceAddress', 'yes');
					$this->customer->set('InvoiceCompanyName', 	$_POST['InvoiceCompanyName']);
                    $this->customer->set('InvoiceSex', 	        $_POST['InvoiceSex']);
					$this->customer->set('InvoiceInitials', 	$_POST['InvoiceInitials']);
					$this->customer->set('InvoiceSurName', 		$_POST['InvoiceSurName']);
					$this->customer->set('InvoiceAddress', 		$_POST['InvoiceAddress']);
					$this->customer->set('InvoiceZipCode', 		$_POST['InvoiceZipCode']);
					$this->customer->set('InvoiceCity', 		$_POST['InvoiceCity']);
					$this->customer->set('InvoiceCountry', 		$_POST['InvoiceCountry']);
					$this->customer->set('InvoiceEmailAddress', $_POST['InvoiceEmailAddress']);
					
					if(IS_INTERNATIONAL){
						$this->customer->set('InvoiceAddress2', 	$_POST['InvoiceAddress2']);
						$this->customer->set('InvoiceState', 		(isset($_POST['InvoiceStateCode']) && $_POST['InvoiceStateCode']) ? $_POST['InvoiceStateCode'] : $_POST['InvoiceState']);
					}
				}
				else
				{
					// We don't have custom invoice data
					$this->customer->set('CustomInvoiceAddress', 'no');
				}
				
				// Custom fields?
				CustomClientFields_Model::setCustomFields('customer');
				
				// Validate entered customer data with the validate() function in Customer_Model
				if(!$this->customer->validate())
				{
					// There is some invalid data, merge error
					$this->Error = array_merge($this->Error, $this->customer->Error);
				}
				
			}
		}
		
		// Pass customer to template
		if($this->order->get('ExistingCustomer') == 'yes')
		{
			$this->set('deb_Username', 	$this->debtor->get('Username'));
			$this->set('deb_Password', 	$this->debtor->get('Password'));
			
			$this->debtor->checkLogin();
			$debtor_info = $this->debtor->show();
			$this->set('debtor_info', $debtor_info);
		}
		else
		{
			$this->set('customer', 	$this->customer->show());
			
			// Load optional custom fields
			$this->set('customer_custom_fields_values', CustomClientFields_Model::getCustomValues('customer'));
		}
		
		// Pass arrays to template
		$this->set('customer_custom_fields',CustomClientFields_Model::getCustomFields('customer'));
		$this->set('array_legaltype', 		$this->order->_settings->get('array_legaltype'));
		$this->set('array_sex', 			$this->order->_settings->get('array_sex'));
		$this->set('array_states', 			$this->order->_settings->get('array_states'));
		$this->set('array_country', 		$this->order->_settings->get('array_country'));
	}
	
	function __handlePaymentData()
	{

		$payment_method_list = $this->order->_settings->get('array_paymentmethods');
				
		// If form is posted, we need to process the data		
		if(isset($_POST['step']))
		{
			// Load all items into the order object
			$this->getCart();

			// Pass order to view
			$order = $this->order->show();
			if(!isset($order['AmountIncl']) || $order['AmountIncl'] <= 0)
			{
				return;
			}

			// Store payment method
			$this->order->set('PaymentMethod',	(isset($_POST['PaymentMethod'])) ? $_POST['PaymentMethod'] : '');
			$this->order->set('AuthAgree',		(isset($_POST['AuthAgree']) && $_POST['AuthAgree'] == 'agree') ? 'agree' : 'disagree');
			
			// A payment method must be chosen
			if(!isset($_POST['PaymentMethod']) || !$_POST['PaymentMethod'])
			{
				$this->Error[] = __('please select a payment method');
			}
			// Save auth information
			elseif(isset($_POST['PaymentMethod']) && $_POST['PaymentMethod'] == 'auth' && (!isset($_POST['action']) || !$_POST['action']))
			{
				$this->order->set('AccountNumber', 	$_POST['AccountNumber']);
				$this->order->set('AccountBIC', 	$_POST['AccountBIC']);
				$this->order->set('AccountName', 	$_POST['AccountName']);
				$this->order->set('AccountCity', 	$_POST['AccountCity']);
                                
                if(isset($_POST['PaymentMethod']) && $_POST['PaymentMethod'] == "auth")
                {
                    if(!$this->order->get('AccountNumber') || $this->order->get('AccountNumber') == '')
                    {
                    	$this->Error[] = __('no accountnumber given');
                    }
                    
                    if($this->order->get('AccountNumber') != '' && !checkIBAN($this->order->get('AccountNumber')))
                    {
                    	$this->Error[] = __('invalid accountnumber');
                    }
                    
                    if($this->order->get('AccountBIC') != '' && !checkBIC($this->order->get('AccountBIC')))
                    {
                    	$this->Error[] = __('invalid bic');
                    }
                    
                    if($this->order->get('AuthAgree') != 'agree')
                    {
                    	$this->Error[] = __('you must agree to the authorization');
                    }  
                }
			}
			elseif(isset($payment_method_list[$_POST['PaymentMethod']]))
			{
				$method = $payment_method_list[$_POST['PaymentMethod']];
	
				// If payment method has payment provider class, initiate
				if(file_exists(ORDERFORM_TO_PAYMENTDIR.$method['Directory']."/payment_provider.php"))
				{
					$working_dir = getcwd();
					chdir(ORDERFORM_TO_PAYMENTDIR);
					include_once "application/payment_provider_base.php";
					include_once $method['Directory']."/payment_provider.php";
					$tmp_payment_provider = new $method['Class'];
									
					// Validate the chosen paymentmethod (like selecting a bank)
					if(!$tmp_payment_provider->validateChosenPaymentMethod())
					{
						$this->Error[] = $tmp_payment_provider->Error;	
					}
					
					chdir($working_dir);
				}
				// Old iDEAL methods
				elseif(isset($_POST['PaymentMethod']) && substr($_POST['PaymentMethod'],0,5) == "ideal" && isset($_POST['issuerID-'.$_POST['PaymentMethod']]))
				{
					// Save iDEAL choice
					if($_POST['issuerID-'.$_POST['PaymentMethod']] != "0")
					{
						$this->order->set('issuerID',	$_POST['issuerID-'.$_POST['PaymentMethod']]);	
					}
					else
					{
						$this->Error[] = __('please select a bank');
					}
				}
			}		
		}
		
		// Pass payment methods to views
		$this->set('array_paymentmethods', 	$payment_method_list);
		
		// Temp MySQL fix, because of integration with older payment gateway
		if(version_compare(PHP_VERSION, '7.0.0', '<') && function_exists('mysql_connect')) {
			if (defined("DB_CRYPT") && DB_CRYPT) {
				@mysql_connect(db_decrypt(DB_HOST), db_decrypt(DB_USERNAME), db_decrypt(DB_PASSWORD));
				@mysql_select_db(db_decrypt(DB_NAME));
			} else {
				@mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
				@mysql_select_db(DB_NAME);
			}
		}
		
	}
	
	function __handleCustomWhoisData()
	{
		if(DOMAIN_CUSTOM_WHOIS != 'yes')
		{
			return false;
		}
		
		$this->ownerhandle = new Handle_Model();

		// If form is posted, we need to process the data		
		if(isset($_POST['step']))
		{
			// Store custom WHOIS data
			if(isset($_POST['CustomWHOIS']) && $_POST['CustomWHOIS'] == 'yes')
			{
				$this->order->set('CustomWHOIS', 'yes');
				$this->ownerhandle->set('id', 0); // Reset chosen handle to revalidate

				if(isset($_POST['WHOISHandle']) && $_POST['WHOISHandle'] > 0)
				{
					$this->ownerhandle->set('id', $_POST['WHOISHandle']);
				}
				else
				{
					$this->ownerhandle->set('CompanyName', 		$_POST['WHOISCompanyName']);
					$this->ownerhandle->set('CompanyNumber', 	$_POST['WHOISCompanyNumber']);
					$this->ownerhandle->set('TaxNumber', 		$_POST['WHOISTaxNumber']);
					$this->ownerhandle->set('LegalForm', 		$_POST['WHOISLegalForm']);
					$this->ownerhandle->set('Sex', 				$_POST['WHOISSex']);
					$this->ownerhandle->set('Initials', 		$_POST['WHOISInitials']);
					$this->ownerhandle->set('SurName', 			$_POST['WHOISSurName']);
					$this->ownerhandle->set('Address', 			$_POST['WHOISAddress']);
					$this->ownerhandle->set('ZipCode', 			$_POST['WHOISZipCode']);
					$this->ownerhandle->set('City', 			$_POST['WHOISCity']);
					$this->ownerhandle->set('Country', 			$_POST['WHOISCountry']);
					$this->ownerhandle->set('EmailAddress', 	$_POST['WHOISEmailAddress']);
					$this->ownerhandle->set('PhoneNumber', 		$_POST['WHOISPhoneNumber']);

					if(IS_INTERNATIONAL){
						$this->ownerhandle->set('Address2', 	$_POST['WHOISAddress2']);
						$this->ownerhandle->set('State', 		(isset($_POST['WHOISStateCode']) && $_POST['WHOISStateCode']) ? $_POST['WHOISStateCode'] : $_POST['WHOISState']);
					}

					// Custom fields?
					CustomClientFields_Model::setCustomFields('handle');

					// Validate entered WHOIS data with the validate() function in Handle_Model
					if(!$this->ownerhandle->validate())
					{
						// There is some invalid data, merge error
						$this->Error = array_merge($this->Error, $this->ownerhandle->Error);
					}
				}
			}
			else
			{
				$this->order->set('CustomWHOIS', 'no');
			}
		}
		
		// Pass variable so we know that we can offer custom WHOIS data
		$this->set('allow_custom_whois_data', true);
		$this->set('handle', 	$this->ownerhandle->show());
		
		// Load optional custom fields
		$this->set('handle_custom_fields', CustomClientFields_Model::getCustomFields('handle'));
		$this->set('handle_custom_fields_values', CustomClientFields_Model::getCustomValues('handle'));
		
	}
	
	function __sendMail()
	{
		// Do we want to send a mail?
		if(ORDERMAIL_SENT != 'yes')
		{
			return true;
		}
		
		// Get customer data
		if($this->order->get('ExistingCustomer') == 'yes')
		{
			$this->debtor = new Debtor_Model();
			if(!$this->debtor->checkLogin())
			{
				// Return to customer step, because login has become invalid
				return $this->customer();	
			}
			
			// Pass debtor data to template
			$this->set('customer_data', $this->debtor->show());
		}
		else
		{
			$this->customer = new Customer_Model();
			
			// Pass customer data to template
			$this->set('customer_data', $this->customer->show());
		}
			
		// Load all items into the order object
		$this->getCart();
		
		// Pass order object to view
		$this->set('order', $this->order->show());
		
		// Load arrays
		$this->set('array_sex', $this->order->_settings->get('array_sex'));
		$this->set('array_country', $this->order->_settings->get('array_country'));
		$this->set('array_paymentmethods', $this->order->_settings->get('array_paymentmethods'));
		
		$this->set('array_taxpercentages', 			$this->order->_settings->get('array_taxpercentages'));
		$this->set('array_taxpercentages_info', 	$this->order->_settings->get('array_taxpercentages_info'));
		$this->set('array_total_taxpercentages',	$this->order->_settings->get('array_total_taxpercentages'));
		
		// Skip header/footer-part
		$this->setType('inline');
		
		// Start buffering
		ob_start();
		$this->display('../includes/language/'.LANG.'/mail.phtml');
		$mail_body = ob_get_contents();
		ob_end_clean();
		
		$this->setType('default');
		
		// Ophalen van de bestanden voor het verzenden van e-mail
		require_once "includes/mail/PHPMailer.php";
        require_once "includes/mail/SMTP.php";
        require_once "includes/mail/Exception.php";
		
		$mailer = new PHPMailer\PHPMailer\PHPMailer();
		$mailer->SMTPOptions = array('ssl' => array('verify_peer' => false, 'allow_self_signed' => false, 'verify_peer_name' => false));

		// Do we need to sent via SMTP?
		if($this->order->_settings->get('SMTP_ON') == "1"){
			$mailer->IsSMTP();

			// Parse login details
			$mailer->SMTPSecure = (substr($this->order->_settings->get('SMTP_HOST'),0,6) == 'tls://') ? 'tls' : ((substr($this->order->_settings->get('SMTP_HOST'),0,6) == 'ssl://') ? 'ssl' : $mailer->SMTPSecure);
			$mailer->Host 		= (substr($this->order->_settings->get('SMTP_HOST'),0,6) == 'tls://') ? substr($this->order->_settings->get('SMTP_HOST'),6) : ((substr($this->order->_settings->get('SMTP_HOST'),0,6) == 'ssl://') ? substr($this->order->_settings->get('SMTP_HOST'),6) : $this->order->_settings->get('SMTP_HOST'));	
			$mailer->SMTPAuth 	= ($this->order->_settings->get('SMTP_AUTH') == "1") ? true : false;
			$mailer->Username 	= $this->order->_settings->get('SMTP_USERNAME');
			$mailer->Password 	= passcrypt($this->order->_settings->get('SMTP_PASSWORD'));
		}else{
			// Use PHP-mail function instead
			$mailer->IsMail();
		}

		// Sender name
		$mailer->From 		= COMPANY_EMAIL;
		$mailer->FromName 	= COMPANY_NAME;
	
		$mailer->IsHTML(true);
		$mailer->CharSet = 'UTF-8';
		$mailer->SetLanguage(substr(LANG,0,2));
		
		$customer_data = $this->get('customer_data');
		
		// Add address and CC
		$recips = array();
		$intRecipients = 0;
		$recipients = explode(";", check_email_address($customer_data['EmailAddress'], 'convert'));
		foreach($recipients as $recipient)
		{
			if($intRecipients === 0)
			{
				$mailer->AddAddress($recipient);
				$recips[] = $recipient;
			}
			else
			{
				if(!in_array($recipient,$recips))
				{
					$mailer->AddCC($recipient);
					$recips[] = $recipient;
				}
			}
			$intRecipients++;
		}
		
		if(ORDERMAIL_SENT_BCC != ""){
			$orderbccs = explode(";", check_email_address(ORDERMAIL_SENT_BCC, 'convert'));
			foreach($orderbccs as $orderbcc)
			{
				if(!in_array($orderbcc,$recips))
				{
					$mailer->AddBCC($orderbcc);
					$recips[] = $orderbcc;
				}
			}
		}

		$mailer->Encoding = 'quoted-printable';
		$mailer->Body = $mail_body;
		$mailer->AltBody = trim(strip_html_tags($mail_body));

		// U kunt hieronder het onderwerp van uw bestelling opgeven.
		$mailer->Subject = sprintf(__('confirmation of your order from'),COMPANY_NAME);

		// DKIM support
		$current_dkim = json_decode(htmlspecialchars_decode(DKIM_DOMAINS), true);
		$dkim_domain = substr($mailer->From,strrpos($mailer->From, '@')+1);

		if($current_dkim && isset($current_dkim[$dkim_domain]))
		{
			// Create temp file
			$dkim_filename = @tempnam('temp/','dkim');
			if(@file_put_contents($dkim_filename, $current_dkim[$dkim_domain]['private']))
			{
				$mailer->DKIM_domain = $dkim_domain;
				$mailer->DKIM_private = $dkim_filename; //path to file on the disk.
				$mailer->DKIM_selector = $current_dkim[$dkim_domain]['selector'];// change this to whatever you set during step 2
				$mailer->DKIM_passphrase = "";
				$mailer->DKIM_identifier = $mailer->From;
			}
		}

		$result = $mailer->Send();
	
		if(!$result){
			//echo $mailer->ErrorInfo;
		}
	
	}
	
	function __getStates()
	{
		$array_states 	= $this->order->_settings->get('array_states');
		$countrycode 	= $_POST['countrycode'];
		
		if(isset($array_states[$countrycode])){ 
			
			$options = '<option value="">'.__('please choose').'</option>';
			foreach($array_states[$countrycode] as $key=>$value){ 	
				$options .= '<option value="'.$key.'">'.$value.'</option>';
			}
			
			$return = array('type' => 'select', 'options' => $options);
		}else{
			$return = array('type' => 'input');
		}
		echo json_encode($return);
		exit;
	}

	function __getHandleDetails()
	{
		$handle_id = $_POST['handle_details'];
		$handle_debtor_list = Handle_Model::listDebtorHandles();
		if($handle_debtor_list && !empty($handle_debtor_list))
		{
			foreach($handle_debtor_list as $_handle)
			{
				// Check if handle is from debtor
				if($_handle->id == $handle_id)
				{
					$this->set('array_legaltype', 		$this->order->_settings->get('array_legaltype'));
					$this->set('array_sex', 			$this->order->_settings->get('array_sex'));
					$this->set('array_states', 			$this->order->_settings->get('array_states'));
					$this->set('array_country', 		$this->order->_settings->get('array_country'));

					$this->set('handle', $_handle);
					$this->set('handle_custom_fields', CustomClientFields_Model::getCustomFields('handle'));
					$this->element('handle_details.phtml', 'domain');
				}
			}
		}
		exit;
	}
	
}

