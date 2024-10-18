<?php

class DomainForm_Controller extends OrderForm_Controller
{
	
	public function __construct()
	{	
		// Load parent constructor
		parent::__construct();
		
		
		// Create instances of uses objects
		$this->domain 	= new Domain_Model();
		$this->hosting 	= new Hosting_Model();
		
		// Use custom domain files
		$this->setTheme('domain');
	}
		
	function start()
	{	
		// Check if we should stay at this step, if we already have domains, go to details instead
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

			// If we do have domains left, go to details
			if(!empty($this->domain->_elementlist))
			{
				return $this->details();
			}
		}
		
		// Get domains and pass list to view
		$domain_list = $this->domain->listItems();
		$this->set('domains', $domain_list);
		
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
		
		// Handle other POST data from domainform
		if(isset($_POST['step']))
		{
			// Save posted variables in session
			$this->order->set('HostingType', 		$_POST['HostingType']);
			$this->order->set('Domain', 			$_POST['Domain']);
			$this->order->set('CustomNameServers', 	(isset($_POST['CustomNameServers'])) ? $_POST['CustomNameServers'] : 'no');
			
			if(DOMAIN_OWN_NAMESERVERS == 'yes')
			{
				$this->order->set('NS1', 				$_POST['NS1']);
				$this->order->set('NS2', 				$_POST['NS2']);
				$this->order->set('NS3', 				$_POST['NS3']);
			}

			// Set period
			global $period_choice_options;
			if(PERIOD_CHOICE == 'yes' && isset($_POST['BillingPeriod']) && in_array($_POST['BillingPeriod'], $period_choice_options))
			{
				$this->order->set('PricePeriod', $_POST['BillingPeriod']);
			}
			
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
			
			// Hosting choice 
			switch($_POST['HostingType'])
			{
				case 'new':
					// Remove existing hosting and set custom nameservers to false
					$this->element->removeItem('ExistingHosting');
					$this->order->set('CustomNameServers', 	'no');

					// Add new hosting product
					$this->hosting->newItem('hosting');
					$this->hosting->setAttribute('ProductCode', (isset($_POST['Hosting'])) ? $_POST['Hosting'] : '');
					
					// Set default domain
					if(HOSTING_DEFAULT_DOMAIN_CHOICE == 'yes' && isset($_POST['DefaultDomain']))
					{
						$this->hosting->setAttribute('Domain', $_POST['DefaultDomain']);
						$this->order->set('DefaultDomain', 		$_POST['DefaultDomain']);
					}
					else
					{
						$first_domain = current($this->domain->listItems());
						$this->hosting->setAttribute('Domain',$first_domain['Domain']);
					}
					
					$this->hosting->saveItem();	
					
					// We do offcourse need a product code to be selected
					if(!isset($_POST['Hosting']) || !$_POST['Hosting'])
					{
						$this->Error[] = __('you need one of the hosting packages to select');
					}			
					break;
				case 'existing':
					if(isset($_POST['Domain']) && strlen($_POST['Domain']) > 0){
						// Remove new hosting account and set custom nameservers to false
						$this->hosting->removeItem('hosting');
						$this->order->set('CustomNameServers', 	'no');
						
						// Escape domain and Strip www., http:// and https://
						$domain = strtolower(stripslashes(trim($_POST['Domain'])));
						$domain = str_replace(array('www.','http://', 'https://'),'',$domain);
						$this->order->set('Domain', $domain);
						
						// Create description in order, so we now to which account the domains should be connected
						$this->element->newItem('ExistingHosting');
						$this->element->setAttribute('Description', sprintf(__('link to hosting account'),htmlspecialchars($domain)));
						$this->element->saveItem();
					}else{
						$this->Error[] = __('domain name required for a existing account');
					}
					break;
				default:
					// Remove new and existing hosting
					$this->hosting->removeItem('hosting');
					$this->element->removeItem('ExistingHosting');
					
					// If we do have custom nameservers, check for valid input
					if(isset($_POST['CustomNameServers']) && $_POST['CustomNameServers'] == 'yes')
					{
						// We require two nameservers
						if(!trim($_POST['NS1']) || !trim($_POST['NS2']))
						{
							$this->Error[] = __('you need at least two nameservers');
						}
					}					
					break;
				
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
		// Handling custom WHOIS data
		$this->__handleCustomWhoisData();
		
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