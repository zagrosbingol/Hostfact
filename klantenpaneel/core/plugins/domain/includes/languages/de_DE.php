<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

$LANG['mainmenu domain'] = 'Domains';


/** URLS */
$LANG['url']['domain']        = 'domainnames';
$LANG['url']['nameservers']   = 'nameservers';
$LANG['url']['getHosting']    = 'hostingaccount';
$LANG['url']['getToken']      = 'transfer';
$LANG['url']['whois']         = 'contact-information';
$LANG['url']['dnsmanagement'] = 'dns-management';


/** PAGES */
$LANG['domains']                                  = 'Domains';
$LANG['domain']                                   = 'Domain';
$LANG['hostingaccount']                           = 'Hosting account';
$LANG['hosting package']                          = 'Hosting Paket';
$LANG['registration date']                        = 'Registrierungsdatum';
$LANG['expiration date']                          = 'Gültig bis';
$LANG['renewal date']                             = 'Extend Datum';
$LANG['autorenew']                                = 'Automatisch verlängern?';
$LANG['nameservers']                              = 'Nameserver';
$LANG['nameserver 1']                             = 'Nameserver 1';
$LANG['nameserver 2']                             = 'Nameserver 2';
$LANG['nameserver 3']                             = 'Nameserver 3';
$LANG['nameserver 1 ip']                          = 'Nameserver 1 IP Adresse';
$LANG['nameserver 2 ip']                          = 'Nameserver 2 IP Adresse';
$LANG['nameserver 3 ip']                          = 'Nameserver 3 IP Adresse';
$LANG['change nameservers']                       = 'Nameservers ändern';
$LANG['change whois']                             = 'Kontakt details bearbeiten';
$LANG['transfer domain']                          = 'Authorisierungs code Anfrage';
$LANG['dns management']                           = 'DNS-Verwaltung';
$LANG['new domain']                               = 'Neuen Domain';
$LANG['handle data']                              = 'Kontaktinformationen';
$LANG['handle owner']                             = 'Halterkontakt';
$LANG['handle admin']                             = 'Administrativen Kontakt';
$LANG['handle tech']                              = 'Technischer Kontakt';
$LANG['check domain availability']                = 'Überprüfen Sie Domain Verfügbarkeit';
$LANG['domain does not exist']                    = 'Der Domainname is nicht (mehr) vorhanden.';
$LANG['nameservers update succeed']               = 'Die Nameserver aktualisiert erfolgreich';
$LANG['nameservers invalid']                      = 'Eine oder mehrere Nameserver(s) sind ungültig';
$LANG['nameservers ipaddress invalid']            = 'Es gibt eine ungültige IP-Adresse zur Verfügung gestellt';
$LANG['nameservers update failed']                = 'Die Name-Server konnte nicht eingestellt werden. Kontaktieren Sie uns.';
$LANG['whois edit succeed']                       = 'Kontaktinformationen erfolgreich aktualisiert.';
$LANG['whois edit failed']                        = 'Den Kontakt Einstellung fehlgeschlagen. Kontaktieren Sie uns.';
$LANG['token could not be retrieved, contact us'] = 'Das Token des Umzugs nicht abgerufen werden konnte. Bitte kontaktieren Sie uns für Ihre Token.';
$LANG['used bandwidth']                           = 'Genutzte Traffic';
$LANG['used diskspace']                           = 'Genutzte webspace';


/** DNS zone */
$LANG['no dns records given']                                    = 'Keine DNS-Einträge angegeben';
$LANG['change dns records']                                      = 'Ändern DNS-Einträge';
$LANG['dns zone could not be retrieved']                         = 'Die aktuellen DNS-Einträge können derzeit nicht abgerufen werden.';
$LANG['dns zone records']                                        = 'DNS-Einträge';
$LANG['add dns record']                                          = 'Hinzufügen neuer DNS-Eintrag';
$LANG['delete dns record']                                       = 'Entfernen DNS-Eintrag';
$LANG['dns record name']                                         = 'Name';
$LANG['dns record type']                                         = 'Typ';
$LANG['dns record value']                                        = 'Wert';
$LANG['dns record priority']                                     = 'Priorität';
$LANG['dns record ttl']                                          = 'TTL';
$LANG['edit dns zone succeed']                                   = 'Die DNS-Einträge aktualisiert erfolgreich.';
$LANG['edit dns zone failed']                                    = 'Die DNS-Einträge einstellen fehlgeschlagen. Kontaktieren Sie uns.';
$LANG['warning editwhois modification awaiting approval']        = 'Kontaktangaben wurden zu %s geändert und anhängige.';
$LANG['warning editdnszone modification awaiting approval']      = 'Die DNS-Einträge werden auf %s geändert und anhängige.';
$LANG['warning changenameserver modification awaiting approval'] = 'Die Nameserver werden auf %s geändert und anhängige.';
$LANG['warning editwhois modification error']                    = 'Kontaktangaben wurden zu %s geändert, die automatische Verarbeitung ist jedoch fehlgeschlagen. Kontaktieren Sie uns für weitere Informationen.';
$LANG['warning editdnszone modification error']                  = 'Die DNS-Einträge werden auf %s geändert, die automatische Verarbeitung ist jedoch fehlgeschlagen. Kontaktieren Sie uns für weitere Informationen.';
$LANG['warning changenameserver modification error']             = 'Die Nameserver werden auf %s geändert, die automatische Verarbeitung ist jedoch fehlgeschlagen. Kontaktieren Sie uns für weitere Informationen.';
$LANG['record name may not end with dot']                        = 'DNS-Eintrag Name darf nicht mit einem Ende \'.\'';
$LANG['record name may not contain domain']                      = 'DNS-Eintrag Name kann nicht der Domain selbst enthalten';
$LANG['record name may not contain spaces']                      = 'DNS-Eintrag Name darf keine Leerzeichen enthalten';
$LANG['record ttl incorrect data']                               = 'DNS-TTL-Eintrag muss nur Zahlen enthalten und muss größer als 0';
$LANG['record priority incorrect data']                          = 'DNS-Eintrag Priorität kann nur Zahlen zwischen 0 und 100 enthalten';
$LANG['record value may not be empty']                           = 'DNS-Eintrag Wert darf nicht leer sein';
$LANG['record value invalid ipv4']                               = 'DNS-Eintrag vom Typ A hat einen Wert als gültige IPv4-Adresse';
$LANG['record value invalid ipv6']                               = 'DNS-Eintrag vom Typ AAAA hat einen Wert als gültige IPv6-Adresse';


/** STATUS */
$LANG['array_domainstatus'] = array(
	"-1" => "Warte auf Bearbeitung",
	"1"  => "Warte auf Bearbeitung",
	"3"  => "In Bearbeitung",
	"4"  => "Aktiv",
	"5"  => "Abgelaufen",
	"6"  => "In Bearbeitung",
	"7"  => "In Bearbeitung",
	"8"  => "Gekündigt");

