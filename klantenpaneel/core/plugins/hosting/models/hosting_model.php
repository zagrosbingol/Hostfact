<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Model;

class Hosting_Model extends Service_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all hosting accounts from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listHosting()
	{
		$result = $this->APIRequest('hosting', 'list', array('status' => '-1|1|3|4|5|7'), array('cacheable' => true));

		if($result === FALSE || !isset($result['hosting']))
		{
			return FALSE;
		}

		return $result['hosting'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('hosting', 'show', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['hosting'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function getDomains()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('hosting', 'getdomainlist', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE || !isset($result['hosting']['domainlist']))
		{
			return FALSE;
		}

		return $result['hosting']['domainlist'];
	}

	public function getServerLogin($allowed_ips = array())
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('hosting', 'singlesignon', array('Identifier' => $this->id, 'IPAddresses' => $allowed_ips), array('useAPIError' => FALSE));

		if($result === FALSE || !isset($result['hosting']))
		{
			return FALSE;
		}

		return $result['hosting'];
	}
}