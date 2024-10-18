<?php


$_LANG = array();
$_LANG['language title'] = 'Deutsch';

/**
 * WHOIS form
 */
// views/whois/domain_form.phtml
$_LANG['whois_btn'] = 'Verfügbarkeit prüfen';

// views/whois/header.phtml
$_LANG['whois page title'] = 'Domain auswählen';
$_LANG['check domain'] = 'Prüfen';

// views/whois/result_table.phtml
$_LANG['to orderform'] = 'Bestellung';
$_LANG['whois resulttable domain'] = 'Domain';
$_LANG['whois resulttable result'] = 'Ergebnis';
$_LANG['whois resulttable price'] = 'Preis';
$_LANG['whois resulttable period'] = '&nbsp;';
$_LANG['no products in domain productgroup'] = 'Der Produktgruppe sind keine Produkte zugeordnet';
$_LANG['show more tlds'] = 'Mehr Domains';

// controllers/whois_controller.php
$_LANG['whois status available'] = 'frei';
$_LANG['whois status unavailable'] = 'belegt';
$_LANG['whois status error'] = 'unbekannt';

$_LANG['whois link available'] = 'registrieren';
$_LANG['whois link unavailable'] = 'transferieren';
$_LANG['whois link error'] = 'order';

$_LANG['in shopping cart'] = 'im Warenkorb';

// models/whois_model.php   * some translations are also used in models/domain_model.php
$_LANG['domain name required for a existing account'] = 'Keine Domain eingetragen.';
$_LANG['could not connect to whois server'] = 'Keine Verbindung zum WHOIS Server: %s';
$_LANG['unknown whois server'] = 'Kein WHOIS Server gefunden für %s';
$_LANG['no domain entered'] = 'Bitte geben Sie einen Domainnamen ein.';
$_LANG['sld must be between 2 and 63 characters'] = 'Eine Domain muss zwischen 2 und 63 Zeichen enthalten.';
$_LANG['sld should not contain dots'] = 'Bitte geben Sie keine Subdomain ein (keine Punkte im Namen)';
$_LANG['sld contains invalid characters'] = 'Der Domainname enthält ungültige Zeichen.';
$_LANG['tld not available'] = 'Die gewählte Domain ist nicht verfügbar. Bitte wählen Sie eine andere aus den folgenden Domains.';

/**
 * Order form - controllers
 */
// controllers/domainform_controller.php
$_LANG['authkey for domain is required'] = 'Für die Domain %s wird ein Authorisierungscode ben&ouml;tigt.';
$_LANG['you need one of the hosting packages to select'] = 'Bitte wählen Sie ein Hostingpaket aus.';
$_LANG['link to hosting account'] = 'Verknüpfe Domain(s) mit Hostingaccount %s.';
$_LANG['you need at least two nameservers'] = 'Bitte tragen Sie mindestens zwei Nameserver ein.';

// controllers/hostingform_controller.php
$_LANG['current domain description'] = 'Aktuelle Domain: %s';

// controllers/orderform_controller.php
$_LANG['you need to select a product'] = 'Bitte wählen Sie ein Produkt.';
$_LANG['you must agree to the terms and conditions'] = 'Um fortzufahren, müssen Sie die Allgemeinen Geschäftsbedingungen akzeptieren';
$_LANG['please select a payment method'] = 'Bitte wählen Sie eine Zahlungsart.';
$_LANG['please select a bank'] = 'Bitte tragen Sie den Namen Ihrer Bank ein.';
$_LANG['you must agree to the authorization'] = 'Zur Nutzung von Lastschrift als Zahlungsmethode müssen Sie die Genehmigung erteilen.';
$_LANG['confirmation of your order from'] = 'Ihre Bestellung von %s';

/**
 * Order form - models
 */
// models/customer_model.php
$_LANG['invalid username'] = 'Falscher Benutzername';
$_LANG['the username already exists'] = 'Dieser Benutzername existiert bereits.';
$_LANG['invalid password'] = 'Falsches Passwort';
$_LANG['invalid companyname'] = 'Bitte prüfen Sie den Firmennamen';
$_LANG['invalid companynumber'] = 'Ungültig IHK-Nummer';
$_LANG['invalid taxnumber'] = 'Bitte prüfen Sie die USt-IdNr';
$_LANG['invalid gender'] = 'Bitte prüfen Sie die Anrede';
$_LANG['invalid initials'] = 'Bitte prüfen Sie die Initialen';
$_LANG['invalid surname'] = 'Bitte prüfen Sie den Nachnamen';
$_LANG['invalid address'] = 'Bitte prüfen Sie die Adresse';
$_LANG['invalid zipcode'] = 'Bitte prüfen Sie die Postleitzahl (PLZ)';
$_LANG['invalid city'] = 'Bitte prüfen Sie der Ort';
$_LANG['invalid state'] = 'Bitte prüfen Sie das Land';
$_LANG['invalid country'] = 'Bitte wählen Sie Ihr Land';
$_LANG['invalid emailaddress'] = 'Bitte prüfen Sie die E-Mail Adresse';
$_LANG['invalid phonenumber'] = 'Die Telefonnummer enthält ungültige Zeichen oder ist zu lang.';
$_LANG['invalid mobile number'] = 'Die Mobilfunknummer enthält ungültige Zeichen oder ist zu lang.';
$_LANG['invalid faxnumber'] = 'Die Faxnummer enthält ungültige Zeichen oder ist zu lang.';
$_LANG['invalid invoicemethod'] = 'Bitte wählen Sie die Art der Rechnungsstellung';
$_LANG['invalid authorization value'] = 'Bitte prüfen Sie die Lastschriftdaten';
$_LANG['invalid custom invoice template'] = 'Falsche Angabe für eigene Rechnungsvorlage';
$_LANG['invalid custom pricequote template'] = 'Falsche Angabe für eigene Angebotsvorlage';
$_LANG['invalid invoice sex'] = 'Bitte prüfen Sie die Sex';
$_LANG['invalid invoice initials'] = 'Bitte prüfen Sie die Initialen';
$_LANG['invalid invoice surname'] = 'Bitte prüfen Sie den Nachnamen';
$_LANG['invalid invoice address'] = 'Bitte prüfen Sie die Stra&szlig;e';
$_LANG['invalid invoice zipcode'] = 'Bitte prüfen Sie die Postleitzahl (PLZ)';
$_LANG['invalid invoice city'] = 'Bitte prüfen Sie der Ort';
$_LANG['invalid invoice state'] = 'Bitte prüfen Sie das Land';
$_LANG['invalid invoice country'] = 'Bitte wählen Sie Ihr Land';
$_LANG['invalid invoice emailaddress'] = 'Bitte wählen Sie die E-Mail Adresse für Rechnungen';
$_LANG['invalid accountnumber'] = 'Bitte prüfen Sie die Kontonummer';
$_LANG['invalid iban'] = 'Bitte prüfen Sie die Kontonummer';
$_LANG['invalid bic'] = 'Bitte prüfen Sie die Bankleitzahl';
$_LANG['invalid accountname'] = 'Bitte prüfen Sie den Kontoinhaber';
$_LANG['invalid bank'] = 'Bitte prüfen Sie den Namen der Bank';
$_LANG['invalid account city'] = 'Bitte prüfen Sie den Sitz Ihrer Bank';
$_LANG['no companyname and no surname'] = 'Bitte tragen Sie einen Nachnamen oder einen Firmennamen ein';
$_LANG['no accountnumber given'] = 'Sie müssen ein Konto-Nummer eingeben.';
$_LANG['no phonenumber given'] = 'Sie müssen ein telefonnummer eingeben.';

// models/database_model.php
$_LANG['error in mysql query'] = 'Fehler in MySQL-Abfrage';

// models/debtor_model.php
$_LANG['invalid login credentials'] = 'Falscher Benutzer und/oder Passwort';
$_LANG['invalid debtor id'] = 'Fehler beim Laden Ihre Kundendaten';

// models/domain_model.php * some translations can be found in WHOIS-part of this file

// models/hosting_model.php
$_LANG['could not generate new accountname based on company name'] = 'Es kann kein neuer Kontoname angelegt werden für das bestellte Hosting-Konto';
$_LANG['could not generate new accountname based on debtor name'] = 'Es kann kein neuer Kontoname angelegt werden für das bestellte Hosting-Konto';
$_LANG['could not generate new accountname based on debtor'] = 'Es kann kein neuer Kontoname angelegt werden für das bestellte Hosting-Konto';
$_LANG['could not generate new accountname based on domain'] = 'Es kann kein neuer Kontoname angelegt werden für das bestellte Hosting-Konto';
$_LANG['could not generate new accountname'] = 'Es kann kein neuer Kontoname angelegt werden für das bestellte Hosting-Konto';

// models/order_model.php * some translations can be found in customer_model-part of this file
$_LANG['discountpercentage on product'] = '%s%% Produktrabatt';
$_LANG['ordercode already in use'] = 'Diese Bestellnummer existiert bereits.';
$_LANG['could not found debtor data'] = 'Cannot retrieve client information.';
$_LANG['no products in order'] = 'Keine Produkte im Warenkorb gefunden.';
$_LANG['could not generate ordercode'] = 'Could not generate new order id.';

// models/setting_model.php
$_LANG['gender male'] = 'Herr';
$_LANG['gender female'] = 'Frau';
$_LANG['gender department'] = 'Abt.';
$_LANG['gender unknown'] = 'Unbekannt';

/**
 * Order form - views
 */
// views/domain/elements/domain_table.phtml
$_LANG['domaintable domain'] = 'Domain';
$_LANG['domaintable result'] = 'Ergebnis';
$_LANG['domaintable price'] = 'Preis';
$_LANG['domaintable period'] = '&nbsp;';
$_LANG['authkey'] = 'Authorisierungscode';
$_LANG['domain status available'] = 'registieren';
$_LANG['domain status unavailable'] = 'transferieren';
$_LANG['domain status error'] = 'order';
$_LANG['add another domain'] = 'Weitere Domain hinzufügen';

// views/domain/details.phtml
$_LANG['domains'] = 'Domains';
$_LANG['hosting'] = 'Hosting';
$_LANG['order a hosting account'] = 'Hosting Paket bestellen';
$_LANG['i already have a hosting account'] = 'Ich habe bereits einen Hostingaccount bei %s';
$_LANG['order domains only'] = 'Ich m&ouml;chte nur eine Domain registrieren';
$_LANG['current domain'] = 'Aktuelle Domain';
$_LANG['use own nameservers'] = 'Eigene Nameserver nutzen';
$_LANG['nameserver 1'] = 'Nameserver 1';
$_LANG['nameserver 2'] = 'Nameserver 2';
$_LANG['nameserver 3'] = 'Nameserver 3';
$_LANG['button to customerdata'] = 'Eingabe der Kundendaten &raquo;';

// views/domain/start.phtml
$_LANG['choose your domain'] = 'Wählen Sie Ihre Domain';
$_LANG['to shopping cart'] = 'Zum Warenkorb';

// views/elements/billingperiod.phtml
$_LANG['billing period'] = 'Abrechnungszeitraum';

// views/elements/errors.phtml
$_LANG['error message'] = 'Error:';

// views/elements/options.phtml
$_LANG['options'] = 'Optionale Features';

// views/hosting/elements/hosting_new.phtml
$_LANG['no products in productgroup'] = 'Der Produktgruppe sind keine Produkte zugeordnet';
$_LANG['default domain'] = 'Hauptdomain';

// views/hosting/elements/hosting_new_simple.phtml
$_LANG['hosting package'] = 'Hostingpaket';
$_LANG['please choose'] = '- Bitte auswählen -';

// views/hosting/details.phtml
$_LANG['order new domains'] = 'Neue Domain bestellen oder Domaintransfer starten';
$_LANG['i already have a domain'] = 'Ich habe bereits eine Domain und m&ouml;chte nur ein Hostingpaket';
$_LANG['domain'] = 'Domain';

// views/hosting/start.phtml
$_LANG['choose your domain for hosting'] = 'Bitte wählen Sie eine Domain zu Ihrem Hostingpaket';

// views/completed.phtml
$_LANG['thanks for your order'] = 'Vielen Dank für Ihre Bestellung';
$_LANG['we have successfully received your order'] = 'Wir haben Ihre Bestellung erfolgreich entgegen genommen.';
$_LANG['for confirmation, we send an e-mail containing a summary of your order'] = 'Eine Bestellbestätigung wurde Ihnen per E-Mail zugeschickt.';
$_LANG['online payment'] = 'Online bezahlen';
$_LANG['you have chosen to pay online via'] = 'Sie m&ouml;chte mit %s online bezahlen';
$_LANG['click here to pay'] = 'Weiter zur Zahlung';
$_LANG['if you have any questions, please contact us'] = 'Bei weiteren Fragen stehen wir Ihnen jederzeit zur Verfügung.';

// views/customer.phtml
$_LANG['customer data'] = 'Kundendaten';
$_LANG['i am already a customer'] = 'Ich bin bereits %s Kunde';
$_LANG['companyname'] = 'Firma';
$_LANG['companynumber'] = 'IHK-Nummer';
$_LANG['taxnumber'] = 'USt-IdNr';
$_LANG['legalform'] = 'Rechtsform';
$_LANG['contact person'] = 'Ansprechpartner';
$_LANG['address'] = 'Stra&szlig;e';
$_LANG['zipcode and city'] = 'PLZ und Ort';
$_LANG['state'] = 'Bundesland:';
$_LANG['country'] = 'Land';
$_LANG['phonenumber'] = 'Telefon';
$_LANG['emailaddress'] = 'E-Mail Adresse';
$_LANG['debtorcode'] = 'Kundennummer';
$_LANG['logout'] = 'Abmelden';
$_LANG['your companyname'] = 'Ihr Firmenname';
$_LANG['your name'] = 'Ihr Name';
$_LANG['username'] = 'Benutername';
$_LANG['password'] = 'Passwort';
$_LANG['login'] = 'Anmelden';
$_LANG['use custom invoice address'] = 'Andere Rechnungsadresse';
$_LANG['use custom data for domain owner'] = 'Andere Domaininhaberdaten verwenden';
$_LANG['custom invoice address'] = 'Rechnungsadresse';
$_LANG['domain owner'] = 'Domaininhaber';
$_LANG['use domain contact'] = 'Kontakt';
$_LANG['create a new domain contact'] = '- Erstellen Sie einen neuen Kontakt -';
$_LANG['choose your payment method'] = 'Zahlungsmethode';
$_LANG['your accountnumber'] = 'Kontonummer';
$_LANG['iban'] = 'Kontonummer';
$_LANG['bic'] = 'Bankleitzahl';
$_LANG['account name'] = 'Kontoinhaber';
$_LANG['account city'] = 'Sitz der Bank';
$_LANG['i authorize for the total amount'] = 'Ich erteile %s die Genehmigung den Gesamtbetrag meinem Bankkonto zu belasten. Weiterhin erteile ich die Genehmigung auch zukünftige Rechnungen meinem Bankonto zu belasten.';
$_LANG['comment'] = 'Kommentar';
$_LANG['button back to cart'] = '&laquo; Warenkorb';
$_LANG['button to overview'] = 'Bestellübersicht &raquo;';

// views/details.phtml
$_LANG['choose your product'] = 'Bitte wählen Sie Ihr Produkt';
$_LANG['product'] = 'Produkt';

// views/header.phtml
$_LANG['order page title'] = 'Bestellung';

// views/onlinepayment.phtml
$_LANG['your payment is processed'] = 'Wir haben Ihre Zahlung erhalten.';
$_LANG['transaction id'] = 'Transaktionscode';
$_LANG['we will process your order as soon as possible'] = 'Wir werden Ihre Bestellung so schnell wie m&ouml;glich bearbeiten. Vielen Dank für Ihr Vertrauen.';

// views/overview.phtml
$_LANG['summary of your order'] = 'Bestellübersicht';
$_LANG['overviewtable number'] = 'Bestellnummer'; 
$_LANG['overviewtable description'] = 'Beschreibung';
$_LANG['overviewtable period'] = 'Zeit';
$_LANG['overviewtable amount excl'] = 'Betrag exkl. MwSt';
$_LANG['overviewtable amount incl'] = 'Betrag inkl. MwSt';
$_LANG['overviewtable amount'] = 'Gesamtsumme';
$_LANG['enter discount coupon'] = '+ Gutscheincode eingeben';
$_LANG['discount coupon'] = 'Gutscheincode';
$_LANG['discount check coupon'] = 'übernehmen';
$_LANG['percentage discount'] = '%s%% Rabatt';
$_LANG['subtotal'] = 'Zwischensumme';
$_LANG['vat'] = 'Mehrwertsteuer';
$_LANG['total incl'] = 'Gesamtsumme';
$_LANG['total'] = 'Gesamtsumme';
$_LANG['your customerdata'] = 'Kundendaten';
$_LANG['your invoiceaddress'] = 'Rechnungsadresse';
$_LANG['payment method'] = 'Zahlungsmethode';
$_LANG['terms and conditions'] = 'AGB';
$_LANG['i agree with the terms and conditions'] = 'Ich akzeptiere die %s und bestätige, sie gelesen zu haben';
$_LANG['download terms and conditions'] = 'Download der AGB';
$_LANG['button back to customerdata'] = '&laquo; Kundendaten';
$_LANG['button to completed'] = 'Zahlungspflichtig bestellen &raquo;';
$_LANG['footer prices are including tax'] = 'Alle Preise sind inkl. MwSt.';
$_LANG['footer prices are excluding tax'] = 'Alle Preise sind exkl. MwSt.';

/**
 * Arrays
 */
$_LANG['per'] = 'pro';
$_LANG['array_periods'][''] = 'einmalig';
$_LANG['array_periods']['d'] = 'Tag';
$_LANG['array_periods']['w'] = 'Woche';
$_LANG['array_periods']['m'] = 'Monat';
$_LANG['array_periods']['k'] = 'Quartal';
$_LANG['array_periods']['h'] = 'halbes Jahr';
$_LANG['array_periods']['j'] = 'Jahr';
$_LANG['array_periods']['t'] = 'zwei Jahre';

$_LANG['array_periods_plural']['d'] = 'Tage';
$_LANG['array_periods_plural']['w'] = 'Wochen';
$_LANG['array_periods_plural']['m'] = 'Monate';
$_LANG['array_periods_plural']['k'] = 'Quartale';
$_LANG['array_periods_plural']['h'] = 'halbe Jahre';	
$_LANG['array_periods_plural']['j'] = 'Jahre';
$_LANG['array_periods_plural']['t'] = 'zwei Jahre';	

?>