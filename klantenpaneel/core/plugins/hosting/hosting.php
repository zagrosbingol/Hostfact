<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Hook;
use Settings_Model;

class Hosting
{
	public $ClassName;

	public $UrlString;

	public function __construct()
	{
		// strip the namespace
		$this->ClassName = implode('', array_slice(explode('\\', __CLASS__), -1));

		// call hooks
		Hook::addFilter('main_menu', array(__NAMESPACE__, $this->ClassName, 'filter_main_menu'));
		Hook::addFilter('service_page', array(__NAMESPACE__, $this->ClassName, 'filter_service_page'));
		Hook::addFilter('home_services', array(__NAMESPACE__, $this->ClassName, 'filter_home_services'));
		Hook::addFilter('services_orderform', array(__NAMESPACE__, $this->ClassName, 'filter_services_orderform'));
	}

	function filter_main_menu($main_menu, $parameters)
	{
		// First check if we have hostingaccounts at all
		$hostingModel = new Hosting_Model;
		$list_hosting = $hostingModel->listHosting();

		if(empty($list_hosting))
		{
			// No hostingaccounts, do not extend menu
			return $main_menu;
		}

		// add plugin url's to active state for services
		array_push($main_menu['services']['active'], 'hosting');

		// add hosting to main menu, as child of services
		$main_menu['services']['children']['hosting'] = array('title'  => __('mainmenu hosting', __CLASS__),
															   'url'    => __SITE_URL . '/' . __('hosting', 'url', __CLASS__),
															   'active' => array('hosting'));

		return $main_menu;
	}

	function filter_service_page($services, \Template $template)
	{
		$hostingModel = new Hosting_Model;
		$list_hosting = $hostingModel->listHosting();

		if(empty($list_hosting))
		{
			// No hostingaccounts, do not extend page
			return $services;
		}

		// Extend list of services to display
		$services['hosting'] = array('element' => 'hosting.table',
									 'title' => __('mainmenu hosting', __CLASS__),
									 'pluginName' => __CLASS__);

		// Put variables in template
		$template->list_hosting = $list_hosting;

		return $services;
	}

	function filter_home_services($services)
	{
		$hostingModel = new Hosting_Model();
		$list_hosting = $hostingModel->listHosting();

		if(empty($list_hosting))
		{
			// No hosting, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['hosting'] = array('amount'            => count($list_hosting),
									 'service_label'     => __('hostingaccount', __CLASS__),
									 'services_label'    => __('hostingaccounts', __CLASS__),
									 'url_services_page' => __SITE_URL . '/' . __('hosting', 'url', __CLASS__),
									 'pluginName'        => __CLASS__);

		return $services;
	}

	function filter_services_orderform($services)
	{
		if(!Settings_Model::get('CLIENTAREA_HOSTING_ORDERFORM') || (int)Settings_Model::get('CLIENTAREA_HOSTING_ORDERFORM') === 0)
		{
			return $services;
		}

		// Extend list with service info & orderform link
		$services['hosting'] = array('service_label'     => __('hostingaccount', __CLASS__),
									'services_label'    => __('hostingaccounts', __CLASS__),
									'url_order_service' => __SITE_URL . '/' . __('hosting', 'url', __CLASS__) . '/' . __('orderNew', 'url'),
									'url_order_service_responsive' => Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_HOSTING_ORDERFORM'),
									'pluginName'        =>__CLASS__);

		return $services;
	}


	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('hosting', 'url', __CLASS__);
	}

	/**
	 * Extend initial calls
	 * @param $cache_calls
	 * @return array
	 */
	function extendInitialMultiCall($cache_calls)
	{
		$cache_calls[] = array('controller' => 'hosting', 'action' => 'list', 'params' => array('status' => '-1|1|3|4|5|7'), 'options' => array('cacheable' => TRUE));

		return $cache_calls;
	}
}

return __NAMESPACE__;