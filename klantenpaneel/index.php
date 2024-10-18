<?php

// define the core folder
define('COREPATH', 'core');
define('CUSTOMPATH', 'custom');
define('PLUGINPATH', 'plugins');

// include the config file which sets vars and includes the required files
require COREPATH . '/includes/config.php';
// Load csrf protection
require_once COREPATH . '/includes/csrf.php';

// Check POST limit
if(!empty($_POST))
{
	$ini_get_max_input_vars = @ini_get('max_input_vars');
	if($ini_get_max_input_vars > 0 && count($_POST, true) > $ini_get_max_input_vars)
	{
		// flashmessage function is not available here, so we set it in the session
		$_SESSION['flashMessage']['Error'][] = __('max_input_vars reached');
		header('Location: ' . __SITE_URL . '/' . $_GET['rt']);
		exit;
	}
}

// if the clientarea has not yet been activated
if(!Settings_Model::get('CLIENTAREA_URL'))
{
	// Reset cache
	Cache::clean();
	session_unset();
	// Regenerate session id for security and caching issues
	session_regenerate_id(true);

    // Warn user that clientarea is not active.
    if(Settings_Model::get('IS_INTERNATIONAL') == 'yes')
    {
        fatal_error('This client area is not active', 'Please activate this client area via your backoffice and check your client area URL.');
    }
    else
    {
        fatal_error('Het klantenpaneel is nog niet geactiveerd.', 'Activeer het klantenpaneel via uw backoffice en controleer de URL van het klantenpaneel.');
    }
}

// if the user is redirected from WFH with a key, validate it here
// this must go first to logout the user when it is already loggedin
if(isset($_GET['wfh_key']) && $_GET['wfh_key'] != '')
{
	// set the key in the session, to validate later on
	$_SESSION['WFH_key']    = $_GET['wfh_key'];
	$_GET['rt']         	= __('login', 'url') . '/' . __('validatekey', 'url');

	// redirect to validate key function
	$router->loader();
}
// user is logged in, route url to the correct page
elseif(isset($_SESSION['User']) && isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] === TRUE)
{
	// CSRF check
	if(!empty($_POST) && (!defined("SKIP_CSRF_CHECK") || SKIP_CSRF_CHECK !== TRUE))
	{
		$result = CSRF_Model::validateToken();
	}

	$router->loader();
}
// user has submitted login form
else if(isset($_POST) && isset($_POST['username']) && $_GET['rt'] == __('login', 'url'))
{
	$router->loader();
}
// redirect user to password reset page
else if(!isset($_SESSION['user']) && $_GET['rt'] == __('login', 'url') . '/' .__('resetPassword', 'url'))
{
	$router->loader();
}
// redirect user to two factor login page
else if(!isset($_SESSION['user']) && $_GET['rt'] == __('login', 'url') . '/' .__('twoFactorAuth', 'url'))
{
	$router->loader();
}
// redirect user to forgot password page
else if(!isset($_SESSION['user']) && $_GET['rt'] == __('login', 'url') . '/' .__('forgotPassword', 'url'))
{
	$router->loader();
}
// loading of CSS files or JS files should be possible without being logged in (for login screen)
else if(strpos(trim($_GET['rt'], '/'), 'asset/css') === 0 || strpos(trim($_GET['rt'], '/'), 'asset/js') === 0)
{
	$router->loader();
}
// user not logged in and not on login page, redirect to login page
else if(!(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] === TRUE) && $_GET['rt'] != __('login', 'url'))
{
	// Load page, but check if it is allowed without user logged in
	if($router->loader(true) === false)
	{
		// Save redirect url (if not starting with asset-folder)
		if (strpos($_GET['rt'], 'asset/') !== 0 && strpos($_GET['rt'], 'favicon.ico') === false)
		{
			$_SESSION['redirect_url'] = $_GET['rt'];
		}

		header('Location: ' . __SITE_URL . '/' . __('login', 'url'));
		exit;
	}
}
// login page
else
{
	$router->loader();
}

