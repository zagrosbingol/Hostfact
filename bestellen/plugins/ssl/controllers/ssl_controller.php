<?php

class SSL_Controller extends OrderForm_Controller
{
	
	public function __construct()
	{	
		// Load parent constructor
		parent::__construct();
		
		// Create instances of uses objects
		$this->ssl 	= new SSL_Model();
		global $orderform_settings;
		$this->orderform_settings = $orderform_settings->OtherSettings->ssl;
		$this->set('orderform_settings', $this->orderform_settings);
	
	}
	
	function start()
	{
		// Check if we should stay at this step, if we already have domains, go to details instead
		if(!isset($_GET['step']))
		{
			// Check also for GET variables
			if(isset($_GET['ssl']))
			{
				// Set SSL
				$this->ssl->newItem('SSL');
				$this->ssl->setAttribute('ProductCode', $_GET['ssl']);	
				$this->ssl->saveItem();		
			}

			// If we have a ssl certificate, go to details
			if(!empty($this->ssl->_elementlist))
			{
				return $this->details();
			}
		}
		
		if($this->orderform_settings->hasProductWizard == 'no')
		{
			return $this->details();
		}
		else
		{
			global $orderform_settings;
			$list_products = $this->ssl->getProductsFromGroup($orderform_settings->ProductGroups->ssl);
		
			$hasWildcard = $hasMultiDomain = $hasExtended = false;
		
			// Check if we need to ask for questions
			foreach($list_products as $tmp_product)
			{
				if($tmp_product->Wildcard == 'yes')
				{
					$hasWildcard = true;
				}
				
				if($tmp_product->MultiDomain == 'yes')
				{
					$hasMultiDomain = true;
				}
				
				if($tmp_product->Type == 'extended')
				{
					$hasExtended = true;
				}				
			}

			// Show start page
			$this->set('hasWildcard', 		$hasWildcard);
			$this->set('hasMultiDomain', 	$hasMultiDomain);
			$this->set('hasExtended', 		$hasExtended);
			$this->display('start.phtml');
		}
		
	}
	
	function details()
	{
		// Get products and pass to view
		global $orderform_settings;
		$list_products = $this->ssl->getProductsFromGroup($orderform_settings->ProductGroups->ssl);
		
		$this->set('list_products', $list_products);
		
		// Handling options
		$this->__handleOptions();
		
		// Handle other POST data from domainform
		if(isset($_POST['step']))
		{
			// Store product in session
			$this->ssl->getItem('SSL');
			$this->ssl->setAttribute('ProductCode', $_POST['SSL']);

			$commonname = rtrim(preg_replace('/^https?:\/\//i', '', $_POST['CommonName']), '/'); // Remove http:// or https:// and / after domain
			$this->ssl->setAttribute('CommonName', $commonname);
			
			$this->ssl->setAttribute('ApproverEmail', ($this->orderform_settings->askforApproverMail == 'yes') ? $_POST['ApproverEmail'] : '');
			$this->ssl->setAttribute('CSR', ($this->orderform_settings->askforCSR == 'yes' || (isset($_POST['hasCSR']) && $_POST['hasCSR'] == 'yes')) ? $_POST['CSR'] : '');
			$this->ssl->setAttribute('Period', $_POST['Period']);
			$this->ssl->setAttribute('Periods', $_POST['Period']); // Set billing cycle same as ssl period
			$this->ssl->setAttribute('Periodic', 'j'); // Set billing cycle same as ssl period
		
			// Get some additional information
			if($_POST['SSL'] && $sslinfo = $this->ssl->getSSLTypeInfo($_POST['SSL']))
			{
				$this->ssl->setAttribute('ValidationType', $sslinfo->Type);
				
				$this->ssl->setAttribute('Wildcard', ($sslinfo->Wildcard == 'yes') ? 'yes' : 'no');
				$this->ssl->setAttribute('MultiDomain', ($sslinfo->MultiDomain == 'yes') ? 'yes' : 'no');
				$this->ssl->setAttribute('MultiDomainRecords', ($sslinfo->MultiDomain == 'yes' && isset($_POST['MultiDomain'])) ? $_POST['MultiDomain'] : array());	
			}
			
			// Store helpers
			if($this->orderform_settings->askforCSR == 'optional')
			{
				$this->order->set('hasCSR', $_POST['hasCSR']);
			}
			
			$this->ssl->saveItem();
	
			// We do ofcourse need a product code to be selected
			if(!isset($_POST['SSL']) || !$_POST['SSL'])
			{
				$this->Error[] = __('you need to select a ssl certificate', 'ssl');
			}
			// Make the common name required
			if(!trim($commonname))
			{
				$this->Error[] = __('you need to select a common name', 'ssl');
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
		
		// Create a group-divided array for certificates
		if($this->orderform_settings->Groupable != 'no')
		{
			$list_products_by_group = array();
			
			
			$list_products_by_group['domain'] = array('title' => __('validation type domain', 'ssl'), 'products' => array());
			$list_products_by_group['organization'] = array('title' => __('validation type organization', 'ssl'), 'products' => array());
			$list_products_by_group['extended'] = array('title' => __('validation type extended', 'ssl'), 'products' => array());
			
			foreach($list_products as $product_tmp)
			{
				$list_products_by_group[$product_tmp->Type]['products'][] = $product_tmp;
			}

			$this->set('list_products_by_group' , $list_products_by_group);
		}
		
		// Get elements in session and pass to view
		$element_list = $this->ssl->listItems();
		
		$this->set('ssl', isset($element_list['SSL']) ? $element_list['SSL'] : array());
		$this->set('sslinfo', (isset($element_list['SSL']['ProductCode']) && $element_list['SSL']['ProductCode']) ? $this->ssl->getSSLTypeInfo($element_list['SSL']['ProductCode']) : false);

		// Get other elements in session and pass to view
		$element_list = $this->element->listItems();
		$this->set('element_list', $element_list);
		
		// Pass order instance to view
		$this->set('order',  $this->order->show());	
		
		// Load view
		$this->display('details.phtml');	
	}
	
	function router()
	{
		
		if(isset($_POST['step']) && $_POST['step'])
		{
			// Only valid steps will return the corresponding action name
			switch($_POST['step'])
			{
				case 'get_ssl_info': return $this->__getSSLInfo(); break;
				case 'get_ssl_wizard_results': return $this->__getSSLWizardResults(); break;
			}
		}

		return parent::router();
		
	}
		
	function getCart($escaped = true)
	{
		// Add all different elements to order object, in the following order: ssl/other		
		$this->order->elements = array();
		
		// Get SSL
		$element_list = $this->ssl->listItems($escaped);
		foreach($element_list as $ssl => $ssl_info)
		{
			// Add element into order
			$this->order->addElement($ssl_info);
		}
		
		// Get other items
		$element_list = $this->element->listItems($escaped);
		foreach($element_list as $element => $element_info)
		{
			// Add element into order
			$this->order->addElement($element_info);
		}
	}
	
	private function __getSSLInfo()
	{
		
		$sslinfo = $this->ssl->getSSLTypeInfo($_POST['ssl_product']);
		
		if($sslinfo)
		{
			$response = array();
			$response['Periods'] 				= $sslinfo->Periods;
			$response['Type'] 					= $sslinfo->Type;
			$response['Wildcard'] 				= $sslinfo->Wildcard;
			$response['MultiDomain'] 			= $sslinfo->MultiDomain;
			$response['MultiDomainIncluded'] 	= $sslinfo->MultiDomainIncluded;
			$response['MultiDomainMax'] 		= $sslinfo->MultiDomainMax;
			
			// Format price labels
			if(SHOW_SELECT_PRICE)
			{
				if($sslinfo->PeriodPrices)
				{
					// Loop prices
					foreach($sslinfo->PeriodPrices['period'] as $key => $tmp_price_period)
					{
						$sslinfo->PeriodPrices['period'][$key]['PriceLabel'] = w_money((SHOW_VAT_INCLUDED) ? $tmp_price_period['PriceIncl'] : $tmp_price_period['PriceExcl']).' '.w_period(1, 'j', 'per');
					}
					
					$response['PeriodPrices'] 		= $sslinfo->PeriodPrices;
				}
				else
				{
					$this->element->getProductDetails($_POST['ssl_product']);
					
					$response['PriceLabel'] = w_money((SHOW_VAT_INCLUDED) ? $this->element->PriceExcl * (1+$this->element->TaxPercentage) : $this->element->PriceExcl).' '.w_period(1, $this->element->Periodic, 'per');
				}
			}
			echo json_encode($response);
			exit;
		}

		exit;
	}
	
	private function __getSSLWizardResults()
	{
		// Process filters
		$wildcard = false;
		$multidomain = false;
		
		if(isset($_POST['wizard_one_or_more']) && $_POST['wizard_one_or_more'])
		{
			switch($_POST['wizard_one_or_more'])
			{
				case 'one':
					$wildcard = 'no';
					$multidomain = 'no';
					break;
				case 'wildcard':
					$wildcard = true;
					$multidomain = false;
					break;
				case 'multidomain':
					$wildcard = false;
					$multidomain = true;
					break;
			}
		}
		
		$extended = false;
		if($extended === false && isset($_POST['wizard_extended']) && $_POST['wizard_extended'])
		{
			switch($_POST['wizard_extended'])
			{
				case 'yes':
					$extended = true;
					break;
				case 'no':
					$extended = false;
					break;
			}
		}
		
		global $orderform_settings;
		$list_products = $this->ssl->getProductsFromGroup($orderform_settings->ProductGroups->ssl);
		
		$list_products_meets_choices = array();
		
		// Filter products
		foreach($list_products as $tmp_product)
		{
			if(	(($extended === true && $tmp_product->Type == 'extended') || ($extended === false && $tmp_product->Type != 'extended'))
				&&
				(($wildcard === true && $tmp_product->Wildcard == 'yes') || $wildcard === false || ($wildcard === 'no' && $tmp_product->Wildcard != 'yes'))
				&&
				(($multidomain === true && $tmp_product->MultiDomain == 'yes') || $multidomain === false || ($multidomain === 'no' && $tmp_product->MultiDomain != 'yes'))
				)
			{
				$list_products_meets_choices[] = $tmp_product;
			}
			
		}
		
		// Create table HTML
		$this->set('list_products_meets_choices', $list_products_meets_choices);
		$this->element('wizard_results.phtml');
	}
}
