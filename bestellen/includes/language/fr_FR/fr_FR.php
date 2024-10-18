<?php


$_LANG = array();
$_LANG['language title'] = 'Français';

/**
 * WHOIS form
 */
// views/whois/domain_form.phtml
$_LANG['whois_btn'] = 'Vérifiez la disponibilité';

// views/whois/header.phtml
$_LANG['whois page title'] = 'Choisissez un nom de domaine';
$_LANG['check domain'] = 'Vérifiez';

// views/whois/result_table.phtml
$_LANG['to orderform'] = 'Commander';
$_LANG['whois resulttable domain'] = 'Nom de domaine';
$_LANG['whois resulttable result'] = 'Resultat';
$_LANG['whois resulttable price'] = 'Prix';
$_LANG['whois resulttable period'] = '&nbsp;';
$_LANG['no products in domain productgroup'] = 'Aucun produit ne correspond au groupe';
$_LANG['show more tlds'] = 'Affichez plus de noms de domaine';

// controllers/whois_controller.php
$_LANG['whois status available'] = 'disponible';
$_LANG['whois status unavailable'] = 'non disponible';
$_LANG['whois status error'] = 'inconnu';
$_LANG['whois status invalid'] = 'invalide';

$_LANG['whois link available'] = 'enregistrer';
$_LANG['whois link unavailable'] = 'transférer';
$_LANG['whois link error'] = 'commander';

$_LANG['in shopping cart'] = 'dans le caddy';

// models/whois_model.php   * some translations are also used in models/domain_model.php
$_LANG['domain name required for a existing account'] = 'Il n\'ya pas de domaine spécifié.';
$_LANG['could not connect to whois server'] = 'Ne peut se connecter au serveur WHOIS : %s';
$_LANG['unknown whois server'] = 'Aucun serveur WHOIS trouvé pour %s';
$_LANG['no domain entered'] = 'Entrez un nom de domaine.';
$_LANG['sld must be between 2 and 63 characters'] = 'Un nom de domaine doit faire entre 2 et 63 caractères.';
$_LANG['sld should not contain dots'] = 'N\'entrez pas un sous-domaine (pas de point dans le nom de domaine).';
$_LANG['sld contains invalid characters'] = 'Le nom de domaine saisi contient des caractères invalides.';
$_LANG['tld not available'] = 'L\'extension n\'est pas disponible, veuillez choisir une extension disponible.';

/**
 * Order form - controllers
 */
// controllers/domainform_controller.php
$_LANG['authkey for domain is required'] = 'Le code d\'autorisation est requis pour le nom de domaine %s.';
$_LANG['you need one of the hosting packages to select'] = 'Veuillez choisir un pack hébergement.';
$_LANG['link to hosting account'] = 'Veuillez lier un nom de domaine au compte d\'hébergement %s.';
$_LANG['you need at least two nameservers'] = 'Vous devez entrer au moins 2 serveurs DNS.';

// controllers/hostingform_controller.php
$_LANG['current domain description'] = 'Nom de domaine concerné : %s';

// controllers/orderform_controller.php
$_LANG['you need to select a product'] = 'Veuillez sélectionner un produit.';
$_LANG['you must agree to the terms and conditions'] = 'Vous devez accepter les présentes CGV pour continuer';
$_LANG['please select a payment method'] = 'Veuillez choisir un moyen de paiement.';
$_LANG['please select a bank'] = 'Merci de sélectionner votre banque.';
$_LANG['you must agree to the authorization'] = 'Vous devez cocher la case à cocher pour un débit direct';
$_LANG['confirmation of your order from'] = 'Votre commande %s';

/**
 * Order form - models
 */
// models/customer_model.php
$_LANG['invalid username'] = 'Identifiant invalide';
$_LANG['the username already exists'] = 'Ce nom d\'utilisateur existe déjà';
$_LANG['invalid password'] = 'Mot de passe invalide';
$_LANG['invalid companyname'] = 'Nom de société invalide';
$_LANG['invalid companynumber'] = 'Numéro de RCS invalide';
$_LANG['invalid taxnumber'] = 'Numéro de TVA invalide';
$_LANG['invalid gender'] = 'Genre invalide';
$_LANG['invalid initials'] = 'Prénom invalides';
$_LANG['invalid surname'] = 'Nom et prénom invalide';
$_LANG['invalid address'] = 'Adresse invalide';
$_LANG['invalid zipcode'] = 'Code postal invalide';
$_LANG['invalid city'] = 'Ville invalide';
$_LANG['invalid state'] = 'Etat invalide';
$_LANG['invalid country'] = 'Veuillez entrer votre ville';
$_LANG['invalid emailaddress'] = 'Adresse mail invalide';
$_LANG['invalid phonenumber'] = 'Le numéro de téléphone contient des caractères invalides ou a trop de caractères';
$_LANG['invalid mobile number'] = 'Le numéro de mobile contient des caractères invalides ou a trop de caractères';
$_LANG['invalid faxnumber'] = 'Le numéro de fax contient des caractères invalides ou a trop de caractères';
$_LANG['invalid invoicemethod'] = 'Veuillez sélectionner une méthode de facturation';
$_LANG['invalid authorization value'] = 'Information de débit direct incorrect';
$_LANG['invalid custom invoice template'] = 'Modèle de facture invalide';
$_LANG['invalid custom pricequote template'] = 'Modèle de devis invalide';
$_LANG['invalid invoice sex'] = 'Sexe incorrectes pour l\'adresse de facturation';
$_LANG['invalid invoice initials'] = 'Prénom incorrectes pour l\'adresse de facturation';
$_LANG['invalid invoice surname'] = 'Nom incorrect pour l\'adresse de facturation';
$_LANG['invalid invoice address'] = 'Adresse incorrecte pour l\'adresse de facturation';
$_LANG['invalid invoice zipcode'] = 'Code postal incorrect pour l\'adresse de facturation';
$_LANG['invalid invoice city'] = 'Ville incorrecte pour l\'adresse de facturation';
$_LANG['invalid invoice state'] = 'Etat incorrect pour l\'adresse de facturation';
$_LANG['invalid invoice country'] = 'Entrez la ville pour l\'adresse de facturation';
$_LANG['invalid invoice emailaddress'] = 'Adresse mail invalide pour le contact de facturation';
$_LANG['invalid accountnumber'] = 'Numéro de compte en banque invalide';
$_LANG['invalid iban'] = 'Numéro de compte en banque (IBAN) invalide';
$_LANG['invalid bic'] = 'Numéro de compte en banque (BIC) invalide';
$_LANG['invalid accountname'] = 'Nom du détenteur du compte en banque incorrect';
$_LANG['invalid bank'] = 'Nom de la banque incorrect';
$_LANG['invalid account city'] = 'Ville de la banque invalide';
$_LANG['no companyname and no surname'] = 'Veuillez entrer le nom de la société';
$_LANG['custom client fields regex'] = '%s invalide';
$_LANG['no phonenumber given'] = 'Veuillez entrer le numéro de téléphone';

// models/database_model.php
$_LANG['error in mysql query'] = 'Erreur de traitement';

// models/debtor_model.php
$_LANG['invalid login credentials'] = 'Identifiants de connexion incorrects';
$_LANG['invalid debtor id'] = 'Erreur lors de la récupération des informations client';

// models/domain_model.php * some translations can be found in WHOIS-part of this file

// models/hosting_model.php
$_LANG['could not generate new accountname based on company name'] = 'Impossible de générer un nouveau compte d\'hébergement';
$_LANG['could not generate new accountname based on debtor name'] = 'Impossible de générer un nouveau compte d\'hébergement';
$_LANG['could not generate new accountname based on debtor'] = 'Impossible de générer un nouveau compte d\'hébergement';
$_LANG['could not generate new accountname based on domain'] = 'Impossible de générer un nouveau compte d\'hébergement';
$_LANG['could not generate new accountname'] = 'Impossible de générer un nouveau compte d\'hébergement';

// models/order_model.php * some translations can be found in customer_model-part of this file
$_LANG['discountpercentage on product'] = '%s%% de remise';
$_LANG['ordercode already in use'] = 'Numéro de commande déjà utilisé.';
$_LANG['could not found debtor data'] = 'Impossible de récupérer les informations client.';
$_LANG['no products in order'] = 'Aucun produit dans votre caddy.';
$_LANG['could not generate ordercode'] = 'Impossible de générer un nouveau numéro de commande.';

// models/setting_model.php
$_LANG['gender male'] = 'M.';
$_LANG['gender female'] = 'Mme';
$_LANG['gender department'] = 'Dép.';
$_LANG['gender unknown'] = 'Inconnue';

/**
 * Order form - views
 */
// views/domain/elements/domain_table.phtml
$_LANG['domaintable domain'] = 'Nom de domaine';
$_LANG['domaintable result'] = 'Résultat';
$_LANG['domaintable price'] = 'Prix';
$_LANG['domaintable period'] = '&nbsp;';
$_LANG['authkey'] = 'Code d\'autorisation';
$_LANG['domain status available'] = 'enregistrer';
$_LANG['domain status unavailable'] = 'transférer';
$_LANG['domain status error'] = 'commander';
$_LANG['add another domain'] = 'Ajouter un nom de domaine';

// views/domain/details.phtml
$_LANG['domains'] = 'Noms de domaine';
$_LANG['hosting'] = 'Hébergement';
$_LANG['order a hosting account'] = 'Commander un pack hébergement';
$_LANG['i already have a hosting account'] = 'J\'ai déjà un hébergement chez %s';
$_LANG['order domains only'] = 'Je veux uniquement un nom de domaine';
$_LANG['current domain'] = 'Nom de domaine actuel';
$_LANG['use own nameservers'] = 'Utiliser mes serveurs DNS';
$_LANG['nameserver 1'] = 'Serveur DNS 1';
$_LANG['nameserver 2'] = 'Serveur DNS 2';
$_LANG['nameserver 3'] = 'Serveur DNS 3';
$_LANG['button to customerdata'] = 'Information client &raquo;';

// views/domain/start.phtml
$_LANG['choose your domain'] = 'Choisissez votre nom de domaine';
$_LANG['to shopping cart'] = 'Voir mon caddy';

// views/elements/billingperiod.phtml
$_LANG['billing period'] = 'Périodicité de facturation';

// views/elements/errors.phtml
$_LANG['error message'] = 'Erreur:';

// views/elements/options.phtml
$_LANG['options'] = 'Addons';

// views/hosting/elements/hosting_new.phtml
$_LANG['no products in productgroup'] = 'Aucun produit ne correspond au groupe';
$_LANG['default domain'] = 'Nom de domaine par défaut';

// views/hosting/elements/hosting_new_simple.phtml
$_LANG['hosting package'] = 'Pach d\'hébergement';
$_LANG['please choose'] = '- Veuillez choisir -';

// views/hosting/details.phtml
$_LANG['order new domains'] = 'Commander ou transférer un nom de domaine';
$_LANG['i already have a domain'] = 'J\'ai déjà un nom de domaine que je ne souhaite pas transférer';
$_LANG['domain'] = 'Nom de domaine';

// views/hosting/start.phtml
$_LANG['choose your domain for hosting'] = 'Veuillez choisir un nom de domaine pour votre pack hébergement';

// views/completed.phtml
$_LANG['thanks for your order'] = 'Merci pour votre commande';
$_LANG['we have successfully received your order'] = 'Nous avons bien reçu votre commande.';
$_LANG['for confirmation, we send an e-mail containing a summary of your order'] = 'Un résumé de votre commande vous a été adressé par mail.';
$_LANG['online payment'] = 'Paiement en ligne';
$_LANG['you have chosen to pay online via'] = 'Vous avez choisi de payer en ligne via %s';
$_LANG['click here to pay'] = 'Cliquez ici pour payer';
$_LANG['if you have any questions, please contact us'] = 'Si vous avez la moindre question, n\'hésitez pas à nous contacter.';

// views/customer.phtml
$_LANG['customer data'] = 'Information client';
$_LANG['i am already a customer'] = 'Je suis déjà client %s';
$_LANG['companyname'] = 'Société';
$_LANG['companynumber'] = 'Numéro SIRET';
$_LANG['taxnumber'] = 'Numéro de TVA';
$_LANG['legalform'] = 'Forme juridique';
$_LANG['contact person'] = 'Nom et prénom';
$_LANG['address'] = 'Adresse';
$_LANG['zipcode and city'] = 'Code postal et ville';
$_LANG['state'] = 'Etat';
$_LANG['country'] = 'Pays';
$_LANG['phonenumber'] = 'Téléphone';
$_LANG['emailaddress'] = 'Email';
$_LANG['debtorcode'] = 'Numéro de client';
$_LANG['logout'] = 'Déconnexion';
$_LANG['your companyname'] = 'Nom de votre société';
$_LANG['your name'] = 'Votre nom';
$_LANG['username'] = 'Nom d\'utilisateur';
$_LANG['password'] = 'Mot de passe';
$_LANG['login'] = 'Identifiant';
$_LANG['use custom invoice address'] = 'Adresse de facturation différente';
$_LANG['use custom data for domain owner'] = 'Utilisez d\'autres informations que celles du propriétaire du domaine';
$_LANG['custom invoice address'] = 'Adresse de facturation';
$_LANG['domain owner'] = 'Propriétaire du nom de domaine';
$_LANG['use domain contact'] = 'Contact';
$_LANG['create a new domain contact'] = '- Créer un nouveau contact -';
$_LANG['choose your payment method'] = 'Choisissez votre moyen de paiement';
$_LANG['your accountnumber'] = 'Votre numéro de compte en banque';
$_LANG['iban'] = 'Votre numéro (IBAN) de compte en banque';
$_LANG['bic'] = 'Votre numéro (BIC) de compte en banque';
$_LANG['account name'] = 'Nom du titulaire du compte bancaire';
$_LANG['account city'] = 'Ville de la banque';
$_LANG['i authorize for the total amount'] = 'J\'autorise %s à prélever la somme totale de cette commande de mon compte en banque ';
$_LANG['comment'] = 'Commentaires';
$_LANG['button back to cart'] = '&laquo; Caddy';
$_LANG['button to overview'] = 'Détails de la commande &raquo;';

// views/details.phtml
$_LANG['choose your product'] = 'Veuillez choisir votre produit';
$_LANG['product'] = 'Produit';

// views/header.phtml
$_LANG['order page title'] = 'Commander';

// views/onlinepayment.phtml
$_LANG['your payment is processed'] = 'Nous avons bien reçu votre paiement';
$_LANG['transaction id'] = 'Identifiant de la transaction';
$_LANG['we will process your order as soon as possible'] = 'Nous allons nous charger de votre commande aussi rapidement que possible, merci pour votre confiance';

// views/overview.phtml
$_LANG['summary of your order'] = 'Détails de la commande';
$_LANG['overviewtable number'] = 'Numéro'; 
$_LANG['overviewtable description'] = 'Description';
$_LANG['overviewtable period'] = 'Périodicité';
$_LANG['overviewtable amount excl'] = 'Total HT';
$_LANG['overviewtable amount incl'] = 'Total TTC';
$_LANG['overviewtable amount'] = 'Total';
$_LANG['enter discount coupon'] = '+ entrez le code de réduction';
$_LANG['discount coupon'] = 'Code de réduction';
$_LANG['discount check coupon'] = 'appliquer';
$_LANG['percentage discount'] = '%s%% de réduction';
$_LANG['subtotal'] = 'Total';
$_LANG['vat'] = 'TVA';
$_LANG['total incl'] = 'Total TTC';
$_LANG['total'] = 'Total de la commande';
$_LANG['your customerdata'] = 'Information client';
$_LANG['your invoiceaddress'] = 'Adresse de facturation';
$_LANG['payment method'] = 'Moyen de paiement';
$_LANG['terms and conditions'] = 'CGV (Conditions Générales de Vente)';
$_LANG['i agree with the terms and conditions'] = 'J\'accepte les %s et je certifie les avoir lues.';
$_LANG['download terms and conditions'] = 'Télécharger les CGV (Conditions Générales de Vente)';
$_LANG['button back to customerdata'] = '&laquo; Information client';
$_LANG['button to completed'] = 'Terminer la commande &raquo;';
$_LANG['footer prices are including tax'] = 'Tous les prix sont TTC';
$_LANG['footer prices are excluding tax'] = 'Tous les prix sont HT';

/**
 * Arrays
 */
$_LANG['per'] = 'par';
$_LANG['array_periods'][''] = 'une seule fois';
$_LANG['array_periods']['d'] = 'jour';
$_LANG['array_periods']['w'] = 'semaine';
$_LANG['array_periods']['m'] = 'mois';
$_LANG['array_periods']['k'] = 'trimestre';
$_LANG['array_periods']['h'] = 'semestre';
$_LANG['array_periods']['j'] = 'année';
$_LANG['array_periods']['t'] = '2 ans';

$_LANG['array_periods_plural']['d'] = 'jours';
$_LANG['array_periods_plural']['w'] = 'semainess';
$_LANG['array_periods_plural']['m'] = 'moiss';
$_LANG['array_periods_plural']['k'] = 'trimestres';
$_LANG['array_periods_plural']['h'] = 'semestres';	
$_LANG['array_periods_plural']['j'] = 'années';
$_LANG['array_periods_plural']['t'] = '2 ans';		