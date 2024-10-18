<?php


$_LANG = array();
$_LANG['language title'] = 'Nederlands';

/**
 * WHOIS form
 */
// views/whois/domain_form.phtml
$_LANG['whois_btn'] = 'Controleer';

// views/whois/header.phtml
$_LANG['whois page title'] = 'Domeinnaam controleren';
$_LANG['check domain'] = 'Controleren';

// views/whois/result_table.phtml
$_LANG['to orderform'] = 'Naar bestelformulier';
$_LANG['whois resulttable domain'] = 'Domeinnaam';
$_LANG['whois resulttable result'] = 'Resultaat';
$_LANG['whois resulttable price'] = 'Prijs';
$_LANG['whois resulttable period'] = '&nbsp;';
$_LANG['no products in domain productgroup'] = 'Geen producten in productgroep voor domeinnamen';
$_LANG['show more tlds'] = 'Toon meer extensies';

// controllers/whois_controller.php
$_LANG['whois status available'] = 'vrij';
$_LANG['whois status unavailable'] = 'bezet';
$_LANG['whois status error'] = 'onbekend';
$_LANG['whois status invalid'] = 'ongeldig';

$_LANG['whois link available'] = 'bestellen';
$_LANG['whois link unavailable'] = 'bestellen';
$_LANG['whois link error'] = 'bestellen';

$_LANG['in shopping cart'] = 'in winkelwagen';

// models/whois_model.php   * some translations are also used in models/domain_model.php
$_LANG['domain name required for a existing account'] = 'Er is geen huidige domeinnaam opgegeven.';
$_LANG['could not connect to whois server'] = 'Kan niet verbinden met de whois-server: %s';
$_LANG['unknown whois server'] = 'Kan geen whois-server vinden voor de extensie .%s';
$_LANG['no domain entered'] = 'Er is geen domeinnaam opgegeven.';
$_LANG['sld must be between 2 and 63 characters'] = 'Domeinnaam moet minimaal 2 karakters en maximaal 63 karakters bevatten.';
$_LANG['sld should not contain dots'] = 'Subdomeinnamen kunt u niet controleren.';
$_LANG['sld contains invalid characters'] = 'Domeinnaam bevat ongeldige tekens.';
$_LANG['tld not available'] = 'De gekozen extensie is helaas niet beschikbaar. U kunt een keuze maken uit onderstaande alternatieven.';

/**
 * Order form - controllers
 */
// controllers/domainform_controller.php
$_LANG['authkey for domain is required'] = 'Autorisatiecode voor domeinnaam %s is niet ingevuld.';
$_LANG['you need one of the hosting packages to select'] = 'U dient een van de hostingpakketten te selecteren.';
$_LANG['link to hosting account'] = 'Koppelen aan hostingpakket van %s.';
$_LANG['you need at least two nameservers'] = 'U dient ten minste twee nameservers op te geven.';

// controllers/hostingform_controller.php
$_LANG['current domain description'] = 'Huidige domeinnaam: %s';

// controllers/orderform_controller.php
$_LANG['you need to select a product'] = 'U dient een product te selecteren.';
$_LANG['you must agree to the terms and conditions'] = 'U dient akkoord te gaan met de algemene voorwaarden';
$_LANG['please select a payment method'] = 'U dient een van de betaalmethoden te selecteren.';
$_LANG['please select a bank'] = 'U dient een bank te selecteren';
$_LANG['you must agree to the authorization'] = 'U dient akkoord te gaan met de machtiging';
$_LANG['confirmation of your order from'] = 'Uw bestelling bij %s';

/**
 * Order form - models
 */
// models/customer_model.php
$_LANG['invalid username'] = 'Ongeldige gebruikersnaam';
$_LANG['the username already exists'] = 'De gebruikersnaam is reeds in gebruik';
$_LANG['invalid password'] = 'Ongeldig wachtwoord';
$_LANG['invalid companyname'] = 'Er is geen correcte bedrijfsnaam opgegeven';
$_LANG['invalid companynumber'] = 'Het KVK-nummer is niet correct';
$_LANG['invalid taxnumber'] = 'Het BTW-nummer is niet juist';
$_LANG['invalid gender'] = 'Er is geen geldig geslacht opgegeven';
$_LANG['invalid initials'] = 'Er zijn geen correcte voorletters ingevuld';
$_LANG['invalid surname'] = 'Er is geen correcte achternaam ingevuld';
$_LANG['invalid address'] = 'Er is geen adres ingevuld';
$_LANG['invalid zipcode'] = 'Er is een ongeldige postcode opgegeven';
$_LANG['invalid city'] = 'Er is een foutieve plaats ingevuld';
$_LANG['invalid state'] = 'Er is een foutieve staat ingevuld';
$_LANG['invalid country'] = 'Het selecteren van het land is niet juist verlopen';
$_LANG['invalid emailaddress'] = 'Er is geen geldig e-mailadres opgegeven';
$_LANG['invalid phonenumber'] = 'Het telefoonnummer bevat ongeldige tekens of is te lang';
$_LANG['invalid mobile number'] = 'Het mobiele-telefoon-nummer bevat ongeldige tekens of is te lang';
$_LANG['invalid faxnumber'] = 'Het faxnummer bevat ongeldige tekens of is te lang';
$_LANG['invalid invoicemethod'] = 'Er is een ongeldige verzendmethode geselecteerd';
$_LANG['invalid authorization value'] = 'Er is geen geldige waarde voor machtiging meegegeven';
$_LANG['invalid custom invoice template'] = 'Er is geen geldige waarde voor de afwijkende factuur template meegegeven';
$_LANG['invalid custom pricequote template'] = 'Er is geen geldige waarde voor de afwijkende offerte template meegegeven';
$_LANG['invalid invoice sex'] = 'Er is geen correct geslacht ingevuld voor het factuuradres';
$_LANG['invalid invoice initials'] = 'Er zijn geen correcte voorletters ingevuld voor het factuuradres';
$_LANG['invalid invoice surname'] = 'Er is geen correcte achternaam ingevuld voor het factuuradres';
$_LANG['invalid invoice address'] = 'Er is geen adres ingevuld voor het factuuradres';
$_LANG['invalid invoice zipcode'] = 'Er is een ongeldige postcode opgegeven voor het factuuradres';
$_LANG['invalid invoice city'] = 'Er is een foutieve plaats ingevuld voor het factuuradres';
$_LANG['invalid invoice state'] = 'Er is een foutieve staat ingevuld voor het factuuradres';
$_LANG['invalid invoice country'] = 'Het selecteren van het facturatie-land is niet juist verlopen';
$_LANG['invalid invoice emailaddress'] = 'Er is geen geldig facturatie e-mailadres opgegeven';
$_LANG['invalid accountnumber'] = 'Er is een ongeldig rekeningnummer opgegeven';
$_LANG['invalid bic'] = 'Er is een ongeldig BIC opgegeven';
$_LANG['invalid accountname'] = 'De tenaamstelling van het rekeningnummer is ongeldig';
$_LANG['invalid bank'] = 'Er is een ongeldige banknaam opgegeven';
$_LANG['invalid account city'] = 'Er is een ongeldige plaats opgegeven bij het rekeningnummer';
$_LANG['no companyname and no surname'] = 'U dient een bedrijfsnaam of achternaam op te geven';
$_LANG['custom client fields regex'] = 'Ongeldige waarde voor het veld %s';
$_LANG['no accountnumber given'] = 'U dient uw rekeningnummer in te vullen';
$_LANG['no phonenumber given'] = 'U dient uw telefoonnummer in te vullen';

// models/database_model.php
$_LANG['error in mysql query'] = 'Fout in MySQL query';

// models/debtor_model.php
$_LANG['invalid login credentials'] = 'Ongeldige logingegevens';
$_LANG['invalid debtor id'] = 'Fout tijdens ophalen van debiteurgegevens';

// models/domain_model.php * some translations can be found in WHOIS-part of this file

// models/hosting_model.php
$_LANG['could not generate new accountname based on company name'] = 'Kan geen nieuwe accountnaam genereren voor het bestelde hostingaccount';
$_LANG['could not generate new accountname based on debtor name'] = 'Kan geen nieuwe accountnaam genereren voor het bestelde hostingaccount';
$_LANG['could not generate new accountname based on debtor'] = 'Kan geen nieuwe accountnaam genereren voor het bestelde hostingaccount';
$_LANG['could not generate new accountname based on domain'] = 'Kan geen nieuwe accountnaam genereren voor het bestelde hostingaccount';
$_LANG['could not generate new accountname'] = 'Kan geen nieuwe accountnaam genereren voor het bestelde hostingaccount';

// models/order_model.php * some translations can be found in customer_model-part of this file
$_LANG['discountpercentage on product'] = '%s%% korting op product';
$_LANG['ordercode already in use'] = 'Bestellingskenmerk is reeds in gebruik.';
$_LANG['could not found debtor data'] = 'Kan de klantgegevens niet gevonden krijgen.';
$_LANG['no products in order'] = 'Geen producten gevonden voor bestelling.';
$_LANG['could not generate ordercode'] = 'Er kon geen nieuw bestellingskenmerk gegenereerd worden.';

// models/setting_model.php
$_LANG['gender male'] = 'Dhr.';
$_LANG['gender female'] = 'Mevr.';
$_LANG['gender department'] = 'Afd.';
$_LANG['gender unknown'] = 'Onbekend';

/**
 * Order form - views
 */
// views/domain/elements/domain_table.phtml
$_LANG['domaintable domain'] = 'Domeinnaam';
$_LANG['domaintable result'] = 'Resultaat';
$_LANG['domaintable price'] = 'Prijs';
$_LANG['domaintable period'] = '&nbsp;';
$_LANG['authkey'] = 'autorisatiecode';
$_LANG['domain status available'] = 'registreren';
$_LANG['domain status unavailable'] = 'verhuizen';
$_LANG['domain status error'] = 'aanvragen';
$_LANG['add another domain'] = 'Extra domeinnaam toevoegen';

// views/domain/details.phtml
$_LANG['domains'] = 'Domeinnamen';
$_LANG['hosting'] = 'Hosting';
$_LANG['order a hosting account'] = 'Hostingpakket bestellen';
$_LANG['i already have a hosting account'] = 'Ik heb al een hostingpakket bij %s';
$_LANG['order domains only'] = 'Alleen domeinnamen bestellen';
$_LANG['current domain'] = 'Huidige domeinnaam';
$_LANG['use own nameservers'] = 'Eigen nameservers opgeven';
$_LANG['nameserver 1'] = 'Nameserver 1';
$_LANG['nameserver 2'] = 'Nameserver 2';
$_LANG['nameserver 3'] = 'Nameserver 3';
$_LANG['button to customerdata'] = 'Klantgegevens &raquo;';

// views/domain/start.phtml
$_LANG['choose your domain'] = 'Kies uw domeinnaam';
$_LANG['to shopping cart'] = 'Naar winkelwagen';

// views/elements/billingperiod.phtml
$_LANG['billing period'] = 'Facturatieperiode';

// views/elements/errors.phtml
$_LANG['error message'] = 'Foutmelding:';

// views/elements/options.phtml
$_LANG['options'] = 'Opties';

// views/hosting/elements/hosting_new.phtml
$_LANG['no products in productgroup'] = 'Geen producten in productgroep';
$_LANG['default domain'] = 'Hoofd domeinnaam';

// views/hosting/elements/hosting_new_simple.phtml
$_LANG['hosting package'] = 'Hostingpakket';
$_LANG['please choose'] = '- Maak een keuze -';

// views/hosting/details.phtml
$_LANG['order new domains'] = 'Nieuwe domeinnaam bestellen of domeinnaam verhuizen';
$_LANG['i already have a domain'] = 'Ik heb al een domeinnaam, maar wens deze niet te verhuizen';
$_LANG['domain'] = 'Domeinnaam';

// views/hosting/start.phtml
$_LANG['choose your domain for hosting'] = 'Kies een domeinnaam bij uw hosting';

// views/completed.phtml
$_LANG['thanks for your order'] = 'Bedankt voor uw bestelling';
$_LANG['we have successfully received your order'] = 'We hebben uw bestelling succesvol ontvangen.';
$_LANG['for confirmation, we send an e-mail containing a summary of your order'] = 'Ter bevestiging hebben we u een e-mail verstuurd met daarin een overzicht van uw bestelling.';
$_LANG['online payment'] = 'Online betalen';
$_LANG['you have chosen to pay online via'] = 'U heeft gekozen om online te betalen via %s.';
$_LANG['click here to pay'] = 'Klik hier om te betalen';
$_LANG['if you have any questions, please contact us'] = 'Mocht u nog vragen en/of opmerkingen hebben, dan kunt u contact met ons opnemen.';

// views/customer.phtml
$_LANG['customer data'] = 'Klantgegevens';
$_LANG['i am already a customer'] = 'Ik ben al klant bij %s';
$_LANG['companyname'] = 'Bedrijfsnaam';
$_LANG['companynumber'] = 'KVK nummer';
$_LANG['taxnumber'] = 'BTW nummer';
$_LANG['legalform'] = 'Rechtsvorm';
$_LANG['contact person'] = 'Contactpersoon';
$_LANG['address'] = 'Adres';
$_LANG['zipcode and city'] = 'Postcode en plaats';
$_LANG['state'] = 'Staat';
$_LANG['country'] = 'Land';
$_LANG['phonenumber'] = 'Telefoonnummer';
$_LANG['emailaddress'] = 'E-mailadres';
$_LANG['debtorcode'] = 'Klantnummer';
$_LANG['logout'] = 'uitloggen';
$_LANG['your companyname'] = 'Uw bedrijfsnaam';
$_LANG['your name'] = 'Uw naam';
$_LANG['username'] = 'Gebruikersnaam';
$_LANG['password'] = 'Wachtwoord';
$_LANG['login'] = 'inloggen';
$_LANG['use custom invoice address'] = 'Afwijkende facturatiegegevens';
$_LANG['use custom data for domain owner'] = 'Gebruik afwijkende contactgegevens als domeinnaamhouder';
$_LANG['custom invoice address'] = 'Facturatiegegevens';
$_LANG['domain owner'] = 'Domeinnaam houder';
$_LANG['use domain contact'] = 'Gebruik contact';
$_LANG['create a new domain contact'] = '- Maak een nieuw contact aan -';
$_LANG['choose your payment method'] = 'Kies uw betaalmethode';
$_LANG['your accountnumber'] = 'IBAN';
$_LANG['bic'] = 'BIC';
$_LANG['account name'] = 'Ten name van';
$_LANG['account city'] = 'Plaats';
$_LANG['i authorize for the total amount'] = 'Ik machtig %s om het totaalbedrag van deze bestelling af te schrijven van het door mij opgegeven rekeningnummer.';
$_LANG['comment'] = 'Opmerking';
$_LANG['button back to cart'] = '&laquo; Winkelwagen';
$_LANG['button to overview'] = 'Bestel overzicht &raquo;';

// views/details.phtml
$_LANG['choose your product'] = 'Kies uw product';
$_LANG['product'] = 'Product';

// views/header.phtml
$_LANG['order page title'] = 'Bestelformulier';

// views/onlinepayment.phtml
$_LANG['your payment is processed'] = 'Uw betaling is verwerkt';
$_LANG['transaction id'] = 'Transactiecode';
$_LANG['we will process your order as soon as possible'] = 'Na controle van uw betaling zullen we uw zojuist geplaatste bestelling zo spoedig mogelijk behandelen.<br /><br />Nogmaals bedankt voor uw bestelling,';

// views/overview.phtml
$_LANG['summary of your order'] = 'Overzicht van uw bestelling';
$_LANG['overviewtable number'] = 'Aantal'; 
$_LANG['overviewtable description'] = 'Omschrijving';
$_LANG['overviewtable period'] = 'Periode';
$_LANG['overviewtable amount excl'] = 'Bedrag excl. BTW';
$_LANG['overviewtable amount incl'] = 'Bedrag incl. BTW';
$_LANG['overviewtable amount'] = 'Bedrag';
$_LANG['enter discount coupon'] = '+ kortingscode invoeren';
$_LANG['discount coupon'] = 'Kortingscode';
$_LANG['discount check coupon'] = 'toepassen';
$_LANG['percentage discount'] = '%s%% korting';
$_LANG['subtotal'] = 'Subtotaal';
$_LANG['vat'] = 'BTW';
$_LANG['total incl'] = 'Totaal incl. BTW';
$_LANG['total'] = 'Totaal bestelling';
$_LANG['your customerdata'] = 'Uw klantgegevens';
$_LANG['your invoiceaddress'] = 'Factuuradres';
$_LANG['payment method'] = 'Betaalmethode';
$_LANG['terms and conditions'] = 'Algemene voorwaarden';
$_LANG['i agree with the terms and conditions'] = 'Ik ga akkoord met de %s en ben bekend met de inhoud daarvan.';
$_LANG['download terms and conditions'] = 'Download onze algemene voorwaarden';
$_LANG['button back to customerdata'] = '&laquo; Klantgegevens';
$_LANG['button to completed'] = 'Bestelling plaatsen &raquo;';
$_LANG['footer prices are including tax'] = 'Alle genoemde prijzen zijn incl. BTW';
$_LANG['footer prices are excluding tax'] = 'Alle genoemde prijzen zijn excl. BTW';

/**
 * Arrays
 */
$_LANG['per'] = 'per';
$_LANG['array_periods'][''] = 'eenmalig';
$_LANG['array_periods']['d'] = 'dag';
$_LANG['array_periods']['w'] = 'week';
$_LANG['array_periods']['m'] = 'maand';
$_LANG['array_periods']['k'] = 'kwartaal';
$_LANG['array_periods']['h'] = 'half jaar';
$_LANG['array_periods']['j'] = 'jaar';
$_LANG['array_periods']['t'] = 'twee jaar';

$_LANG['array_periods_plural']['d'] = 'dagen';
$_LANG['array_periods_plural']['w'] = 'weken';
$_LANG['array_periods_plural']['m'] = 'maanden';
$_LANG['array_periods_plural']['k'] = 'kwartalen';
$_LANG['array_periods_plural']['h'] = 'halve jaren';	
$_LANG['array_periods_plural']['j'] = 'jaar';
$_LANG['array_periods_plural']['t'] = 'twee jaar';	