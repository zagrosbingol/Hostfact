<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

$LANG['mainmenu hosting'] = 'Hosting accounts';


/** URLS */
$LANG['url']['hosting']    = 'hosting';
$LANG['url']['getDomains'] = 'domains';
$LANG['url']['serverLogin']  = 'login-server';
$LANG['url']['singleSignOn'] = 'direkt-login-server';
$LANG['url']['changePassword'] = 'hosting-passwort-andern';

/** PAGES */
$LANG['hostingaccounts']        = 'Hosting accounts';
$LANG['hostingaccount']         = 'Hosting account';
$LANG['account']                = 'Account';
$LANG['package']                = 'Paket';
$LANG['bandwidth limit']        = 'Bandbreite';
$LANG['diskspace limit']        = 'Traffic';
$LANG['domain']                 = 'Domain';
$LANG['domains']                = 'Domains';
$LANG['new hosting account']    = 'Neue Hosting-Account';
$LANG['hosting does not exist'] = 'Das Hosting-Account is nicht (mehr) vorhanden.';
$LANG['used bandwidth']         = 'Genutzte Traffic';
$LANG['used diskspace']         = 'Genutzte webspace';

$LANG['login control panel']                       = 'Anmelden <br />Control Panel';
$LANG['you are being redirected to control panel'] = 'Sie werden an die Control Panel weitergeleitet';
$LANG['change password control panel']             = 'Passwort ändern';

$LANG['hosting change password - title'] 			= 'Passwort ändern';
$LANG['hosting change password - text'] 			= 'Hinweis: Das Passwort für das FTP, MySQL und E-Mail-Konto wird auch geändert.';
$LANG['hosting change password - confirm'] 			= 'Bestätigen Sie, indem Sie das Passwort Ihres Kundenbereichs ausfüllen.';
$LANG['hosting change password - success'] 			= 'Das Passwort wurde geändert.';
$LANG['new password too short'] 					= 'Das neue Passwort sollte mindestens 6 Zeichen haben.';
$LANG['random'] 									= 'willkürlich';

/** STATUS */
$LANG['array_hostingstatus'] = array(
	"-1" => "Warte auf Bearbeitung",
	"1"  => "Warte auf Bearbeitung",
	"2"  => "Warte auf Bearbeitung",
	"3"  => "Warte auf Bearbeitung",
	"4"  => "Aktiv",
	"5"  => "Suspended",
	"7"  => "In Bearbeitung",
	"8"  => "Gekündigt",
	"9"  => "Abgelaufen");
