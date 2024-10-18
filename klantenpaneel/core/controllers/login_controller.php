<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Login_Controller extends Base_Controller
{

	function __construct(Template $template)
	{
		parent::__construct($template);

		$this->DebtorModel = new Debtor_Model();

		// Set different header
		$this->Template->setHeader('login');
	}

	public function index()
	{
		// process login form data
		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$this->DebtorModel->Username = $_POST['Username'];
			$this->DebtorModel->Password = $_POST['Password'];

			if($this->DebtorModel->login())
			{
				if(isset($this->DebtorModel->TwoFactorAuthentication) && $this->DebtorModel->TwoFactorAuthentication == 'on')
				{
					$_SESSION['User']     = $this->DebtorModel->Identifier;
					$_SESSION['Username'] = $this->DebtorModel->Username;
					$_SESSION['Password'] = encrypt_data($_POST['Password']);
					$_SESSION['DefaultLanguage'] = $this->DebtorModel->DefaultLanguage;
					$_SESSION['SecurePassword']  = $this->DebtorModel->SecurePassword;

					$this->setLoggedIn();
					unset($_SESSION['LoggedIn']);

					header('Location: ' . __SITE_URL . '/' . __('login', 'url') . '/' . __('twoFactorAuth', 'url'));
					exit;
				}
				else if($this->DebtorModel->ResetPassword === TRUE)
				{
					$_SESSION['User']     = $this->DebtorModel->Identifier;
					$_SESSION['Username'] = $this->DebtorModel->Username;
					$_SESSION['Password'] = encrypt_data($_POST['Password']);
					header('Location: ' . __SITE_URL . '/' . __('login', 'url') . '/' . __('resetPassword', 'url'));
					exit;
				}
				else
				{
					$this->setLoggedIn();

					// Check for redirect url
					if(isset($_SESSION['redirect_url']) && $_SESSION['redirect_url'])
					{
						$redirect = $_SESSION['redirect_url'];
						unset($_SESSION['redirect_url']);

						header('Location: ' . __SITE_URL . '/' . $redirect);
						exit;
					}

					header('Location: ' . __SITE_URL);
					exit;
				}
			}
		}

		// Check if already logged in
		if(isset($_SESSION['User']) && $_SESSION['User'] > 0 && isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] === TRUE)
		{
			header('Location: ' . __SITE_URL);
			exit;
		}

		$this->Template->parseMessage($this->DebtorModel);

		// Set sidebar
		$this->Template->show('login');
	}

	public function twoFactorAuth()
	{
		// Verify auth code
		if(isset($_POST['authCode']) && $_POST['authCode'] != '')
		{
			require_once "core/models/authentication_model.php";

			$authentication = new Authentication_Model();
			$verify_result = $authentication->authenticateUser($_SESSION['Username'], preg_replace('/\D/', '', $_POST['authCode']));

			if($verify_result)
			{
				$this->DebtorModel->Identifier 	= $_SESSION['User'];
				$this->DebtorModel->Username  	= $_SESSION['Username'];
				$this->DebtorModel->Password   	= $_SESSION['Password'];
				$this->DebtorModel->DefaultLanguage 	= $_SESSION['DefaultLanguage'];
				$this->DebtorModel->SecurePassword 		= $_SESSION['SecurePassword'];

				$this->setLoggedIn();

				// Check for redirect url
				if(isset($_SESSION['redirect_url']) && $_SESSION['redirect_url'])
				{
					$redirect = $_SESSION['redirect_url'];
					unset($_SESSION['redirect_url']);

					header('Location: ' . __SITE_URL . '/' . $redirect);
					exit;
				}
				header('Location: ' . __SITE_URL);
				exit;
			}
			else
			{
				$this->DebtorModel->Error[] 	= __('two factor login invalid');
			}
		}

		$this->Template->parseMessage($this->DebtorModel);
		$this->Template->show('login.twofactor');
	}

	public function resetPassword()
	{
		if(!isset($_SESSION['User']) || !$_SESSION['User'])
		{
			// Redirect to login instead
			header('Location: ' . __SITE_URL . '/' . __('login', 'url'));
			exit;
		}

		// process form data
		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$this->DebtorModel->NewPassword       = $_POST['NewPassword'];
			$this->DebtorModel->NewPasswordRepeat = $_POST['NewPasswordRepeat'];

			$this->DebtorModel->Identifier = $_SESSION['User'];
			$this->DebtorModel->Password   = $_SESSION['Password'];

			// password reset successfull, redirect to home
			if($this->DebtorModel->passwordReset())
			{
                $this->setLoggedIn();

                $this->Template->flashMessage($this->DebtorModel);

                // Still force the 2FA after resetting password.
                if(isset($this->DebtorModel->TwoFactorAuthentication) && $this->DebtorModel->TwoFactorAuthentication == 'on')
                {
                    unset($_SESSION['LoggedIn']);

                    header('Location: ' . __SITE_URL . '/' . __('login', 'url') . '/' . __('twoFactorAuth', 'url'));
                    exit;
                }

				// Check for redirect url
				if(isset($_SESSION['redirect_url']) && $_SESSION['redirect_url'])
				{
					$redirect = $_SESSION['redirect_url'];
					unset($_SESSION['redirect_url']);

					header('Location: ' . __SITE_URL . '/' . $redirect);
					exit;
				}
				header('Location: ' . __SITE_URL);
				exit;
			}
		}

		$this->Template->parseMessage($this->DebtorModel);

		$this->Template->show('login.passwordreset');
	}

	public function logout()
	{
		$clientarea_logout_url = Settings_Model::get('CLIENTAREA_LOGOUT_URL');

		$this->setLoggedOut();

		if($clientarea_logout_url == '')
		{
			$this->DebtorModel->Success[] = __('logout successfull');
			header('Location: ' . __SITE_URL . '/' . __('login', 'url'));
		}
		else
		{
			header('Location: ' . $clientarea_logout_url);
		}

		exit;
	}

	public function forgotPassword()
	{
		// process form data
		if(!empty($_POST))
		{
			// sanitize post strings
			$_POST = sanitize_post_values($_POST);

			$this->DebtorModel->Username     = $_POST['Username'];
			$this->DebtorModel->EmailAddress = $_POST['EmailAddress'];

			// password forgot successfull, redirect to home
			if($this->DebtorModel->forgotPassword())
			{
				$this->Template->flashMessage($this->DebtorModel);

				header('Location: ' . __SITE_URL);
				exit;
			}
		}

		// Check if already logged in
		if(isset($_SESSION['User']) && $_SESSION['User'] > 0 && isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] === TRUE)
		{
			header('Location: ' . __SITE_URL);
			exit;
		}

		$this->Template->debtor = $this->DebtorModel;
		$this->Template->parseMessage($this->DebtorModel);

		$this->Template->show('login.forgotpassword');
	}

	public function setLoggedIn()
	{
        session_regenerate_id(true);

		// set user values in session
		$_SESSION['User']            = $this->DebtorModel->Identifier;
		$_SESSION['Username']        = $this->DebtorModel->Username;
		$_SESSION['DefaultLanguage'] = $this->DebtorModel->DefaultLanguage;
		$_SESSION['SecurePassword']  = $this->DebtorModel->SecurePassword;
		$_SESSION['LoggedIn']        = TRUE;

		// build hash for api authentication, only once per session
		$data = array();
		$data['timestamp']			= date('Y-m-d H:i:s');
		$data['debtor_id']			= $this->DebtorModel->Identifier;
		$data['debtor_username']	= $this->DebtorModel->Username;
		$data['debtor_password']	= sha1($this->DebtorModel->SecurePassword);

		$_SESSION['ca_api_hash']	= base64_encode(serialize($data));
	}

	public function setLoggedOut()
	{
		$_SESSION['User']                                = "";
		$_SESSION['Username']                            = "";
		$_SESSION['SecurePassword']                      = "";
		$_SESSION['Password']                            = "";
		$_SESSION['LoggedIn']                            = FALSE;
		$_SESSION['ca_api_hash']                         = "";

		// Clean cache
		Cache::clean();

		session_unset();
		unset($_SESSION['User'], $_SESSION['LoggedIn']);

		// Regenerate session id for security and caching issues
		session_regenerate_id(true);
	}

	function validatekey()
	{
		// store these values first, then destroy session values
		$key = $_SESSION['WFH_key'];
		unset($_SESSION['WFH_key']);

		// logout user if it is already logged in, makes it possible to easily switch between debtors from the backoffice
		$this->setLoggedOut();

		$key_data = unserialize(base64_decode($key));

		if(isset($key_data['time_generated']))
		{
			// key is valid for 4 hours
			$date_now           = new DateTime("now");
			$date_generated     = new DateTime($key_data['time_generated']);
			$date_valid_untill  = $date_generated->modify("+4 hours");
		}

		if(!isset($key_data['debtor_id']) || (int)$key_data['debtor_id'] === 0 || !isset($key_data['debtor_key']) || $key_data['debtor_key'] == '' || !isset($key_data['db_key']))
		{
			$this->DebtorModel->Error[] = __('login from wfh - key invalid');

		}
		elseif(!isset($key_data['time_generated']) || $date_now > $date_valid_untill)
		{
			$this->DebtorModel->Error[] = __('login from wfh - key expired');
		}
		else
		{
			$this->DebtorModel->Identifier       = $key_data['debtor_id'];
			$this->DebtorModel->DebtorKey        = $key_data['debtor_key'];
			$this->DebtorModel->CustomerPanelKey = $key_data['db_key'];

			if($this->DebtorModel->loginFromBackoffice())
			{
				// successfully logged in from backoffice!
				$this->setLoggedIn();
				header('Location: ' . __SITE_URL);
				exit;
			}
		}


		$this->Template->flashMessage($this->DebtorModel);
		header('Location: ' . __SITE_URL . '/' . __('login', 'url'));
		exit;
	}
}