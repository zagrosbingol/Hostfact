<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Debtor_Controller extends Base_Controller
{

	function __construct(Template $template)
	{
		parent::__construct($template);

		// Set sidebar
		$this->Template->setSidebar('debtor.sidebar');
		$this->Template->sidebar_widgets = $this->getSidebarWidgets();
	}

	public function index()
	{
		// Allowed to change general data?
		if(Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('loginData', 'url'));
			exit;
		}

		$this->generalData();
	}

	public function generalData()
	{
		// Allowed to change general data?
		if(Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('loginData', 'url'));
			exit;
		}

		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// if there are pending modifications, show the new modified data
		if(isset($debtor_model->ClientareaModifications['editgeneral']['Data']))
		{
			$modified_data = json_decode($debtor_model->ClientareaModifications['editgeneral']['Data'], TRUE);

			if(!empty($modified_data))
			{
				foreach($modified_data as $_field => $_value)
				{
					if(isset($debtor_model->{$_field}))
					{
						$debtor_model->{$_field} = $_value;
					}
				}
			}
		}

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			// when the state is chosen from a list (select) the statecode is used instead of state
			if(Settings_Model::get('IS_INTERNATIONAL') && isset($_POST['StateCode']) && $_POST['StateCode'])
			{
				$_POST['State'] = $_POST['StateCode'];
			}

			// convert emailadres, else the is_data_modified will always return true when debtor has multiple emailadresses
			$_POST['EmailAddress'] = check_email_address($_POST['EmailAddress'], 'convert');

			// validate that user has modified data
			$data_is_changed = FALSE;
			if($data_is_changed === FALSE)
			{
				$data_is_changed = is_data_modified($_POST, $debtor_model);
			}

			$debtor_model->CompanyName 		= $_POST['CompanyName'];
			$debtor_model->LegalForm 		= $_POST['LegalForm'];
			$debtor_model->CompanyNumber 	= $_POST['CompanyNumber'];
			$debtor_model->TaxNumber 		= $_POST['TaxNumber'];

			$debtor_model->Sex 				= $_POST['Sex'];
			$debtor_model->Initials 		= $_POST['Initials'];
			$debtor_model->SurName 			= $_POST['SurName'];

			$debtor_model->Address 			= $_POST['Address'];
			$debtor_model->ZipCode 			= $_POST['ZipCode'];
			$debtor_model->City 			= $_POST['City'];
			$debtor_model->Country 			= $_POST['Country'];

			$debtor_model->EmailAddress 	= $_POST['EmailAddress'];
			$debtor_model->PhoneNumber 		= $_POST['PhoneNumber'];
			$debtor_model->FaxNumber 		= $_POST['FaxNumber'];
			$debtor_model->MobileNumber 	= $_POST['MobileNumber'];

			$debtor_model->Mailing 			= $_POST['Mailing'];

			if(Settings_Model::get('IS_INTERNATIONAL'))
			{
				$debtor_model->Address2 = $_POST['Address2'];
				$debtor_model->State    = $_POST['State'];
			}

			if($data_is_changed === FALSE)
			{
				$debtor_model->Success[] = __('debtor general data saved');
				$this->Template->flashMessage($debtor_model);
				header('Location: ' . __SITE_URL . '/' . __('debtor', 'url'));
				exit;
			}
			else
			{
				if($debtor_model->editGeneralData())
				{
					$this->Template->flashMessage($debtor_model);
					header('Location: ' . __SITE_URL . '/' . __('debtor', 'url'));
					exit;
				}
			}
		}

		// show warning for pending modification
		$this->Template->modification_html = $this->showDebtorModifications('editgeneral', $debtor_model);

		$this->Template->parseMessage($debtor_model);

		$this->Template->debtor = $debtor_model;
		$this->Template->show('debtor.view');
	}

	public function billingData()
	{
		// Allowed to change billing data?
		if(Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('loginData', 'url'));
			exit;
		}

		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// if there are pending modifications, show the new modified data
		if(isset($debtor_model->ClientareaModifications['editbilling']['Data']))
		{
			$modified_data = json_decode($debtor_model->ClientareaModifications['editbilling']['Data'], TRUE);

			if(!empty($modified_data))
			{
				foreach($modified_data as $_field => $_value)
				{
					if(isset($debtor_model->{$_field}))
					{
						$debtor_model->{$_field} = $_value;
					}
				}
			}
		}

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			// when the state is chosen from a list (select) the statecode is used instead of state
			if(Settings_Model::get('IS_INTERNATIONAL') && isset($_POST['InvoiceStateCode']) && $_POST['InvoiceStateCode'])
			{
				$_POST['InvoiceState'] = $_POST['InvoiceStateCode'];
			}

			// convert emailadres, else the is_data_modified will always return true when debtor has multiple emailadresses
			$_POST['InvoiceEmailAddress'] = check_email_address($_POST['InvoiceEmailAddress'], 'convert');

			// validate that user has modified data
			$data_is_changed = FALSE;
			if($data_is_changed === FALSE)
			{
				$data_is_changed = is_data_modified($_POST, $debtor_model);
			}

			// only set the data which actually differentiates from the normal invoice data
			$has_custom_data = true;


			if($_POST['InvoiceCompanyName'] == $debtor_model->CompanyName && $_POST['InvoiceInitials'] == $debtor_model->Initials
				&& $_POST['InvoiceSurName'] == $debtor_model->SurName && $_POST['InvoiceAddress'] == $debtor_model->Address
				&& $_POST['InvoiceZipCode'] == $debtor_model->ZipCode && $_POST['InvoiceCity'] == $debtor_model->City
				&& $_POST['InvoiceCountry'] == $debtor_model->Country && $_POST['InvoiceEmailAddress'] == $debtor_model->EmailAddress
				&& (!Settings_Model::get('IS_INTERNATIONAL') || ($_POST['InvoiceAddress2'] == $debtor_model->Address2)))
			{
				$has_custom_data = false;
			}

			$debtor_model->InvoiceCompanyName = ($has_custom_data === false) ? '' : $_POST['InvoiceCompanyName'];

			$debtor_model->InvoiceSex      = ($has_custom_data === false) ? $debtor_model->Sex : $_POST['InvoiceSex'];
			$debtor_model->InvoiceInitials = ($has_custom_data === false) ? '' : $_POST['InvoiceInitials'];
			$debtor_model->InvoiceSurName  = ($has_custom_data === false) ? '' : $_POST['InvoiceSurName'];

			$debtor_model->InvoiceAddress = ($has_custom_data === false) ? '' : $_POST['InvoiceAddress'];
			$debtor_model->InvoiceZipCode = ($has_custom_data === false) ? '' : $_POST['InvoiceZipCode'];
			$debtor_model->InvoiceCity    = ($has_custom_data === false) ? '' : $_POST['InvoiceCity'];
			$debtor_model->InvoiceCountry = ($has_custom_data === false) ? $debtor_model->Country : $_POST['InvoiceCountry'];

			$debtor_model->InvoiceEmailAddress = ($has_custom_data === false) ? '' : $_POST['InvoiceEmailAddress'];

			if(Settings_Model::get('IS_INTERNATIONAL'))
			{
				$debtor_model->InvoiceAddress2 = ($has_custom_data === FALSE) ? '' : $_POST['InvoiceAddress2'];
				$debtor_model->InvoiceState    = ($has_custom_data === FALSE) ? $debtor_model->State : $_POST['InvoiceState'];
			}

			if($data_is_changed === FALSE)
			{
				$debtor_model->Success[] = __('debtor general data saved');
				$this->Template->flashMessage($debtor_model);
				header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('billingData', 'url'));
				exit;
			}
			else
			{
				if($debtor_model->editBillingData())
				{
					$this->Template->flashMessage($debtor_model);
					header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('billingData', 'url'));
					exit;
				}
			}
		}

		// determine if there is deviating invoice data
		if($debtor_model->InvoiceCompanyName || $debtor_model->InvoiceInitials || $debtor_model->InvoiceSurName || $debtor_model->InvoiceAddress
			|| $debtor_model->InvoiceZipCode || $debtor_model->InvoiceCity
			|| ($debtor_model->InvoiceCountry && $debtor_model->InvoiceCountry != $debtor_model->Country) || $debtor_model->InvoiceEmailAddress
			|| (Settings_Model::get('IS_INTERNATIONAL') && $debtor_model->InvoiceAddress2))
		{
			$this->Template->general_data_is_used = FALSE;
		}
		else
		{
			$this->Template->general_data_is_used = TRUE;
		}

		// show warning for pending modification
		$this->Template->modification_html = $this->showDebtorModifications('editbilling', $debtor_model);

		$this->Template->parseMessage($debtor_model);

		$this->Template->debtor = $debtor_model;
		$this->Template->show('debtor.billing');
	}

	public function loginData()
	{
		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$debtor_model->CurrentPassword   = $_POST['CurrentPassword'];
			$debtor_model->NewPassword       = $_POST['NewPassword'];
			$debtor_model->NewPasswordRepeat = $_POST['NewPasswordRepeat'];

			$debtor_model->SecurePassword = $_SESSION['SecurePassword'];

			if($debtor_model->editLoginData())
			{
				unset($_SESSION['ca_api_hash']);
				// reset session values with new password
				$login_controller = new Login_Controller($this->Template);
				$login_controller->DebtorModel = $debtor_model;
				$login_controller->setLoggedIn();

				$this->Template->flashMessage($debtor_model);
				header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('loginData', 'url'));
				exit;
			}
		}

		$this->Template->parseMessage($debtor_model);

		$this->Template->debtor = $debtor_model;
		$this->Template->show('debtor.login');
	}

	public function twoFactorAuth()
	{
		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// Setting is disabled for client area
		if((!isset($debtor_model->ClientareaSettings['TwoFactorAuthentication']) || $debtor_model->ClientareaSettings['TwoFactorAuthentication'] != 'on') && !(isset($debtor_model->TwoFactorAuthentication) && $debtor_model->TwoFactorAuthentication == 'on'))
		{
			header('Location: ' . __SITE_URL . '/' . __('debtor', 'url'));
			exit;
		}

		// Verify auth code
		if(isset($_POST['authCode']) && $_POST['authCode'] != '')
		{
			require_once "core/models/authentication_model.php";
			$authentication 	= new Authentication_Model();
			$verify_result  	= $authentication->authenticateUser($_SESSION['Username'], preg_replace('/\D/', '', $_POST['authCode']));

			if($verify_result)
			{
				$result = $debtor_model->APIRequest('debtor', 'edit', array('TwoFactorAuthentication' => 'on'));
				if($result === FALSE)
				{
					$debtor_model->Error[]		= __('two step authentication activate - error');
				}
				else
				{
					$debtor_model->Success[]	= __('two step authentication - success');
					$debtor_model->TwoFactorAuthentication 	= 'on';
					unset($_SESSION['two_factor_key']);
				}

				// Reset cache
				Cache::reset('debtor');
			}
			else
			{
				$debtor_model->Error[]		= __('two step authentication - error');
			}


		}

		$this->Template->showCode 					= isset($_SESSION['two_factor_key']) ? TRUE : FALSE;
		if($this->Template->showCode)
		{
			require_once "core/models/authentication_model.php";
			$authentication 			= new Authentication_Model();
			$this->Template->auth_key 	= $authentication->setUser($_SESSION['Username'], 'TOTP');
			$this->Template->qr_url 	= urlencode($authentication->createURL($_SESSION['Username']));
		}

		$this->Template->TwoFactorAuthentication 	= $debtor_model->TwoFactorAuthentication;
		$this->Template->parseMessage($debtor_model);
		$this->Template->debtor = $debtor_model;
		$this->Template->show('debtor.twofactorauth');
	}

	public function paymentData()
	{
		// Allowed to change general data?
		if(Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE') != 'yes' && Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE') != 'approve')
		{
			header('Location: ' . __SITE_URL . '/' . __('debtor', 'url'));
			exit;
		}

		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// if there are pending modifications, show the new modified data
		if(isset($debtor_model->ClientareaModifications['editpayment']['Data']))
		{
			$modified_data = json_decode($debtor_model->ClientareaModifications['editpayment']['Data'], TRUE);

			$debtor_model->AccountNumber        = (isset($modified_data['AccountNumber'])) ? $modified_data['AccountNumber'] : $debtor_model->AccountNumber;
			$debtor_model->AccountName          = (isset($modified_data['AccountName'])) ? $modified_data['AccountName'] : $debtor_model->AccountName;
			$debtor_model->AccountBank          = (isset($modified_data['AccountBank'])) ? $modified_data['AccountBank'] : $debtor_model->AccountBank;
			$debtor_model->AccountBIC           = (isset($modified_data['AccountBIC'])) ? $modified_data['AccountBIC'] : $debtor_model->AccountBIC;
			$debtor_model->InvoiceAuthorisation = (isset($modified_data['InvoiceAuthorisation'])) ? $modified_data['InvoiceAuthorisation'] : $debtor_model->InvoiceAuthorisation;
		}

		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$_POST['InvoiceAuthorisation'] = (isset($_POST['InvoiceAuthorisation']) && $_POST['InvoiceAuthorisation'] == 'yes') ? 'yes' : 'no';
			if((Settings_Model::get('AUTHORISATION_AVAILABLE') === TRUE && $_POST['InvoiceAuthorisation'] == 'yes') || ($_POST['InvoiceAuthorisation'] == 'no' && Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_AUTHORISATION') == 'yes'))
			{
				// We are allowed to activate/deactivate direct debit
			}
			else
			{
				// remove value if not allowed
				unset($_POST['InvoiceAuthorisation']);
			}

			// validate that user has modified data
			$data_is_changed = FALSE;
			if($data_is_changed === FALSE)
			{
				$data_is_changed = is_data_modified($_POST, $debtor_model);
			}

			$debtor_model->AccountNumber        = $_POST['AccountNumber'];
			$debtor_model->AccountName          = $_POST['AccountName'];
			$debtor_model->AccountBank          = $_POST['AccountBank'];
			$debtor_model->AccountBIC           = $_POST['AccountBIC'];
			$debtor_model->InvoiceAuthorisation = isset($_POST['InvoiceAuthorisation']) ? $_POST['InvoiceAuthorisation'] : $debtor_model->InvoiceAuthorisation;

			if($data_is_changed === FALSE)
			{
				$debtor_model->Success[] = __('debtor general data saved');
				$this->Template->flashMessage($debtor_model);
				header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('paymentData', 'url'));
				exit;
			}
			else
			{
				if($debtor_model->editPaymentData())
				{
					$this->Template->flashMessage($debtor_model);
					header('Location: ' . __SITE_URL . '/' . __('debtor', 'url') . '/' . __('paymentData', 'url'));
					exit;
				}
			}
		}

		// show warning for pending modification
		$this->Template->modification_html = $this->showDebtorModifications('editpayment', $debtor_model);

		$this->Template->parseMessage($debtor_model);

		$this->Template->debtor = $debtor_model;
		$this->Template->show('debtor.payment');
	}

	private function getSidebarWidgets()
	{
		// build array with widgets
		$sidebar_widgets = array();

		// Initialize debtor model to retrieve clientarea settings
		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		$_widget = array('type'  => 'submenu',
						 'items' => array());

		if(Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') == 'yes' || Settings_Model::get('CLIENTAREA_DEBTOR_DATA_CHANGE') == 'approve')
		{
			$_widget['items'][] = array('type'  => 'debtor.submenu',
										'title' => __('general data'),
										'url'   => __SITE_URL . '/' . __('debtor', 'url'));

			$_widget['items'][] = array('type'  => 'debtor.submenu',
										'title' => __('billing data'),
										'url'   => __SITE_URL . '/' . __('debtor', 'url') . '/' . __('billingData', 'url'));
		}

		$_widget['items'][] = array('type'  => 'debtor.submenu',
									  'title' => __('change password'),
									  'url'   => __SITE_URL . '/' . __('debtor', 'url') . '/' . __('loginData', 'url'));

		if((isset($debtor_model->ClientareaSettings['TwoFactorAuthentication']) && $debtor_model->ClientareaSettings['TwoFactorAuthentication'] == 'on') || (isset($debtor_model->TwoFactorAuthentication) && $debtor_model->TwoFactorAuthentication == 'on'))
		{
			$_widget['items'][] = array('type'  => 'debtor.submenu',
				'title' => __('two step authentication'),
				'url'   => __SITE_URL . '/' . __('debtor', 'url') . '/' . __('twoFactorAuth', 'url'));
		}

		if(Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE') == 'yes' || Settings_Model::get('CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE') == 'approve')
		{
			$_widget['items'][] = array('type'  => 'debtor.submenu',
										'title' => __('payment data'),
										'url'   => __SITE_URL . '/' . __('debtor', 'url') . '/' . __('paymentData', 'url'));
		}
		$sidebar_widgets[] = $_widget;

		return $sidebar_widgets;
	}

	public function getStates()
	{
		if(!isset($_POST['country']) || !$_POST['country'])
		{
			exit;
		}

		$countrycode = $_POST['country'];

		if(isset(Settings_Model::$states[$countrycode]))
		{
			$options = '<option value="">' . __('make your choice') . '</option>';
			foreach(Settings_Model::$states[$countrycode] as $_key => $_value)
			{
				$options .= '<option value="' . $_key . '">' . normalize($_value) . '</option>';
			}

			$return = array('type' => 'select', 'options' => $options);
		}
		else
		{
			$return = array('type' => 'input');
		}
		echo json_encode($return);
		exit;
	}

	public function showDebtorModifications($modificiation_type, $debtor_model)
	{
		$modification_html = '';
		if(!empty($debtor_model->ClientareaModifications))
		{
			// do not show the div if there is only one warning which we want to skip, else this will show a empty yellow bar
			$hide_warning = (isset($_SESSION['SkipModificationWarning']) && count($debtor_model->ClientareaModifications) == 1 && isset($debtor_model->ClientareaModifications[$_SESSION['SkipModificationWarning']])) ? 'hide' : '';

			foreach($debtor_model->ClientareaModifications as $_modification_type => $_modification_data)
			{
				// skip the warning because we are already showing a success message
				if(isset($_SESSION['SkipModificationWarning']) && $_SESSION['SkipModificationWarning'] == $_modification_type)
				{
					unset($_SESSION['SkipModificationWarning']);
				}
				else
				{
					if($_modification_type == $modificiation_type)
					{
						$modification_html .= '<div class="alert alert-warning ' . $hide_warning . '" role="alert">';
						$modification_html .= '<p>' . sprintf(__('warning ' . $_modification_type . ' modification awaiting approval'), rewrite_date_db2site($_modification_data['Modified']) . ' ' . __('at') . ' ' . rewrite_date_db2site($_modification_data['Modified'], 'H:i'));
						$modification_html .= '</p>';
						$modification_html .= '</div>';
					}
				}
			}
		}

		return $modification_html;
	}

	public function changeLanguage()
	{
		$new_lang = get_url_var($_GET['rt']);

		if(array_key_exists($new_lang, Settings_Model::$languages))
		{
			$debtor_model = new Debtor_Model();
			$debtor_model->DefaultLanguage = $new_lang;
			$debtor_model->editDefaultLanguage();

			Cache::clean();
		}

		header('Location:' .  __SITE_URL);
		exit;
	}

	public function twoFactorGenerateKey()
	{
		require_once "core/models/authentication_model.php";
		$authentication 		= new Authentication_Model();
		$response['auth_key']   = $authentication->setUser($_SESSION['Username'], 'TOTP');
		$response['qr_url']     = urlencode($authentication->createURL($_SESSION['Username']));

		echo json_encode($response);
		exit;
	}

	public function twoFactorDeactivate()
	{
		$debtor_model = new Debtor_Model();
		$debtor_model->show();

		// validate password first
		if(!isset($_POST['Password']) || wf_password_verify($_POST['Password'], $_SESSION['SecurePassword']) === FALSE)
		{
			if(isset($_POST['Password']))
			{
				$debtor_model->Error[] 			= __('password is invalid');
				$debtor_model->ErrorFields[] 	= 'Password';
				$this->Template->parseMessage($debtor_model);
			}

			$this->Template->showElement('debtor.twofactorauth.deactivate');
		}
		else
		{
			if($debtor_model->updateTokenData(''))
			{
				$debtor_model->Success[]	= __('two step authentication deactivate - success');
			}
			else
			{
				$debtor_model->Error[]		= __('two step authentication deactivate - error');
			}

			$this->Template->flashMessage($debtor_model);

			die('reload');
		}
	}
}

?>