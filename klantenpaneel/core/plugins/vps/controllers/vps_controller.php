<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Controller;
use Settings_Model;

class VPS_Controller extends Service_Controller
{
	public function __construct(\Template $template)
	{
		// Call service controller construct
		parent::__construct($template);

		// Make alias for vps model
		$this->VPSModel = $this->ServiceModel;

		// Set sidebar
		$this->Template->setSidebar('vps.sidebar');
	}

	public function index()
	{
		$this->setListVPS();

		if(!empty($this->Template->vps_list))
		{
			// We want to show the latest vps
			header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->Template->vps_list[0]['Identifier']);
			exit;
		}
		else
		{
			// No vps, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function setListVPS()
	{
		$this->Template->vps_list = $this->VPSModel->listVPS();
	}

	public function orderNew()
	{
		// Get list of VPS for submenu
		$this->setListVPS();

		// Set iframe URL for orderform
		$this->Template->iframe_url = Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_VPS_ORDERFORM');

		$this->Template->show('page.iframe');
	}

	public function view()
	{
		$this->VPSModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->VPSModel->show())
		{
			$this->formatSubscription($this->VPSModel);
			$this->Template->service = $this->VPSModel;
			$this->Template->servicetype = 'vps';
		}

		$this->setListVPS();

		$this->Template->parseMessage($this->VPSModel);

		if(isset($result) && $result)
		{
			$this->Template->show('vps.view');
		}
		else
		{
			$this->Template->header = __('vps service');
			$this->Template->text   = __('vps does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function start()
	{
		$this->VPSModel->id = intval(get_url_var($_GET['rt']));

		if(Settings_Model::get('CLIENTAREA_VPS_ACTIONS') != 'yes')
		{
			header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
			exit;
		}

		$this->isActionValid('start');

		$this->VPSModel->startVPS();

		$this->Template->flashMessage($this->VPSModel);
		header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
		exit;
	}

	public function pause()
	{
		$this->VPSModel->id = intval(get_url_var($_GET['rt']));

		if(Settings_Model::get('CLIENTAREA_VPS_ACTIONS') != 'yes')
		{
			header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
			exit;
		}

		$this->isActionValid('pause');

		$this->VPSModel->pauseVPS();

		$this->Template->flashMessage($this->VPSModel);
		header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
		exit;
	}

	public function restart()
	{
		$this->VPSModel->id = intval(get_url_var($_GET['rt']));

		if(Settings_Model::get('CLIENTAREA_VPS_ACTIONS') != 'yes')
		{
			header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
			exit;
		}

		$this->isActionValid('restart');

		$this->VPSModel->restartVPS();

		$this->Template->flashMessage($this->VPSModel);
		header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
		exit;
	}

	public function isActionValid($action)
	{
		switch($action)
		{
			default:
				if(!$this->VPSModel->show() || $this->VPSModel->Status != 'active'
					|| !isset($this->VPSModel->NodeInfo['Platform']) || !$this->VPSModel->NodeInfo['Platform'])
				{
					$this->VPSModel->Error[] = __('the requested action could not be executed');
					$this->Template->flashMessage($this->VPSModel);
					header('Location: ' . __SITE_URL . '/' . __('vps', 'url') . '/' . __('view', 'url') . '/' . $this->VPSModel->id);
					exit;
				}
			break;
		}
	}

}