<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Invoice_Controller extends Base_Controller
{

	public function __construct(Template $template)
	{
		parent::__construct($template);

		$this->InvoiceModel = new Invoice_Model();

		// Set sidebar
		$this->Template->setSidebar('invoice.sidebar');
	}

	public function index()
	{
		$this->setListInvoices(false);

		$outstanding_invoices	= array();
		if($this->Template->invoice_list !== FALSE)
		{
			foreach ($this->Template->invoice_list as $_invoice)
			{
				// Store outstanding invoices in separate array
				if(in_array($_invoice['Status'], array(2, 3)))
				{
					$outstanding_invoices[] = $_invoice;
				}
			}
		}
		$this->Template->outstanding_invoices 	= $outstanding_invoices;

		// If we have extended search, get searched list
		if(Cache::get('search-invoice-list'))
		{
			$this->setListInvoices();
		}

		// Check if there are any open invoices
		if(!empty($this->Template->outstanding_invoices))
		{
			// Get some billing information for index page
			$this->getBillingInformation();

			// We want to show the index page
			$this->Template->parseMessage($this->InvoiceModel);
			$this->Template->show('invoice.index');
		}
		elseif(!empty($this->Template->invoice_list))
		{
			// We want to show the latest invoice
			$this->InvoiceModel->id = $this->Template->invoice_list[0]['Identifier'];
			if($this->InvoiceModel->show())
			{
				$this->Template->invoice_object = $this->InvoiceModel;
			}

			$this->Template->parseMessage($this->InvoiceModel);
			$this->Template->show('invoice.view');
		}
		else
		{
			// Get some billing information for index page
			$this->getBillingInformation();

			// We want to show the index page
			$this->Template->parseMessage($this->InvoiceModel);
			$this->Template->show('invoice.index');
		}
	}

	public function view()
	{
		$this->InvoiceModel->id = intval(get_url_var($_GET['rt']));
		if($this->InvoiceModel->show())
		{
			$this->Template->invoice_object = $this->InvoiceModel;
		}
		else
		{
			$this->Template->flashMessage($this->InvoiceModel);
			header('Location: ' . __SITE_URL . '/' . __('invoice', 'url'));
			exit;
		}

		$this->setListInvoices();

		$this->Template->parseMessage($this->InvoiceModel);
		$this->Template->show('invoice.view');
	}

	public function payOnline()
	{
		$this->InvoiceModel->id = intval(get_url_var($_GET['rt']));
		if($this->InvoiceModel->show() && $this->InvoiceModel->Authorisation != 'yes')
		{
			// Reset cache for invoice, so we see updated status after payment
			Cache::reset('invoice.'.$this->InvoiceModel->id);

			header('Location: ' . $this->InvoiceModel->PaymentURL);
			exit;
		}
		else
		{
			$this->Template->flashMessage($this->InvoiceModel);
			header('Location: ' . __SITE_URL . '/' . __('invoice', 'url'));
			exit;
		}
	}

	public function setListInvoices($searchable = true)
	{
		$invoice_list 			= array();

		// Are we searching?
		$search = ($searchable) ? Cache::get('search-invoice-list') : false;

		$invoices = $this->InvoiceModel->listInvoices($search);

		if($invoices !== FALSE)
		{
			foreach ($invoices as $_invoice)
			{
				// All invoices
				$invoice_list[] = $_invoice;
			}
		}

		$this->Template->invoice_list        	= $invoice_list;
	}

	public function ajaxExtendedSearch()
	{
		// Store parameters
		parent::ajaxExtendedSearch();

		// Get new list
		$this->setListInvoices();

		// Force active
		$this->InvoiceModel->id = intval($_POST['activeID']);
		$this->Template->invoice_object = $this->InvoiceModel;

		$this->Template->showElement('invoice.list');
	}

	public function download()
	{
		$this->InvoiceModel->id = intval(get_url_var($_GET['rt']));
		if($result = $this->InvoiceModel->printInvoice())
		{
			download_file($result);
		}
		else
		{
			$this->Template->flashMessage($this->InvoiceModel);
			header('Location: ' . __SITE_URL . '/' . __('invoice', 'url'));
			exit;
		}
	}

	private function getBillingInformation()
	{
		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// determine if there is deviating invoice data
		if($debtor_model->InvoiceCompanyName || $debtor_model->InvoiceInitials || $debtor_model->InvoiceSurName || $debtor_model->InvoiceAddress
			|| $debtor_model->InvoiceZipCode || $debtor_model->InvoiceCity
			|| ($debtor_model->InvoiceCountry && $debtor_model->InvoiceCountry != $debtor_model->Country) || $debtor_model->InvoiceEmailAddress
			// if state is a select field (country has state list), check if it is the same as general data
			// if state is a input field (country has no state list), just check if it is filled
			|| (Settings_Model::get('IS_INTERNATIONAL') && $debtor_model->InvoiceAddress2))
		{
			$this->Template->general_data_is_used = FALSE;
		}
		else
		{
			$this->Template->general_data_is_used = TRUE;
		}

		$this->Template->debtor = $debtor_model;
	}

	public function downloadAttachment()
	{
		$parts = explode('/', $_GET['rt']);
		$invoice_id = intval($parts[count($parts)-2]);
		$attachment_id = $parts[count($parts)-1];

		$this->InvoiceModel->id = $invoice_id;
		if(!$this->InvoiceModel->show())
		{
			header('Location: ' . __SITE_URL . '/' . __('invoice', 'url'));
			return FALSE;
		}

		// check if attachment belongs to invoice
		$attachment_in_invoice = FALSE;
		if(!empty($this->InvoiceModel->Attachments))
		{
			foreach($this->InvoiceModel->Attachments as $_attachment)
			{
				if($_attachment['Identifier'] == $attachment_id)
				{
					$attachment_in_invoice = TRUE;
				}
			}
		}

		if($attachment_in_invoice === FALSE)
		{
			header('Location: ' . __SITE_URL . '/' . __('invoice', 'url'));
			return FALSE;
		}


		$attachment_model = new Attachment_Model();
		if($file_result = $attachment_model->download($attachment_id, $invoice_id, 'invoice'))
		{
			download_file($file_result);
			exit;
		}

		$this->Template->flashMessage($attachment_model);
		header('Location: ' . __SITE_URL . '/' . __('invoice', 'url').'/'.__('view', 'url').'/'.$invoice_id);
		exit;
	}

}