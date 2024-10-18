<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Controller;
use Plugin;
use Settings_Model;

class Hosting_Controller extends Service_Controller
{
	public function __construct(\Template $template)
	{
		parent::__construct($template);

		// Make alias for domain model
		$this->HostingModel = $this->ServiceModel;

		// Set sidebar
		$this->Template->setSidebar('hosting.sidebar');
	}

	public function index()
	{
		$this->setListHosting();

		if(!empty($this->Template->hosting_accounts))
		{
			// We want to show the latest hosting account
			header('Location: ' . __SITE_URL . '/' . __('hosting', 'url') . '/' . __('view', 'url') . '/' . $this->Template->hosting_accounts[0]['Identifier']);
			exit;
		}
		else
		{
			// No hosting accounts, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function setListHosting()
	{
		$this->Template->hosting_accounts = $this->HostingModel->listHosting();
	}

	public function orderNew()
	{
		// Get list of hosting accounts for submenu
		$this->setListHosting();

		// Set iframe URL for orderform
		$this->Template->iframe_url = Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_HOSTING_ORDERFORM');

		$this->Template->show('page.iframe');
	}

	public function view()
	{
		$this->HostingModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->HostingModel->show())
		{
			$this->formatSubscription($this->HostingModel);

			$this->Template->service = $this->HostingModel;
			$this->Template->servicetype = 'hosting';
		}

		$this->setListHosting();

		$this->Template->parseMessage($this->HostingModel);

		if(isset($result) && $result)
		{
			$this->Template->show('hosting.view');
		}
		else
		{
			$this->Template->header = __('hostingaccount');
			$this->Template->text   = __('hosting does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function getDomains()
	{
		$this->HostingModel->id = intval(get_url_var($_GET['rt']));

		if(!$domain_list = $this->HostingModel->getDomains())
		{
			echo json_encode(array('message' => __('data could not be retrieved')));
			exit;
		}

		// same as PHP > 5.5 array_column function
		$domain_names = array_map(function($element) {return $element['Domain'];}, $domain_list);

		// re-sort the domains, convert from flat array to multidimensional array by adding the children to the parent domain
		foreach($domain_list as $_key => $domain_data )
		{
			// Check if there is a parent domain
			if(isset($domain_data['Parent']) && in_array($domain_data['Parent'], $domain_names))
			{
				// Add the child domain to the parent domain
				$domain_list[array_search($domain_data['Parent'], $domain_names)]['Children'][] = $domain_data;
				// remove child from main array
				unset($domain_list[$_key]);
			}
		}

		// re-order the key index
		$domain_list = array_values($domain_list);

		$json_array = array('domains' => $domain_list);

		// get the URL from the domain plugin
		if(isset(Plugin::$loaded_plugins) && isset(Plugin::$loaded_plugins[__NAMESPACE__ . '\Domain']) && isset(Plugin::$loaded_plugins[__NAMESPACE__ . '\Domain']['instance']->UrlString))
		{
			$json_array['domain_url'] = __SITE_URL . '/' . Plugin::$loaded_plugins[__NAMESPACE__ . '\Domain']['instance']->UrlString . '/' . __('view', 'url') . '/';
		}

		echo json_encode($json_array);
		exit;
	}

	public function serverLogin()
	{
		$this->HostingModel->id = intval(get_url_var($_GET['rt']));

		// Only allow for active hosting accounts with SSOsupport and permission to do so
		if(!$this->HostingModel->show() || $this->HostingModel->Status != 4 || !isset($this->HostingModel->ServerInfo['SSOSupport']) || !$this->HostingModel->ServerInfo['SSOSupport'] || Settings_Model::get('CLIENTAREA_HOSTING_SINGLE_SIGN_ON') != 'yes')
		{
			$this->HostingModel->Error[] = __('the requested action could not be executed');
			$this->Template->flashMessage($this->HostingModel);
			header('Location: ' . __SITE_URL . '/' . __('hosting', 'url') . '/' . __('view', 'url') . '/' . $this->HostingModel->id);
			exit;
		}

		$this->Template->service = $this->HostingModel;

		$this->Template->loadHeader = FALSE;
		$this->Template->loadSidebar = FALSE;
		$this->Template->show('hosting.serverlogin');
		exit;
	}

	public function singleSignOn()
	{
		$this->HostingModel->id = intval(get_url_var($_GET['rt']));

		// Only allow for active hosting accounts with SSOsupport and permission to do so
		if(!$this->HostingModel->show() || $this->HostingModel->Status != 4 || !isset($this->HostingModel->ServerInfo['SSOSupport']) || !$this->HostingModel->ServerInfo['SSOSupport'] || Settings_Model::get('CLIENTAREA_HOSTING_SINGLE_SIGN_ON') != 'yes')
		{
			$this->HostingModel->Error[] = __('the requested action could not be executed');
			$this->Template->flashMessage($this->HostingModel);
			exit;
		}

		$allowed_ips = array($_SERVER['REMOTE_ADDR']);

		if($result = $this->HostingModel->getServerLogin($allowed_ips))
		{
			if(isset($result['form_action']))
			{
				echo json_encode(array('data' => $result['data'], 'form_action' => $result['form_action']));
				exit;
			}
			elseif(isset($result['url']))
			{
				echo json_encode(array('url' => $result['url']));
				exit;
			}
		}

		$this->HostingModel->Error[] = __('the requested action could not be executed');
		$this->Template->flashMessage($this->HostingModel);
		exit;
	}

	public function changePassword()
	{
		$this->HostingModel->id = intval(get_url_var($_GET['rt']));

		// Only allow for active hosting accounts with clientarea permissio to change passwords
		if(!$this->HostingModel->show() || $this->HostingModel->Status != 4 || Settings_Model::get('CLIENTAREA_HOSTING_PASSWORD_RESET') != 'yes')
		{
			$this->HostingModel->Error[] = __('the requested action could not be executed');
			$this->Template->flashMessage($this->HostingModel);
			die('reload');
		}

		if(isset($_POST['PasswordNew']) && strlen($_POST['PasswordNew']) < 6)
		{
			$this->HostingModel->Error[] 		= __('new password too short');
			$this->HostingModel->ErrorFields[] 	= 'PasswordNew';
		}
		if(!isset($_POST['Password']) || wf_password_verify($_POST['Password'], $_SESSION['SecurePassword']) === FALSE)
		{
			$this->HostingModel->Error[] 		= __('password is invalid');
			$this->HostingModel->ErrorFields[] 	= 'Password';
		}

		if(empty($this->HostingModel->Error))
		{
			$params 	= array(
				'Identifier' 				=> $this->HostingModel->id,
				'ChangePasswordOnServer' 	=> 'yes',
				'Password' 					=> $_POST['PasswordNew']
			);

			$result 	= $this->HostingModel->APIRequest('hosting', 'edit', $params);
			if($result !== FALSE)
			{
				$this->HostingModel->Success[] 		= __('hosting change password - success');
				$this->Template->flashMessage($this->HostingModel);
				die('reload');
			}
		}

		$this->Template->parseMessage($this->HostingModel);
		$this->Template->service 				= $this->HostingModel;
		$this->Template->servicetype 			= 'hosting';
		$this->Template->showElement('hosting.changepassword');
	}

}