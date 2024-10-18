<?php
// Load database connection and settings
require_once "config.php";

$tmp_payment_provider = new Payment_Provider_Base;

if(isset($_GET['id']))
{
	$tmp_payment_provider->setInvoice($_GET['id']);
	
	$_SESSION['payment']['type'] 			= 'invoice';
	$_SESSION['payment']['id'] 				= $tmp_payment_provider->InvoiceID;
}

$_SESSION['payment']['status'] 			= 'paid';
$_SESSION['payment']['date'] 			= date('Y-m-d H:i:s');

header("Location: ".IDEAL_EMAIL);
exit;