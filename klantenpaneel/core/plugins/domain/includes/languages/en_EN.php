<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

$LANG['mainmenu domain'] = 'Domain names';


/** URLS */
$LANG['url']['domain']        = 'domainnames';
$LANG['url']['nameservers']   = 'nameservers';
$LANG['url']['getHosting']    = 'hostingaccount';
$LANG['url']['getToken']      = 'transfer';
$LANG['url']['whois']         = 'contact-information';
$LANG['url']['dnsmanagement'] = 'dns-management';


/** PAGES */
$LANG['domains']                                  = 'Domain names';
$LANG['domain']                                   = 'Domain name';
$LANG['hosting account']                          = 'Hosting account';
$LANG['hosting package']                          = 'Hosting package';
$LANG['registration date']                        = 'Registration date';
$LANG['expiration date']                          = 'Expires on';
$LANG['renewal date']                             = 'Renewal date';
$LANG['autorenew']                                = 'Auto renew?';
$LANG['nameservers']                              = 'Nameservers';
$LANG['nameserver 1']                             = 'Nameserver 1';
$LANG['nameserver 2']                             = 'Nameserver 2';
$LANG['nameserver 3']                             = 'Nameserver 3';
$LANG['nameserver 1 ip']                          = 'Nameserver 1 IP address';
$LANG['nameserver 2 ip']                          = 'Nameserver 2 IP address';
$LANG['nameserver 3 ip']                          = 'Nameserver 3 IP address';
$LANG['change nameservers']                       = 'Change nameservers';
$LANG['change whois']                             = 'Change contact information';
$LANG['transfer domain']                          = 'Get authorization code';
$LANG['dns management']                           = 'DNS management';
$LANG['new domain']                               = 'New domain name';
$LANG['handle data']                              = 'Contact information';
$LANG['handle owner']                             = 'Owner contact';
$LANG['handle admin']                             = 'Administrative contact';
$LANG['handle tech']                              = 'Technical contact';
$LANG['check domain availability']                = 'Check if your domain name is still available';
$LANG['domain does not exist']                    = 'The domain name does not exist (anymore).';
$LANG['nameservers update succeed']               = 'Nameservers have been changed.';
$LANG['nameservers invalid']                      = 'One or more nameservers are invalid';
$LANG['nameservers ipaddress invalid']            = 'An invalid IP address has been entered';
$LANG['nameservers update failed']                = 'Unable to change the nameservers. Please contact us.';
$LANG['whois edit succeed']                       = 'The contact information has been changed.';
$LANG['whois edit failed']                        = 'Unable to change the contact information. Please contact us.';
$LANG['token could not be retrieved, contact us'] = 'Could not retrieve the authorization code for your domain name. Please contact us.';
$LANG['used bandwidth']                           = 'Used bandwidth';
$LANG['used diskspace']                           = 'Used disk space';


/** DNS zone */
$LANG['no dns records given']                                    = 'No DNS records entered';
$LANG['change dns records']                                      = 'Change DNS records';
$LANG['dns zone could not be retrieved']                         = 'The current DNS records could not be retrieved.';
$LANG['dns zone records']                                        = 'DNS records';
$LANG['add dns record']                                          = 'Add DNS record';
$LANG['delete dns record']                                       = 'Delete record';
$LANG['dns record name']                                         = 'Name';
$LANG['dns record type']                                         = 'Type';
$LANG['dns record value']                                        = 'Value';
$LANG['dns record priority']                                     = 'Priority';
$LANG['dns record ttl']                                          = 'TTL';
$LANG['edit dns zone succeed']                                   = 'The DNS record have been changed.';
$LANG['edit dns zone failed']                                    = 'Could not change the DNS records. Please contact us.';
$LANG['warning editwhois modification awaiting approval']        = 'Contact information changed on %s and is in process.';
$LANG['warning editdnszone modification awaiting approval']      = 'DNS records changed on %s and are in process.';
$LANG['warning changenameserver modification awaiting approval'] = 'Nameservers changed on %s and are in process.';
$LANG['warning editwhois modification error']                    = 'Contact information changed on %s, but automated processing failed. Please contact us for more information.';
$LANG['warning editdnszone modification error']                  = 'DNS records changed on %s, but automated processing failed. Please contact us for more information.';
$LANG['warning changenameserver modification error']             = 'Nameservers changed on %s, but automated processing failed. Please contact us for more information.';
$LANG['record name may not end with dot']                        = 'DNS record name cannot end with \'.\'';
$LANG['record name may not contain domain']                      = 'DNS record name cannot contain the domain name';
$LANG['record name may not contain spaces']                      = 'DNS record name cannot contain spaces';
$LANG['record ttl incorrect data']                               = 'DNS record TTL can only contain numbers and must be larger dan 0';
$LANG['record priority incorrect data']                          = 'DNS record priority can only contain numbers between 0 and 100';
$LANG['record value may not be empty']                           = 'DNS record value cannot be empty';
$LANG['record value invalid ipv4']                               = 'DNS record with type A must have a valid IPv4 address as value';
$LANG['record value invalid ipv6']                               = 'DNS record with type AAAA must have a valid IPv6 address as value';


/** STATUS */
$LANG['array_domainstatus'] = array(
	"-1" => "In process",
	"1"  => "In process",
	"3"  => "In process",
	"4"  => "Active",
	"5"  => "Expired",
	"6"  => "In process",
	"6"  => "In process",
	"7"  => "In process",
	"8"  => "Terminated");

