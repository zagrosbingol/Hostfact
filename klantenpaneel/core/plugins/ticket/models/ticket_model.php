<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Base_Model;
use Cache;

class Ticket_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public $Subject;
	public $Message;
	public $Attachments;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all tickets from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listTickets()
	{
		$result = $this->APIRequest('ticket', 'list', array('status' => '0|1|2|3', 'sort' => 'LastDate', 'order' => 'DESC'), array('cacheable' => true));

		if($result === FALSE || !isset($result['tickets']))
		{
			return FALSE;
		}

		return $result['tickets'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('ticket', 'show', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'cacheable' => $this->id, 'cacheTime' => 60));

		if($result === FALSE)
		{
			$this->Error[] = __('ticket does not exist');
			return FALSE;
		}

		foreach($result['ticket'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function close()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('ticket', 'changestatus', array('Identifier' => $this->id, 'Status' => 3), array('useAPIError' => FALSE));

		if($result === FALSE)
		{
			$this->Error[] = __('ticket does not exist');
			return FALSE;
		}

		// Reset cache
		Cache::reset('ticket.'.$this->id);
		Cache::reset('ticket.list');

		$this->Success[] = __('ticket is closed');
		return TRUE;
	}

	public function add(TicketMessage_Model $TicketMessage_Model)
	{
		if($this->validateTicketData() === FALSE || $TicketMessage_Model->validateMessageData() === FALSE)
		{
			return FALSE;
		}

		global $account;
		$message = array(	'Message' 		=> nl2br(normalize($TicketMessage_Model->Message)), // We should send HTML data, so already normalize
							'SenderName' 	=> ($account->CompanyName) ? $account->CompanyName : $account->Initials . ' ' . $account->SurName,
							'SenderEmail'	=> $account->EmailAddress,
							 'Attachments'  => $TicketMessage_Model->Attachments);

		$ticket_request = array('Debtor'		=> $account->Identifier,
								'Subject'		=> $this->Subject,
								'TicketMessages' => array($message),
								'ticket_sent_notification_or_email' => 'yes');

		$result = $this->APIRequest('ticket', 'add', $ticket_request, array('useAPIError' => true));

		if($result === FALSE)
		{
			$this->Error[] = __('ticket cannot be added');
			return FALSE;
		}

		// Set ID
		$this->id = $result['ticket']['Identifier'];

		// Reset cache
		Cache::reset('ticket.list');

		$this->Success[] = __('ticket added');

		return true;
	}

	public function addMessage(TicketMessage_Model $TicketMessage_Model)
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		if($TicketMessage_Model->validateMessageData() === FALSE)
		{
			return FALSE;
		}

		global $account;
		$message = array(	'Message' 		=> nl2br(normalize($TicketMessage_Model->Message)), // We should send HTML data, so already normalize
							 'SenderName' 	=> ($account->CompanyName) ? $account->CompanyName : $account->Initials . ' ' . $account->SurName,
							 'SenderEmail'	=> $account->EmailAddress,
							 'Attachments'  => $TicketMessage_Model->Attachments);

		$result = $this->APIRequest('ticket', 'addmessage', array('Identifier' => $this->id,
																  'TicketMessages' => array($message),
																  'ticket_sent_notification_or_email' => 'yes'), array('useAPIError' => false));

		if($result === FALSE)
		{
			$this->Error[] = __('ticket reply cannot be added');
			return FALSE;
		}

		// Reset cache
		Cache::reset('ticket.'.$this->id);
		Cache::reset('ticket.list');

		$this->Success[] = __('ticket reply added');

		return TRUE;
	}

	public function validateTicketData()
	{
		// Check subject
		if(!trim($this->Subject))
		{
			$this->Error[] = __('invalid subject');
			$this->ErrorFields[] = 'Subject';
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}
}