<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "ideal.abn.idealonly/payment_provider.php";
$tmp_payment_provider = new ideal_abn_idealonly();
	
if(isset($_GET['SHASIGN']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction();
}
else
{
	// If no POST-variable
	$tmp_payment_provider->paymentStatusUnknown();
}