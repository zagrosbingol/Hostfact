<?php
//ini_set('display_errors','off');error_reporting(0);

// Load files & classes
// set the session name, this way the sessions set are unique to the clientarea, prevents sharing sessions on the same domain with other applications.
// generate a suffix based on the database name
include_once '../connect.php';
$suffix = (defined('DB_NAME')) ? substr(md5(DB_NAME),0,8) : '';
session_name('wfhc' . $suffix);

// Secure session cookie (httponly & secure flags).
$current_session_params = session_get_cookie_params();
$http_only = true;
$secure_flag =  ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443);
session_set_cookie_params($current_session_params['lifetime'], $current_session_params['path'], $current_session_params['domain'], $secure_flag, $http_only);

session_start();
spl_autoload_register('hostfact_autoloader');
require_once "application/base_functions.php";

$settings = Setting_Model::getInstance();

// Load language file
$load_language = (isset($_SESSION['client_language']) && $_SESSION['client_language'] && @file_exists("includes/language/".htmlspecialchars($_SESSION['client_language']).".php")) ? htmlspecialchars($_SESSION['client_language']) : LANGUAGE;
if(@file_exists("includes/language/".$load_language.".php"))
{
	require_once "includes/language/".$load_language.".php";
}
else
{
	fatal_error('A file is missing', 'Can not find the necessary language file for the language "'.LANGUAGE.'"');
}

/**
 * hostfact_autoloader()
 * Loads correct files for classes
 * 
 * @param mixed $class
 * @return void
 */
function hostfact_autoloader($class)
{
	// Load application classes
	if(@file_exists("application/".strtolower($class).".php"))
	{
		require_once "application/".strtolower($class).".php";
	}
	// Load controllers
	elseif(strpos($class, 'Controller') !== false && @file_exists("controllers/".strtolower($class).".php"))
	{
		require_once "controllers/".strtolower($class).".php";
	}
	// Load models
	elseif(strpos($class, 'Model') !== false && @file_exists("models/".strtolower($class).".php"))
	{
		require_once "models/".strtolower($class).".php";
	}
	else
	{
		fatal_error('A file is missing', 'Can not find the necessary file for class "'.$class.'".');
	}
}

function __($message){
	global $_LANG;
	
	return (isset($_LANG[$message])) ? $_LANG[$message] : '';
}

/**
 * fatal_error()
 * End the script and trhows a nice looking error
 * 
 * @param mixed $title
 * @param mixed $msg
 * @return void
 */
function fatal_error($title, $msg)
{
	die('<html><head><title>Error</title><style type="text/css">body{margin:40px;font-family:Verdana;font-size:12px;color:#000;}#content{border:1px solid #999;background-color:#fff;padding: 25px;width:600px;position:absolute;left:50%;margin-left:-300px;}a{color:#000099;}h1{font-weight:normal;font-size:14px;color:#990000;margin:0 0 4px 0;}</style></head><body><div id="content"><h1>'.$title.'</h1>'.$msg.'</div></body></html>');
}

function money($amount, $currency_sign = true)
{
	if(!is_numeric($amount))
	{
		return $amount;
	}
	
	if($currency_sign)
	{
		return CURRENCY_SIGN_LEFT.' '.number_format($amount,AMOUNT_DEC_PLACES,AMOUNT_DEC_SEPERATOR,AMOUNT_THOU_SEPERATOR).' '.CURRENCY_SIGN_RIGHT;
	}
	else
	{
		return number_format($amount,AMOUNT_DEC_PLACES,AMOUNT_DEC_SEPERATOR,AMOUNT_THOU_SEPERATOR);
	}
}

function is_email($email){

  if (substr_count( $email, "@" ) > 1) {
    return false;
  }

    // old -- if(preg_match("/^[_a-z0-9-]+(\.[_a-z.0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,63})/", strtolower(trim(rtrim($email)))) == 0)
    // any character allowed except /, \, space, @, <, >
    // /u at the end makes it UTF-8 compatible
    if(preg_match("/^[^\\\ \/@<>.]+(\.[^\\\ \/@<>.]+)*@[^\\\ \/@<>.]+(\.[^\\\ \/@<>.]+)*(\.[^\\\ \/@<>.]{2,63})$/u", strtolower(trim(rtrim($email)))) == 0) {
		return false;
    }
	return true;
}