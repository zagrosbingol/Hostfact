<?php


$_LANG = array();
$_LANG['language title'] = 'English';

/**
 * WHOIS form
 */
// views/whois/domain_form.phtml
$_LANG['whois_btn'] = 'Check availability';

// views/whois/header.phtml
$_LANG['whois page title'] = 'Choose a domain';
$_LANG['check domain'] = 'Check';

// views/whois/result_table.phtml
$_LANG['to orderform'] = 'Order';
$_LANG['whois resulttable domain'] = 'Domain name';
$_LANG['whois resulttable result'] = 'Result';
$_LANG['whois resulttable price'] = 'Price';
$_LANG['whois resulttable period'] = '&nbsp;';
$_LANG['no products in domain productgroup'] = 'No products linked to the productgroup';
$_LANG['show more tlds'] = 'Show more domain names';

// controllers/whois_controller.php
$_LANG['whois status available'] = 'available';
$_LANG['whois status unavailable'] = 'unavailable';
$_LANG['whois status error'] = 'unknown';
$_LANG['whois status invalid'] = 'invalid';

$_LANG['whois link available'] = 'register';
$_LANG['whois link unavailable'] = 'transfer';
$_LANG['whois link error'] = 'order';

$_LANG['in shopping cart'] = 'in cart';

// models/whois_model.php   * some translations are also used in models/domain_model.php
$_LANG['domain name required for a existing account'] = 'No domain name entered.';
$_LANG['could not connect to whois server'] = 'Could not connect to WHOIS server: %s';
$_LANG['unknown whois server'] = 'No WHOIS server found for %s';
$_LANG['no domain entered'] = 'Please enter a domain name.';
$_LANG['sld must be between 2 and 63 characters'] = 'A domain name needs to be between 2 and 63 characters.';
$_LANG['sld should not contain dots'] = 'Please do not fill in a subdomain (no dots in the domain name).';
$_LANG['sld contains invalid characters'] = 'The domain name contains invalid characters.';
$_LANG['tld not available'] = 'The selected TLD is not available. Please make a choice from the following available TLDs.';

/**
 * Order form - controllers
 */
// controllers/domainform_controller.php
$_LANG['authkey for domain is required'] = 'Authorization code for domain name %s is required.';
$_LANG['you need one of the hosting packages to select'] = 'Please select a hosting package.';
$_LANG['link to hosting account'] = 'Link domain name(s) to hosting account %s.';
$_LANG['you need at least two nameservers'] = 'You need to fill in at least two nameservers.';

// controllers/hostingform_controller.php
$_LANG['current domain description'] = 'Current domain name: %s';

// controllers/orderform_controller.php
$_LANG['you need to select a product'] = 'Please select a product.';
$_LANG['you must agree to the terms and conditions'] = 'You must accept the terms and conditions to continue';
$_LANG['please select a payment method'] = 'Please choose a payment method.';
$_LANG['please select a bank'] = 'Please select your bank.';
$_LANG['you must agree to the authorization'] = 'You must check the checkbox for direct debit';
$_LANG['confirmation of your order from'] = 'Your order at %s';

/**
 * Order form - models
 */
// models/customer_model.php
$_LANG['invalid username'] = 'Incorrect username';
$_LANG['the username already exists'] = 'This username already exists';
$_LANG['invalid password'] = 'Invalid password';
$_LANG['invalid companyname'] = 'Incorrect company name';
$_LANG['invalid companynumber'] = 'Incorrect Chamber of Commerce number';
$_LANG['invalid taxnumber'] = 'No valid tax number';
$_LANG['invalid gender'] = 'No valid gender';
$_LANG['invalid initials'] = 'Incorrect initials';
$_LANG['invalid surname'] = 'Incorrect surname';
$_LANG['invalid address'] = 'Incorrect address';
$_LANG['invalid zipcode'] = 'Incorrect zipcode';
$_LANG['invalid city'] = 'Incorrect city';
$_LANG['invalid state'] = 'Incorrect state';
$_LANG['invalid country'] = 'Please select your country';
$_LANG['invalid emailaddress'] = 'Invalid emailaddress';
$_LANG['invalid phonenumber'] = 'Phone number contains invalid or too many characters';
$_LANG['invalid mobile number'] = 'Mobile number contains invalid or too many characters';
$_LANG['invalid faxnumber'] = 'Fax number contains invalid or too many characters';
$_LANG['invalid invoicemethod'] = 'No valid invoice method selected';
$_LANG['invalid authorization value'] = 'Incorrect direct debit information';
$_LANG['invalid custom invoice template'] = 'Incorrect value for deviant invoice template';
$_LANG['invalid custom pricequote template'] = 'Incorrect value for deviant estimate template';
$_LANG['invalid invoice sex'] = 'Incorrect sex for billing address';
$_LANG['invalid invoice initials'] = 'Incorrect initials for billing address';
$_LANG['invalid invoice surname'] = 'Incorrect surname for billing address';
$_LANG['invalid invoice address'] = 'Incorrect address for billing address';
$_LANG['invalid invoice zipcode'] = 'Incorrect zipcode for billing address';
$_LANG['invalid invoice city'] = 'Incorrect city for billing address';
$_LANG['invalid invoice state'] = 'Incorrect state for billing address';
$_LANG['invalid invoice country'] = 'Please select your country for billing address';
$_LANG['invalid invoice emailaddress'] = 'Invalid billing email address';
$_LANG['invalid accountnumber'] = 'Invalid bank account number';
$_LANG['invalid iban'] = 'Invalid bank account number';
$_LANG['invalid bic'] = 'Invalid BIC';
$_LANG['invalid accountname'] = 'Invalid bank account holder';
$_LANG['invalid bank'] = 'Invalid bank name';
$_LANG['invalid account city'] = 'Invalid bank city';
$_LANG['no companyname and no surname'] = 'Please fill in a company name or surname';
$_LANG['custom client fields regex'] = 'Incorrect value for %s';
$_LANG['no accountnumber given'] = 'No bank account number given';
$_LANG['no phonenumber given'] = 'No phone number given';

// models/database_model.php
$_LANG['error in mysql query'] = 'Error in processing';

// models/debtor_model.php
$_LANG['invalid login credentials'] = 'Invalid login details';
$_LANG['invalid debtor id'] = 'Error when retrieving client information';

// models/domain_model.php * some translations can be found in WHOIS-part of this file

// models/hosting_model.php
$_LANG['could not generate new accountname based on company name'] = 'Unable to generate a new hosting accountname';
$_LANG['could not generate new accountname based on debtor name'] = 'Unable to generate a new hosting accountname';
$_LANG['could not generate new accountname based on debtor'] = 'Unable to generate a new hosting accountname';
$_LANG['could not generate new accountname based on domain'] = 'Unable to generate a new hosting accountname';
$_LANG['could not generate new accountname'] = 'Unable to generate a new hosting accountname';

// models/order_model.php * some translations can be found in customer_model-part of this file
$_LANG['discountpercentage on product'] = '%s%% product discount';
$_LANG['ordercode already in use'] = 'Order number is already in use.';
$_LANG['could not found debtor data'] = 'Cannot retrieve client information.';
$_LANG['no products in order'] = 'No products found in your cart.';
$_LANG['could not generate ordercode'] = 'Could not generate new order id.';

// models/setting_model.php
$_LANG['gender male'] = 'Mr.';
$_LANG['gender female'] = 'Ms.';
$_LANG['gender department'] = 'Dep.';
$_LANG['gender unknown'] = 'Unknown';

/**
 * Order form - views
 */
// views/domain/elements/domain_table.phtml
$_LANG['domaintable domain'] = 'Domain name';
$_LANG['domaintable result'] = 'Result';
$_LANG['domaintable price'] = 'Price';
$_LANG['domaintable period'] = '&nbsp;';
$_LANG['authkey'] = 'authorization code';
$_LANG['domain status available'] = 'register';
$_LANG['domain status unavailable'] = 'transfer';
$_LANG['domain status error'] = 'order';
$_LANG['add another domain'] = 'Add another domain name';

// views/domain/details.phtml
$_LANG['domains'] = 'Domain names';
$_LANG['hosting'] = 'Hosting';
$_LANG['order a hosting account'] = 'Order hosting package';
$_LANG['i already have a hosting account'] = 'I already have a hosting account at %s';
$_LANG['order domains only'] = 'I only want a domain name';
$_LANG['current domain'] = 'Current domain name';
$_LANG['use own nameservers'] = 'Use my own nameservers';
$_LANG['nameserver 1'] = 'Nameserver 1';
$_LANG['nameserver 2'] = 'Nameserver 2';
$_LANG['nameserver 3'] = 'Nameserver 3';
$_LANG['button to customerdata'] = 'Client information &raquo;';

// views/domain/start.phtml
$_LANG['choose your domain'] = 'Choose your domain name';
$_LANG['to shopping cart'] = 'To cart';

// views/elements/billingperiod.phtml
$_LANG['billing period'] = 'Billing period';

// views/elements/errors.phtml
$_LANG['error message'] = 'Error:';

// views/elements/options.phtml
$_LANG['options'] = 'Addons';

// views/hosting/elements/hosting_new.phtml
$_LANG['no products in productgroup'] = 'No products linked to the product group';
$_LANG['default domain'] = 'Default domain name';

// views/hosting/elements/hosting_new_simple.phtml
$_LANG['hosting package'] = 'Hosting package';
$_LANG['please choose'] = '- Make a choice -';

// views/hosting/details.phtml
$_LANG['order new domains'] = 'Order a new domain or transfer a domain';
$_LANG['i already have a domain'] = 'I already have a domain which I don\'t want to transfer';
$_LANG['domain'] = 'Domain name';

// views/hosting/start.phtml
$_LANG['choose your domain for hosting'] = 'Please choose a domain name for your hosting package';

// views/completed.phtml
$_LANG['thanks for your order'] = 'Thank you for your order';
$_LANG['we have successfully received your order'] = 'We succesfully received your order.';
$_LANG['for confirmation, we send an e-mail containing a summary of your order'] = 'A summary of your order has been sent by email.';
$_LANG['online payment'] = 'Pay online';
$_LANG['you have chosen to pay online via'] = 'You have chosen to pay online by %s';
$_LANG['click here to pay'] = 'Click here to pay';
$_LANG['if you have any questions, please contact us'] = 'If you have any questions, please don\'t hesitate to contact us.';

// views/customer.phtml
$_LANG['customer data'] = 'Client information';
$_LANG['i am already a customer'] = 'I\'m already an existing %s client';
$_LANG['companyname'] = 'Company name';
$_LANG['companynumber'] = 'CoC number';
$_LANG['taxnumber'] = 'Tax number';
$_LANG['legalform'] = 'Legalform';
$_LANG['contact person'] = 'Contact person';
$_LANG['address'] = 'Address';
$_LANG['zipcode and city'] = 'Zipcode and city';
$_LANG['state'] = 'State';
$_LANG['country'] = 'Country';
$_LANG['phonenumber'] = 'Phone number';
$_LANG['emailaddress'] = 'Email address';
$_LANG['debtorcode'] = 'Client number';
$_LANG['logout'] = 'logout';
$_LANG['your companyname'] = 'Your company name';
$_LANG['your name'] = 'Your name';
$_LANG['username'] = 'Username';
$_LANG['password'] = 'Password';
$_LANG['login'] = 'login';
$_LANG['use custom invoice address'] = 'Different billing address';
$_LANG['use custom data for domain owner'] = 'Use different domain owner information';
$_LANG['custom invoice address'] = 'Billing address';
$_LANG['domain owner'] = 'Domain owner';
$_LANG['use domain contact'] = 'Use contact';
$_LANG['create a new domain contact'] = '- Create a new contact -';
$_LANG['choose your payment method'] = 'Choose your payment method';
$_LANG['your accountnumber'] = 'Your bank account number';
$_LANG['iban'] = 'Bank account number';
$_LANG['bic'] = 'BIC number';
$_LANG['account name'] = 'Bank account holder';
$_LANG['account city'] = 'Bank city';
$_LANG['i authorize for the total amount'] = 'I authorize %s to collect the total amount of this order from my bank account';
$_LANG['comment'] = 'Comment';
$_LANG['button back to cart'] = '&laquo; Cart';
$_LANG['button to overview'] = 'Order details &raquo;';

// views/details.phtml
$_LANG['choose your product'] = 'Choose your product';
$_LANG['product'] = 'Product';

// views/header.phtml
$_LANG['order page title'] = 'Order';

// views/onlinepayment.phtml
$_LANG['your payment is processed'] = 'Your payment is succesfully received';
$_LANG['transaction id'] = 'Transaction code';
$_LANG['we will process your order as soon as possible'] = 'We will process your order as soon as possible. Thank you for your trust in us,';

// views/overview.phtml
$_LANG['summary of your order'] = 'Order details';
$_LANG['overviewtable number'] = 'Number'; 
$_LANG['overviewtable description'] = 'Description';
$_LANG['overviewtable period'] = 'Period';
$_LANG['overviewtable amount excl'] = 'Amount excl. tax';
$_LANG['overviewtable amount incl'] = 'Amount incl. tax';
$_LANG['overviewtable amount'] = 'Amount';
$_LANG['enter discount coupon'] = '+ add discount code';
$_LANG['discount coupon'] = 'Discount code';
$_LANG['discount check coupon'] = 'apply';
$_LANG['percentage discount'] = '%s%% discount';
$_LANG['subtotal'] = 'Subtotal';
$_LANG['vat'] = 'Tax';
$_LANG['total incl'] = 'Total due';
$_LANG['total'] = 'Order total';
$_LANG['your customerdata'] = 'Client information';
$_LANG['your invoiceaddress'] = 'Billing address';
$_LANG['payment method'] = 'Payment method';
$_LANG['terms and conditions'] = 'Terms and conditions';
$_LANG['i agree with the terms and conditions'] = 'I agree with the %s and am aware of its content.';
$_LANG['download terms and conditions'] = 'Download the terms and conditions';
$_LANG['button back to customerdata'] = '&laquo; Client information';
$_LANG['button to completed'] = 'Complete order &raquo;';
$_LANG['footer prices are including tax'] = 'All prices are tax included';
$_LANG['footer prices are excluding tax'] = 'All prices are tax excluded ';

/**
 * Arrays
 */
$_LANG['per'] = 'per';
$_LANG['array_periods'][''] = 'once';
$_LANG['array_periods']['d'] = 'day';
$_LANG['array_periods']['w'] = 'week';
$_LANG['array_periods']['m'] = 'month';
$_LANG['array_periods']['k'] = 'quarter';
$_LANG['array_periods']['h'] = 'half year';
$_LANG['array_periods']['j'] = 'year';
$_LANG['array_periods']['t'] = 'two years';

$_LANG['array_periods_plural']['d'] = 'days';
$_LANG['array_periods_plural']['w'] = 'weeks';
$_LANG['array_periods_plural']['m'] = 'months';
$_LANG['array_periods_plural']['k'] = 'quarters';
$_LANG['array_periods_plural']['h'] = 'half years';	
$_LANG['array_periods_plural']['j'] = 'years';
$_LANG['array_periods_plural']['t'] = 'two years';	