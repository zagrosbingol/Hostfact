<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Hook;
use Settings_Model;


class Ticket
{
	public $ClassName;

	public $UrlString;

	public function __construct()
	{
		// strip the namespace
		$this->ClassName = implode('', array_slice(explode('\\', __CLASS__), -1));

		// call hooks
		Hook::addFilter('main_menu', array(__NAMESPACE__, $this->ClassName, 'filter_main_menu'));
		Hook::addFilter('home_content', array(__NAMESPACE__, $this->ClassName, 'filter_home_content'));
	}

	function filter_main_menu($main_menu, $parameters)
	{
		// add to main menu, as child of services
		$main_menu['ticket'] = array('title'  => __('mainmenu ticket', __CLASS__),
									   'url'    => __SITE_URL . '/' . __('ticket', 'url', __CLASS__),
									   'active' => array('ticket'));

		return $main_menu;
	}

	function filter_home_content($home_content, \Template $template)
	{
		$ticketModel = new Ticket_Model();
		$ticket_list = $ticketModel->listTickets();

		// Get open tickets
		$open_tickets = array();
		if(is_array($ticket_list))
		{
			foreach($ticket_list as $_ticket)
			{
				if($_ticket['Status'] != 3)
				{
					$open_tickets[] = $_ticket;
				}
			}
		}

		if(!empty($open_tickets))
		{
			// Extend list of services to display
			$home_content['open_tickets'] = array('element'        => 'ticket.home',
												  'pluginName'     => __CLASS__);

			$template->open_tickets = $open_tickets;
		}

		return $home_content;
	}


	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('ticket', 'url', __CLASS__);
	}


	/** function determines if the plugin should be activated (TRUE) or not (FALSE)
	 *
	 * @return bool
	 */
	function activatePlugin()
	{
		// only when the module is active, do we want to activate the plugin
		if(Settings_Model::get('TICKET_USE') && Settings_Model::get('CLIENTAREA_USE_TICKETSYSTEM') == 'yes')
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
		$cache_calls[] = array('controller' => 'ticket', 'action' => 'list', 'params' => array('status' => '0|1|2|3', 'sort' => 'LastDate', 'order' => 'DESC'), 'options' => array('cacheable' => TRUE));
		return $cache_calls;
	}
}

return __NAMESPACE__;