<?php
$_LANG = array();

// controllers/paymentform_controller.php
$_LANG['cannot find open invoice with number'] = 'Can not find unpaid invoice with reference %s.';
$_LANG['cannot find open invoice'] = 'Can not find unpaid invoice.';
$_LANG['please select a payment method'] = 'You have not selected a payment method.';

$_LANG['paymentmethod type auth'] = 'Direct debit';
$_LANG['paymentmethod type paypal'] = 'PayPal';
$_LANG['paymentmethod type ideal'] = 'iDEAL';
$_LANG['paymentmethod type other'] = 'Online payment';

$_LANG['your payment has failed'] = 'Your payment failed';
$_LANG['try again or contact us'] = 'Please try to pay again or contact us at %s.';

// models/database_model.php
$_LANG['error in mysql query'] = 'MySQL query error';

// views/header.phtml
$_LANG['payment page title'] = 'Pay online';

// views/paid.phtml
$_LANG['payment completed'] = 'Payment completed';
$_LANG['we received the payment for invoice'] = 'We successfully recieved your payment for invoice %s.';
$_LANG['we received the payment for order'] = 'We successfully recieved your payment for order %s.';
$_LANG['payment details'] = 'Payment details';
$_LANG['invoice number'] = 'Invoice number';
$_LANG['invoice date'] = 'Invoice date';
$_LANG['invoice amount'] = 'Total due';
$_LANG['order number'] = 'Order number';
$_LANG['order date'] = 'Order date';
$_LANG['order amount'] = 'Total due';
$_LANG['payment date'] = 'Payment day';
$_LANG['at'] = 'at';
$_LANG['payment via'] = 'Paid by';
$_LANG['payment transactionid'] = 'Transaction ID';

// views/paymentform.phtml
$_LANG['pay invoice for company'] = 'Pay invoice from %s';
$_LANG['invoice already paid'] = 'Already paid';
$_LANG['error invoice already paid'] = 'Invoice is already paid.';
$_LANG['error invoice already in direct debit batch'] = 'You can not pay online, because already issued a direct debit.';
$_LANG['invoice open amount'] = 'Amount due';
$_LANG['invoice already authorized'] = 'Direct debit is active for this invoice.';
$_LANG['choose from one of the following payment methods'] = 'Please choose one of the following payment methods';
$_LANG['button pay'] = 'Pay';

// views/pending.phtml
$_LANG['payment pending'] = 'Your payment is being processed';
$_LANG['payment for invoice is pending'] = 'Your payment for invoice %s is in process.';
$_LANG['payment for order is pending'] = 'Your payment for order %s is in process.';


// Other
$_LANG['please select a bank'] = 'You did not select a bank.';
$_LANG['description prefix invoice'] = 'Invoice';
$_LANG['description prefix order'] = 'Order';

// payment.auth/payment_provider.php
$_LANG['i authorize company to collect this invoice'] = 'I authorize %s to collect the amount of this invoice of my bank account.';
$_LANG['other open invoices'] = 'Unpaid invoices';
$_LANG['i authorize company to collect all invoices'] = 'I authorize %s to also collect other unpaid invoices of my bank account.';

$_LANG['auth error, accountnumber or iban needed'] = 'Please enter your bank account number.';
$_LANG['auth error, accountname needed'] = 'Please enter the bank account holder name.';
$_LANG['auth error, accountcity needed'] = 'Please enter the bank city.';
$_LANG['auth error, please agree'] = 'To process your direct debit request, you need to check the checkbox';
$_LANG['auth error, we cannot process your authorization'] = 'Unfortunately, we cannot process your direct debit request';
$_LANG['auth notification mail subject'] = 'Direct debit for unpaid invoice';
$_LANG['auth error, accountnumber invalid'] = 'The entered bank account number is invalid.';
$_LANG['auth error, accountbic invalid'] = 'The entered BIC code is invalid.';


// payment.auth/paid.phtml
$_LANG['auth payment completed'] = 'Payment will be collected';
$_LANG['we received the authorization for invoice'] = 'The payment for invoice %s will automatically be collected from your bank account.';
$_LANG['we received the authorization for order'] = 'The payment for order %s will automatically be collected from your bank account.';
$_LANG['auth payment details'] = 'Details';

$_LANG['auth accountnumber'] = 'Bank account number';
$_LANG['auth accountbic'] = 'BIC code';
$_LANG['auth accountname'] = 'Bank account holder';
$_LANG['auth accountcity'] = 'Bank city';
$_LANG['direct debit mandate id'] = 'Mandate ID';