<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Controller;
use Settings_Model;

class SSL_Controller extends Service_Controller
{
	public function __construct(\Template $template)
	{
		// Call service controller construct
		parent::__construct($template);

		// Make alias for ssl model
		$this->SSLModel = $this->ServiceModel;

		// Set sidebar
		$this->Template->setSidebar('ssl.sidebar');
	}

	public function index()
	{
		$this->setListSSL();

		if(!empty($this->Template->ssl_list))
		{
			// We want to show the latest ssl certificate
			header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->Template->ssl_list[0]['Identifier']);
			exit;
		}
		else
		{
			// No ssl certificates, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function setListSSL()
	{
		$this->Template->ssl_list = $this->SSLModel->listSSL();
	}

	public function orderNew()
	{
		// Get list of certificates for submenu
		$this->setListSSL();

		// Set iframe URL for orderform
		$this->Template->iframe_url = Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_SSL_ORDERFORM');

		$this->Template->show('page.iframe');
	}

	public function view()
	{
		$this->SSLModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->SSLModel->show())
		{
			$this->formatSubscription($this->SSLModel);
			$this->Template->service = $this->SSLModel;
			$this->Template->servicetype = 'ssl';
		}

		$this->setListSSL();

		$this->Template->parseMessage($this->SSLModel);

		if(isset($result) && $result)
		{
			$this->Template->show('ssl.view');
		}
		else
		{
			$this->Template->header = __('ssl certificate');
			$this->Template->text   = __('ssl does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function reissue()
	{
		$this->SSLModel->id = intval(get_url_var($_GET['rt']));

		if(Settings_Model::get('CLIENTAREA_SSL_REISSUE') != 'yes')
		{
			header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->SSLModel->id);
			exit;
		}

		$this->isActionValid('reissue');

		$this->SSLModel->reissueSSL();

		$this->Template->flashMessage($this->SSLModel);
		header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->SSLModel->id);
		exit;
	}

	public function download()
	{
		$this->SSLModel->id = intval(get_url_var($_GET['rt']));

		if(Settings_Model::get('CLIENTAREA_SSL_DOWNLOAD') != 'yes')
		{
			header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->SSLModel->id);
			exit;
		}

		$this->isActionValid('download');

		if($result = $this->SSLModel->downloadSSL())
		{
			$_SESSION['force_download_file'] = $result;
		}

		$this->Template->flashMessage($this->SSLModel);
		header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->SSLModel->id);
		exit;
	}

	public function isActionValid($action)
	{
		switch($action)
		{
			default:
				if(!$this->SSLModel->show() || $this->SSLModel->Status != 'active'
					|| !isset($this->SSLModel->RegistrarInfo['Class']) || !$this->SSLModel->RegistrarInfo['Class'])
				{
					$this->SSLModel->Error[] = __('the requested action could not be executed');
					$this->Template->flashMessage($this->SSLModel);
					header('Location: ' . __SITE_URL . '/' . __('ssl', 'url') . '/' . __('view', 'url') . '/' . $this->SSLModel->id);
					exit;
				}
			break;
		}
	}

}