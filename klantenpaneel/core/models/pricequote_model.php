<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class PriceQuote_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	/** Get all pricequotes from debtor, based on filters
	 *
	 * @param array $options
	 * @return bool
	 */
	public function listPriceQuotes($searchfor = false)
	{
		$parameters = array('status' => '2|3|4|8');

		if($searchfor)
		{
			$parameters['searchat'] = 'PriceQuoteCode|CompanyName|SurName|Description';
			$parameters['searchfor'] = $searchfor;
		}

		$result = $this->APIRequest('pricequote', 'list', $parameters, array('cacheable' => TRUE));

		if($result === FALSE || !isset($result['pricequotes']))
		{
			return FALSE;
		}

		return $result['pricequotes'];
	}

	public function show()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$result = $this->APIRequest('pricequote', 'show', array('Identifier' => $this->id, 'ShowAcceptedDetails' => 'yes'), array('cacheable' => $this->id));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['pricequote'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function showByHash($pricequote_code, $hash)
	{
		if(!$pricequote_code || !$hash)
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		$data['public_call'] = TRUE;
		$api_hash = base64_encode(serialize($data));
        HostFactAPI::setAPIHash($api_hash);

		$result = $this->APIRequest('pricequote', 'showbyhash', array('PriceQuoteCode' => $pricequote_code,'ShowAcceptedDetails' => 'yes', 'Hash' => $hash, 'IPAddress' => $_SERVER['REMOTE_ADDR']));

		if($result === FALSE)
		{
			$this->Error[] = __('invalid identifier');
			return FALSE;
		}

		// Now do a call for debtor
		$result_debtor = $this->APIRequest('debtor', 'show', array('Identifier' => $result['pricequote']['Debtor']));

		if($result_debtor && isset($result_debtor['debtor']['ClientareaSettings']['Rights']))
		{
			// Change language if needed
			$debtor_lang = ($result_debtor['debtor']['DefaultLanguage']) ? $result_debtor['debtor']['DefaultLanguage'] : Settings_Model::get('CLIENTAREA_DEFAULT_LANG');
			if(!isset($_SESSION['CLIENTAREA_DEFAULT_LANG']) || $_SESSION['CLIENTAREA_DEFAULT_LANG'] != $debtor_lang)
			{

				$_SESSION['CLIENTAREA_DEFAULT_LANG'] = $debtor_lang;

				global $language;
				$language->loadLanguageFiles($_SESSION['CLIENTAREA_DEFAULT_LANG']);

			}


			$debtor_settings = $result_debtor['debtor']['ClientareaSettings']['Rights'];
			Settings_Model::setDebtorSettings($debtor_settings);
		}

		foreach($result['pricequote'] as $key => $value)
		{
			$this->{$key} = $value;
		}
		$this->id = $result['pricequote']['Identifier'];

		return TRUE;
	}

	public function printPriceQuote()
	{
		$params = array('Identifier' => $this->id);
		// When accepted, download other variant
		if($this->AcceptPDF)
		{
			$params['ShowAcceptedDetails'] = 'yes';
		}

		$result = $this->APIRequest('pricequote', 'download', $params, array('cacheable' => $this->id));

		if($result === FALSE || !isset($result['pricequote']) || !$result['pricequote']['Base64'] || !$result['pricequote']['Filename'])
		{
			return FALSE;
		}

		return $result['pricequote'];
	}

	public function declinePriceQuote()
	{
		// validate password first
		if(wf_password_verify($this->Password, $_SESSION['SecurePassword']) === FALSE)
		{
			$this->Error[] = __('password is invalid');
			$this->ErrorFields[] = 'Password';
		}

		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
		}

		if(!empty($this->Error))
		{
			return false;
		}

		$result = $this->APIRequest('pricequote', 'decline', array('Identifier' => $this->id,
																  'Reason' => $this->Reason,
																  'IPAddress' => $_SERVER['REMOTE_ADDR'],
																  'SendNotification' => (Settings_Model::get('CLIENTAREA_PRICEQUOTE_ACCEPT_NOTIFICATION') == 'email') ? 'yes' : 'no'));

		if($result !== FALSE)
		{
			$this->Success[] = sprintf(__('pricequote declined successfull'), normalize($this->PriceQuoteCode));

			// Reset cache
			Cache::reset('pricequote.'.$this->id);
			Cache::reset('pricequote.list');

			return TRUE;
		}

		return FALSE;
	}

	public function acceptPriceQuoteWithSignature()
	{
		if(!is_numeric($this->id))
		{
			$this->Error[] = __('invalid identifier');
		}

		// Check for agreement on terms (if it was visible)
		if(Settings_Model::get('CLIENTAREA_TERMS_URL') != '' && Settings_Model::get('CLIENTAREA_PRICEQUOTE_ACCEPT_TERMS') == 'yes' && $this->Terms != 'agree')
		{
			$this->Error[] = __('please agree to the terms and conditions');
			$this->ErrorFields[] = 'Terms';
		}

		if(!empty($this->Error))
		{
			return false;
		}

		if(!$this->validate())
		{
			return FALSE;
		}

		/** FIX SVG DOCUMENT SIZE, because on mobile you can write outside canvas **/
		if($this->SignatureSize != ";")
		{
			$svg_document_size		= explode(";", $this->SignatureSize); // format = width;height
			$pattern 				= '/version="(.*)" width="[^"]+" height="[^"]+"/i';
			$replace 				= 'version="$1" width="'.$svg_document_size[0].'" height="'.$svg_document_size[1].'"';
			$this->AcceptSignature 	= preg_replace($pattern, $replace, $this->AcceptSignature);
		}

		/** REMOVE X-axis AND Y-axis (-2,-2) TO KEEP SIGNATURE ON THE RIGHT POSITION **/
		$this->AcceptSignature 		= str_replace('<path stroke-linejoin="round" stroke-linecap="round" stroke-width="2" stroke="rgb(68, 68, 68)" fill="none" d="M -2 -2 l 1 1"/>', '', $this->AcceptSignature);

		/** SIGNATURE SVG */
		$this->AcceptSignatureBase64 = base64_encode($this->AcceptSignature);

		$result = $this->APIRequest('pricequote', 'accept_signature', array('Identifier' => $this->id,
			'AcceptName'         => $this->AcceptName,
			'AcceptEmailAddress' => $this->AcceptEmailAddress,
			'AcceptComment'      => $this->AcceptComment,
			'AcceptSignatureBase64'    => $this->AcceptSignatureBase64,
			'AcceptIPAddress'    => $_SERVER['REMOTE_ADDR'],
			'AcceptUserAgent'    => $_SERVER['HTTP_USER_AGENT'],
			'SendNotification' => (Settings_Model::get('CLIENTAREA_PRICEQUOTE_ACCEPT_NOTIFICATION') == 'email') ? 'yes' : 'no',
			'ShowAcceptedDetails' => 'yes'));

		if($result !== FALSE)
		{
			$this->Success[] = sprintf(__('pricequote accepted successfull'), normalize($this->PriceQuoteCode));

			// Reset cache
			Cache::reset('pricequote.'.$this->id);
			Cache::reset('pricequote.list');

			return TRUE;
		}

		return FALSE;
	}

	public function validate()
	{
		if(!$this->AcceptName)
		{
			$this->Error[] = __('name is required');
		}

		if(!$this->AcceptEmailAddress)
		{
			$this->Error[] = __('email is required');
		}

		if(!check_email_address($this->AcceptEmailAddress))
		{
			$this->Error[] = __('email not valid');
		}

		// the xml string is a empty signature
		if(!$this->AcceptSignature || $this->AcceptSignature == '"<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="0" height="0"></svg>"')
		{
			$this->Error[] = __('signature is required');
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

}