<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Attachment_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	public function download($attachment_id, $reference_id, $reference_type)
	{
		$params = array('Identifier'          => $attachment_id,
						'ReferenceIdentifier' => $reference_id,
						'Type'                => $reference_type);

		$result = $this->APIRequest('attachment', 'download', $params, array('useAPIError' => FALSE));

		if($result['status'] == 'success' && isset($result['success']))
		{
			// Fix for API inconsistency
			$file_result = array();
			$file_result['Filename'] = $result['success'][0];
			$file_result['Base64'] 	= $result['success'][1];

			return $file_result;
		}

		$this->Error[] = __('downoad attachment failed');
		return false;
	}
}