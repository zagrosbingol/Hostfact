<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Company_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	public function show()
	{
		$result = $this->APIRequest('company', 'show', array(), array('cacheable' => true, 'cacheGlobal' => true));
		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['company'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}
}