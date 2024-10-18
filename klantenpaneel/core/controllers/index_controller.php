<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Index_Controller extends Base_Controller
{

	public function __construct(Template $template)
	{
		parent::__construct($template);
	}

	public function index()
	{
		// get outstanding invoices
		$invoiceModel = new Invoice_Model();
		$invoices = $invoiceModel->listInvoices();

		$outstanding_invoices	= array();
		if($invoices !== FALSE)
		{
			foreach ($invoices as $_invoice)
			{
				// Store outstanding invoices in separate array
				if(in_array($_invoice['Status'], array(2, 3)))
				{
					$outstanding_invoices[] = $_invoice;
				}
			}
		}

		$this->Template->outstanding_invoices 	= $outstanding_invoices;

		// get open pricequotes
		$pricequoteModel = new PriceQuote_Model();
		$pricequotes = $pricequoteModel->listPriceQuotes();

		$open_pricequotes	= array();
		if($pricequotes !== FALSE)
		{
			foreach ($pricequotes as $_pricequote)
			{
				// Store open (and non expired) pricequotes in separate array
				if(in_array($_pricequote['Status'], array(2)) && $_pricequote['ExpirationDate'] >= date('Y-m-d'))
				{
					$open_pricequotes[] = $_pricequote;
				}
			}
		}

		$this->Template->open_pricequotes 	= $open_pricequotes;

		// call hook for additional content blocks on home
		$home_content = array();
		$home_content = Hook::doFilter('home_content', $home_content, $this->Template);
		$this->Template->home_content = $home_content;


		// call hook to retrieve the services of the customer
		$home_services = array();
		$home_services = Hook::doFilter('home_services', $home_services);

		$serviceModel = new Service_Model();
		$list_services = $serviceModel->listServices();
		// check for other services
		if(!empty($list_services))
		{
			// Extend list of services to display
			$home_services['other'] = array('amount'            => count($list_services),
											'service_label'     => __('other service'),
											'services_label'    => __('other services'),
											'url_services_page' => __SITE_URL . '/' . __('service', 'url'));
		}
		$this->Template->home_services = $home_services;


		$services_orderforms = array();
		// call hook to retrieve the services order links
		$services_orderforms = Hook::doFilter('services_orderform', $services_orderforms);
		// check for other service orderform
		if(Settings_Model::get('CLIENTAREA_OTHER_ORDERFORM') && Settings_Model::get('CLIENTAREA_OTHER_ORDERFORM') > 0)
		{
			// Extend list of with service info & orderform link
			$services_orderforms['other'] = array('service_label'                => __('other service'),
												  'services_label'               => __('other services'),
												  'url_order_service'            => __SITE_URL . '/' . __('service', 'url') . '/' . __('orderNew', 'url'),
												  'url_order_service_responsive' => Settings_Model::get('ORDERFORM_URL') . '?cart=' . Settings_Model::get('CLIENTAREA_OTHER_ORDERFORM'));
		}
		$this->Template->services_orderforms = $services_orderforms;

		$this->Template->parseMessage($invoiceModel);

		$this->Template->show('home');
	}

	public function customPage()
	{
		// get page name
		$route = (empty($_GET['rt'])) ? '' : $_GET['rt'];

		// get the parts of the route
		$parts = explode('/', $route);

		$template_file = preg_replace('/([^a-z0-9-_])+/i','',$parts[0]);

		$this->Template->show($template_file);
	}
}