<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

$LANG['mainmenu domain'] = 'Domeinnamen';


/** URLS */
$LANG['url']['domain']        = 'domeinnamen';
$LANG['url']['nameservers']   = 'nameservers';
$LANG['url']['getHosting']    = 'hostingaccount';
$LANG['url']['getToken']      = 'verhuizen';
$LANG['url']['whois']         = 'contactgegevens';
$LANG['url']['dnsmanagement'] = 'dnsbeheer';


/** PAGES */
$LANG['domains']                                  = 'Domeinnamen';
$LANG['domain']                                   = 'Domeinnaam';
$LANG['hostingaccount']                           = 'Hostingaccount';
$LANG['hosting package']                          = 'Hostingpakket';
$LANG['registration date']                        = 'Registratiedatum';
$LANG['expiration date']                          = 'Verloopt op';
$LANG['renewal date']                             = 'Verlengdatum';
$LANG['autorenew']                                = 'Automatisch verlengen?';
$LANG['nameservers']                              = 'Nameservers';
$LANG['nameserver 1']                             = 'Nameserver 1';
$LANG['nameserver 2']                             = 'Nameserver 2';
$LANG['nameserver 3']                             = 'Nameserver 3';
$LANG['nameserver 1 ip']                          = 'Nameserver 1 IP-adres';
$LANG['nameserver 2 ip']                          = 'Nameserver 2 IP-adres';
$LANG['nameserver 3 ip']                          = 'Nameserver 3 IP-adres';
$LANG['change nameservers']                       = 'Nameservers wijzigen';
$LANG['change whois']                             = 'Contact gegevens wijzigen';
$LANG['transfer domain']                          = 'Autorisatiecode opvragen';
$LANG['dns management']                           = 'DNS beheer';
$LANG['new domain']                               = 'Nieuwe domeinnaam';
$LANG['handle data']                              = 'Contact gegevens';
$LANG['handle owner']                             = 'Houder contact';
$LANG['handle admin']                             = 'Administratief contact';
$LANG['handle tech']                              = 'Technisch contact';
$LANG['check domain availability']                = 'Beschikbaarheid domeinnaam controleren';
$LANG['domain does not exist']                    = 'De domeinnaam bestaat niet (meer).';
$LANG['nameservers update succeed']               = 'De nameservers zijn succesvol gewijzigd';
$LANG['nameservers invalid']                      = 'Één of meerdere nameserver(s) zijn ongeldig';
$LANG['nameservers ipaddress invalid']            = 'Er is een ongeldig IP-adres opgegeven';
$LANG['nameservers update failed']                = 'De nameservers konden niet worden aangepast. Neem contact met ons op.';
$LANG['whois edit succeed']                       = 'De contact gegevens zijn succesvol gewijzigd.';
$LANG['whois edit failed']                        = 'Het aanpassen van de contactgegevens is niet gelukt. Neem contact met ons op.';
$LANG['token could not be retrieved, contact us'] = 'De token van de verhuizing kon niet worden opgehaald. Neem contact met ons op voor uw token.';
$LANG['used bandwidth']                           = 'Gebruikte bandbreedte';
$LANG['used diskspace']                           = 'Gebruikte webruimte';


/** DNS zone */
$LANG['no dns records given']                                    = 'Er zijn geen DNS records opgegeven';
$LANG['change dns records']                                      = 'DNS records wijzigen';
$LANG['dns zone could not be retrieved']                         = 'De huidige DNS records kunnen momenteel niet worden opgehaald.';
$LANG['dns zone records']                                        = 'DNS records';
$LANG['add dns record']                                          = 'DNS record toevoegen';
$LANG['delete dns record']                                       = 'Verwijder record';
$LANG['dns record name']                                         = 'Naam';
$LANG['dns record type']                                         = 'Type';
$LANG['dns record value']                                        = 'Waarde';
$LANG['dns record priority']                                     = 'Prioriteit';
$LANG['dns record ttl']                                          = 'TTL';
$LANG['edit dns zone succeed']                                   = 'De DNS records zijn succesvol gewijzigd.';
$LANG['edit dns zone failed']                                    = 'Het aanpassen van de DNS records is niet gelukt. Neem contact met ons op.';
$LANG['warning editwhois modification awaiting approval']        = 'De contact gegevens zijn gewijzigd op %s en zijn in behandeling.';
$LANG['warning editdnszone modification awaiting approval']      = 'De DNS records zijn gewijzigd op %s en zijn in behandeling.';
$LANG['warning changenameserver modification awaiting approval'] = 'De nameservers zijn gewijzigd op %s en zijn in behandeling.';
$LANG['warning editwhois modification error']                    = 'De contact gegevens zijn gewijzigd op %s, maar automatische verwerking is mislukt. U kunt contact met ons opnemen voor meer informatie.';
$LANG['warning editdnszone modification error']                  = 'De DNS records zijn gewijzigd op %s, maar automatische verwerking is mislukt. U kunt contact met ons opnemen voor meer informatie.';
$LANG['warning changenameserver modification error']             = 'De nameservers zijn gewijzigd op %s, maar automatische verwerking is mislukt. U kunt contact met ons opnemen voor meer informatie.';
$LANG['record name may not end with dot']                        = 'DNS record naam mag niet eindigen met een \'.\'';
$LANG['record name may not contain domain']                      = 'DNS record naam mag niet de domeinnaam zelf bevatten';
$LANG['record name may not contain spaces']                      = 'DNS record naam mag geen spaties bevatten';
$LANG['record ttl incorrect data']                               = 'DNS record TTL mag alleen cijfers bevatten en moet groter dan 0 zijn';
$LANG['record priority incorrect data']                          = 'DNS record prioriteit mag alleen cijfers bevatten tussen 0 en 100';
$LANG['record value may not be empty']                           = 'DNS record value mag niet leeg zijn';
$LANG['record value invalid ipv4']                               = 'DNS record van het type A moet als value een geldig IPv4 adres zijn';
$LANG['record value invalid ipv6']                               = 'DNS record van het type AAAA moet als value een geldig IPv6 adres zijn';


/** STATUS */
$LANG['array_domainstatus'] = array(
	"-1" => "In behandeling",
	"1"  => "In behandeling",
	"3"  => "In behandeling",
	"4"  => "Actief",
	"5"  => "Verlopen",
	"6"  => "In behandeling",
	"7"  => "In behandeling",
	"8"  => "Opgezegd");

