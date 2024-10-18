<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Model;
use Cache;

class VPS_Model extends Service_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all vps from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listVPS()
	{
		$result = $this->APIRequest('vps', 'list', array('status' => 'create|building|active|suspended|error'), array('cacheable' => true));

		if($result === FALSE || !isset($result['vps']))
		{
			return FALSE;
		}

		return $result['vps'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('vps', 'show', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'cacheable' => $this->id));

		if($result === FALSE)
		{
			$this->Error[] = __('vps does not exist');
			return FALSE;
		}

		foreach($result['vps'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function startVPS()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('vps', 'start', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE));

		if($result === FALSE)
		{
			$this->Error[] = __('vps could not be started');
			return FALSE;
		}

		// Reset cache
		Cache::reset('vps.'.$this->id);

		return TRUE;
	}

	public function pauseVPS()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('vps', 'pause', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE));

		if($result === FALSE)
		{
			$this->Error[] = __('vps could not be paused');
			return FALSE;
		}

		// Reset cache
		Cache::reset('vps.'.$this->id);

		return TRUE;
	}

	public function restartVPS()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('vps', 'restart', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE));

		if($result === FALSE)
		{
			$this->Error[] = __('vps could not be restarted');
			return FALSE;
		}

		// Reset cache
		Cache::reset('vps.'.$this->id);

		return TRUE;
	}

}