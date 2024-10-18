<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "paypal/payment_provider.php";
$tmp_payment_provider = new paypal();

if(isset($_SESSION['paypal_session']['id']) && isset($_SESSION['paypal_session']['type']))
{
	if($_SESSION['paypal_session']['type'] == 'invoice')
	{
		$tmp_payment_provider->setInvoice($_SESSION['paypal_session']['id']);
	}
	elseif($_SESSION['paypal_session']['type'] == 'order')
	{
		$tmp_payment_provider->setOrder($_SESSION['paypal_session']['id']);
	}
	
	// Validate transaction			
	$tmp_payment_provider->paymentStatusUnknown('');
}
else
{
	$tmp_payment_provider->paymentStatusUnknown('');
}