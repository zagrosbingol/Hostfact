<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Hook;
use Settings_Model;


class SSL
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
		// First check if we have ssl certificates at all
		$sslModel = new SSL_Model;
		$list_ssl = $sslModel->listSSL();

		if(empty($list_ssl))
		{
			// No ssl certificates, do not extend menu
			return $main_menu;
		}

		// add plugin url's to active state for services
		array_push($main_menu['services']['active'], 'ssl');

		// add to main menu, as child of services
		$main_menu['services']['children']['ssl'] = array('title'  => __('mainmenu ssl', __CLASS__),
														   'url'    => __SITE_URL . '/' . __('ssl', 'url', __CLASS__),
														   'active' => array('ssl'));

		return $main_menu;
	}

	function filter_service_page($services, \Template $template)
	{
		// First check if we have ssl certificates at all
		$sslModel = new SSL_Model;
		$list_ssl = $sslModel->listSSL();

		if(empty($list_ssl))
		{
			// No ssl certificates, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['ssl'] = array('element' => 'ssl.table',
								 'title' => __('mainmenu ssl', __CLASS__),
								 'pluginName' => __CLASS__);

		// Put variables in template
		$template->list_ssl = $list_ssl;

		return $services;
	}

	function filter_home_services($services)
	{
		$sslModel = new SSL_Model;
		$list_ssl = $sslModel->listSSL();

		if(empty($list_ssl))
		{
			// No ssl, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['ssl'] = array('amount'            => count($list_ssl),
								 'service_label'     => __('ssl certificate', __CLASS__),
								 'services_label'    => __('ssl certificates', __CLASS__),
								 'url_services_page' => __SITE_URL . '/' . __('ssl', 'url', __CLASS__),
								 'pluginName'        => __CLASS__);

		return $services;
	}

	function filter_services_orderform($services)
	{
		if(!Settings_Model::get('CLIENTAREA_SSL_ORDERFORM') || (int)Settings_Model::get('CLIENTAREA_SSL_ORDERFORM') === 0)
		{
			return $services;
		}

		// Extend list with service info & orderform link
		$services['ssl'] = array('service_label'     => __('ssl certificate', __CLASS__),
									'services_label'    => __('ssl certificates', __CLASS__),
									'url_order_service' => __SITE_URL . '/' . __('ssl', 'url', __CLASS__) . '/' . __('orderNew', 'url'),
								 	'url_order_service_responsive' => Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_SSL_ORDERFORM'),
									'pluginName'        =>__CLASS__);

		return $services;
	}

	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('ssl', 'url', __CLASS__);
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
		if(	$active_service_modules && is_array($active_service_modules) && in_array('ssl', $active_service_modules))
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
		$cache_calls[] = array('controller' => 'ssl', 'action' => 'list', 'params' => array('status' => 'inrequest|install|active|expired|error'), 'options' => array('cacheable' => TRUE));
		return $cache_calls;
	}
}

return __NAMESPACE__;