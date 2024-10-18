<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

/** URLS */

// controllers
$LANG['url']['index']      = 'index';
$LANG['url']['debtor']     = 'mein-profil';
$LANG['url']['invoice']    = 'rechnungen';
$LANG['url']['pricequote'] = 'angebote';
$LANG['url']['order']      = 'bestellungen';
$LANG['url']['login']      = 'anmelden';
$LANG['url']['service']    = 'dienste';
$LANG['url']['serviceAll'] = 'alle-dienste';

// action
$LANG['url']['view']               = 'view';
$LANG['url']['download']           = 'download';
$LANG['url']['payOnline']          = 'pay-online';
$LANG['url']['billingData']        = 'billing-information';
$LANG['url']['loginData']          = 'change-password';
$LANG['url']['twoFactorAuth']      = '2-step-authentication';
$LANG['url']['twoFactorGenerateKey'] 	= 'two-factor-generate-key';
$LANG['url']['twoFactorVerifyKey'] 		= 'two-factor-verify-key';
$LANG['url']['twoFactorDeactivate'] 	= 'two-factor-deactivate';
$LANG['url']['paymentData']        = 'payment-information';
$LANG['url']['resetPassword']      = 'reset-password';
$LANG['url']['forgotPassword']     = 'password-forgotten';
$LANG['url']['logout']             = 'logout';
$LANG['url']['terminate']          = 'terminate';
$LANG['url']['accept']             = 'accept';
$LANG['url']['decline']            = 'decline';
$LANG['url']['cancelModification'] = 'cancel-change';
$LANG['url']['validatekey']        = 'validate-key';
$LANG['url']['getStates']          = 'states';
$LANG['url']['orderNew']           = 'order';
$LANG['url']['changeLanguage']     = 'change-language';
$LANG['url']['downloadAttachment'] = 'download-anlage';


/** GLOBAL MESSAGES (ERROR/WARNING/SUCCESS) */
$LANG['api connect error']                     		= "Fehler der Verbindung zum Server: HTTP code %s";
$LANG['api response no json']                     	= "Falsche Antwort vom Server. Überprüfen Sie die URLs im Back-Office angegeben.";
$LANG['api action failed, reason unknown']          = 'Die Aktion konnte nicht aus unbekannter Ursache ausgeführt werden.';
$LANG['invalid emailaddress']                       = 'Die E-Mail-Adresse ist ungültig';
$LANG['no surname given']                           = "Es gibt keinen Nachname gegeben";
$LANG['the requested action could not be executed'] = "Die angeforderte Aktion kann nicht ausgeführt werden";
$LANG['changes successfully processed']             = "Die Änderungen werden erfolgreich verarbeitet";
$LANG['missing plugin file']                        = "Eine Datei des Plugins fehlt";
$LANG['missing plugin file description']            = "Die folgende Datei fehlt: %s";
$LANG['downoad attachment failed']                  = "Die angeforderte Datei konnte nicht gefunden werden";


/** ACCOUNT/DEBTOR */
$LANG['general data']                                       = 'Allgemeine Informationen';
$LANG['billing data']                                       = 'Rechnungsdetails';
$LANG['change billing data']                                = 'Ändern Sie Rechnungsdetails';
$LANG['change password']                                    = 'Passwort ändern';
$LANG['two step authentication']                            = '2-step authentication setup';
$LANG['payment data']                                       = 'Zahlungsdetails';
$LANG['change payment data']                                = 'Informationen ändern';
$LANG['name']                                               = 'Name';
$LANG['sex']                                                = 'Anrede';
$LANG['gender male']                                        = 'Herr';
$LANG['gender female']                                      = 'Frau';
$LANG['gender department']                                  = 'Abtlg.';
$LANG['initials']                                           = 'Vorname';
$LANG['surname']                                            = 'Nachname';
$LANG['address']                                            = 'Adresse';
$LANG['zipcode']                                            = 'Postleitzahl';
$LANG['city']                                               = 'Ort';
$LANG['state']                                              = 'Bundesland';
$LANG['country']                                            = 'Land';
$LANG['emailaddress']                                       = 'E-Mail Adresse';
$LANG['companyname']                                        = 'Firma';
$LANG['companynumber']                                      = 'IHK-Nummer';
$LANG['legalform']                                          = 'Rechtsform';
$LANG['taxnumber']                                          = 'USt-IdNr';
$LANG['phonenumber']                                        = 'Telefonnummer';
$LANG['faxnumber']                                          = 'Faxnummer';
$LANG['mobilenumber']                                       = 'Handynummer';
$LANG['website']                                            = 'Webseite';
$LANG['second emailaddress']                                = 'Weitere E-Mail Adresse';
$LANG['authorisation']                                      = 'Lastschrift';
$LANG['accountnumber']                                      = 'IBAN';
$LANG['accountname']                                        = 'Kontoinhaber';
$LANG['accountbank']                                        = 'Name der Bank';
$LANG['accountcity']                                        = 'Sitz der Bank';
$LANG['accountbic']                                         = 'BIC';
$LANG['direct debit']                                       = 'Lastschriftdaten';
$LANG['debtor mailings']                                    = 'Newsletter erhalten?';
$LANG['personal data']                                      = 'Ansprechpartner';
$LANG['company data']                                       = 'Firma';
$LANG['address data']                                       = 'Adressdaten';
$LANG['contact data']                                       = 'Kontaktdaten';
$LANG['debtor preferences']                                 = 'Einstellungen';
$LANG['bank details']                                       = 'Bankdetails';
$LANG['username']                                           = 'Benutzername';
$LANG['password']                                           = 'Passwort';
$LANG['current password']                                   = 'Aktuelles Passwort';
$LANG['new password']                                       = 'Neues Passwort';
$LANG['repeat password']                                    = 'Passwort wiederholen';
$LANG['repeat new password']                                = 'Neues Passwort wiederholen';
$LANG['accountnumber']                                      = 'Rechnung (IBAN)';
$LANG['accountname']                                        = 'Kontoinhaber';
$LANG['accountbank']                                        = 'Name der Bank';
$LANG['accountcity']                                        = 'Sitz der Bank';
$LANG['AccountBIC']                                         = 'BIC';
$LANG['pay with direct debit']                              = 'Ich möchte per Lastschrift zu bezahlen';
$LANG['save data']                                          = 'Daten speichern';
$LANG['debtor general data saved']                          = 'Ihre Daten werden gespeichert';
$LANG['debtor login data saved']                            = 'Ihr Passwort wurde geändert. Das nächste Mal, wenn Sie sich anmelden, können Sie Ihr neues Passwort verwenden.';
$LANG['no companyname and no surname are given']            = "Es gibt keine Firmennamen oder Nachnamen gegeben";
$LANG['no password given']                                  = "Kein Passwort angegeben";
$LANG['repeat password not correct']                        = "Die Passwörter stimmen nicht überein";
$LANG['invalid bic']                                        = "Der BIC angegeben ist ungültig";
$LANG['invalid iban']                                       = "Die angegebene IBAN ist ungültig";
$LANG['password should at least be 8 char']                 = "Das Passwort sollte mindestens 8 Zeichen lang sein";
$LANG['invalid current password']                           = "Das aktuelle Passwort ist nicht korrekt";
$LANG['invalid state']                                      = "Ungültige Bundesland";
$LANG['warning editgeneral modification awaiting approval'] = 'Die allgemeinen Daten wurde an %s geändert und anhängige.';
$LANG['warning editbilling modification awaiting approval'] = 'Die Abrechnungsdaten wurde an %s geändert und sind anhängig.';
$LANG['warning editpayment modification awaiting approval'] = 'Die Zahlungsdetails wurden %s geändert und anhängige.';
$LANG['two step authentication - text'] 					= 'Die Zwei-Faktor-Authentifizierung sorgt dafür, dass Sie neben Ihrem Benutzernamen und Passwort auch einen zusätzlichen Authentifizierungscode eingeben müssen. Dieser Authentifizierungscode wird von einer der Authentifizierungs-Apps generiert, die Sie auf Ihrem Smartphone oder Computer verwenden können. Wählen Sie eine "zeitbasierte" innerhalb Ihrer Anwendung, wenn sie mehrere Typen unterstützt. Das zusätzliche Authentifizierungsverfahren erhöht den Schutz gegen Eindringlinge.';
$LANG['two step authentication - apps'] 					= 'Beispiel Apps, die du verwenden kannst';
$LANG['two step authentication - generate code'] 			= 'Code generieren';
$LANG['two step authentication - generate code text'] 		= 'Durch das Generieren eines Codes können Sie ein Konto innerhalb Ihrer Authentifizierungs-App erstellen. Wählen Sie eine "zeitbasierte" innerhalb Ihrer Anwendung, wenn sie mehrere Typen unterstützt. In der Zukunft benötigen Sie einen Authentifizierungscode, um sich anzumelden.';
$LANG['two step authentication - generate code link']		= 'Klicken Sie hier, um Ihren Code zu generieren';
$LANG['two step authentication - activate text']			= '<strong>Hinweis:</strong> Sie müssen die Zwei-Faktor-Authentifizierung bestätigen, um ihn zu aktivieren.';
$LANG['two step authentication - auth code']				= 'Authentication Code';
$LANG['two step authentication - submit']					= 'Aktivieren';
$LANG['two step authentication - already active']			= 'Zwei-Faktor-Authentifizierung ist derzeit aktiv.';
$LANG['two step authentication - deactivate link']			= 'Klicken Sie hier, um die Zwei-Faktor-Authentifizierung wieder einzurichten oder zu deaktivieren.';
$LANG['two step authentication - success']					= 'Eine Zwei-Faktor-Authentifizierung wurde eingerichtet.';
$LANG['two step authentication - error']					= 'Die Aktivierung der Zwei-Faktor-Authentifizierung ist aufgrund eines falschen Codes fehlgeschlagen.';
$LANG['two step authentication deactivate - success']		= 'Die Zwei-Faktor-Authentifizierung wurde deaktiviert.';
$LANG['two step authentication deactivate - error']			= 'Die Deaktivierung der Zwei-Faktor-Authentifizierung ist fehlgeschlagen.';
$LANG['two step authentication deactivate - title']			= 'Deaktivieren Sie die Zwei-Faktor-Authentifizierung';
$LANG['two step authentication deactivate - text']			= 'Bestätigen Sie die Deaktivierung der Zwei-Faktor-Authentifizierung, indem Sie Ihr Passwort eingeben?';
$LANG['confirm']											= 'Bestätigen';

/** INVOICES */
$LANG['invoice']                                         = 'Rechnung';
$LANG['invoices']                                        = 'Rechnungen';
$LANG['invoice nr']                                      = 'Rechnungsnummer';
$LANG['due date']                                        = 'Zur Bezahlung';
$LANG['to pay']                                          = 'Nicht bezahlt';
$LANG['invoice date']                                    = 'Rechnungsdatum';
$LANG['reference number']                                = 'Referenz';
$LANG['invoice lines']                                   = 'Rechnung Regeln';
$LANG['outstanding invoices']                            = 'Unbezahlte Rechnungen';
$LANG['invoice open amount']                             = 'Ausstehender Betrag';
$LANG['pay invoice']                                     = 'Online-Zahlung';
$LANG['download invoice']                                = 'Download-Rechnung';
$LANG['no invoices found']                               = 'Es gibt keine Rechnungen gefunden.';
$LANG['invoice does not exist']                          = 'Die Rechnung ist nicht (mehr) vorhanden.';
$LANG['copy general data']                               = 'Kopieren der allgemeinen Daten';
$LANG['billing data uses general data']                  = 'Die allgemeinen Daten für die Abrechnung verwendet.';
$LANG['use different billing data']                      = 'Abnormal Abrechnungsdaten?';
$LANG['you have outstanding invoices']                   = 'Sie haben %d ausstehenden Rechnungen, klicken Sie auf einer Rechnung um die Rechnung zu sehen.';
$LANG['you have no outstanding invoices']                = 'Sie haben keine ausstehenden Rechnungen.';
$LANG['payment data - bank transfer or online payments'] = 'Per Banküberweisung oder eine Online-Zahlung.';
$LANG['payment data - activate authorisation']           = 'Wenn Sie per Lastschrift zahlen möchten, können Sie es hier ändern.';
$LANG['payment data - direct debit']                     = 'Sie haben eine Lastschrift gegeben. Wir schreiben, um die Beträge, die auf IBAN <strong>%s</strong> im Namen von <strong>%s</strong>.';
$LANG['direct debit invoice abbr']                       = '- lastschrift';
$LANG['direct debit invoice']                            = '- Zahlung per Lastschrift';
$LANG['direct debit paid invoice']                       = '- per Lastschrift bezahlt';
$LANG['already paid']                                    = 'Bereits bezahlt';
$LANG['open amount to be paid']                          = 'Ausstehende Zahlungen';


/** OTHER SERVICES */
$LANG['other services']                      = 'Andere Dienste';
$LANG['other service']                       = 'Andere Dienst';
$LANG['order new service']                   = 'Neuen Service';
$LANG['you have other services']             = 'Sie haben %d andere Dienste, klicken Sie auf einen Dienst um den Dienst zu sehen.';
$LANG['you have no other services']          = 'Sie haben keine andere Dienste.';
$LANG['no other services found']             = 'Es gibt keine anderen Dienste gefunden.';
$LANG['other service does not exist']        = 'Der andere Dienst is nicht (mehr) vorhanden.';
$LANG['error during termination of service'] = 'Unbekannter Fehler trat bei der Verarbeitung der Kündigung.';
$LANG['next invoice date']                   = 'Nächste Rechnung';
$LANG['terminated']                          = 'gekündigt';


/** PRICEQUOTES */
$LANG['open pricequotes']                         = 'Angbote wartet auf die Genehmigung';
$LANG['pricequote']                               = 'Angbot';
$LANG['pricequotes']                              = 'Angbote';
$LANG['pricequote code']                          = 'Angebotsnummer';
$LANG['pricequote nr']                            = 'Angebotsnummer';
$LANG['pricequote date']                          = 'Angebots Datum';
$LANG['expiration date']                          = 'Gültig bis';
$LANG['download pricequote']                      = 'Download-Angebot';
$LANG['accept pricequote']                        = 'Akzeptieren';
$LANG['decline pricequote']                       = 'Absagen';
$LANG['pricequote lines']                         = 'Angebots Regeln';
$LANG['modal title decline pricequote']           = 'Angebots abweisen';
$LANG['pricequote accepted successfull']          = 'Vielen Dank für akzeptieren Angebot %s.';
$LANG['pricequote declined successfull']          = 'Vielen Dank für die Weitergabe Ihrer Wahl.';
$LANG['please agree to the terms and conditions'] = 'Sie haben noch keine Einigung die Allgemeinen Geschäftsbedingungen gegeben';
$LANG['draw in this field'] 					  = 'Anmeldung in diesem Bereich';
$LANG['again'] 					  				  = 'Zurücksetzen';
$LANG['signed by'] 					  			  = 'Von';
$LANG['ipaddress'] 					  		  	  = 'IP Adresse';
$LANG['comment'] 					  		 	  = 'Bemerkung';
$LANG['signature'] 					  		 	  = 'Unterschrift';
$LANG['download signed pricequote'] 			  = 'Laden Sie den unterzeichneten Angebot';
$LANG['not mandatory'] 			 				  = 'Nicht zwingend';
$LANG['accept'] 			 				      = 'Akzeptieren';
$LANG['see pricequote pdf'] 			 		  = 'Ansicht Schätzung PDF';
$LANG['accept pricequote online'] 			 	  = 'Akzeptieren Sie die Schätzung online';
$LANG['pricequote is already accepted']			  = 'Angebot wird angenommen';
$LANG['accept the pricequote online'] 				= 'Durch das Ausfüllen Ihrer Daten stimmen Sie mit dem Zitat "%s" und verwandte Begriffe.';
$LANG['ip address']									= 'IP Adresse';
$LANG['contact']									= 'Kontakt';
$LANG['the pricequote is accepted online on %s'] = 'Der Auftrag wurde online vergeben am %s';
$LANG['pricequote expired title']					= 'Angebot ist abgelaufen';
$LANG['pricequote expired text']					= 'Das Angebot, das Sie annehmen möchten, ist abgelaufen.<br />Kontaktieren Sie uns, wenn Sie das Angebot weiterhin annehmen möchten.';


/** ORDERS */
$LANG['order']                = 'Bestellung';
$LANG['orders']               = 'Bestellungen';
$LANG['order date']           = 'Bestellt';
$LANG['order lines']          = 'Bestellung Regeln';
$LANG['order does not exist'] = 'Der Auftrag is nicht (mehr) vorhanden.';


/** CHANGES */
$LANG['cancel modification']                = "Änderungen widerrufen";
$LANG['cancel modification?']               = "Änderungen widerrufen?";
$LANG['cancel modification are you sure']   = "Sind Sie sicher, dass Sie die Änderung rückgängig machen möchten?";
$LANG['modification successfully canceled'] = "Die Änderung wurde abgesagt.";
$LANG['modification could not be canceled'] = "Die Änderung konnte nicht rückgängig gemacht werden. Vielleicht hat die Änderung bereits verarbeitet.";
$LANG['view modification']                  = "Anzeigen der Änderungen";


/** GENERAL */
$LANG['client area'] = 'Kundencenter';

$LANG['invalid identifier']                    = 'Ungültige ID';
$LANG['priceExcl']                             = 'Preis exkl. MwSt';
$LANG['priceIncl']                             = 'Preis inkl. MwSt';
$LANG['quantity']                              = 'Anzahl';
$LANG['date']                                  = 'Datum';
$LANG['date and time']                         = 'Datum und Uhrzeit';
$LANG['amountExcl']                            = 'Betrag exkl. MwSt';
$LANG['amountIncl']                            = 'Betrag inkl. MwSt';
$LANG['tax excluded']                          = 'exkl. %s%% MwSt';
$LANG['tax included']                          = 'inkl. %s%% MwSt';
$LANG['till']                                  = 'tot';
$LANG['discount on product']                   = '%s Rabatt auf das produkt';
$LANG['discount on invoice']                   = '%s Rabatt auf der Rechnung';
$LANG['discount on pricequote']                = '%s Rabatt auf der Angebot';
$LANG['period']                                = 'Zeitraum';
$LANG['amount']                                = 'Summe';
$LANG['total amount']                          = 'Gesamtbetrag';
$LANG['description']                           = 'Beschreibung';
$LANG['discount']                              = 'Rabatt';
$LANG['discount amount']                       = 'Rabattbetrag';
$LANG['status']                                = 'Status';
$LANG['actions']                               = 'Acties';
$LANG['page not found title']                  = 'Seite nicht gefunden';
$LANG['page not found text']                   = 'Die von Ihnen angeforderte Seite konnte nicht gefunden werden.';
$LANG['search']                                = 'Suche';
$LANG['search results']                        = 'Suchergebnisse';
$LANG['and']                                   = 'und';
$LANG['yes']                                   = 'ja';
$LANG['no']                                    = 'nein';
$LANG['from']                                  = 'von';
$LANG['at']                                    = 'zu';
$LANG['submit']                                = 'Senden';
$LANG['check']                                 = 'Checken';
$LANG['back']                                  = 'Zurück';
$LANG['undo']                                  = 'Rückgängig';
$LANG['logout']                                = 'Abmelden';
$LANG['my account']                            = 'Mein Konto';
$LANG['megabytes short']                       = 'Mb';
$LANG['gigabytes short']                       = 'Gb';
$LANG['VAT']                                   = 'MwSt';
$LANG['terminate']                             = 'Kündigen';
$LANG['cancel']                                = 'Abbrechen';
$LANG['no search results found']               = 'Keine Ergebnisse gefunden';
$LANG['extended search']                       = 'Erweiterte Suche';
$LANG['extended search results']               = 'Erweiterte Suche';
$LANG['extended search search again']          = 'Neue erweiterte Suche';
$LANG['retrieving data']                       = 'Daten werden abgefragt';
$LANG['data could not be retrieved']           = 'Die Daten konnten nicht abgefragt werden';
$LANG['comment']                               = 'Bemerkung';
$LANG['i agree with the terms and conditions'] = 'Ich stimme mit der %s';
$LANG['terms and conditions']                  = 'AGB';
$LANG['year']                                  = 'Jahr';
$LANG['make your choice']                      = 'Bitte wählen';
$LANG['unlimited']                             = 'Unbegrenzt';
$LANG['attachment']                            = 'Anhang';
$LANG['attachments']                           = 'Anhänge';


/** HOME */
$LANG['my data']                      = 'Meine Daten';
$LANG['change your data']             = 'Ändern Sie Ihre Daten';
$LANG['what would you like to order'] = 'Was möchten Sie bestellen';
$LANG['place new service order']      = 'Neuer Service Bestellen';
$LANG['pricequote to be accepted']    = 'Angeboten akzeptieren noch';


/** SUBSCRIPTIONS */
$LANG['subscription']                                  = 'Abonnement';
$LANG['subscription description']                      = 'Beschreibung';
$LANG['subscription current period']                   = 'Aktuelle Periode';
$LANG['terminate subscription']                        = 'Service Kündigen';
$LANG['terminate subscription?']                       = 'Service Kündigen?';
$LANG['terminate subscription are you sure']           = 'Sind Sie sicher dass Sie diesen Service Kündigen möchten?';
$LANG['terminate reason']                              = 'Grund für die Kündigung';
$LANG['password is invalid']                           = 'Das eingegebene Passwort ist ungültig';
$LANG['subscription termination successfull']          = 'Der Dienst wurde erfolgreich abgefragt und endet am %s.';
$LANG['subscription termination waiting for approval'] = 'Die Kündigung des Dienstes durch %s vergangen.';
$LANG['subscription terminated and expires on']        = 'Der Dienst wird in %s abgefragt und wird auf %s verfallen.';
$LANG['subscription terminated and expires today']     = 'Der Dienst wird in %s abgefragt und läuft heute ab.';
$LANG['subscription terminated and is expired']        = 'Der Dienst wird in %s abgefragt und ist seit %s abgelaufen.';
$LANG['subscription termination waiting for approval'] = 'Die Beendigung dieses Dienstes steht noch aus.';
$LANG['service list termination at']                   = 'Gekündigt, endet am %s';


/** LOGIN */
$LANG['login']                                 = 'Anmelden';
$LANG['forgot password']                       = 'Passwort vergessen?';
$LANG['no username or password given']         = 'Es gibt kein Benutzername oder Passwort eingegeben';
$LANG['username or password invalid']          = 'Die Kombination aus Benutzername und Passwort ist nicht korrekt';
$LANG['password reset title']                  = 'Passwort ändern';
$LANG['password reset description']            = 'Bitte geben Sie unten Ihr eigenes Passwort loggen Sie sich mit.';
$LANG['password reset successfull']            = 'Ihr Passwort wurde erfolgreich geändert.';
$LANG['password forgot title']                 = 'Passwort vergessen';
$LANG['password forgot description']           = 'Geben Sie Ihren Benutzernamen und E-Mail-Adresse ein. Das Passwort wird an Ihre E-Mail-Adresse gesendet werden.';
$LANG['back to login page']                    = 'Zurück zur Login-Seite';
$LANG['no username or email given']            = 'Es gibt keine Benutzernamen oder E-Mail-Adresse zur Verfügung gestellt';
$LANG['user with username email unknown']      = 'Keine Daten mit dem angegebenen Benutzernamen und E-Mail-Adresse';
$LANG['password reset successfull, email send'] = 'Es gibt eine E-Mail an %s mit einem Einmalpasswort gesendet Sie Ihr Passwort zurücksetzen.';
$LANG['logout successfull']                    = 'Sie haben sich erfolgreich angemeldet.';
$LANG['login from wfh - key invalid']          = 'Ihr Schlüssel ist ungültig.';
$LANG['login from wfh - signature invalid']    = 'Ihr Schlüssel ist ungültig. Überprüfen Sie die Datenbankverbindung Dateien.';
$LANG['login from wfh - key expired']          = 'Ihr Schlüssel ist abgelaufen.';
$LANG['user blocked for x minutes']            = 'Sie sind auf Grund zu vieler Anmeldeversuche gesperrt. Über %x Minuten können Sie versuchen, sich erneut anzumelden.';
$LANG['two factor login title'] 				= 'Zwei-Faktor-Authentifizierung';
$LANG['two factor login description'] 			= 'Füllen Sie den Authentifizierungscode aus, um fortzufahren.';
$LANG['two factor login invalid'] 				= 'Sie haben einen falschen Authentifizierungscode ausgefüllt.';

/** MAIN MENU */
$LANG['mainmenu menu']           = 'Menü';
$LANG['mainmenu home']           = 'Home';
$LANG['mainmenu services']       = 'Diensten';
$LANG['mainmenu all services']   = 'Diensten Übersicht';
$LANG['mainmenu other services'] = 'Andere Dienste';
$LANG['mainmenu billing']        = 'Billing';
$LANG['mainmenu invoices']       = 'Rechnungen';
$LANG['mainmenu pricequotes']    = 'Angeboten';
$LANG['mainmenu orders']         = 'Bestellungen';
$LANG['mainmenu subscriptions']  = 'Abonnements';
$LANG['mainmenu account']        = 'Mein Konto';


/** ARRAY VALUES */
$LANG['array_periodic'] = array(
	"" => "einmalig",
	"d" => "Tag",
	"w" => "Woche",
	"m" => "Monat", 
	"k" => "Quartal",
	"h" => "halbes Jahr",
	"j" => "Jahr",
	"t" => "zwei Jahre");

$LANG['array_periodic_multi'] = array(
	"d" => "Tage",
	"w" => "Wochen",
	"m" => "Monate", 
	"k" => "Quartale",
	"h" => "halbes Jahr",
	"j" => "Jahre",
	"t" => "zwei Jahre");

$LANG['array_pricequotestatus'] = array(
	"2" => "Gesendet",
	"3" => "Angenommen",
	"4" => "Rechnung erstellt",
	"8" => "Abgelehnt");

$LANG['array_orderstatus'] = array(
	"0" => "Erhalten",
	"1" => "In Bearbeitung",
	"2" => "In Bearbeitung",
	"8" => "Bearbeitet",
	"9" => "Abgelaufen");

$LANG['array_invoicestatus'] = array(
	"2" => "Nicht bezahlt",
	"3" => "Teilweise bezahlt",
	"4" => "Bezahlt",
	"8" => "Gutschrift",
	"9" => "Abgelaufen");

$LANG['array_authorisation'] = array(
	"yes" => "Ja",
	"no"  => "Nein");

$LANG['array_mailingopt'] = array(
	"yes" => "Ja",
	"no"  => "Nein");

$LANG['array_boolean'] = array(
	"1" => "Ja",
	"0" => "Nein");

$LANG['array_invoicemethod'] = array(
	"0" => "Per E-Mail",
	"1" => "Per Post",
	"3" => "Per E-Mail und Post");

$LANG['array_sex'] = array(
	"m" => "Herr",
	"f" => "Frau",
	"d" => "Abt.",
	"u" => "Unbekannt");

$LANG['array_months'] = array(
	"01" => "Januar",
	"02" => "Februar",
	"03" => "März",
	"04" => "April",
	"05" => "Mai",
	"06" => "Juni",
	"07" => "Juli",
	"08" => "August",
	"09" => "September",
	"10" => "Oktober",
	"11" => "November",
	"12" => "Dezember");

$LANG['array_days'] = array(
	"1" => "Montag",
	"2" => "Dienstag",
	"3" => "Mittwoch",
	"4" => "Donnerstag",
	"5" => "Freitag",
	"6" => "Samstag",
	"7" => "Sonntag");

