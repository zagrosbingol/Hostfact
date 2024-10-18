<?php

class VPS_Controller extends OrderForm_Controller
{
	
	public function __construct()
	{	
		// Load parent constructor
		parent::__construct();
		
		// Create instances of uses objects
		$this->vps 	= new VPS_Model();
		global $orderform_settings;
		$this->orderform_settings = $orderform_settings->OtherSettings->vps;
		$this->set('orderform_settings', $this->orderform_settings);
		
		// Set theme information
		if($this->orderform_settings->Theme == 'packages')
		{
			define("PACKAGES_GRID",			(isset($this->orderform_settings->PackagesGrid)) ? $this->orderform_settings->PackagesGrid : 4); // 4 | 3 | 2 | 1
			
			$package_descriptions = array();
			
			if(isset($this->orderform_settings->PackagesDescription)){ 
				foreach($this->orderform_settings->PackagesDescription as $prod_id => $prod_desc){
					$package_descriptions[$prod_id] = $prod_desc;
				}
			}
			
			$this->set('package_descriptions', $package_descriptions);
		}
		elseif($this->orderform_settings->Theme == 'compare')
		{
			$compare_matrix = array();
			$compare_matrix['legend'] = $this->orderform_settings->CompareLabels;
		
			if(isset($this->orderform_settings->CompareValues)){ 
				foreach($this->orderform_settings->CompareValues as $prod_id => $prod_values){
					$compare_matrix[$prod_id] = $prod_values;
				}
			}

			$this->set('compare_matrix', $compare_matrix);
		}
		
		// Default values
		if($this->orderform_settings->DefaultPackage && $this->vps->getItem('VPS') === false)
		{
			// Default VPS package
			$this->vps->newItem('VPS');
			$this->vps->setAttribute('ProductCode', $this->orderform_settings->DefaultPackage);
			$this->vps->saveItem();	
		}
		
	
	}
	
	function start()
	{	
		
		// Check also for GET variables
		if(isset($_GET['vps']))
		{
			$this->vps->newItem('VPS');
			$this->vps->setAttribute('ProductCode', $_GET['vps']);
			$this->vps->saveItem();	
		}
		
		// Default orderform doesn't have an extra step
		return $this->details();
	}
	
	function details()
	{
		// Get products and pass to view
		global $orderform_settings;
		$list_products = $this->vps->getProductsFromGroup($orderform_settings->ProductGroups->vps);
		
		$this->set('list_products', $list_products);
		
		// Handling options
		$this->__handleOptions();
		
		// Handle other POST data from domainform
		if(isset($_POST['step']))
		{
			// Store product in session
			// Set period
			if(PERIOD_CHOICE == 'yes' && isset($_POST['BillingPeriod']))
			{
				$this->order->set('PricePeriod', $_POST['BillingPeriod']);
			}
			
			// Add new VPS product
			$this->vps->newItem('VPS');
			$this->vps->setAttribute('ProductCode', (isset($_POST['VPS'])) ? $_POST['VPS'] : '');
			$this->vps->setAttribute('Hostname', (isset($_POST['Hostname'])) ? $_POST['Hostname'] : '');
			$this->vps->setAttribute('Image', (isset($_POST['Image'])) ? $_POST['Image'] : '');
			$this->vps->saveItem();	
			
			// We do offcourse need a product code to be selected
			if(!isset($_POST['VPS']) || !$_POST['VPS'])
			{
				$this->Error[] = __('you need to select a product');
			}
			else
			{
				// Validate entered VPS data with the validate() function in VPS_Model
				if(!$this->vps->validate())
				{
					// There is some invalid data, merge error
					$this->Error = array_merge($this->Error, $this->vps->Error);
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
		
		// Get elements in session and pass to view
		$element_list = $this->vps->listItems();
		
		$vps = isset($element_list['VPS']) ? $element_list['VPS'] : array();
		$this->set('vps', $vps);
		if(isset($vps['ProductCode']) && $vps['ProductCode'])
		{
			$package_info = $this->vps->getPackageInfo($vps['ProductCode']);
			$this->set('package_info', $package_info);
		}
		
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
				case 'get_package_info': return $this->__getPackageInfo(); break;
			}
		}

		return parent::router();
		
	}
	
	private function __getPackageInfo()
	{
		
		$vpsinfo = $this->vps->getPackageInfo($_POST['vps_product']);
		
		if($vpsinfo)
		{
			$response = array();
			$response['ImageList'] 		= $vpsinfo->ImageList;
			

			echo json_encode($response);
			exit;
		}

		exit;
	}
		
	function getCart($escaped = true)
	{
		// Add all different elements to order object, in the following order: vps/other		
		$this->order->elements = array();
		
		// Get VPS
		$element_list = $this->vps->listItems($escaped);
		foreach($element_list as $vps => $vps_info)
		{
			// Add element into order
			$this->order->addElement($vps_info);
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
