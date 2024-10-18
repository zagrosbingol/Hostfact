<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Model;
use Cache;

class SSL_Model extends Service_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all ssl from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listSSL()
	{
		$result = $this->APIRequest('ssl', 'list', array('status' => 'inrequest|install|active|expired|error'), array('cacheable' => true));

		if($result === FALSE || !isset($result['ssl']))
		{
			return FALSE;
		}

		return $result['ssl'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('ssl', 'show', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'cacheable' => $this->id));

		if($result === FALSE)
		{
			$this->Error[] = __('ssl does not exist');
			return FALSE;
		}

		foreach($result['ssl'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function reissueSSL()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('ssl', 'reissue', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE));

		if($result === FALSE)
		{
			$this->Error[] = __('ssl could not be rissued');
			return FALSE;
		}

		// Reset cache
		Cache::reset('ssl.'.$this->id);

		return TRUE;
	}

	public function downloadSSL()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('ssl', 'download', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE, 'cacheable' => $this->id));

		if($result === FALSE || !isset($result['ssl']) || !$result['ssl']['Base64'] || !$result['ssl']['Filename'])
		{
			$this->Error[] = __('ssl could not be downloaded');
			return FALSE;
		}

		return $result['ssl'];
	}

}