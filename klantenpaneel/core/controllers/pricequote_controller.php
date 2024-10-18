<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class PriceQuote_Controller extends Base_Controller
{

	public function __construct(Template $template)
	{
		parent::__construct($template);

		$this->PriceQuoteModel = new PriceQuote_Model();

		// Set sidebar
		$this->Template->setSidebar('pricequote.sidebar');
	}

	public function index()
	{
		$this->setListPriceQuotes(false);

		$open_pricequotes	= array();
		if($this->Template->pricequote_list !== FALSE)
		{
			foreach ($this->Template->pricequote_list as $_pricequote)
			{
				// Store open (and non expired) pricequotes in separate array
				if(in_array($_pricequote['Status'], array(2)) && $_pricequote['ExpirationDate'] >= date('Y-m-d'))
				{
					$open_pricequotes[] = $_pricequote;
				}
			}
		}
		$this->Template->open_pricequotes 	= $open_pricequotes;

		// Check if there are any open pricequotes waiting for confirmation
		if(!empty($this->Template->open_pricequotes))
		{
			// If we have extended search, get searched list
			if(Cache::get('search-pricequote-list'))
			{
				$this->setListPriceQuotes();
			}
			
			// We want to show the index page
			$this->Template->parseMessage($this->PriceQuoteModel);
			$this->Template->show('pricequote.index');
		}
		elseif(!empty($this->Template->pricequote_list))
		{
			// We want to show the latest pricequote
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url') . '/' . __('view', 'url') . '/' . $this->Template->pricequote_list[0]['Identifier']);
			exit;
		}
		else
		{
			// No pricequotes, redirect to dashboard
			header('Location: ' . __SITE_URL . '/');
			exit;
		}
	}

	public function view()
	{
		$this->PriceQuoteModel->id = intval(get_url_var($_GET['rt']));
		if($this->PriceQuoteModel->show())
		{
			$this->Template->pricequote_object = $this->PriceQuoteModel;
		}
		else
		{
			$this->Template->flashMessage($this->PriceQuoteModel);
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url'));
			exit;
		}

		$this->setListPriceQuotes();

		$this->Template->parseMessage($this->PriceQuoteModel);
		$this->Template->show('pricequote.view');
	}

	public function setListPriceQuotes($searchable = true)
	{
		$pricequote_list 	= array();

		// Are we searching?
		$search = ($searchable) ? Cache::get('search-pricequote-list') : false;

		$pricequotes = $this->PriceQuoteModel->listPriceQuotes($search);

		if($pricequotes !== FALSE)
		{
			foreach ($pricequotes as $_pricequote)
			{
				// All pricequotes
				$pricequote_list[] = $_pricequote;
			}
		}

		$this->Template->pricequote_list    = $pricequote_list;
	}

	public function ajaxExtendedSearch()
	{
		// Store parameters
		parent::ajaxExtendedSearch();

		// Get new list
		$this->setListPriceQuotes();

		// Force active
		$this->PriceQuoteModel->id = intval($_POST['activeID']);
		$this->Template->pricequote_object = $this->PriceQuoteModel;

		$this->Template->showElement('pricequote.list');
	}

	public function download()
	{
		$this->PriceQuoteModel->id = intval(get_url_var($_GET['rt']));
		if($this->PriceQuoteModel->show() && $result = $this->PriceQuoteModel->printPriceQuote())
		{
			download_file($result);
		}
		else
		{
			$this->Template->flashMessage($this->PriceQuoteModel);
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url'));
			exit;
		}
	}

	public function accept()
	{
		$this->PriceQuoteModel->id 			= intval(get_url_var($_GET['rt']));
		if($this->PriceQuoteModel->show() && Settings_Model::get('CLIENTAREA_PRICEQUOTE_ACCEPT') == 'yes')
		{
			$this->Template->pricequote_object = $this->PriceQuoteModel;
		}
		else
		{
			$this->Template->flashMessage($this->PriceQuoteModel);
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url'));
			exit;
		}

		// Get new list
		$this->setListPriceQuotes();

		if(!empty($_POST))
		{
			$this->PriceQuoteModel->AcceptName      = (isset($_POST['name'])) ? $_POST['name'] : '';
			$this->PriceQuoteModel->AcceptEmailAddress     = (isset($_POST['email'])) ? $_POST['email'] : '';
			$this->PriceQuoteModel->AcceptComment   = (isset($_POST['comment'])) ? $_POST['comment'] : '';
			$this->PriceQuoteModel->AcceptSignature = (isset($_POST['signature'])) ? $_POST['signature'] : '';
			$this->PriceQuoteModel->SignatureSize   = (isset($_POST['signature_size'])) ? $_POST['signature_size'] : ';';
			$this->PriceQuoteModel->Terms   = (isset($_POST['terms'])) ? $_POST['terms'] : 'no';

			// user has accepted the pricequote
			if($this->PriceQuoteModel->acceptPriceQuoteWithSignature())
			{
				$this->Template->flashMessage($this->PriceQuoteModel);
				header('Location: '.__SITE_URL.'/'.__('pricequote', 'url').'/'.__('view', 'url').'/'.$this->PriceQuoteModel->id);
				exit;
			}
		}

		$this->Template->parseMessage($this->PriceQuoteModel);
		$this->Template->pricequote_object = $this->PriceQuoteModel;
		$this->Template->accept_url = __SITE_URL.'/'.__('pricequote', 'url').'/'.__('accept', 'url').'/'.$this->PriceQuoteModel->id;
		$this->Template->download_url = __SITE_URL.'/'.__('pricequote', 'url').'/'.__('download', 'url').'/'.$this->PriceQuoteModel->id;
		$this->Template->show('pricequote.accept');


	}

	public function decline()
	{
		$this->PriceQuoteModel->id 			= intval(get_url_var($_GET['rt']));
		if($this->PriceQuoteModel->show() && Settings_Model::get('CLIENTAREA_PRICEQUOTE_ACCEPT') == 'yes')
		{
			$this->Template->pricequote_object = $this->PriceQuoteModel;
		}
		else
		{
			$this->Template->flashMessage($this->PriceQuoteModel);
			die('reload');
		}

		// sanitize post strings
		$_POST = sanitize_post_values($_POST);

		$this->PriceQuoteModel->Reason		= $_POST['Comment'];
		$this->PriceQuoteModel->Password	= $_POST['Password'];

		$result = $this->PriceQuoteModel->declinePriceQuote();

		if($result === true)
		{
			// flashmessage and redirect back to pricequote page
			$this->Template->flashMessage($this->PriceQuoteModel);

			die('reload');
		}
		else
		{

			$this->Template->parseMessage($this->PriceQuoteModel);
			$this->Template->showElement('modal.pricequote.decline');
		}
	}

	public function downloadAttachment()
	{
		$parts = explode('/', $_GET['rt']);
		$pricequote_id = intval($parts[count($parts)-2]);
		$attachment_id = $parts[count($parts)-1];

		$this->PriceQuoteModel->id = $pricequote_id;
		if(!$this->PriceQuoteModel->show())
		{
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url'));
			return FALSE;
		}

		// check if attachment belongs to pricequote
		$attachment_in_pricequote = FALSE;
		if(!empty($this->PriceQuoteModel->Attachments))
		{
			foreach($this->PriceQuoteModel->Attachments as $_attachment)
			{
				if($_attachment['Identifier'] == $attachment_id)
				{
					$attachment_in_pricequote = TRUE;
				}
			}
		}

		if($attachment_in_pricequote === FALSE)
		{
			header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url'));
			return FALSE;
		}

		$attachment_model = new Attachment_Model();
		if($file_result = $attachment_model->download($attachment_id, $pricequote_id, 'pricequote'))
		{
			download_file($file_result);
			exit;
		}

		$this->Template->flashMessage($attachment_model);
		header('Location: ' . __SITE_URL . '/' . __('pricequote', 'url').'/'.__('view', 'url').'/'.$pricequote_id);
		exit;
	}

}