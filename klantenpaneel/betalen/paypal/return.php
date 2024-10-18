<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "paypal/payment_provider.php";
$tmp_payment_provider = new paypal();

if(isset($_GET['tx']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_GET['tx']);
}
else
{
	$transaction = '';
	// If session exists, use this one (IPN might be too late)
	if(isset($_SESSION['paypal_session']['type']) && $_SESSION['paypal_session']['type'] == 'invoice')
	{
		$tmp_payment_provider->setInvoice($_SESSION['paypal_session']['id']);
	}
	elseif(isset($_SESSION['paypal_session']['type']) && $_SESSION['paypal_session']['type'] == 'order')
	{
		$tmp_payment_provider->setOrder($_SESSION['paypal_session']['id']);
	}
	else
	{
		// If no session, use transaction ID from POST
		$transaction = (isset($_POST['txn_id'])) ? htmlspecialchars($_POST['txn_id']) : '';
	}
	
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($transaction);
}