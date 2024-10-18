<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

namespace hostfact;

use Service_Model;
use Settings_Model;
use Cache;

class Domain_Model extends Service_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all domains from debtor, based on filters
	 *
	 * @return bool
	 */
	public function listDomains()
	{
		$result = $this->APIRequest('domain', 'list', array('status' => 'client_visible'), array('cacheable' => true));

		if($result === FALSE || !isset($result['domains']))
		{
			return FALSE;
		}

		return $result['domains'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('domain', 'show', array('Identifier' => $this->id, 'ShowHandleInfo' => 'yes'), array('useAPIError' => FALSE, 'cacheable' => $this->id));

		if($result === FALSE)
		{
			$this->Error[] = __('domain does not exist');
			return FALSE;
		}

		// overwrite cache, cache only 60 seconds if item has pending modifications
		if(!isset($result['fromCache']) && !empty($result['domain']['ClientareaModifications']))
		{
			Cache::updateCacheTime('domain.'.$this->id, 60);
		}

		foreach($result['domain'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function editNameservers()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		if($this->validateNameservers() === FALSE)
		{
			return FALSE;
		}

		$params = array('Identifier' => $this->id,
						'DNS1'       => $this->DNS1,
						'DNS2'       => $this->DNS2,
						'DNS3'       => $this->DNS3,
						'DNS1IP'     => $this->DNS1IP,
						'DNS2IP'     => $this->DNS2IP,
						'DNS3IP'     => $this->DNS3IP,
						'SendNotification' => (Settings_Model::get('CLIENTAREA_DOMAIN_NAMESERVER_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no',

						'IPAddress' => $_SERVER['REMOTE_ADDR']);

		$result = $this->APIRequest('domain', 'changenameserver', $params, array('useAPIError' => FALSE));

		if($result === FALSE)
		{
			$this->Error[] = __('nameservers update failed');
			return FALSE;
		}

		// Reset cache
		Cache::reset('domain.'.$this->id);

		$this->Success[] = sprintf(__('warning changenameserver modification awaiting approval'), rewrite_date_db2site(date('Y-m-d')) . ' ' . __('at') . ' ' . date('H:i'));

		return TRUE;
	}

	public function editWhois($whois_changes)
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		if($this->validateWhois($whois_changes) === FALSE)
		{
			return FALSE;
		}

		$params = array('Identifier' => $this->id,
						'PlaceInQueue'       => 'yes',
						'IPAddress' => $_SERVER['REMOTE_ADDR'],
						'SendNotification' => (Settings_Model::get('CLIENTAREA_DOMAIN_WHOIS_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no');
		$params = array_merge($params, $whois_changes);

		$result = $this->APIRequest('domain', 'editwhois', $params, array('useAPIError' => FALSE));

		if($result === FALSE)
		{
			$this->Error[] = __('whois edit failed');
			return FALSE;
		}

		// Reset cache
		Cache::reset('domain.'.$this->id);

		$this->Success[] = sprintf(__('warning editwhois modification awaiting approval'), rewrite_date_db2site(date('Y-m-d')) . ' ' . __('at') . ' ' . date('H:i'));

		return TRUE;
	}

	public function editDNSZone($dns_zone_edit)
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		if($this->validateDNSZone($dns_zone_edit) === FALSE)
		{
			return FALSE;
		}

		$params = array('Identifier' => $this->id,
						'SendNotification' => (Settings_Model::get('CLIENTAREA_DOMAIN_DNSZONE_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no',
						'IPAddress' => $_SERVER['REMOTE_ADDR']);
		$params = array_merge($params, $dns_zone_edit);

		$result = $this->APIRequest('domain', 'editdnszone', $params, array('useAPIError' => true));

		if($result === FALSE)
		{
			$this->Error[] = __('edit dns zone failed');
			return FALSE;
		}

		// Reset cache
		Cache::reset('domain.'.$this->id);

		$this->Success[] = sprintf(__('warning editdnszone modification awaiting approval'), rewrite_date_db2site(date('Y-m-d')) . ' ' . __('at') . ' ' . date('H:i'));

		return TRUE;
	}

	public function validateNameservers()
	{
		$validHostnameRegex = "/^(([a-z0-9]|[a-z0-9][a-z0-9\-]*[a-z0-9])\.)*([a-z0-9]|[a-z0-9][a-z0-9\-])+(\.[a-z0-9]{2,63})$/i";
		$full_domain = '.' . $this->Domain . '.' . $this->Tld;

		for($i = 1; $i <= 3; $i++)
		{
			if((isset($this->{'DNS'.$i}) && $this->{'DNS'.$i} && preg_match($validHostnameRegex, $this->{'DNS'.$i}) == FALSE))
			{
				$this->Error[] = __('nameservers invalid');
				$this->ErrorFields[] = 'DNS'.$i;
			}

			if(isset($this->{'DNS'.$i.'IP'}) && $this->{'DNS'.$i.'IP'} && filter_var($this->{'DNS'.$i.'IP'}, FILTER_VALIDATE_IP) === FALSE)
			{
				$this->Error[] = __('nameservers ipaddress invalid');
				$this->ErrorFields[] = 'DNS'.$i.'IP';
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validateWhois($whois_changes)
	{
		foreach($whois_changes as $_handle_type => $_handle)
		{
			// Surname is required (this is needed for creating the handle at the registrar
			if(isset($whois_changes[$_handle_type]['SurName']) && !trim($whois_changes[$_handle_type]['SurName']))
			{
				$this->Error[] = __('handle ' . strtolower($_handle_type)) . ': ' . __('no surname given');
			}

			if(Settings_Model::get('IS_INTERNATIONAL'))
			{
				if($whois_changes[$_handle_type]['State'] && isset(Settings_Model::$states[$whois_changes[$_handle_type]['Country']]) && !in_array($whois_changes[$_handle_type]['State'], array_keys(Settings_Model::$states[$whois_changes[$_handle_type]['Country']])))
				{
					$this->Error[] = __('handle ' . strtolower($_handle_type)) . ': ' . __('invalid state');
				}
			}
		}


		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validateDNSZone($dns_zone_edit)
	{
		if(count($dns_zone_edit['DNSZone']['records']) === 0)
		{
			$this->Error[] = __('no dns records given');
		}

		foreach($dns_zone_edit['DNSZone']['records'] as $dns_record)
		{
			// name cannot end with a dot
			if(substr($dns_record['name'], -1) == '.')
			{
				$this->Error[] = __('record name may not end with dot');
			}

			if(strpos($dns_record['name'], $this->Domain . '.' . $this->Tld) !== FALSE)
			{
				$this->Error[] = __('record name may not contain domain');
			}

			if(strpos($dns_record['name'], ' ') !== FALSE)
			{
				$this->Error[] = __('record name may not contain spaces');
			}

			if($dns_record['value'] == '')
			{
				$this->Error[] = __('record value may not be empty');
			}

			// A records should contain a valid IPV4 address
			if((strtoupper($dns_record['type']) == 'A') && filter_var($dns_record['value'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
			{
				$this->Error[] = __('record value invalid ipv4');
			}

			// AAAA records should contain a valid IPV6 address
			if((strtoupper($dns_record['type']) == 'AAAA') && filter_var($dns_record['value'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE)
			{
				$this->Error[] = __('record value invalid ipv6');
			}

			if(!is_numeric($dns_record['ttl']) || (int)$dns_record['ttl'] === 0)
			{
				$this->Error[] = __('record ttl incorrect data');
			}

			if(isset($dns_record['priority']) && $dns_record['priority'] && (!is_numeric($dns_record['priority']) || $dns_record['priority'] < 0 || $dns_record['priority'] > 100))
			{
				$this->Error[] = __('record priority incorrect data');
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function getHosting()
	{
		if(!is_numeric($this->HostingID))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('hosting', 'show', array('Identifier' => $this->HostingID), array('cacheable' => $this->HostingID));

		if($result === FALSE || !isset($result['hosting']))
		{
			return FALSE;
		}

		return $result['hosting'];
	}

	public function getToken()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('domain', 'gettoken', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'useAPISuccess' => TRUE, 'cacheable' => $this->id));

		if($result === FALSE)
		{
			$this->Error[] = __('token could not be retrieved, contact us');
			return FALSE;
		}

		return TRUE;
	}

	public function getDNSZone()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('domain', 'getdnszone', array('Identifier' => $this->id), array('useAPIError' => FALSE, 'cacheable' => $this->id));

		if($result === FALSE || !isset($result['domain']['dns_zone']['records']) || count($result['domain']['dns_zone']['records']) === 0)
		{
			$this->Error[] = __('dns zone could not be retrieved');
			return FALSE;
		}

		// Set DNS zone in object
		$this->DNSZone = $result['domain']['dns_zone'];
		return TRUE;
	}
}