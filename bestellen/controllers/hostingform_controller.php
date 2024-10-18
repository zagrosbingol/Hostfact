<?php

class HostingForm_Controller extends OrderForm_Controller
{
	
	public function __construct()
	{	
		// Load parent constructor
		parent::__construct();
		
		// Create instances of uses objects
		$this->hosting 	= new Hosting_Model();
		$this->domain 	= new Domain_Model();
		
		
		// Use custom hosting files
		$this->setTheme('hosting');
	}

	function start()
	{	
		// Check if we should really go to step start
		if(!isset($_GET['step']))
		{
		
			// Check also for GET variables
			if(isset($_GET['domain']) && DOMAIN_AVAILABLE == 'yes')
			{
                $add_domains = explode(',',$_GET['domain']);
                foreach($add_domains as $_domain) {
                    if ($this->domain->parseDomain($_domain)) {
                        $this->domain->newItem($_domain);
                        $this->domain->setAttribute('Domain', $_domain);
                        $this->domain->saveItem();
                    } else {
                        // domain without extension given? redirect to whois check page
                        $_SESSION['whois_' . ORDERFORM_ID . '_domain'] = $_domain;
                        $this->display('start.phtml');
                        exit;
                    }
                }
			}
			if(isset($_GET['hosting']) && HOSTING_AVAILABLE == 'yes')
			{
				$this->hosting->newItem('hosting');
				$this->hosting->setAttribute('ProductCode', $_GET['hosting']);
				$this->hosting->saveItem();	
			}
			
			// Default values
			if(HOSTING_DEFAULT_PACKAGE && $this->hosting->getItem('hosting') === false)
			{
				// Default hosting package
				$this->hosting->newItem('hosting');
				$this->hosting->setAttribute('ProductCode', HOSTING_DEFAULT_PACKAGE);
				$this->hosting->saveItem();	
			}
			
			// Determine step
			return $this->details();
		}
		
		// Show start page
		$this->display('start.phtml');
	}
	
	function details()
	{	
		// Check if domain should be removed
		if(isset($_POST['action']) && $_POST['action'])
		{
			// Explode action and domain name
			$action_expl = explode("=", $_POST['action']);
			
			if($action_expl[0] == 'remove_domain' && isset($action_expl[1]) && $action_expl[1])
			{
				// Remove domain from order session
				$this->domain->removeItem($action_expl[1]);
			
				// Redirect to orderform
				header("Location: ?");
				exit;
			}
		}
		
		// Get hosting products and pass to view
		$hosting_products = $this->hosting->getHostingProducts();
		$this->set('hosting_products', $hosting_products);	
		
		// Handle other POST data from hostingform
		if(isset($_POST['step']))
		{
			// Save posted variables in session
			if(DOMAIN_AVAILABLE == 'yes')
			{
				$this->order->set('DomainType', 		$_POST['DomainType']);
				$this->order->set('Domain', 			$_POST['Domain']);
			}
			
			// Set period
			global $period_choice_options;
			if(PERIOD_CHOICE == 'yes' && isset($_POST['BillingPeriod']) && in_array($_POST['BillingPeriod'], $period_choice_options))
			{
				$this->order->set('PricePeriod', $_POST['BillingPeriod']);
			}
			
			// Add new hosting product
			$this->hosting->newItem('hosting');
			$this->hosting->setAttribute('ProductCode', (isset($_POST['Hosting'])) ? $_POST['Hosting'] : '');
			
			// Set default domain
			if(DOMAIN_AVAILABLE == 'yes')
			{
				if(HOSTING_DEFAULT_DOMAIN_CHOICE == 'yes' && isset($_POST['DefaultDomain']) && isset($_POST['DomainType']) && $_POST['DomainType'] == 'new')
				{
					$this->hosting->setAttribute('Domain', $_POST['DefaultDomain']);
					$this->order->set('DefaultDomain', 		$_POST['DefaultDomain']);
				}
				elseif(isset($_POST['DomainType']) && $_POST['DomainType'] == 'new')
				{
					$first_domain = current($this->domain->listItems());
					$this->hosting->setAttribute('Domain',$first_domain['Domain']);
				}
				else
				{
					$this->hosting->setAttribute('Domain', $_POST['Domain']);
				}
			}
			$this->hosting->saveItem();	
			
			// We do offcourse need a product code to be selected
			if(!isset($_POST['Hosting']) || !$_POST['Hosting'])
			{
				$this->Error[] = __('you need one of the hosting packages to select');
			}			
			
			// Domain choice 
			if(DOMAIN_AVAILABLE == 'yes')
			{
				switch($_POST['DomainType'])
				{
					case 'new':
						$this->element->removeItem('ExistingDomain');
						
						// Do we have any auth codes?
						if(isset($_POST['AuthKey']) && is_array($_POST['AuthKey']))
						{
							foreach($_POST['AuthKey'] as $tmp_domain => $auth_code)
							{
								$this->domain->getItem($tmp_domain);
								$this->domain->setAttribute('AuthKey', $auth_code);
								$this->domain->saveItem();
								
								// If no authcode has been entered, create error
								if(DOMAIN_AUTH_KEY_REQUIRED == 'yes' && !trim($auth_code))
								{
									$this->Error[] = sprintf(__('authkey for domain is required'),htmlspecialchars($tmp_domain));
								}
							}
						}
						
						break;
					case 'existing':
						
						// Delete domains from session
						$domain_list = $this->domain->listItems();
						
						foreach($domain_list as $domain_index => $tmp_domain){
							// Remove domain from order session
							$this->domain->removeItem($domain_index);
						}
						
						if(isset($_POST['Domain']) && strlen($_POST['Domain']) > 0){	
							// Create description in order
							$this->element->newItem('ExistingDomain');
							$this->element->setAttribute('Description', sprintf(__('current domain description'), htmlspecialchars($_POST['Domain'])));
							$this->element->saveItem();
						}else{
							$this->Error[] = __('domain name required for a existing account');
						}
						break;				
				}
			}
			
			// Handling options
			$this->__handleOptions();
			
			// Check if domain should be checked
			if(isset($_POST['action']) && $_POST['action'])
			{
				// Explode action and domain name
				$action_expl = explode("=", $_POST['action']);
				
				if($action_expl[0] == 'check_domain')
				{
					$_SESSION['whois_'.ORDERFORM_ID.'_domain'] = (isset($action_expl[1])) ? $action_expl[1] : '';
					// Redirect to orderform
					header("Location: ?step=start");
					exit;
				}
			}
			
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

		// Get domains and pass list to view
		$domain_list = $this->domain->listItems();
		$this->set('domains', $domain_list);
		
		// Get hosting account and pass element to view
		$hosting_list = $this->hosting->listItems();
		$this->set('hosting', isset($hosting_list['hosting']) ? $hosting_list['hosting'] : array());
		
		// Get other elements in session and pass to view
		$element_list = $this->element->listItems();
		$this->set('element_list', $element_list);
		
		// Pass order instance to view
		$this->set('order', $this->order->show());

		// Show details page
		$this->display('details.phtml');		
	}
	
	function customer()
	{
		// Handling custom WHOIS data if domains are in cart
		$domain_list = $this->domain->listItems();
		if(!empty($domain_list))
		{		
			$this->__handleCustomWhoisData();
		}
		
		// Call parent customer
		return parent::customer();
	}
	
	function getCart($escaped = true)
	{
		// Add all different elements to order object, in the following order: domains/hosting/other		
		$this->order->elements = array();
		
		// Get domains
		$domain_list = $this->domain->listItems($escaped);
		foreach($domain_list as $domain => $domain_info)
		{
			// Add element into order
			$this->order->addElement($domain_info);
		}
		
		// Get hosting account(s)
		$hosting_list = $this->hosting->listItems($escaped);
		foreach($hosting_list as $hosting_index => $hosting_info)
		{
			// Add element into order
			$this->order->addElement($hosting_info);
		}
		
		// Get other items
		$element_list = $this->element->listItems($escaped);
		foreach($element_list as $element => $element_info)
		{
			// Add element into order
			$this->order->addElement($element_info);
		}
	}
}