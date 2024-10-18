<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Base_Model;
use Cache;

class TicketMessage_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public $Message;
	public $Attachments;

	public function __construct()
	{
		// Configuration for max number of attachments
		$this->MaxAttachments = 5;

		$this->Error = $this->Warning = $this->Success = array();
	}

	public function validateMessageData()
	{
		// Check message
		if(!trim($this->Message))
		{
			$this->Error[] = __('invalid message');
			$this->ErrorFields[] = 'Message';
		}

		// Check number of attachments
		if(count($this->Attachments) > $this->MaxAttachments)
		{
			$this->Error[] = sprintf(__('invalid number of attachments'), $this->MaxAttachments);
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function setMessage($message, $attachments = false)
	{
		$this->Message = $message;

		$this->Attachments = array();
		if($attachments && is_array($attachments))
		{
			foreach($attachments['name'] as $i => $_name)
			{
				if($attachments['error'][$i] == '0')
				{
					$this->Attachments[] = array('Filename' => $_name,
												 'Base64'   => base64_encode(file_get_contents($attachments['tmp_name'][$i]))
					);
				}
			}
		}
	}

	public function download(Ticket_Model $TicketModel, $message_id, $file_id)
	{
		// Loop through all messages to find the corresponding message with attachment
		foreach($TicketModel->TicketMessages as $_message)
		{
			if($_message['Identifier'] == $message_id)
			{
				// If we find the message, look for it's attachments to validate file hash and be sure it is an attachment of the message
				foreach($_message['Attachments'] as $_attachment)
				{
					if(md5($_attachment['location']) == $file_id)
					{
						$result = $this->APIRequest('attachment', 'download', array('TicketID' => $TicketModel->TicketID, 'Type' => 'ticket', 'Filename' => $_attachment['name']), array('useAPIError' => FALSE));

						if($result['status'] == 'success' && isset($result['success']))
						{
							// Fix for API inconsistency
							$file_result = array();
							$file_result['Filename'] = $result['success'][0];
							$file_result['Base64'] 	= $result['success'][1];

							return $file_result;
						}
					}
				}
			}
		}

		return false;
	}
}