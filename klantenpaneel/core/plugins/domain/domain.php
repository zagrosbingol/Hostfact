<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Hook;
use Settings_Model;

class Domain
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
		// First check if we have domains at all
		$domainModel = new Domain_Model;
		$list_domains = $domainModel->listDomains();

		if(empty($list_domains))
		{
			// No domains, do not extend menu
			return $main_menu;
		}

		// add plugin url's to active state for services
		array_push($main_menu['services']['active'], 'domain');

		// add to main menu, as child of services
		$main_menu['services']['children']['domain'] = array('title'  => __('mainmenu domain', __CLASS__),
															   'url'    => __SITE_URL . '/' . __('domain', 'url', __CLASS__),
															   'active' => array('domain'));

		return $main_menu;
	}

	function filter_service_page($services, \Template $template)
	{
		$domainModel = new Domain_Model;
		$list_domains = $domainModel->listDomains();

		if(empty($list_domains))
		{
			// No domains, do not extend page
			return $services;
		}

		// Extend list of services to display
		$services['domain'] = array('element' => 'domain.table',
									'title' => __('mainmenu domain', __CLASS__),
									'pluginName' => __CLASS__);

		// Put variables in template
		$template->list_domains = $list_domains;


		return $services;
	}

	function filter_home_services($services)
	{
		$domainModel = new Domain_Model();
		$list_domains = $domainModel->listDomains();

		if(empty($list_domains))
		{
			// No domains, do not extend services
			return $services;
		}

		// Extend list of services to display
		$services['domain'] = array('amount'            => count($list_domains),
									'service_label'     => __('domain', __CLASS__),
									'services_label'    => __('domains', __CLASS__),
									'url_services_page' => __SITE_URL . '/' . __('domain', 'url', __CLASS__),
									'pluginName'        =>__CLASS__);

		return $services;
	}

	function filter_services_orderform($services)
	{
		if(!Settings_Model::get('CLIENTAREA_DOMAIN_ORDERFORM') || (int)Settings_Model::get('CLIENTAREA_DOMAIN_ORDERFORM') === 0)
		{
			return $services;
		}

		// Extend list of with service info & orderform link
		$services['domain'] = array('service_label'     => __('domain', __CLASS__),
									'services_label'    => __('domains', __CLASS__),
									'url_order_service' => __SITE_URL . '/' . __('domain', 'url', __CLASS__) . '/' . __('orderNew', 'url'),
									'url_order_service_responsive' => Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_DOMAIN_ORDERFORM'),
									'pluginName'        =>__CLASS__);

		return $services;
	}

	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('domain', 'url', __CLASS__);
	}

	/**
	 * Extend initial calls
	 * @param $cache_calls
	 * @return array
	 */
	function extendInitialMultiCall($cache_calls)
	{
		$cache_calls[] = array('controller' => 'domain', 'action' => 'list', 'params' => array('status' => 'client_visible'), 'options' => array('cacheable' => TRUE));
		return $cache_calls;
	}
}

return __NAMESPACE__;