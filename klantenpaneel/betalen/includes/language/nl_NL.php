<?php
$_LANG = array();

// controllers/paymentform_controller.php
$_LANG['cannot find open invoice with number'] = 'Kan geen openstaande factuur vinden met kenmerk %s.';
$_LANG['cannot find open invoice'] = 'Kan geen openstaande factuur vinden.';
$_LANG['please select a payment method'] = 'U heeft nog geen betaalmethode geselecteerd.';

$_LANG['paymentmethod type auth'] = 'Machtiging';
$_LANG['paymentmethod type paypal'] = 'PayPal';
$_LANG['paymentmethod type ideal'] = 'iDEAL';
$_LANG['paymentmethod type other'] = 'Online betaling';

$_LANG['your payment has failed'] = 'Uw betaling is mislukt';
$_LANG['try again or contact us'] = 'Probeer nogmaals te betalen of neem contact op via %s.';

// models/database_model.php
$_LANG['error in mysql query'] = 'Fout in MySQL query';

// views/header.phtml
$_LANG['payment page title'] = 'Direct online betalen';

// views/paid.phtml
$_LANG['payment completed'] = 'Betaling geslaagd';
$_LANG['we received the payment for invoice'] = 'We hebben uw betaling voor factuur %s ontvangen.';
$_LANG['we received the payment for order'] = 'We hebben uw betaling voor bestelling %s ontvangen.';
$_LANG['payment details'] = 'Specificatie van uw betaling';
$_LANG['invoice number'] = 'Factuurnummer';
$_LANG['invoice date'] = 'Factuurdatum';
$_LANG['invoice amount'] = 'Factuurbedrag';
$_LANG['order number'] = 'Bestelnummer';
$_LANG['order date'] = 'Besteldatum';
$_LANG['order amount'] = 'Bedrag';
$_LANG['payment date'] = 'Betaaldatum';
$_LANG['at'] = 'om';
$_LANG['payment via'] = 'Betaling via';
$_LANG['payment transactionid'] = 'Transactiekenmerk';

// views/paymentform.phtml
$_LANG['pay invoice for company'] = 'Factuur betalen aan %s';
$_LANG['invoice already paid'] = 'Reeds betaald';
$_LANG['error invoice already paid'] = 'Deze factuur is reeds betaald.';
$_LANG['error invoice already in direct debit batch'] = 'U kunt deze factuur niet online betalen, omdat er reeds een machtiging voor automatische incasso is afgegeven.';
$_LANG['invoice open amount'] = 'Nog te betalen';
$_LANG['invoice already authorized'] = 'Op deze factuur is reeds een machtiging gegeven.';
$_LANG['choose from one of the following payment methods'] = 'Maak uw keuze uit een van onderstaande betaalmethoden';
$_LANG['button pay'] = 'Betalen';

// views/pending.phtml
$_LANG['payment pending'] = 'Uw betaling wordt behandeld';
$_LANG['payment for invoice is pending'] = 'Uw betaling voor factuur %s is in behandeling.';
$_LANG['payment for order is pending'] = 'Uw betaling voor bestelling %s is in behandeling.';


// Other
$_LANG['please select a bank'] = 'U heeft nog geen bank geselecteerd.';
$_LANG['description prefix invoice'] = 'Factuur';
$_LANG['description prefix order'] = 'Bestelling';

// payment.auth/payment_provider.php
$_LANG['i authorize company to collect this invoice'] = 'Ik machtig %s om het totaalbedrag van deze factuur af te schrijven van het door mij opgegeven rekeningnummer.';
$_LANG['other open invoices'] = 'Openstaande facturen';
$_LANG['i authorize company to collect all invoices'] = 'Ik machtig %s om ook andere openstaande facturen af te schrijven van het door mij opgegeven rekeningnummer.';

$_LANG['auth error, accountnumber needed'] = 'U dient uw rekeningnummer in te vullen.';
$_LANG['auth error, accountname needed'] = 'U dient de naam van de rekeninghouder in te vullen.';
$_LANG['auth error, accountcity needed'] = 'U dient de plaats van uw bank in te vullen.';
$_LANG['auth error, please agree'] = 'U dient een vinkje te plaatsen en akkoord te gaan met het geven van een machtiging.';
$_LANG['auth error, we cannot process your authorization'] = 'We kunnen uw machtiging helaas niet verwerken.';
$_LANG['auth notification mail subject'] = 'Automatisch incasso gegeven op openstaande factuur';
$_LANG['auth error, accountnumber invalid'] = 'Het opgegeven rekeningnummer is niet geldig.';
$_LANG['auth error, accountbic invalid'] = 'De opgegeven BIC is niet geldig.';

// payment.auth/paid.phtml
$_LANG['auth payment completed'] = 'Betaling zal worden geincasseerd';
$_LANG['we received the authorization for invoice'] = 'De betaling voor factuur %s zal automatisch van uw rekening worden afgeschreven.';
$_LANG['we received the authorization for order'] = 'De betaling voor bestelling %s zal automatisch van uw rekening worden afgeschreven.';
$_LANG['auth payment details'] = 'Specificatie';

$_LANG['auth accountnumber'] = 'IBAN';
$_LANG['auth accountbic'] = 'BIC';
$_LANG['auth accountname'] = 'Naam rekeninghouder';
$_LANG['auth accountcity'] = 'Vestigingsplaats bank';
$_LANG['direct debit mandate id'] = 'Machtigingskenmerk';