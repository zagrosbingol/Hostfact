<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Controller;
use Plugin;
use Settings_Model;

class Domain_Controller extends Service_Controller
{
	public function __construct(\Template $template)
	{
		// Call service controller construct
		parent::__construct($template);

		// Make alias for domain model
		$this->DomainModel = $this->ServiceModel;

		// Set sidebar
		$this->Template->setSidebar('domain.sidebar');
	}

	public function index()
	{
		$this->setListDomains();

		if(!empty($this->Template->domains))
		{
			// We want to show the latest domain
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->Template->domains[0]['Identifier']);
			exit;
		}
		else
		{
			// No domains, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function setListDomains()
	{
		$this->Template->domains = $this->DomainModel->listDomains();
	}

	public function orderNew()
	{
		// Get list of domains for submenu
		$this->setListDomains();

		// Set iframe URL for orderform
		$this->Template->iframe_url = Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_DOMAIN_ORDERFORM');

		$this->Template->show('page.iframe');
	}

	public function view()
	{
		$this->DomainModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->DomainModel->show())
		{
			$this->formatSubscription($this->DomainModel);
			$expiration_date = ($this->DomainModel->ExpirationDate && $this->DomainModel->ExpirationDate != '0000-00-00') ? $this->DomainModel->ExpirationDate : FALSE;
			$this->DomainModel->EndDate = $this->getEndDate($this->DomainModel, $expiration_date);
			$this->Template->service = $this->DomainModel;
			$this->Template->servicetype = 'domain';
		}

		$this->setListDomains();

		$this->Template->parseMessage($this->DomainModel);

		$active_service_modules = Settings_Model::get('ACTIVE_SERVICE_MODULES');
		// only when the module is active, do we want to show dns management
		if(	$active_service_modules && is_array($active_service_modules) && in_array('dnsmanagement', $active_service_modules))
		{
			$this->Template->dnsmanagement = TRUE;
		}
		else
		{
			$this->Template->dnsmanagement = FALSE;
		}

		// Allowed to retrieve token? It can depend on open invoices, so we will need this list...
		$allowed_token = (Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') != 'yes' && Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') != 'checkinvoice') ? FALSE : TRUE;

		if(Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') == 'checkinvoice')
		{
			$invoiceModel = new \Invoice_Model();
			$invoices = $invoiceModel->listInvoices();
			if($invoices !== FALSE)
			{
				foreach ($invoices as $_invoice)
				{
					// Store outstanding invoices in separate array
					if(in_array($_invoice['Status'], array(2, 3)))
					{
						$this->DomainModel->Error[] = __('token could not be retrieved, contact us');
						$allowed_token = FALSE;
						break;
					}
				}
			}
		}

		$this->Template->allowed_token = $allowed_token;

		if(isset($result) && $result)
		{
			$this->Template->show('domain.view');
		}
		else
		{
			$this->Template->header = __('domain');
			$this->Template->text   = __('domain does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function nameservers()
	{
		$this->DomainModel->id = intval(get_url_var($_GET['rt']));

		// Allowed to change nameservers?
		if(Settings_Model::get('CLIENTAREA_DOMAIN_NAMESERVER_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DOMAIN_NAMESERVER_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
			exit;
		}

		$this->isActionValid('nameservers');

		$this->formatSubscription($this->DomainModel);

		// if there are pending modifications, show the new modified data
		if(isset($this->DomainModel->ClientareaModifications['changenameserver']['Data']))
		{
			$modified_data = json_decode($this->DomainModel->ClientareaModifications['changenameserver']['Data'], TRUE);

			$this->DomainModel->DNS1 = (isset($modified_data['DNS1'])) ? $modified_data['DNS1'] : $this->DomainModel->DNS1;
			$this->DomainModel->DNS2 = (isset($modified_data['DNS2'])) ? $modified_data['DNS2'] : $this->DomainModel->DNS2;
			$this->DomainModel->DNS3 = (isset($modified_data['DNS3'])) ? $modified_data['DNS3'] : $this->DomainModel->DNS3;
			$this->DomainModel->DNS1IP = (isset($modified_data['DNS1IP'])) ? $modified_data['DNS1IP'] : $this->DomainModel->DNS1IP;
			$this->DomainModel->DNS2IP = (isset($modified_data['DNS2IP'])) ? $modified_data['DNS2IP'] : $this->DomainModel->DNS2IP;
			$this->DomainModel->DNS3IP = (isset($modified_data['DNS3IP'])) ? $modified_data['DNS3IP'] : $this->DomainModel->DNS3IP;
		}

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			// validate that user has modified data
			$data_is_changed = is_data_modified($_POST, $this->DomainModel);

			$full_domain = '.' . $this->DomainModel->Domain . '.' . $this->DomainModel->Tld;
			for($i = 1; $i <= 3; $i++)
			{
				$this->DomainModel->{'DNS'.$i} = $_POST['DNS'.$i];
				if($this->DomainModel->{'DNS'.$i} && strpos($this->DomainModel->{'DNS'.$i}, $full_domain) !== FALSE)
				{
					$this->DomainModel->{'DNS'.$i.'IP'} = $_POST['DNS'.$i.'IP'];
				}
				else
				{
					$this->DomainModel->{'DNS'.$i.'IP'} = '';
				}
			}

			if($data_is_changed === FALSE)
			{
				$this->DomainModel->Success[] = __('changes successfully processed');
				$this->Template->flashMessage($this->DomainModel);
				header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
				exit;
			}
			else
			{
				if($this->DomainModel->editNameservers())
				{
					$this->Template->flashMessage($this->DomainModel);
					// we set this to prevent showing a warning and success message at the same time
					$_SESSION['SkipModificationWarning'] = 'changenameserver';
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
					exit;
				}
			}
		}

		$this->setListDomains();

		$this->Template->service = $this->DomainModel;
		$this->Template->servicetype = 'domain';
		$this->Template->modificationtype = 'changenameserver';

		$this->Template->parseMessage($this->DomainModel);
		$this->Template->show('domain.nameservers');
	}

	public function getHosting()
	{
		$this->DomainModel->HostingID = intval(get_url_var($_GET['rt']));

		if(!$hosting_account = $this->DomainModel->getHosting())
		{
			echo json_encode(array('message' => __('data could not be retrieved')));
			exit;
		}

		$json_array = array('Username' => $hosting_account['Username'],
							'PackageName' => (isset($hosting_account['PackageName'])) ? $hosting_account['PackageName'] : '');

		// get the URL from the domain plugin
		if(isset(Plugin::$loaded_plugins) && isset(Plugin::$loaded_plugins[__NAMESPACE__ . '\Hosting']) && isset(Plugin::$loaded_plugins[__NAMESPACE__ . '\Hosting']['instance']->UrlString))
		{
			$json_array['hosting_url'] = __SITE_URL . '/' . Plugin::$loaded_plugins[__NAMESPACE__ . '\Hosting']['instance']->UrlString . '/' . __('view', 'url') . '/';
			$json_array['hosting_id']  = $this->DomainModel->HostingID;
		}

		echo json_encode($json_array);
		exit;
	}

	public function getToken()
	{
		$this->DomainModel->id = get_url_var($_GET['rt']);

		// Allowed to retrieve token? It can depend on open invoices, so we will need this list...
		$allowed = (Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') != 'yes' && Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') != 'checkinvoice') ? FALSE : TRUE;

		if(Settings_Model::get('CLIENTAREA_DOMAIN_TOKEN') == 'checkinvoice')
		{
			$invoiceModel = new \Invoice_Model();
			$invoices = $invoiceModel->listInvoices();
			if($invoices !== FALSE)
			{
				foreach ($invoices as $_invoice)
				{
					// Store outstanding invoices in separate array
					if(in_array($_invoice['Status'], array(2, 3)))
					{
						$this->DomainModel->Error[] = __('token could not be retrieved, contact us');
						$allowed = FALSE;
						break;
					}
				}
			}
		}

		if($allowed === false)
		{
			$this->Template->flashMessage($this->DomainModel);
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
			exit;
		}

		$this->isActionValid('gettoken');

		$this->DomainModel->getToken();

		$this->Template->flashMessage($this->DomainModel);
		header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
		exit;
	}

	public function whois()
	{
		$this->DomainModel->id = intval(get_url_var($_GET['rt']));

		// Allowed to change whois?
		if(Settings_Model::get('CLIENTAREA_DOMAIN_WHOIS_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DOMAIN_WHOIS_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
			exit;
		}

		$this->isActionValid('whois');

		$this->formatSubscription($this->DomainModel);

		// if there are pending modifications, show the new modified data
		if(isset($this->DomainModel->ClientareaModifications['editwhois']['Data']))
		{
			$modified_whois_data = json_decode($this->DomainModel->ClientareaModifications['editwhois']['Data'], TRUE);
			if($modified_whois_data && count($modified_whois_data) > 0)
			{
				foreach($modified_whois_data as $_handle_type => $_handle)
				{
					foreach($_handle as $_handle_field => $_handle_field_value)
					{
						if(isset($this->DomainModel->HandleInfo[$_handle_type . 'Handle'][$_handle_field]))
						{
							$this->DomainModel->HandleInfo[$_handle_type . 'Handle'][$_handle_field] = $_handle_field_value;
						}
					}
				}
			}
		}

		if(!empty($_POST))
		{
			$whois_changes = array();

			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$data_is_changed = FALSE;
			$handle_types = array('owner', 'admin', 'tech');
			foreach($handle_types as $_handle_type)
			{
				if(isset($_POST[$_handle_type]) && count($_POST[$_handle_type]) > 0)
				{
					// when the state is chosen from a list (select) the statecode is used instead of state
					if(isset($_POST[$_handle_type]['StateCode']) && $_POST[$_handle_type]['StateCode'])
					{
						$_POST[$_handle_type]['State'] = $_POST[$_handle_type]['StateCode'];
					}

					// validate that user has modified data
					if($data_is_changed === FALSE)
					{
						$data_is_changed = is_data_modified($_POST[$_handle_type], (object)$this->DomainModel->HandleInfo[ucfirst($_handle_type) . 'Handle']);
					}

					foreach($_POST[$_handle_type] as $_handle_field => $_field_value)
					{
						$this->DomainModel->HandleInfo[ucfirst($_handle_type) . 'Handle'][$_handle_field] = $_field_value;
						$whois_changes[ucfirst($_handle_type)][$_handle_field] = $_field_value;
					}
				}
			}

			if($data_is_changed === FALSE)
			{
				$this->DomainModel->Success[] = __('changes successfully processed');
				$this->Template->flashMessage($this->DomainModel);
				header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
				exit;
			}
			else
			{
				if($this->DomainModel->editWhois($whois_changes))
				{
					$this->Template->flashMessage($this->DomainModel);
					// we set this to prevent showing a warning and success message at the same time
					$_SESSION['SkipModificationWarning'] = 'editwhois';
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
					exit;
				}
			}
		}

		$this->setListDomains();

		$this->Template->service          = $this->DomainModel;
		$this->Template->servicetype      = 'domain';
		$this->Template->modificationtype = 'editwhois';
		$this->Template->owner            = $this->DomainModel->HandleInfo['OwnerHandle'];
		$this->Template->admin            = $this->DomainModel->HandleInfo['AdminHandle'];
		$this->Template->tech             = $this->DomainModel->HandleInfo['TechHandle'];

		$this->Template->parseMessage($this->DomainModel);
		$this->Template->show('domain.whois');
	}

	public function dnsmanagement()
	{
		$this->DomainModel->id = intval(get_url_var($_GET['rt']));

		// Allowed to change dnszone?
		if(Settings_Model::get('CLIENTAREA_DOMAIN_DNSZONE_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DOMAIN_DNSZONE_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
			exit;
		}

		$this->isActionValid('dnsmanagement');

		$this->formatSubscription($this->DomainModel);

		// we always need to do getDNSZone for the extra settings (like SettingSingleTTL)
		if(!$this->DomainModel->getDNSZone())
		{
			$this->Template->flashMessage($this->DomainModel);
			header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
			exit;
		}

		// if there are pending modifications, show the new modified data
		if(isset($this->DomainModel->ClientareaModifications['editdnszone']['Data']))
		{
			$modified_dnszone_data = json_decode($this->DomainModel->ClientareaModifications['editdnszone']['Data'], TRUE);
			if(!empty($modified_dnszone_data))
			{
				unset($this->DomainModel->DNSZone['records']);
				// convert the json decode array to the same format as the format the getDNSzone function returns
				foreach($modified_dnszone_data['Records']['Name'] as $_record_key => $_value)
				{
                    $this->DomainModel->DNSZone['records'][$_record_key]['id']      = (isset($modified_dnszone_data['Records']['id'][$_record_key])) ? $modified_dnszone_data['Records']['id'][$_record_key] : 0;
					$this->DomainModel->DNSZone['records'][$_record_key]['name']     = $modified_dnszone_data['Records']['Name'][$_record_key];
					$this->DomainModel->DNSZone['records'][$_record_key]['type']     = $modified_dnszone_data['Records']['Type'][$_record_key];
					$this->DomainModel->DNSZone['records'][$_record_key]['value']    = $modified_dnszone_data['Records']['Value'][$_record_key];
					$this->DomainModel->DNSZone['records'][$_record_key]['priority'] = $modified_dnszone_data['Records']['Priority'][$_record_key];
					$this->DomainModel->DNSZone['records'][$_record_key]['ttl']      = $modified_dnszone_data['Records']['TTL'][$_record_key];
				}
			}
		}

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$dns_zone_edit = array();
			// convert post values to API param array
			foreach($_POST['DNSZone']['Name'] as $_key => $_value)
			{
				// if the name and value are not set, skip this record
				if(!$_POST['DNSZone']['Name'][$_key] && !$_POST['DNSZone']['Value'][$_key])
				{
					continue;
				}
                $dns_zone_edit['DNSZone']['records'][$_key]['id']       = (isset($_POST['DNSZone']['id'][$_key])) ? $_POST['DNSZone']['id'][$_key] : 0;
				$dns_zone_edit['DNSZone']['records'][$_key]['checksum'] = (isset($_POST['DNSZone']['checksum'][$_key])) ? $_POST['DNSZone']['checksum'][$_key] : '';
				$dns_zone_edit['DNSZone']['records'][$_key]['name']     = $_POST['DNSZone']['Name'][$_key];
				$dns_zone_edit['DNSZone']['records'][$_key]['type']     = $_POST['DNSZone']['Type'][$_key];
				$dns_zone_edit['DNSZone']['records'][$_key]['value']    = $_POST['DNSZone']['Value'][$_key];
				$dns_zone_edit['DNSZone']['records'][$_key]['priority'] = $_POST['DNSZone']['Priority'][$_key];
				$dns_zone_edit['DNSZone']['records'][$_key]['ttl']      = $_POST['DNSZone']['TTL'][$_key];
			}

			// validate that user has modified data
			$data_is_changed = FALSE;
			// if records are deleted or added
			if(count($dns_zone_edit['DNSZone']['records']) != count($this->DomainModel->DNSZone['records']))
			{
				$data_is_changed = TRUE;
			}
			// check if record data is modified
			else
			{
				foreach($dns_zone_edit['DNSZone']['records'] as $_key => $_record)
				{
					if($data_is_changed === FALSE)
					{
						$data_is_changed = is_data_modified($_record, (object)$this->DomainModel->DNSZone['records'][$_key]);
					}
				}
			}

			if($data_is_changed === FALSE)
			{
				$this->DomainModel->Success[] = __('changes successfully processed');
				$this->Template->flashMessage($this->DomainModel);
				header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
				exit;
			}
			else
			{
				if($this->DomainModel->editDNSZone($dns_zone_edit))
				{
					$this->Template->flashMessage($this->DomainModel);
					// we set this to prevent showing a warning and success message at the same time
					$_SESSION['SkipModificationWarning'] = 'editdnszone';
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
					exit;
				}
			}

			// overwrite values with post values
			$this->DomainModel->DNSZone['records'] = $dns_zone_edit['DNSZone']['records'];
		}

		$this->setListDomains();

		$this->Template->service          = $this->DomainModel;
		$this->Template->servicetype      = 'domain';
		$this->Template->modificationtype = 'editdnszone';

		$this->Template->parseMessage($this->DomainModel);
		$this->Template->show('domain.dnsmanagement');
	}

	public function isActionValid($action)
	{
		switch($action)
		{
			case 'dnsmanagement':
				if(!$this->DomainModel->show() || !in_array($this->DomainModel->Status, [4,8]))
				{
					$this->DomainModel->Error[] = __('the requested action could not be executed');
					$this->Template->flashMessage($this->DomainModel);
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
					exit;
				}
			break;

			case 'gettoken':

				if(!$this->DomainModel->show() || !in_array($this->DomainModel->Status, [4,8])
					|| !isset($this->DomainModel->RegistrarInfo['Class']) || !$this->DomainModel->RegistrarInfo['Class'])
				{
					$this->DomainModel->Error[] = __('the requested action could not be executed');
					$this->Template->flashMessage($this->DomainModel);
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url') . '/' . __('view', 'url') . '/' . $this->DomainModel->id);
					exit;
				}

			break;

			default:
				if(!$this->DomainModel->show() || $this->DomainModel->Status != 4)
				{
					$this->Template->flashMessage($this->DomainModel);
					header('Location: ' . __SITE_URL . '/' . __('domain', 'url'));
					exit;
				}
			break;
		}
	}

}