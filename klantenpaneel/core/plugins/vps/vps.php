<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Hook;
use Settings_Model;


class VPS
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
		// First check if we have vps at all
		$vpsModel = new VPS_Model;
		$list_vps = $vpsModel->listVPS();

		if(empty($list_vps))
		{
			// No vps, do not extend menu
			return $main_menu;
		}

		// add plugin url's to active state for services
		array_push($main_menu['services']['active'], 'vps');

		// add to main menu, as child of services
		$main_menu['services']['children']['vps'] = array('title'  => __('mainmenu vps', __CLASS__),
														   'url'    => __SITE_URL . '/' . __('vps', 'url', __CLASS__),
														   'active' => array('vps'));

		return $main_menu;
	}

	function filter_service_page($services, \Template $template)
	{
		// First check if we have vps at all
		$vpsModel = new VPS_Model;
		$list_vps = $vpsModel->listVPS();

		if(empty($list_vps))
		{
			// No vps, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['vps'] = array('element' => 'vps.table',
								 'title' => __('mainmenu vps', __CLASS__),
								 'pluginName' => __CLASS__);

		// Put variables in template
		$template->list_vps = $list_vps;

		return $services;
	}

	function filter_home_services($services)
	{
		// First check if we have vps at all
		$vpsModel = new VPS_Model;
		$list_vps = $vpsModel->listVPS();

		if(empty($list_vps))
		{
			// No vps, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['vps'] = array('amount'            => count($list_vps),
								 'service_label'     => __('vps service', __CLASS__),
								 'services_label'    => __('vps services', __CLASS__),
								 'url_services_page' => __SITE_URL . '/' . __('vps', 'url', __CLASS__),
								 'pluginName'        => __CLASS__);

		return $services;
	}

	function filter_services_orderform($services)
	{
		if(!\Settings_Model::get('CLIENTAREA_VPS_ORDERFORM') || (int)\Settings_Model::get('CLIENTAREA_VPS_ORDERFORM') === 0)
		{
			return $services;
		}

		// Extend list with service info & orderform link
		$services['vps'] = array('service_label'     => __('vps service', __CLASS__),
									'services_label'    => __('vps services', __CLASS__),
									'url_order_service' => __SITE_URL . '/' . __('vps', 'url', __CLASS__) . '/' . __('orderNew', 'url'),
								 	'url_order_service_responsive' => Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_VPS_ORDERFORM'),
									'pluginName'        =>__CLASS__);

		return $services;
	}

	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('vps', 'url', __CLASS__);
	}


	/** function determines if the plugin should be activated (TRUE) or not (FALSE)
	 *
	 * @return bool
	 */
	function activatePlugin()
	{
		// retrieve the setting which indicates which service modules are active
		$active_service_modules = Settings_Model::get('ACTIVE_SERVICE_MODULES');
		// only when the module is active, do we want to activate the plugin
		if(	$active_service_modules && is_array($active_service_modules) && in_array('vps', $active_service_modules))
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Extend initial calls
	 * @param $cache_calls
	 * @return array
	 */
	function extendInitialMultiCall($cache_calls)
	{
		$cache_calls[] = array('controller' => 'vps', 'action' => 'list', 'params' => array('status' => 'create|building|active|suspended|error'), 'options' => array('cacheable' => TRUE));
		return $cache_calls;
	}
}

return __NAMESPACE__;