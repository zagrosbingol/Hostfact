<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

// Load payment provider class
require_once "mollie/payment_provider.php";
$tmp_payment_provider = new mollie();

if(isset($_SESSION['mollie']['transaction_id']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_SESSION['mollie']['transaction_id']);
}
else
{
	// Check based on invoice ID
	if(isset($_GET['id']) && $_GET['id'])
	{

		if($tmp_payment_provider->setInvoice(intval(str_replace('mollieidinvoice','',passcrypt(base64_decode($_GET['id']))))))
		{
			// Validate transaction
            if ($tmp_payment_provider->TransactionID) {
                $tmp_payment_provider->validateTransaction($tmp_payment_provider->TransactionID);
            } else {
                // Transaction might be cancelled, reload screen with possibility to pay again.
                $_SESSION['payment']['type'] = 'invoice';
                $_SESSION['payment']['id'] = $tmp_payment_provider->InvoiceID;
            }
		}
		elseif($tmp_payment_provider->setOrder(intval(str_replace('mollieidorder','',passcrypt(base64_decode($_GET['id']))))))
		{
			// Validate transaction
            if ($tmp_payment_provider->TransactionID) {
                $tmp_payment_provider->validateTransaction($tmp_payment_provider->TransactionID);
            } else {
                // Transaction might be cancelled, reload screen with possibility to pay again.
                $_SESSION['payment']['type'] = 'order';
                $_SESSION['payment']['id'] = $tmp_payment_provider->OrderID;
            }
		}
	}

	// If no GET-variable
	$tmp_payment_provider->paymentStatusUnknown('transactie ID niet bekend');
}
?>