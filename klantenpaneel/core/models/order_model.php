<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Order_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all orders from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listOrders($searchfor = false)
	{
		$parameters = array('status' => '0|1|2|8');

		if($searchfor)
		{
			$parameters['searchat'] = 'OrderCode|CompanyName|SurName|Description';
			$parameters['searchfor'] = $searchfor;
		}

		$result = $this->APIRequest('order', 'list', $parameters, array('cacheable' => true));

		if($result === FALSE || !isset($result['orders']))
		{
			return FALSE;
		}

		return $result['orders'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('order', 'show', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['order'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}
}