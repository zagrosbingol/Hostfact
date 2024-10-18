<?php
$_LANG = array();

// controllers/paymentform_controller.php
$_LANG['cannot find open invoice with number'] = 'Kann unbezahlte Rechnung nicht finden: %s.';
$_LANG['cannot find open invoice'] = 'Kann keine unbezahlte Rechnung finden.';
$_LANG['please select a payment method'] = 'Sie haben noch keine Zahlungsmethode gewählt.';

$_LANG['paymentmethod type auth'] = 'Lastschrift';
$_LANG['paymentmethod type paypal'] = 'PayPal';
$_LANG['paymentmethod type ideal'] = 'iDEAL';
$_LANG['paymentmethod type other'] = 'Online-Zahlung';

$_LANG['your payment has failed'] = 'Ihre Zahlung ist fehlgeschlagen';
$_LANG['try again or contact us'] = 'Versuchen Sie über wieder oder kontaktieren Sie uns %s.';

// models/database_model.php
$_LANG['error in mysql query'] = 'Fehler in MySQL';

// views/header.phtml
$_LANG['payment page title'] = 'Online-Zahlung';

// views/paid.phtml
$_LANG['payment completed'] = 'Zahlung erfolgreich';
$_LANG['we received the payment for invoice'] = 'Wir haben Ihre Zahlung für Rechnung %s erhalten.';
$_LANG['we received the payment for order'] = 'Wir haben Ihre Zahlung für Bestellung %s erhalten.';
$_LANG['payment details'] = 'Zahlungsfestlegung';
$_LANG['invoice number'] = 'Rechnungsnummer';
$_LANG['invoice date'] = 'Rechnungsdatum';
$_LANG['invoice amount'] = 'Rechnungsbetrag';
$_LANG['order number'] = 'Bestellnummer';
$_LANG['order date'] = 'Bestelldatum';
$_LANG['order amount'] = 'Betrag';
$_LANG['payment date'] = 'Zahlungsdatum';
$_LANG['at'] = 'um';
$_LANG['payment via'] = 'Zahlung per';
$_LANG['payment transactionid'] = 'Transaktionsnummer';

// views/paymentform.phtml
$_LANG['pay invoice for company'] = 'Rechnung Bezahlen bei %s';
$_LANG['invoice already paid'] = 'bereits bezahlt';
$_LANG['error invoice already paid'] = 'Rechnung ist bereits bezahlt.';
$_LANG['error invoice already in direct debit batch'] = 'Sie können nicht online diese Rechnung zu bezahlen, weil bereits eine Lastschrift erteilt.';
$_LANG['invoice open amount'] = 'Noch zu zahlenden';
$_LANG['invoice already authorized'] = 'Diese Rechnung wurde von Ihrem Konto abgebucht worden.';
$_LANG['choose from one of the following payment methods'] = 'Wählen Sie eine Zahlungsmethode';
$_LANG['button pay'] = 'Bezahlen';

// views/pending.phtml
$_LANG['payment pending'] = 'Ihre Zahlung wird abgewickelt';
$_LANG['payment for invoice is pending'] = 'Ihre Zahlung anhängig ist für Rechnung %s.';
$_LANG['payment for order is pending'] = 'Ihre Zahlung anhängig ist für Bestellung %s.';


// Other
$_LANG['please select a bank'] = 'Sie habe noch keine Bank ausgewählt.';
$_LANG['description prefix invoice'] = 'Rechnung';
$_LANG['description prefix order'] = 'Bestellung';

// payment.auth/payment_provider.php
$_LANG['i authorize company to collect this invoice'] = 'Ich ermächtige %s, den Betrag von meinem Konto abzuheben.';
$_LANG['other open invoices'] = 'Herausragend Rechnungen';
$_LANG['i authorize company to collect all invoices'] = 'Ich ermächtige %s auch künftige Rechnungen von meinem Konto abzuheben.';

$_LANG['auth error, accountnumber needed'] = 'Sie müssen ein Konto-Nummer eingeben.';
$_LANG['auth error, accountname needed'] = 'Sie müssen ein Kontoinhaber eingeben.';
$_LANG['auth error, accountcity needed'] = 'Sie müssen ein Sitz der Bank eingeben.';
$_LANG['auth error, please agree'] = 'Sie müssen ein Häkchen setzen und vereinbaren, um die Erlaubnis zu geben.';
$_LANG['auth error, we cannot process your authorization'] = 'Wir können Ihre Lastschrift nicht verarbeiten.';
$_LANG['auth notification mail subject'] = 'Lastschrift auf offene Rechnung';
$_LANG['auth error, accountnumber invalid'] = 'Die Konto-Nummer ist ungültig.';
$_LANG['auth error, accountbic invalid'] = 'Die Bankleitzahl ist ungültig.';

// payment.auth/paid.phtml
$_LANG['auth payment completed'] = 'Die Zahlung wird abgeschrieben';
$_LANG['we received the authorization for invoice'] = 'Die Bezahlung der Rechnung %s wird von Ihrem Konto abgeschrieben.';
$_LANG['we received the authorization for order'] = 'Die Bezahlung der Bestellung %s wird von Ihrem Konto abgeschrieben.';
$_LANG['auth payment details'] = 'Spezifikation';

$_LANG['auth accountnumber'] = 'Kontonummer';
$_LANG['auth accountbic'] = 'Bankleitzahl';
$_LANG['auth accountname'] = 'Kontoinhaber';
$_LANG['auth accountcity'] = 'Sitz der Bank';
$_LANG['direct debit mandate id'] = 'Gläubiger-ID';