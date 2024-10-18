<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Order_Controller extends Base_Controller
{

	public function __construct(Template $template)
	{
		parent::__construct($template);

		$this->OrderModel = new Order_Model();

		// Set sidebar
		$this->Template->setSidebar('order.sidebar');
	}

	public function index()
	{
		$this->setListOrders(false);

		if(!empty($this->Template->order_list))
		{
			// We want to show the latest order
			header('Location: ' . __SITE_URL . '/' . __('order', 'url') . '/' . __('view', 'url') . '/' . $this->Template->order_list[0]['Identifier']);
			exit;
		}
		else
		{
			// No orders, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function view()
	{
		$this->OrderModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->OrderModel->show())
		{
			$this->Template->order_object = $this->OrderModel;
		}

		$this->setListOrders();

		$this->Template->parseMessage($this->OrderModel);

		if(isset($result) && $result)
		{
			$this->Template->show('order.view');
		}
		else
		{
			$this->Template->header = __('order');
			$this->Template->text   = __('order does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function setListOrders($searchable = true)
	{
		// Are we searching?
		$search = ($searchable) ? Cache::get('search-list-group-orders') : false;

		$order_list = $this->OrderModel->listOrders($search);

		if($order_list !== FALSE)
		{
			$this->Template->order_list        = $order_list;
		}
		else
		{
			$this->Template->order_list        = array();
		}
	}

	public function ajaxExtendedSearch()
	{
		// Store parameters
		parent::ajaxExtendedSearch();

		// Get new list
		$this->setListOrders();

		// Force active
		$this->OrderModel->id = intval($_POST['activeID']);
		$this->Template->order_object = $this->OrderModel;

		$this->Template->showElement('order.list');
	}
}