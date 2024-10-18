<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Invoice_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all invoices from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listInvoices($searchfor = false)
	{
		$parameters = array('status' => '2|3|4|8|9');

		if($searchfor)
		{
			$parameters['searchat'] = 'InvoiceCode|CompanyName|SurName|Description';
			$parameters['searchfor'] = $searchfor;
		}

		$result = $this->APIRequest('invoice', 'list', $parameters, array('cacheable' => true));

		if($result === FALSE || !isset($result['invoices']))
		{
			return FALSE;
		}

		return $result['invoices'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('invoice', 'show', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['invoice'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function printInvoice()
	{
		$result = $this->APIRequest('invoice', 'download', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE || !isset($result['invoice']) || !$result['invoice']['Base64'] || !$result['invoice']['Filename'])
		{
			return FALSE;
		}

		return $result['invoice'];
	}

}