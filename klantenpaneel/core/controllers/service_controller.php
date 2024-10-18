<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Service_Controller extends Base_Controller
{
	protected $PluginPath = '';

	public function __construct(Template $template)
	{
		parent::__construct($template);

		// check if this controller is a plugin
		if(isset($this->FullPluginName) && $this->FullPluginName && isset(Plugin::$loaded_plugins[$this->FullPluginName]))
		{
			// Store URL
			$this->Template->ServiceUrlString	= Plugin::$loaded_plugins[$this->FullPluginName]['instance']->UrlString;

			// load model
			$model_name = $this->FullPluginName.'_Model';

			$this->ServiceModel = new $model_name;
			$this->ServiceModel->FullPluginName = $this->FullPluginName;
		}
		else
		{
			// Store URL
			$this->Template->ServiceUrlString	= __('service', 'url');

			// load model
			$this->ServiceModel = new Service_Model;
		}

		// Set sidebar
		$this->Template->setSidebar('service.sidebar');
	}

	public function index()
	{
		$this->setListServices();

		if(!empty($this->Template->services))
		{
			// We want to show the latest other service
			header('Location: ' . __SITE_URL . '/' . __('service', 'url') . '/' . __('view', 'url') . '/' . $this->Template->services[0]['Identifier']);
			exit;
		}
		else
		{
			// No other service, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function serviceAll()
	{
		$services = array();

		// Only show when we have other services
		$service_list = $this->ServiceModel->listServices();
		if(!empty($service_list))
		{
			$services['service'] = array('element' 	=> 'service.table',
										 'title'	=> __('mainmenu other services'));

			// Put variables in template
			$this->Template->service_list = $service_list;
		}

		$services = Hook::doFilter('service_page', $services, $this->Template);
		$services = order_service_array($services);

		$this->Template->setSidebar(false);

		$this->Template->services 		= $services;
		$this->Template->show('service.all');
	}

	public function orderNew()
	{
		// Get list of services for submenu
		$this->setListServices();

		// Set iframe URL for orderform
		$this->Template->iframe_url = Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_OTHER_ORDERFORM');

		$this->Template->show('page.iframe');
	}

	public function view()
	{

		$this->ServiceModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->ServiceModel->show())
		{
			$this->formatSubscription($this->ServiceModel);
			$this->Template->service = $this->ServiceModel;
			$this->Template->servicetype = 'service';
		}

		$this->setListServices();

		$this->Template->parseMessage($this->ServiceModel);

		if($result)
		{
			$this->Template->show('service.view');
		}
		else
		{
			$this->Template->header = __('other service');
			$this->Template->text   = __('other service does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function terminate()
	{
		// sanitize post strings
		$_POST = sanitize_post_values($_POST);

		$this->ServiceModel->id = intval($_POST['ServiceID']);
		if(!$this->ServiceModel->show() || !in_array(Settings_Model::get('CLIENTAREA_SERVICE_TERMINATE'), array('yes', 'approve'), TRUE))
		{
			// flashmessage and redirect back to page
			$this->Template->flashMessage($this->ServiceModel);

			die('reload');
		}

		$this->ServiceModel->Password    = $_POST['Password'];
		$this->ServiceModel->ServiceType = $_POST['ServiceType'];
		$this->ServiceModel->ServiceID   = $_POST['ServiceID'];
		$this->ServiceModel->Reason      = $_POST['Reason'];

		$result = $this->ServiceModel->terminate();

		if($result === true)
		{
			// flashmessage and redirect back to page
			$this->Template->flashMessage($this->ServiceModel);

			// Set session variable to prevent double message
			$_SESSION['RedirectedFromTerminate'] = true;

			die('reload');
		}
		else
		{
			$this->Template->service 		= $this->ServiceModel;
			$this->Template->servicetype 	= $this->ServiceModel->ServiceType;

			$this->Template->parseMessage($this->ServiceModel);
			$this->Template->showElement('service.terminate');
		}
	}

	public function setListServices()
	{
		$this->Template->services = $this->ServiceModel->listServices();
	}

	public function cancelModification()
	{
		// sanitize post strings
		$_POST = sanitize_post_values($_POST);

		$this->ServiceModel->id = intval($_POST['ServiceID']);
		if(!$this->ServiceModel->show())
		{
			// flashmessage and redirect back to pricequote page
			$this->Template->flashMessage($this->ServiceModel);

			die('reload');
		}

		$this->ServiceModel->ServiceType      = $_POST['ServiceType'];
		$this->ServiceModel->ServiceID        = $_POST['ServiceID'];
		$this->ServiceModel->ModificationType = $_POST['ModificationType'];

		$result = $this->ServiceModel->cancelModification();

		if($result === true)
		{
			// flashmessage and redirect back to pricequote page
			$this->Template->flashMessage($this->ServiceModel);

			die('reload');
		}
		else
		{
			$this->Template->service          = $this->ServiceModel;
			$this->Template->servicetype      = $this->ServiceModel->ServiceType;
			$this->Template->modificationtype = $this->ServiceModel->ModificationType;

			$this->Template->flashMessage($this->ServiceModel);
			die('reload');
		}
	}

	protected function formatSubscription($service)
	{
		if(isset($service->Subscription))
		{
			// calculate start date of current period
			if($service->Subscription['Periods'] && $service->Subscription['Periodic'] && $service->Subscription['StartPeriod'])
			{
				$periodic_array = array('d' => 'day', 'w' => 'week', 'm' => 'month', 'j' => 'year');

				switch($service->Subscription['Periodic'])
				{
					case 'k':
						$periods = $service->Subscription['Periods'] * 3;
						$periodic = 'month';
					break;

					case 'h':
						$periods = $service->Subscription['Periods'] * 6;
						$periodic = 'month';
					break;

					case 't':
						$periods = $service->Subscription['Periods'] * 2;
						$periodic = 'year';
					break;

					default:
						$periods = $service->Subscription['Periods'];
						$periodic = $periodic_array[$service->Subscription['Periodic']];
					break;
				}

				$newdate = strtotime('-'.$periods.' '.$periodic, strtotime($service->Subscription['StartPeriod']));

				$service->Subscription['StartCurrent'] = date('Y-m-j', $newdate);
				$service->Subscription['EndCurrent']   = $service->Subscription['StartPeriod'];
			}
		}
	}


	/** Used for calculating the date on which a service expires
	 *
	 * @param $service
	 * @param bool|FALSE $service_has_expiration_date	mainly used by domains, which have a expiration date
	 * @return bool|string
	 */
	public function getEndDate($service, $service_has_expiration_date = FALSE)
	{
		// Calculate end date
		if(!empty($service->Subscription))
		{
			$subscription_info = $service->Subscription;

			$end_contract = ($subscription_info['EndContract']) ? $subscription_info['EndContract'] : $subscription_info['StartPeriod'];
			$contract_periods 	= ($subscription_info['ContractPeriods'] > 0) ? $subscription_info['ContractPeriods'] : $subscription_info['Periods'];
			$contract_periodic 	= ($subscription_info['ContractPeriods'] > 0) ? $subscription_info['ContractPeriodic'] : $subscription_info['Periodic'];

			$current_date_with_term = date('Y-m-d', strtotime('+' . (int) Settings_Model::get('TERMINATION_NOTICE_PERIOD') . ' days'));
			$end_date = $end_contract;

			// If before end of contract
			if($current_date_with_term <= $end_date)
			{
				// End date is right, it is the end of contract
			}
			else
			{
				global $account;
				if(Settings_Model::get('TERMINATION_NOTICE_PERIOD_WVD') == 'yes' && $account->CompanyName == '')
				{
					$end_date			= date('Y-m-d');
					$contract_periods 	= (Settings_Model::get('TERMINATION_NOTICE_PERIOD') > 30) ? 1 		: Settings_Model::get('TERMINATION_NOTICE_PERIOD');
					$contract_periodic 	= (Settings_Model::get('TERMINATION_NOTICE_PERIOD') > 30) ? 'm' 	: 'd';

					// Loop till date (Wet van Dam)
					do
					{
						$end_date = calculate_date($end_date, $contract_periods, $contract_periodic);
					}
					while($current_date_with_term > $end_date && $end_date !== false);
				}
				else
				{
					// Loop till date
					do
					{
						$end_date = calculate_date($end_date, $contract_periods, $contract_periodic);
					}
					while($current_date_with_term > $end_date && $end_date !== false);
				}
			}
		}
		else
		{
			// Today with termination notice period
			$end_date = date('Y-m-d', strtotime('+' . (int) Settings_Model::get('TERMINATION_NOTICE_PERIOD') . ' days'));

			// We seems to have a expiration date on the service
			if($service_has_expiration_date !== FALSE && $service_has_expiration_date > $end_date)
			{
				$end_date = $service_has_expiration_date;
			}
		}

		return $end_date;
	}

	// Generate a random password for a control panel account
	public function generateRandomPassword()
	{
		if(!defined('PASSWORD_GENERATION'))
		{
			define('PASSWORD_GENERATION', Settings_Model::get('PASSWORD_GENERATION'));
		}

		$response 	= array(
			'password' => generatePassword()
		);

		echo json_encode($response);
		exit;
	}
}