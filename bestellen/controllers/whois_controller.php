<?php

class Whois_Controller extends Base_Controller
{
	
	public $whois;

	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();	
		
		$this->whois = new Whois_Model();	
		
		$this->setTheme('whois');
		$this->setIncludeWhoisHeader(true);	
	}
	
	/**
	 * Whois_Controller::getForm()
	 * Show form for entering a domain
	 * 
	 * @return void
	 */
	function getForm()
	{		
		// Show form
		$this->display('domain_form.phtml');
	}

	/**
	 * Whois_Controller::getResultTable()
	 * Show de resultpage of WHOIS check
	 * 
	 * @return void
	 */
	function getResultTable()
	{
		
		// Do we have a domain in our session?
		if(isset($_SESSION['whois_'.ORDERFORM_ID.'_domain']))
		{

			// Check if domain is a valid domain
			if(!$this->whois->parseDomain($_SESSION['whois_'.ORDERFORM_ID.'_domain']))
			{
				// put error in variable
				$this->set('domain_error', 	$this->whois->Error[0]);
				
				// Show error page
				$this->display('domain_error.phtml');
				exit;
			}
			
			// put warning in variable
			if(isset($this->whois->Warning[0]) && $this->whois->Warning[0])
			{
				$this->set('domain_warning', 	$this->whois->Warning[0]);
			}
			
			// Get topleveldomains
			$this->set('sld', 			$this->whois->SLD);
			$this->set('tlds_popular', 	$this->whois->getTopLevelDomains('popular'));
			$this->set('tlds_other', 	$this->whois->getTopLevelDomains('other'));
		}

		// Get domains and pass list to view
		$this->domain 	= new Domain_Model();
		$domain_list = $this->domain->listItems();
		$this->set('domains', $domain_list);

		// Show result page
		$this->display('result_table.phtml');
		
	}
	
	/**
	 * Whois_Controller::ajaxCheckDomain()
	 * If domains are checked on public whois-servers, we will execute one by one via AJAX calls.
	 * 
	 * @param mixed $tld
	 * @return json_encoded array with result
	 */
	function ajaxCheckDomain($tld)
	{
		
		// Check domain for valid SLD
		if(!$this->whois->parseDomain($_SESSION['whois_'.ORDERFORM_ID.'_domain']))
		{
			// If we do not have a valid SLD, we don't need to execute the real check.
			$json_result = array('status' => 'error', 'text' => __('whois status error'), 'link'  => '&nbsp;');
			echo json_encode($json_result);
			exit;
		}
		
		// Do we have result in cache?
		if(isset($_SESSION['whois_results'][$this->whois->SLD.'.'.$tld]) && $_SESSION['whois_results'][$this->whois->SLD.'.'.$tld])
		{
			$result = $_SESSION['whois_results'][$this->whois->SLD.'.'.$tld];
		}
		else
		{
			// Check domain
			$result = $this->whois->checkDomain($this->whois->SLD, $tld);
		}
		
		// Build response array
		$json_result = array();
		
		switch($result)
		{
			case 'available':
				$json_result['status'] 	= 'available';
				$json_result['text'] 	= __('whois status available');
				$json_result['link']	= (__('whois link available')) ? '<a class="order_link">'.__('whois link available').'</a>' : '';
				break;
			case 'unavailable':
				$json_result['status'] 	= 'unavailable';
				$json_result['text'] 	= __('whois status unavailable');
				$json_result['link']	= (__('whois link unavailable')) ? '<a class="order_link">'.__('whois link unavailable').'</a>' : '';
				break;
			case 'invalid':
				$json_result['status'] 	= 'invalid';
				$json_result['text'] 	= __('whois status invalid');
				$json_result['link']	= '';
				break;
			default:
				$json_result['status'] 	= 'error';
				$json_result['text'] 	= __('whois status error');
				$json_result['link']	= (__('whois link error')) ? '<a class="order_link">'.__('whois link error').'</a>' : '';
				break;
			
		}
		
		// If domain is already in shopping cart, replace order-link
		$this->domain = new Domain_Model();
		if($this->domain->getItem($this->whois->SLD.'.'.$tld))
		{
			$json_result['link'] = __('in shopping cart').' <a class="remove_link"><img src="'.ORDERFORM_URL.'images/delete.png" alt="" style="float:right;margin: 8px 5px 0px 0px;"/></a>';
		}
		
		// Store result in cache (if we received a correct response)
		$_SESSION['whois_results'][$this->whois->SLD.'.'.$tld] = ($json_result['status'] != 'error') ? $json_result['status'] : '';
		
		// return json_encoded response
		return json_encode($json_result);
	}
	
	/**
	 * Whois_Controller::ajaxSaveDomain()
	 * Simple AJAX command to save domain into session-variable. Checks will be done later.
	 * 
	 * @param mixed $domain
	 * @return void
	 */
	function ajaxSaveDomain($domain)
	{
		// Store domain in session variable
		$_SESSION['whois_'.ORDERFORM_ID.'_domain'] = $domain;
	}
	
	/**
	 * Whois_Controller::ajaxOrderDomain()
	 * Put domain in shoppingcart via AJAX call. 
	 * 
	 * @param mixed $tld
	 * @return json_encoded array with value for 'in shopping cart'.
	 */
	function ajaxOrderDomain($tld)
	{
		// Check domain for valid SLD
		if(!$this->whois->parseDomain($_SESSION['whois_'.ORDERFORM_ID.'_domain']))
		{
			// Build return array
			$json_result = array('error' => $this->whois->Error, 'count' => count($_SESSION['OrderForm'.ORDERFORM_ID]['Domain_Model']['Elements']));
			
			// Return json_encoded array
			echo json_encode($json_result);
			exit;
		}
		
		
		// Check if domain is not already ordered, we need it only once
		$this->domain = new Domain_Model();
		$tmp_domain = $this->whois->SLD.'.'.$tld;
		if(!$this->domain->getItem($tmp_domain))
		{
			$this->domain->newItem($tmp_domain);
			$this->domain->setAttribute('Domain', $tmp_domain);
			$this->domain->saveItem();	
		}
		
		// Build return array
		$json_result = array('ordered' => __('in shopping cart').' <a class="remove_link"><img src="'.ORDERFORM_URL.'images/delete.png" alt="" style="float:right;margin: 8px 5px 0px 0px;"/></a>', 'count' => count($_SESSION['OrderForm'.ORDERFORM_ID]['Domain_Model']['Elements']));
		
		// Return json_encoded array
		echo json_encode($json_result);
		exit;
	}
	
	
	/**
	 * Whois_Controller::ajaxRemoveDomain()
	 * Removes domain from shopping cart
	 * 
	 * @param mixed $tld
	 * @return void
	 */
	function ajaxRemoveDomain($tld)
	{
		// Check domain for valid SLD
		if($this->whois->parseDomain($_SESSION['whois_'.ORDERFORM_ID.'_domain']))
		{
			// Check if domain is already ordered,then remove it
			$this->domain = new Domain_Model();
			$this->domain->removeItem($this->whois->SLD.'.'.$tld);	
		}	
				
		// Check domain status
		$json_result = $this->ajaxCheckDomain($tld);
		$result = json_decode($json_result);
		
		// Build return array
		$json_result = array('link' => $result->link, 'count' => count($_SESSION['OrderForm'.ORDERFORM_ID]['Domain_Model']['Elements']));
		
		// Return json_encoded array
		echo json_encode($json_result);
		exit;
	}


}