<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Accept_Controller extends PriceQuote_Controller
{

	public function __construct(Template $template)
	{
		Base_Controller::__construct($template);

		$this->PriceQuoteModel = new PriceQuote_Model();

		$this->validateParams();

		$this->PublicPage = true;
	}

	public function index()
	{
		if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] === TRUE)
		{
			$this->setListPriceQuotes(false);

			// Set sidebar
			$this->Template->setSidebar('pricequote.sidebar');
		}
		else
		{
			// Set sidebar
			$this->Template->setSidebar('public.sidebar');
		}

        if (in_array($this->PriceQuoteModel->Status, array(3, 4))) {
            if ($this->PriceQuoteModel->AcceptName == '') {
                // Not accepted online, show page not found
                $this->Template->setSidebar(false);
                $this->Template->show('page.error');
                exit;
            }
        } // Check if the pricequote has expired and is not yet accepted
        elseif ($this->PriceQuoteModel->ExpirationDate < date('Y-m-d') && $this->PriceQuoteModel->Status <= 2) {
            $this->Template->header = __('pricequote expired title');
            $this->Template->text   = __('pricequote expired text');
            $this->Template->show('page.objectnotfound');
            exit;
        }

		if(!empty($_POST))
		{
			$this->PriceQuoteModel->AcceptName      = (isset($_POST['name'])) ? $_POST['name'] : '';
			$this->PriceQuoteModel->AcceptEmailAddress     = (isset($_POST['email'])) ? $_POST['email'] : '';
			$this->PriceQuoteModel->AcceptComment   = (isset($_POST['comment'])) ? $_POST['comment'] : '';
			$this->PriceQuoteModel->AcceptSignature = (isset($_POST['signature'])) ? $_POST['signature'] : '';
			$this->PriceQuoteModel->SignatureSize   = (isset($_POST['signature_size'])) ? $_POST['signature_size'] : ';';
			$this->PriceQuoteModel->Terms   		= (isset($_POST['terms'])) ? $_POST['terms'] : 'no';

			// user has accepted the pricequote
			if($this->PriceQuoteModel->acceptPriceQuoteWithSignature())
			{
				$this->Template->flashMessage($this->PriceQuoteModel);
				header('Location: '.__SITE_URL.'/accept/');
				exit;
			}
		}

		$this->Template->parseMessage($this->PriceQuoteModel);
		$this->Template->pricequote_object = $this->PriceQuoteModel;
		$this->Template->accept_url = __SITE_URL.'/accept/';
		$this->Template->download_url = __SITE_URL.'/accept/'.__('download', 'url');
		$this->Template->show('pricequote.accept');
	}

	public function download()
	{
		if($result = $this->PriceQuoteModel->printPriceQuote())
		{
			download_file($result);
		}
		else
		{
			$this->Template->flashMessage($this->PriceQuoteModel);
			header('Location: ' . __SITE_URL . '/accept/');
			exit;
		}
	}

	private function validateParams()
	{
		$pricequote_code = $hash = false;

		// both required parameters are present
		if(isset($_GET['pricequote']) && $_GET['pricequote'] && isset($_GET['key']) && $_GET['key'])
		{
			$pricequote_code = $_GET['pricequote'];
			$hash = $_GET['key'];
		}
		elseif(isset($_SESSION['PriceQuoteCode']) && isset($_SESSION['PriceQuoteHash']))
		{
			$pricequote_code = $_SESSION['PriceQuoteCode'];
			$hash = $_SESSION['PriceQuoteHash'];
		}

		if($pricequote_code && $hash && $this->PriceQuoteModel->showByHash($pricequote_code, $hash) === TRUE)
		{
			$_SESSION['PriceQuoteCode'] = $pricequote_code;
			$_SESSION['PriceQuoteHash'] = $hash;
			return TRUE;
		}

		// Show an error page
		$this->Template->setSidebar(false);
		$this->Template->show('page.error');
		exit;
	}
}