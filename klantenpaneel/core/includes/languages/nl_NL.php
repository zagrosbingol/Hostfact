<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

/** URLS */

// controllers
$LANG['url']['index']      = 'index';
$LANG['url']['debtor']     = 'klantgegevens';
$LANG['url']['invoice']    = 'facturen';
$LANG['url']['pricequote'] = 'offertes';
$LANG['url']['order']      = 'bestellingen';
$LANG['url']['login']      = 'inloggen';
$LANG['url']['service']    = 'diensten';
$LANG['url']['serviceAll'] = 'alle-diensten';

// action
$LANG['url']['view']               = 'bekijken';
$LANG['url']['download']           = 'download';
$LANG['url']['payOnline']          = 'online-betalen';
$LANG['url']['billingData']        = 'facturatiegegevens';
$LANG['url']['loginData']          = 'wachtwoord-wijzigen';
$LANG['url']['twoFactorAuth']      = '2-staps-authenticatie';
$LANG['url']['twoFactorGenerateKey'] 	= 'two-factor-generate-key';
$LANG['url']['twoFactorVerifyKey'] 		= 'two-factor-verify-key';
$LANG['url']['twoFactorDeactivate'] 	= 'two-factor-deactiveren';
$LANG['url']['paymentData']        = 'betaalgegevens';
$LANG['url']['resetPassword']      = 'reset-wachtwoord';
$LANG['url']['forgotPassword']     = 'wachtwoord-vergeten';
$LANG['url']['logout']             = 'uitloggen';
$LANG['url']['terminate']          = 'opzeggen';
$LANG['url']['accept']             = 'accepteren';
$LANG['url']['decline']            = 'weigeren';
$LANG['url']['cancelModification'] = 'wijziging-annuleren';
$LANG['url']['validatekey']        = 'sleutel-valideren';
$LANG['url']['getStates']          = 'staten';
$LANG['url']['orderNew']           = 'bestellen';
$LANG['url']['changeLanguage']     = 'taal-aanpassen';
$LANG['url']['downloadAttachment'] = 'bijlage-downloaden';


/** GLOBAL MESSAGES (ERROR/WARNING/SUCCESS) */
$LANG['api connect error']                     		= "Fout tijdens verbinden met de server: HTTP code %s";
$LANG['api response no json']                     	= "Onjuist antwoord van de server. Controleer de opgegeven URLs in de backoffice.";
$LANG['api action failed, reason unknown']          = 'De actie kon om een onbekende reden niet worden uitgevoerd.';
$LANG['invalid emailaddress']                       = 'Het opgegeven e-mailadres is ongeldig';
$LANG['no surname given']                           = "Er is geen achternaam opgegeven";
$LANG['the requested action could not be executed'] = "De opgevraagde actie kon niet uitgevoerd worden";
$LANG['changes successfully processed']             = "De wijzigingen zijn succesvol verwerkt";
$LANG['missing plugin file']                        = "Er mist een plugin bestand";
$LANG['missing plugin file description']            = "Het volgende bestand is niet aanwezig: %s";
$LANG['downoad attachment failed']                  = "Het opgevraagde bestand kon niet worden gevonden";
$LANG['max_input_vars reached']                     = "Het aantal formuliervelden heeft de limiet van onze server bereikt. Neem contact met ons op.";


/** ACCOUNT/DEBTOR */
$LANG['general data']                                       = 'Algemene gegevens';
$LANG['billing data']                                       = 'Facturatiegegevens';
$LANG['change billing data']                                = 'Facturatiegegevens wijzigen';
$LANG['change password']                                    = 'Wachtwoord wijzigen';
$LANG['two step authentication']                            = '2-staps authenticatie instellen';
$LANG['payment data']                                       = 'Betaalgegevens';
$LANG['change payment data']                                = 'Gegevens wijzigen';
$LANG['name']                                               = 'Naam';
$LANG['sex']                                                = 'Geslacht';
$LANG['gender male']                                        = 'Dhr.';
$LANG['gender female']                                      = 'Mevr.';
$LANG['gender department']                                  = 'Afd.';
$LANG['initials']                                           = 'Voorletters';
$LANG['surname']                                            = 'Achternaam';
$LANG['address']                                            = 'Adres';
$LANG['zipcode']                                            = 'Postcode';
$LANG['city']                                               = 'Plaats';
$LANG['state']                                              = 'Staat';
$LANG['country']                                            = 'Land';
$LANG['emailaddress']                                       = 'E-mailadres';
$LANG['companyname']                                        = 'Bedrijfsnaam';
$LANG['companynumber']                                      = 'KvK-nummer';
$LANG['legalform']                                          = 'Rechtsvorm';
$LANG['taxnumber']                                          = 'BTW-nummer';
$LANG['phonenumber']                                        = 'Telefoonnummer';
$LANG['faxnumber']                                          = 'Faxnummer';
$LANG['mobilenumber']                                       = 'Mobiel nummer';
$LANG['website']                                            = 'Website';
$LANG['second emailaddress']                                = 'Extra e-mailadres';
$LANG['authorisation']                                      = 'Machtiging';
$LANG['accountnumber']                                      = 'IBAN';
$LANG['accountname']                                        = 'Ten name van';
$LANG['accountbank']                                        = 'Bank';
$LANG['accountcity']                                        = 'Plaats';
$LANG['accountbic']                                         = 'BIC';
$LANG['direct debit']                                       = 'Automatische incasso';
$LANG['debtor mailings']                                    = 'Mailings ontvangen?';
$LANG['personal data']                                      = 'Contactpersoon';
$LANG['company data']                                       = 'Bedrijfsgegevens';
$LANG['address data']                                       = 'Adresgegevens';
$LANG['contact data']                                       = 'Contactgegevens';
$LANG['debtor preferences']                                 = 'Voorkeuren';
$LANG['bank details']                                       = 'Bankgegevens';
$LANG['username']                                           = 'Gebruikersnaam';
$LANG['password']                                           = 'Wachtwoord';
$LANG['current password']                                   = 'Huidige wachtwoord';
$LANG['new password']                                       = 'Nieuw wachtwoord';
$LANG['repeat password']                                    = 'Herhaal wachtwoord';
$LANG['repeat new password']                                = 'Herhaal nieuw wachtwoord';
$LANG['accountnumber']                                      = 'Rekeningnummer (IBAN)';
$LANG['accountname']                                        = 'Rekeninghouder';
$LANG['accountbank']                                        = 'Bank';
$LANG['accountcity']                                        = 'Vestigingsplaats bank';
$LANG['AccountBIC']                                         = 'BIC';
$LANG['pay with direct debit']                              = 'Ik wil betalen via automatisch incasso';
$LANG['save data']                                          = 'Bewaar gegevens';
$LANG['debtor general data saved']                          = 'Uw gegevens zijn opgeslagen';
$LANG['debtor login data saved']                            = 'Uw wachtwoord is gewijzigd. De volgende keer dat u inlogt kunt u uw nieuwe wachtwoord gebruiken.';
$LANG['no companyname and no surname are given']            = "Er is geen bedrijfsnaam of achternaam opgegeven";
$LANG['no password given']                                  = "Er is geen wachtwoord opgegeven";
$LANG['repeat password not correct']                        = "De wachtwoorden komen niet overeen";
$LANG['invalid bic']                                        = "De opgegeven BIC is ongeldig";
$LANG['invalid iban']                                       = "Het opgegeven IBAN rekeningnummer is ongeldig";
$LANG['password should at least be 8 char']                 = "Het wachtwoord dient minstens 8 karakters lang te zijn";
$LANG['invalid current password']                           = "Het huidige wachtwoord is incorrect";
$LANG['invalid state']                                      = "Ongeldige staat";
$LANG['warning editgeneral modification awaiting approval'] = 'De algemene gegevens zijn gewijzigd op %s en zijn in behandeling.';
$LANG['warning editbilling modification awaiting approval'] = 'De facturatiegegevens zijn gewijzigd op %s en zijn in behandeling.';
$LANG['warning editpayment modification awaiting approval'] = 'De betaalgegevens zijn gewijzigd op %s en zijn in behandeling.';
$LANG['two step authentication - text'] 					= 'Authenticatie in twee stappen houdt in dat u naast een gebruikersnaam en wachtwoord, ook een authenticatie code in moet vullen om in te loggen in het klantenpaneel. Deze authenticatie code wordt gemaakt door een authenticator applicatie welke u op uw computer of smartphone kunt gebruiken. Kies binnen de applicatie voor een "op tijd gebaseerde" code. Deze 2-staps authenticatie zorgt voor extra bescherming tegen indringers.';
$LANG['two step authentication - apps'] 					= 'Voorbeeld apps die u kunt gebruiken';
$LANG['two step authentication - generate code'] 			= 'Code genereren';
$LANG['two step authentication - generate code text'] 		= 'Door een code te genereren kunt u een account aanmaken in uw authenticator app. Indien uw applicatie verschillende types ondersteunt kies dan voor "op tijd gebaseerd". Vervolgens kunt u met de gegenereerde code uit de app inloggen in dit klantenpaneel.';
$LANG['two step authentication - generate code link']		= 'Klik hier om uw code te genereren';
$LANG['two step authentication - activate text']			= '<strong>Let op:</strong> Om de 2-staps authenticatie in te stellen dient u de code hieronder te activeren.';
$LANG['two step authentication - auth code']				= 'Authenticatie code';
$LANG['two step authentication - submit']					= 'Activeren';
$LANG['two step authentication - already active']			= '2-staps authenticatie is momenteel actief.';
$LANG['two step authentication - deactivate link']			= 'Klik hier om de 2-staps authenticatie te deactiveren.';
$LANG['two step authentication - success']					= 'De 2-staps authenticatie is successvol ingeschakeld.';
$LANG['two step authentication - error']					= 'Het activeren van de 2-staps authenticatie is mislukt, er is een onjuiste authenticatie code opgegeven.';
$LANG['two step authentication activate - error']			= 'Het activeren van de 2-staps authenticatie is mislukt.';
$LANG['two step authentication deactivate - success']		= 'De 2-staps authenticatie is successvol uitgeschakeld.';
$LANG['two step authentication deactivate - error']			= 'Het uitschakelen van de 2-staps authenticatie is mislukt.';
$LANG['two step authentication deactivate - title']			= '2-staps authenticatie deactiveren';
$LANG['two step authentication deactivate - text']			= 'Bevestig het uitschakelen van de 2-staps authenticatie met uw wachtwoord.';
$LANG['confirm']											= 'Bevestigen';

/** INVOICES */
$LANG['invoice']                                         = 'Factuur';
$LANG['invoices']                                        = 'Facturen';
$LANG['invoice nr']                                      = 'Factuurnr';
$LANG['due date']                                        = 'Te betalen voor';
$LANG['to pay']                                          = 'Te betalen';
$LANG['invoice date']                                    = 'Factuurdatum';
$LANG['reference number']                                = 'Referentie';
$LANG['invoice lines']                                   = 'Factuurregels';
$LANG['outstanding invoices']                            = 'Openstaande facturen';
$LANG['invoice open amount']                             = 'Openstaand bedrag';
$LANG['pay invoice']                                     = 'Online betalen';
$LANG['download invoice']                                = 'Download factuur';
$LANG['no invoices found']                               = 'Er zijn geen facturen gevonden.';
$LANG['invoice does not exist']                          = 'De factuur bestaat niet (meer).';
$LANG['copy general data']                               = 'Kopie&euml;r van algemene gegevens';
$LANG['billing data uses general data']                  = 'Voor de facturatie gegevens worden de algemene gegevens gebruikt.';
$LANG['use different billing data']                      = 'Wilt u afwijkende facturatie gegevens opgeven?';
$LANG['you have outstanding invoices']                   = 'U heeft %d openstaande facturen, klik op een factuur om de factuur te bekijken.';
$LANG['you have no outstanding invoices']                = 'U heeft geen openstaande facturen.';
$LANG['payment data - bank transfer or online payments'] = 'Via bankoverschrijving of een online betaalmethode.';
$LANG['payment data - activate authorisation']           = 'Indien u liever via automatische incasso betaalt, kunt u dat hier wijzigen.';
$LANG['payment data - direct debit']                     = 'U heeft een machtiging afgegeven voor automatische incasso. We schrijven de verschuldigde bedragen af van rekening <strong>%s</strong> ten name van <strong>%s</strong>.';
$LANG['direct debit invoice abbr']                       = '- inc';
$LANG['direct debit invoice']                            = '- betaling via incasso';
$LANG['direct debit paid invoice']                       = '- betaald via incasso';
$LANG['already paid']                                    = 'Reeds betaald';
$LANG['open amount to be paid']                          = 'Nog te voldoen';


/** OTHER SERVICES */
$LANG['other services']                      = 'Overige diensten';
$LANG['other service']                       = 'Overige dienst';
$LANG['order new service']                   = 'Nieuwe dienst';
$LANG['you have other services']             = 'U heeft %d overige diensten, klik op een dienst om de dienst te bekijken.';
$LANG['you have no other services']          = 'U heeft geen overige diensten.';
$LANG['no other services found']             = 'Er zijn geen overige diensten gevonden.';
$LANG['other service does not exist']        = 'De overige dienst bestaat niet (meer).';
$LANG['error during termination of service'] = 'Onbekende fout tijdens verwerken van de opzegging.';
$LANG['next invoice date']                   = 'Volgende factuur';
$LANG['terminated']                          = 'opgezegd';


/** PRICEQUOTES */
$LANG['open pricequotes']                         = 'Offertes wachtend op goedkeuring';
$LANG['pricequote']                               = 'Offerte';
$LANG['pricequotes']                              = 'Offertes';
$LANG['pricequote code']                          = 'Offertenummer';
$LANG['pricequote nr']                            = 'Offertenr';
$LANG['pricequote date']                          = 'Offertedatum';
$LANG['expiration date']                          = 'Geldig tot';
$LANG['download pricequote']                      = 'Download offerte';
$LANG['accept pricequote']                        = 'Accepteren';
$LANG['decline pricequote']                       = 'Weigeren';
$LANG['pricequote lines']                         = 'Offerte regels';
$LANG['modal title decline pricequote']           = 'Offerte weigeren';
$LANG['pricequote accepted successfull']          = 'Bedankt voor het accepteren van offerte %s.';
$LANG['pricequote declined successfull']          = 'Bedankt voor het doorgeven van uw keuze.';
$LANG['please agree to the terms and conditions'] = 'U heeft nog geen akkoord gegeven op de algemene voorwaarden';
$LANG['draw in this field'] 					  = 'Teken in dit veld';
$LANG['again'] 					  				  = 'Opnieuw';
$LANG['signed by'] 					  			  = 'Door';
$LANG['ipaddress'] 					  		  	  = 'IP-adres';
$LANG['comment'] 					  		 	  = 'Opmerking';
$LANG['signature'] 					  		 	  = 'Handtekening';
$LANG['download signed pricequote'] 			  = 'Download de ondertekende offerte';
$LANG['not mandatory'] 			 				  = 'Niet verplicht';
$LANG['accept'] 			 				      = 'Accepteren';
$LANG['see pricequote pdf'] 			 		  = 'Offerte PDF bekijken';
$LANG['accept pricequote online'] 			 	  = 'Offerte online accepteren';
$LANG['pricequote is already accepted']			  = 'Offerte is geaccepteerd';
$LANG['accept the pricequote online'] 				= 'Door hieronder uw gegevens in te vullen gaat u akkoord met de offerte "%s" en de bijbehorende voorwaarden.';
$LANG['ip address']									= 'IP adres';
$LANG['contact']									= 'Contact';
$LANG['the pricequote is accepted online on %s'] = 'De offerte is online geaccepteerd op %s';
$LANG['pricequote expired title']					= 'Offerte verlopen';
$LANG['pricequote expired text']					= 'De offerte die u probeert te accepteren is verlopen.<br />Neem contact met ons op indien u de offerte alsnog wilt accepteren.';

/** ORDERS */
$LANG['order']                = 'Bestelling';
$LANG['orders']               = 'Bestellingen';
$LANG['order date']           = 'Besteld op';
$LANG['order lines']          = 'Bestelling regels';
$LANG['order does not exist'] = 'De bestelling bestaat niet (meer).';


/** CHANGES */
$LANG['cancel modification']                = "Wijziging annuleren";
$LANG['cancel modification?']               = "Wijziging annuleren?";
$LANG['cancel modification are you sure']   = "Weet u zeker dat u de wijziging ongedaan wilt maken?";
$LANG['modification successfully canceled'] = "De wijziging is geannuleerd.";
$LANG['modification could not be canceled'] = "De wijziging kon niet worden geannuleerd. Mogelijk is de wijziging reeds verwerkt.";
$LANG['view modification']                  = "Bekijk de wijziging";


/** GENERAL */
$LANG['client area'] = 'Klantenpaneel';

$LANG['invalid identifier']                    = 'Ongeldig ID';
$LANG['priceExcl']                             = 'Prijs excl. BTW';
$LANG['priceIncl']                             = 'Prijs incl. BTW';
$LANG['quantity']                              = 'Aantal';
$LANG['date']                                  = 'Datum';
$LANG['date and time']                         = 'Datum en tijd';
$LANG['amountExcl']                            = 'Bedrag excl. BTW';
$LANG['amountIncl']                            = 'Bedrag incl. BTW';
$LANG['tax excluded']                          = 'excl. %s%% BTW';
$LANG['tax included']                          = 'incl. %s%% BTW';
$LANG['till']                                  = 'tot';
$LANG['discount on product']                   = '%s productkorting';
$LANG['discount on invoice']                   = '%s korting op factuur';
$LANG['discount on pricequote']                = '%s korting op offerte';
$LANG['period']                                = 'Periode';
$LANG['amount']                                = 'Bedrag';
$LANG['total amount']                          = 'Totaalbedrag';
$LANG['description']                           = 'Omschrijving';
$LANG['discount']                              = 'Korting';
$LANG['discount amount']                       = 'Korting bedrag';
$LANG['status']                                = 'Status';
$LANG['actions']                               = 'Acties';
$LANG['page not found title']                  = 'Pagina niet gevonden';
$LANG['page not found text']                   = 'De opgevraagde pagina kan niet gevonden worden.';
$LANG['search']                                = 'Zoeken';
$LANG['search results']                        = 'Zoek resultaten';
$LANG['and']                                   = 'en';
$LANG['yes']                                   = 'ja';
$LANG['no']                                    = 'nee';
$LANG['from']                                  = 'van';
$LANG['at']                                    = 'om';
$LANG['submit']                                = 'Verzenden';
$LANG['check']                                 = 'Controleren';
$LANG['back']                                  = 'Terug';
$LANG['undo']                                  = 'Ongedaan maken';
$LANG['logout']                                = 'Uitloggen';
$LANG['my account']                            = 'Mijn account';
$LANG['megabytes short']                       = 'Mb';
$LANG['gigabytes short']                       = 'Gb';
$LANG['VAT']                                   = 'BTW';
$LANG['terminate']                             = 'Opzeggen';
$LANG['cancel']                                = 'Annuleren';
$LANG['no search results found']               = 'Er zijn geen resultaten gevonden';
$LANG['extended search']                       = 'Uitgebreid zoeken';
$LANG['extended search results']               = 'Uitgebreid zoeken';
$LANG['extended search search again']          = 'Opnieuw uitgebreid zoeken';
$LANG['retrieving data']                       = 'Gegevens worden opgehaald';
$LANG['data could not be retrieved']           = 'Gegevens konden niet worden opgehaald';
$LANG['comment']                               = 'Opmerking';
$LANG['i agree with the terms and conditions'] = 'ik ga akkoord met de %s';
$LANG['terms and conditions']                  = 'algemene voorwaarden';
$LANG['year']                                  = 'jaar';
$LANG['make your choice']                      = 'Maak uw keuze';
$LANG['unlimited']                             = 'Onbeperkt';
$LANG['attachment']                            = 'Bijlage';
$LANG['attachments']                           = 'Bijlagen';


/** HOME */
$LANG['my data']                      = 'Mijn gegevens';
$LANG['change your data']             = 'Wijzig uw gegevens';
$LANG['what would you like to order'] = 'Wat wilt u bestellen';
$LANG['place new service order']      = 'Nieuwe dienst bestellen';
$LANG['pricequote to be accepted']    = 'Offertes nog te accepteren';


/** SUBSCRIPTIONS */
$LANG['subscription']                                  = 'Abonnement';
$LANG['subscription description']                      = 'Omschrijving';
$LANG['subscription current period']                   = 'Huidige periode';
$LANG['terminate subscription']                        = 'Dienst opzeggen';
$LANG['terminate subscription?']                       = 'Dienst opzeggen?';
$LANG['terminate subscription are you sure']           = 'Weet u zeker dat u deze dienst wilt opzeggen?';
$LANG['terminate reason']                              = 'Reden van opzegging';
$LANG['password is invalid']                           = 'Het opgegeven wachtwoord is ongeldig';
$LANG['subscription termination successfull']          = 'De dienst is succesvol opgezegd en verloopt op %s.';
$LANG['subscription termination waiting for approval'] = 'De opzegging voor de dienst per %s is doorgegeven.';
$LANG['subscription terminated and expires on']        = 'De dienst is opgezegd op %s en verloopt op %s.';
$LANG['subscription terminated and expires today']     = 'De dienst is opgezegd op %s en verloopt vandaag.';
$LANG['subscription terminated and is expired']        = 'De dienst is opgezegd op %s en is verlopen sinds %s.';
$LANG['subscription termination waiting for approval'] = 'De opzegging voor deze dienst is in behandeling.';
$LANG['service list termination at']                   = 'Opgezegd, verloopt op %s';


/** LOGIN */
$LANG['login']                                  = 'Inloggen';
$LANG['forgot password']                        = 'Wachtwoord vergeten?';
$LANG['no username or password given']          = 'Er is geen gebruikersnaam of wachtwoord ingevuld';
$LANG['username or password invalid']           = 'De combinatie van gebruikersnaam en wachtwoord is niet correct';
$LANG['password reset title']                   = 'Wachtwoord wijzigen';
$LANG['password reset description']             = 'Vul hieronder een eigen wachtwoord in om in de toekomst mee in te loggen.';
$LANG['password reset successfull']             = 'Uw wachtwoord is succesvol aangepast.';
$LANG['password forgot title']                  = 'Wachtwoord vergeten';
$LANG['password forgot description']            = 'Vul uw gebruikersnaam en e-mailadres in om het wachtwoord naar uw e-mailadres te verzenden.';
$LANG['back to login page']                     = 'Terug naar de login pagina';
$LANG['no username or email given']             = 'Er is geen gebruikersnaam of e-mailadres opgegeven';
$LANG['user with username email unknown']       = 'Geen gegevens gevonden met de opgegeven gebruikernaam en e-mailadres';
$LANG['password reset successfull, email send'] = 'Er is een e-mail verstuurd naar %s met een eenmalig wachtwoord om uw wachtwoord opnieuw in te stellen.';
$LANG['logout successfull']                     = 'U bent succesvol uitgelogd.';
$LANG['login from wfh - key invalid']           = 'Uw sleutel is ongeldig.';
$LANG['login from wfh - signature invalid']     = 'Uw sleutel is ongeldig. Controleer uw database connectie bestanden.';
$LANG['login from wfh - key expired']           = 'Uw sleutel is verlopen.';
$LANG['user blocked for x minutes']             = 'U bent geblokkeerd vanwege teveel inlogpogingen. Over %s minuten kunt u weer proberen in te loggen.';
$LANG['two factor login title'] 				= '2-staps authenticatie';
$LANG['two factor login description'] 			= 'Vul de authenticatie code in om verder te gaan.';
$LANG['two factor login invalid'] 				= 'U heeft een ongeldige authenticatie code ingevuld.';

/** MAIN MENU */
$LANG['mainmenu menu']           = 'Menu';
$LANG['mainmenu home']           = 'Home';
$LANG['mainmenu services']       = 'Diensten';
$LANG['mainmenu all services']   = 'Diensten overzicht';
$LANG['mainmenu other services'] = 'Overige diensten';
$LANG['mainmenu billing']        = 'Facturatie';
$LANG['mainmenu invoices']       = 'Facturen';
$LANG['mainmenu pricequotes']    = 'Offertes';
$LANG['mainmenu orders']         = 'Bestellingen';
$LANG['mainmenu subscriptions']  = 'Abonnementen';
$LANG['mainmenu account']        = 'Mijn account';


/** ARRAY VALUES */
$LANG['array_periodic'] = array(
	""  => "eenmalig",
	"d" => "dag",
	"w" => "week",
	"m" => "maand",
	"k" => "kwartaal",
	"h" => "half jaar",
	"j" => "jaar",
	"t" => "twee jaar");

$LANG['array_periodic_multi'] = array(
	"d" => "dagen",
	"w" => "weken",
	"m" => "maanden",
	"k" => "kwartalen",
	"h" => "halve jaren",
	"j" => "jaren",
	"t" => "twee jaren");

$LANG['array_pricequotestatus'] = array(
	"2" => "Verzonden",
	"3" => "Geaccepteerd",
	"4" => "Geaccepteerd",
	"8" => "Geweigerd");

$LANG['array_orderstatus'] = array(
	"0" => "Ontvangen",
	"1" => "In behandeling",
	"2" => "In behandeling",
	"8" => "Behandeld",
	"9" => "Vervallen");

$LANG['array_invoicestatus'] = array(
	"2" => "Openstaand",
	"3" => "Deels betaald",
	"4" => "Betaald",
	"8" => "Creditfactuur",
	"9" => "Vervallen");

$LANG['array_authorisation'] = array(
	"yes" => "Ja",
	"no"  => "Nee");

$LANG['array_mailingopt'] = array(
	"yes" => "Wel ontvangen",
	"no"  => "Niet ontvangen");

$LANG['array_boolean'] = array(
	"1" => "Ja",
	"0" => "Nee");

$LANG['array_invoicemethod'] = array(
	"0" => "Per e-mail",
	"1" => "Per post",
	"3" => "Per e-mail en post");

$LANG['array_sex'] = array(
	"m" => "Dhr.",
	"f" => "Mevr.",
	"d" => "Afd.",
	"u" => "Onbekend");

$LANG['array_months'] = array(
	"01" => "Januari",
	"02" => "Februari",
	"03" => "Maart",
	"04" => "April",
	"05" => "Mei",
	"06" => "Juni",
	"07" => "Juli",
	"08" => "Augustus",
	"09" => "September",
	"10" => "Oktober",
	"11" => "November",
	"12" => "December");

$LANG['array_days'] = array(
	"1" => "Maandag",
	"2" => "Dinsdag",
	"3" => "Woensdag",
	"4" => "Donderdag",
	"5" => "Vrijdag",
	"6" => "Zaterdag",
	"7" => "Zondag");

