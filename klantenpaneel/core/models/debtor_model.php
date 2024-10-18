<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Debtor_Model extends Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public $Variables = array('Identifier','DebtorCode','Status','Username','Password','SecurePassword','LastDate','CompanyName','CompanyNumber','TaxNumber','Sex','Initials','SurName','Address','Address2','ZipCode','City','State','Country','BirthDate','EmailAddress','PhoneNumber','MobileNumber','FaxNumber',	'Comment','InvoiceMethod','InvoiceCompanyName','InvoiceSex','InvoiceInitials','InvoiceSurName','InvoiceAddress','InvoiceAddress2','InvoiceZipCode','InvoiceCity','InvoiceState','InvoiceCountry','InvoiceEmailAddress','InvoiceAuthorisation','InvoiceTemplate','PriceQuoteTemplate','AccountNumber','AccountBIC','AccountName','AccountBank','AccountCity','Mailing', 'LegalForm', 'DefaultLanguage');

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
	}

	public function show()
	{
		$result = $this->APIRequest('debtor', 'show', array(), array('cacheable' => true));

		if($result === FALSE)
		{
			return FALSE;
		}

		foreach($result['debtor'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		// if no user rights are returned
		if(empty($this->ClientareaSettings['Rights']))
		{
			return FALSE;
		}

		$debtor_settings = $this->ClientareaSettings['Rights'];

		if(!empty($this->ClientareaSettings['Orderforms']))
		{
			$debtor_settings = array_merge($debtor_settings, $this->ClientareaSettings['Orderforms']);
		}

		Settings_Model::setDebtorSettings($debtor_settings);
		Settings_Model::set('CLIENTAREA_WELCOME_TITLE', (isset($this->ClientareaSettings['WelcomeTitle'])) ? $this->ClientareaSettings['WelcomeTitle'] : '');
		Settings_Model::set('CLIENTAREA_WELCOME_MESSAGE', (isset($this->ClientareaSettings['WelcomeMessage'])) ? $this->ClientareaSettings['WelcomeMessage'] : '');

		// Set debtor setting
		Settings_Model::set('SHOW_AMOUNT_VAT', ($this->CompanyName) ? 'excl' : 'incl');
		return TRUE;
	}

	public function editGeneralData()
	{
		if($this->validateGeneralData() === FALSE)
		{
			return FALSE;
		}

		// Prepare API parameters
		$params = array('CompanyName'	=> $this->CompanyName,
						'LegalForm'		=> $this->LegalForm,
						'CompanyNumber' => $this->CompanyNumber,
						'TaxNumber' 	=> $this->TaxNumber,

						'Sex' 		=> $this->Sex,
						'Initials' 	=> $this->Initials,
						'SurName' 	=> $this->SurName,

						'Address' 	=> $this->Address,
						'ZipCode' 	=> $this->ZipCode,
						'City' 		=> $this->City,
						'Country' 	=> $this->Country,

						'EmailAddress' 	=> $this->EmailAddress,
						'PhoneNumber' 	=> $this->PhoneNumber,
						'FaxNumber' 	=> $this->FaxNumber,
						'MobileNumber' 	=> $this->MobileNumber,

						'Mailing' 		=> $this->Mailing,

						'SendNotification' => (Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no',
						'IPAddress' => $_SERVER['REMOTE_ADDR']);

		if(Settings_Model::get('IS_INTERNATIONAL'))
		{
			$params['Address2'] = $this->Address2;
			$params['State']    = $this->State;
		}

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		$this->Success[] = __('debtor general data saved');

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

	public function editBillingData()
	{
		if($this->validateBillingData() === FALSE)
		{
			return FALSE;
		}

		// Prepare API parameters
		$params = array('InvoiceCompanyName'	=> $this->InvoiceCompanyName,

						'InvoiceSex' 		=> $this->InvoiceSex,
						'InvoiceInitials' 	=> $this->InvoiceInitials,
						'InvoiceSurName' 	=> $this->InvoiceSurName,

						'InvoiceAddress' 	=> $this->InvoiceAddress,
						'InvoiceZipCode' 	=> $this->InvoiceZipCode,
						'InvoiceCity' 		=> $this->InvoiceCity,
						'InvoiceCountry' 	=> $this->InvoiceCountry,

						'InvoiceEmailAddress' 	=> $this->InvoiceEmailAddress,

						'SendNotification' => (Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no',
						'IPAddress' => $_SERVER['REMOTE_ADDR']);

		if(Settings_Model::get('IS_INTERNATIONAL'))
		{
			$params['InvoiceAddress2'] = $this->InvoiceAddress2;
			$params['InvoiceState']    = $this->InvoiceState;
		}

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		$this->Success[] = __('debtor general data saved');

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

	public function editLoginData()
	{
		if($this->validateLoginData() === FALSE)
		{
			return FALSE;
		}

		$params = array('SecurePassword' => wf_password_hash($this->NewPassword));

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		$this->Success[] = __('debtor login data saved');

		foreach($result['debtor'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

	public function editPaymentData()
	{
		if($this->validatePaymentData() === FALSE)
		{
			return FALSE;
		}

		// Prepare API parameters
		$params = array('AccountNumber'        => $this->AccountNumber,
						'AccountName'          => $this->AccountName,
						'AccountBank'          => $this->AccountBank,
						'AccountBIC'           => $this->AccountBIC,
						'InvoiceAuthorisation' => $this->InvoiceAuthorisation,

						'SendNotification' => (Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE_NOTIFICATION') == 'email') ? 'yes' : 'no',
						'IPAddress' => $_SERVER['REMOTE_ADDR']);

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		$this->Success[] = __('debtor general data saved');

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

	public function editDefaultLanguage()
	{
		// Prepare API parameters
		$params = array('DefaultLanguage'        => $this->DefaultLanguage);

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

	public function validateGeneralData()
	{
		// Check surname or companyname
		if(isset($this->CompanyName) && isset($this->SurName) && !trim($this->CompanyName) && !trim($this->SurName))
		{
			$this->Error[] = __('no companyname and no surname are given');
			$this->ErrorFields[] = 'CompanyName';
			$this->ErrorFields[] = 'SurName';
		}

		// Check emailaddress
		if(isset($this->EmailAddress) && trim($this->EmailAddress) && !(check_email_address($this->EmailAddress)))
		{
			$this->Error[] = __('invalid emailaddress');
			$this->ErrorFields[] = 'EmailAddress';
		}

		if(Settings_Model::get('IS_INTERNATIONAL'))
		{
			if($this->State && isset(Settings_Model::$states[$this->Country]) && !in_array($this->State, array_keys(Settings_Model::$states[$this->Country])))
			{
				$this->Error[] = __('invalid state');
				$this->ErrorFields[] = 'StateCode';
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validateBillingData()
	{
		// Check emailaddress
		if(isset($this->InvoiceEmailAddress) && trim($this->InvoiceEmailAddress) && !(check_email_address($this->InvoiceEmailAddress)))
		{
			$this->Error[] = __('invalid emailaddress');
			$this->ErrorFields[] = 'InvoiceEmailAddress';
		}

		if(Settings_Model::get('IS_INTERNATIONAL'))
		{
			if($this->InvoiceState && isset(Settings_Model::$states[$this->InvoiceCountry]) && !in_array($this->InvoiceState, array_keys(Settings_Model::$states[$this->InvoiceCountry])))
			{
				$this->Error[] = __('invalid state');
				$this->ErrorFields[] = 'InvoiceStateCode';
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validateLoginData()
	{
		if(!isset($this->CurrentPassword) || !$this->CurrentPassword || wf_password_verify($this->CurrentPassword, $this->SecurePassword) === FALSE)
		{
			$this->Error[] = __('invalid current password');
			$this->ErrorFields[] = 'CurrentPassword';
		}

		if(!isset($this->NewPassword) || !$this->NewPassword)
		{
			$this->Error[] = __('no password given');
			$this->ErrorFields[] = 'NewPassword';
		}

		if(isset($this->NewPassword) && strlen($this->NewPassword) < 8)
		{
			$this->Error[] = __('password should at least be 8 char');
			$this->ErrorFields[] = 'NewPassword';
		}

		if(isset($this->NewPassword))
		{
			if(!isset($this->NewPasswordRepeat) || $this->NewPasswordRepeat != $this->NewPassword)
			{
				$this->Error[] = __('repeat password not correct');
				$this->ErrorFields[] = 'NewPasswordRepeat';
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validatePaymentData()
	{
		if(isset($this->AccountNumber) && $this->AccountNumber && check_IBAN($this->AccountNumber) === FALSE)
		{
			$this->Error[] = __('invalid iban');
			$this->ErrorFields[] = 'AccountNumber';
		}

		if(isset($this->AccountBIC) && $this->AccountBIC && check_BIC($this->AccountBIC) === FALSE)
		{
			$this->Error[] = __('invalid bic');
			$this->ErrorFields[] = 'AccountBIC';
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function login()
	{
		if(!$this->Username || !$this->Password)
		{
			$this->Error[] = __('no username or password given');
			return FALSE;
		}

		$data['debtor_username'] = $this->Username;
		$data['debtor_password'] = encrypt_data($this->Password);
		$data['remote_ip']       = $_SERVER['REMOTE_ADDR'];
		$api_hash = base64_encode(serialize($data));
        HostFactAPI::setAPIHash($api_hash);

		$params = array('Username' => $this->Username,
						'Password' => encrypt_data($this->Password));

		$checklogin_call = array('checklogin'  => array('controller' => 'debtor', 'action' => 'checklogin', 'params' => $params));
		$calls = array_merge($checklogin_call, Settings_Model::$default_calls);
		$multi_result = $this->APIRequestMultiple($calls);

		if($multi_result === FALSE || !isset($multi_result['checklogin']['debtor']))
		{
			$this->Error = array(); // reset errors, we don't want to show the API error

			// user is blocked because of too many login attempts
			if(isset($multi_result['checklogin']['blocked_minutes']))
			{
				$this->Error[] = sprintf(__('user blocked for x minutes'), $multi_result['checklogin']['blocked_minutes']);
			}
			// invalid login
			else
			{
				$this->Error[] = __('username or password invalid');
			}

			return FALSE;
		}
		// user has logged in with a activation code (temp password)
		if(isset($multi_result['checklogin']['debtor']['passwordreset']) && $multi_result['checklogin']['debtor']['passwordreset'])
		{
			$this->ResetPassword = TRUE;
		}
		else
		{
			$this->ResetPassword = FALSE;
		}

		foreach($multi_result['checklogin']['debtor'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function loginFromBackoffice()
	{
		if(!$this->DebtorKey || !$this->CustomerPanelKey || !$this->Identifier)
		{
			$this->Error[] = __('login from wfh - key invalid');
			return FALSE;
		}

		$data['login_from_backoffice']	= TRUE;
		$api_hash = base64_encode(serialize($data));
        HostFactAPI::setAPIHash($api_hash);

		$params = array('DebtorKey'        => $this->DebtorKey,
						'DebtorID'         => $this->Identifier,
						'CustomerPanelKey' => encrypt_data($this->CustomerPanelKey));

		$checklogin_call = array('checklogin'  => array('controller' => 'debtor', 'action' => 'checklogin', 'params' => $params));
		$calls = array_merge($checklogin_call, Settings_Model::$default_calls);
		$multi_result = $this->APIRequestMultiple($calls);

		if($multi_result === FALSE || !isset($multi_result['checklogin']['debtor']))
		{
			$invalid_signature = (in_array("Invalid signature of request", $this->Error)) ? true : false;
			$this->Error = array(); // reset errors, we don't want to show the API error

			if($invalid_signature)
			{
				$this->Error[] = __('login from wfh - signature invalid');
			}
			else
			{
				$this->Error[] = __('login from wfh - key invalid');
			}
			return FALSE;
		}

		foreach($multi_result['checklogin']['debtor'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function passwordReset()
	{
		if($this->validatePasswordReset() === FALSE)
		{
			return FALSE;
		}

		$data['debtor_id']             = $this->Identifier;
		$data['debtor_password']       = $this->Password;
		$data['debtor_password_reset'] = TRUE;
		$api_hash = base64_encode(serialize($data));
        HostFactAPI::setAPIHash($api_hash);

		// API call where we set the new password for the debtor, and delete the activation code
		$params = array('SecurePassword' => wf_password_hash($this->NewPassword), 'Password' => '');
		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		$this->Success[] = __('password reset successfull');

		foreach($result['debtor'] as $key => $value)
		{
			$this->{$key} = $value;
		}

		return TRUE;
	}

	public function validatePasswordReset()
	{
		if(!isset($this->NewPassword) || !$this->NewPassword)
		{
			$this->Error[] = __('no password given');
		}

		if(isset($this->NewPassword) && strlen($this->NewPassword) < 8)
		{
			$this->Error[] = __('password should at least be 8 char');
		}

		if(isset($this->NewPassword))
		{
			if(!isset($this->NewPasswordRepeat) || $this->NewPasswordRepeat != $this->NewPassword)
			{
				$this->Error[] = __('repeat password not correct');
			}
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function forgotPassword()
	{
		if($this->validatePasswordForgot() === FALSE)
		{
			return FALSE;
		}

		$data['debtor_username']        = $this->Username;
		$data['debtor_email']           = $this->EmailAddress;
		$data['debtor_password_forgot'] = TRUE;
		$data['remote_ip']       		= $_SERVER['REMOTE_ADDR'];
		$api_hash = base64_encode(serialize($data));
        HostFactAPI::setAPIHash($api_hash);

		$params = array('Username'                => $this->Username,
						'EmailAddress'            => $this->EmailAddress,
						'SendPasswordForgotEmail' => 'yes'
		);

		// API call where we send an email to the debtor if a debtor with the username/email combination exists
		$result = $this->APIRequest('debtor', 'updatelogincredentials', $params);

		// user is blocked because of too many login attempts
		if(isset($result['blocked_minutes']))
		{
			$this->Error[] = sprintf(__('user blocked for x minutes'), $result['blocked_minutes']);
			return FALSE;
		}

		if($result === FALSE)
		{
			$this->Error = array(); // reset errors, we don't want to show the API error
			$this->Error[] = __('user with username email unknown');
			return FALSE;
		}

		$this->Success[] = sprintf(__('password reset successfull, email send'), normalize(check_email_address($result['debtor']['EmailAddress'], 'convert', ', ')));

		return TRUE;
	}

	public function validatePasswordForgot()
	{
		if(!$this->Username || !$this->EmailAddress)
		{
			$this->Error[] = __('no username or email given');
			$this->ErrorFields[] = 'Username';
			$this->ErrorFields[] = 'EmailAddress';
		}

		if(!is_email($this->EmailAddress))
		{
			$this->Error[] = __('invalid emailaddress');
			$this->ErrorFields[] = 'EmailAddress';
		}

		if(!empty($this->Error))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function updateTokenData($data)
	{
		// Prepare API parameters
		if(strlen($data) > 0)
		{
			$params = array(
				'TokenData' => $data,
				'TwoFactorAuthentication' => 'off'
			);
		}
		else
		{
			$params = array(
				'TokenData' => '',
				'TwoFactorAuthentication' => 'off'
			);
		}

		$result = $this->APIRequest('debtor', 'edit', $params);

		if($result === FALSE)
		{
			return FALSE;
		}

		// Reset cache
		Cache::reset('debtor');

		return TRUE;
	}

}