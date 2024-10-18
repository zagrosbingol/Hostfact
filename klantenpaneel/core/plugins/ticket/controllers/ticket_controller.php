<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Base_Controller;
use Cache;
use Language;

class Ticket_Controller extends Base_Controller
{
	public function __construct(\Template $template)
	{
		// Call parent controller construct
		parent::__construct($template);

		// Load the ticket object
		$this->TicketModel = new Ticket_Model;
		$this->TicketModel->FullPluginName = $this->FullPluginName;

		// Also load the message object
		$this->TicketMessageModel                 = new TicketMessage_Model();
		$this->TicketMessageModel->FullPluginName = $this->FullPluginName;

		// Configuration for max number of attachments
		$this->Template->max_attachments = $this->TicketMessageModel->MaxAttachments;


		// Set sidebar
		$this->Template->setSidebar('ticket.sidebar');

	}

	public function index()
	{
		// Retrieve list of tickets
		$this->setListTickets();

		$this->Template->parseMessage();
		$this->Template->show('ticket.index');
	}

	public function setListTickets()
	{
		// Get tickets
		$ticket_list = $this->TicketModel->listTickets();

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

		$this->Template->open_tickets = $open_tickets;
		$this->Template->ticket_list = $ticket_list;
	}

	public function view()
	{
		$this->TicketModel->id = intval(get_url_var($_GET['rt']));
		$result = $this->TicketModel->show();

		// We also handle a reply post here
		if($result && !empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			// Prepare message and add message to ticket
			$this->TicketMessageModel->setMessage($_POST['Message'], ((isset($_FILES['Attachments'])) ? $_FILES['Attachments'] : false));
			$result_add = $this->TicketModel->addMessage($this->TicketMessageModel);

			// Reload page on success
			if($result_add)
			{
				$this->Template->flashMessage($this->TicketModel);
				header('Location: ' . __SITE_URL . '/' . __('ticket', 'url').'/'.__('view', 'url').'/'.$this->TicketModel->id);
				exit;
			}
			else
			{
				// Push to template, if we need it
				$this->Template->ticketmessage = $this->TicketMessageModel;
				$this->Template->flashMessage($this->TicketMessageModel);
			}
		}

		// Retrieve list of tickets
		$this->setListTickets();

		$this->Template->ticket = $this->TicketModel;
		$this->Template->parseMessage($this->TicketModel);

		if(isset($result) && $result)
		{
			$this->Template->show('ticket.view');
		}
		else
		{
			$this->Template->header = __('ticket');
			$this->Template->text   = __('ticket does not exist');
			$this->Template->show('page.objectnotfound');
		}
	}

	public function ticketmessage()
	{
		// Explode the router-URL, to retrieve both ticketID and messageID
		$parts = explode('/', $_GET['rt']);
		$ticket_id = intval($parts[count($parts)-2]);
		$message_id = intval($parts[count($parts)-1]);

		// Show ticket
		$this->TicketModel->id = $ticket_id;
		$result = $this->TicketModel->show();

		if(isset($result) && $result)
		{
			// Loop through all messages to find the corresponding message
			foreach($this->TicketModel->TicketMessages as $_message)
			{
				if($_message['Identifier'] == $message_id)
				{
					// Already base64_decode the message, so we can replace inline images
					$_message['Message'] = base64_decode($_message['Base64Message']);
					unset($_message['Base64Message']);

					// Find and replace URLs of inline images
					if(isset($_message['Attachments']) && is_array($_message['Attachments']))
					{
						foreach($_message['Attachments'] as $_attachment)
						{
							$_message['Message'] = str_replace($_attachment['location'], __SITE_URL . '/' . __('ticket', 'url') . '/' . __('download', 'url') . '/' . $this->TicketModel->id . '/' . $_message['Identifier'] . '/' . md5($_attachment['location']), $_message['Message']);
						}
					}

					// Push this message to template and break foreach loop
					$this->Template->message = $_message;
					break;
				}
			}

			// Since we are in an iframe, we don't want header or sidebar loaded
			$this->Template->setHeader(false);
			$this->Template->setSidebar(false);

			$this->Template->show('ticket.message');
		}

	}

	public function download()
	{
		// Explode the router-URL, to retrieve ticketID, messageID and file hash
		$parts = explode('/', $_GET['rt']);
		$ticket_id = intval($parts[count($parts)-3]);
		$message_id = intval($parts[count($parts)-2]);
		$file_id = $parts[count($parts)-1];

		// Show ticket
		$this->TicketModel->id = $ticket_id;
		$result = $this->TicketModel->show();

		if(isset($result) && $result)
		{
			if($file_result = $this->TicketMessageModel->download($this->TicketModel, $message_id, $file_id))
			{
				download_file($file_result);
				exit;
			}
		}

		$this->Template->flashMessage($this->TicketMessageModel);
		header('Location: ' . __SITE_URL . '/' . __('ticket', 'url').'/'.__('view', 'url').'/'.$this->TicketModel->id);
		exit;
	}

	public function create()
	{

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$this->TicketModel->Subject = $_POST['Subject'];

			$this->TicketMessageModel->setMessage($_POST['Message'], ((isset($_FILES['Attachments'])) ? $_FILES['Attachments'] : false));

			if($this->TicketModel->add($this->TicketMessageModel))
			{
				$this->Template->flashMessage($this->TicketModel);
				header('Location: ' . __SITE_URL . '/' . __('ticket', 'url').'/'.__('view', 'url').'/'.$this->TicketModel->id);
				exit;
			}
			else
			{
				// Push to template, if we need it
				$this->Template->flashMessage($this->TicketMessageModel);
				$this->Template->ticketmessage = $this->TicketMessageModel;
			}
		}

		// Retrieve list of tickets
		$this->setListTickets();

		$this->Template->parseMessage($this->TicketModel);

		$this->Template->ticket = $this->TicketModel;
		$this->Template->show('ticket.create');

	}

	public function close()
	{
		$this->TicketModel->id = intval(get_url_var($_GET['rt']));
		if($this->TicketModel->show())
		{
			$this->TicketModel->close();
		}

		$this->Template->flashMessage($this->TicketModel);
		header('Location: ' . __SITE_URL . '/' . __('ticket', 'url'));
		exit;
	}
}