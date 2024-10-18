<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Service_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all other services from debtor, based on filters
	 *
	 * @param array $options
	 * @return bool
	 */
	public function listServices()
	{
		$result = $this->APIRequest('service', 'list', array('status' => '1'), array('cacheable' => true));

		if($result === FALSE || !isset($result['services']))
		{
			return FALSE;
		}

		return $result['services'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('service', 'show', array('Identifier' => $this->id), array('cacheable' => $this->id));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['service'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function terminate()
	{
		// validate password first
		if(wf_password_verify($this->Password, $_SESSION['SecurePassword']) === FALSE)
		{
			$this->Error[] = __('password is invalid');
			$this->ErrorFields[] = 'Password';
			return FALSE;
		}

		if(!is_numeric($this->ServiceID))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest($this->ServiceType, 'terminate', array('Identifier' => $this->ServiceID, 'Reason' => $this->Reason, 'IP' => $_SERVER['REMOTE_ADDR'], 'ApproveTermination' => (Settings_Model::get('CLIENTAREA_SERVICE_TERMINATE') == 'approve') ? 'yes' : 'no', 'SendNotification' => (Settings_Model::get('CLIENTAREA_SERVICE_TERMINATE_NOTIFICATION') == 'email') ? 'yes' : 'no'), array('useAPIError' => false));

		if($result !== FALSE)
		{
			if(isset($result[$this->ServiceType]['Termination']['Status']) && $result[$this->ServiceType]['Termination']['Status'] == 'approval')
			{
				$this->Success[] = sprintf(__('subscription termination waiting for approval'), rewrite_date_db2site($result[$this->ServiceType]['Termination']['Date']));
			}
			else
			{
				$this->Success[] = sprintf(__('subscription termination successfull'), rewrite_date_db2site($result[$this->ServiceType]['Termination']['Date']));
			}

			// Reset cache
			Cache::reset($this->ServiceType.'.'.$this->id);
			Cache::reset($this->ServiceType.'.list');

			return TRUE;
		}
		else
		{
			$this->Error[] = __('error during termination of service');
		}

		return FALSE;
	}


	public function cancelModification()
	{
		if(!is_numeric($this->ServiceID))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest($this->ServiceType, 'cancelmodification', array('Identifier' => $this->ServiceID, 'ModificationType' => $this->ModificationType), array('useAPIError' => FALSE));

		if($result !== FALSE)
		{
			$this->Success[] = __('modification successfully canceled');

			// Reset cache
			Cache::reset($this->ServiceType.'.'.$this->id);
			Cache::reset($this->ServiceType.'.list');

			return TRUE;
		}

		// Reset cache on error too, modification might have been processed/accepted
		Cache::reset($this->ServiceType.'.'.$this->id);
		Cache::reset($this->ServiceType.'.list');

		$this->Error[] = __('modification could not be canceled');
		return FALSE;
	}
}