<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

$LANG = array();

/** URLS */

// controllers
$LANG['url']['index']      = 'index';
$LANG['url']['debtor']     = 'client-details';
$LANG['url']['invoice']    = 'invoices';
$LANG['url']['pricequote'] = 'estimates';
$LANG['url']['order']      = 'orders';
$LANG['url']['login']      = 'login';
$LANG['url']['service']    = 'services';
$LANG['url']['serviceAll'] = 'all-services';

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
$LANG['url']['downloadAttachment'] = 'download-attachment';


/** GLOBAL MESSAGES (ERROR/WARNING/SUCCESS) */
$LANG['api connect error']                     		= "Error while connecting to server: HTTP code %s";
$LANG['api response no json']                     	= "Incorrect response from the server. Check the URLs specified in the back office.";
$LANG['api action failed, reason unknown']          = 'Action failed because of an unknown reason.';
$LANG['invalid emailaddress']                       = 'The entered email address is invalid.';
$LANG['no surname given']                           = "Please provide a surname";
$LANG['the requested action could not be executed'] = "The request could not be executed";
$LANG['changes successfully processed']             = "The changes are processed successfully";
$LANG['missing plugin file']                        = "A plugin file is missing";
$LANG['missing plugin file description']            = "The following file is missing: %s";
$LANG['downoad attachment failed']                  = "The requested file could not be found";
$LANG['max_input_vars reached']                     = "The number of form fields has reached the limit of our server. Please contact us.";


/** ACCOUNT/DEBTOR */
$LANG['general data']                                       = 'General information';
$LANG['billing data']                                       = 'Billing information';
$LANG['change billing data']                                = 'Change billing information';
$LANG['change password']                                    = 'Change password';
$LANG['two step authentication']                            = '2 step authentication setup';
$LANG['payment data']                                       = 'Payment information';
$LANG['change payment data']                                = 'Change information';
$LANG['name']                                               = 'Name';
$LANG['sex']                                                = 'Sex';
$LANG['gender male']                                        = 'Mr.';
$LANG['gender female']                                      = 'Mrs.';
$LANG['gender department']                                  = 'Dep.';
$LANG['initials']                                           = 'Initials';
$LANG['surname']                                            = 'Surname';
$LANG['address']                                            = 'Address';
$LANG['zipcode']                                            = 'Zipcode';
$LANG['city']                                               = 'City';
$LANG['state']                                              = 'State';
$LANG['country']                                            = 'Country';
$LANG['emailaddress']                                       = 'Email address';
$LANG['companyname']                                        = 'Company name';
$LANG['companynumber']                                      = 'Company number';
$LANG['legalform']                                          = 'Legal form';
$LANG['taxnumber']                                          = 'Tax number';
$LANG['phonenumber']                                        = 'Phone number';
$LANG['faxnumber']                                          = 'Fax number';
$LANG['mobilenumber']                                       = 'Mobile number';
$LANG['website']                                            = 'Website';
$LANG['second emailaddress']                                = 'Extra email address';
$LANG['authorisation']                                      = 'Direct debit';
$LANG['accountnumber']                                      = 'Bank account number';
$LANG['accountname']                                        = 'Account holder';
$LANG['accountbank']                                        = 'Bank';
$LANG['accountcity']                                        = 'City';
$LANG['accountbic']                                         = 'Bank code (BIC)';
$LANG['direct debit']                                       = 'Direct debit';
$LANG['debtor mailings']                                    = 'Receive mailing?';
$LANG['personal data']                                      = 'Contact person';
$LANG['company data']                                       = 'Company information';
$LANG['address data']                                       = 'Address information';
$LANG['contact data']                                       = 'Contact information';
$LANG['debtor preferences']                                 = 'Preferences';
$LANG['bank details']                                       = 'Bank information';
$LANG['username']                                           = 'Username';
$LANG['password']                                           = 'Password';
$LANG['current password']                                   = 'Current password';
$LANG['new password']                                       = 'New password';
$LANG['repeat password']                                    = 'Repeat password';
$LANG['repeat new password']                                = 'Repeat new password';
$LANG['accountnumber']                                      = 'Bank account number';
$LANG['accountname']                                        = 'Account holder';
$LANG['accountbank']                                        = 'Bank';
$LANG['accountcity']                                        = 'Bank city';
$LANG['AccountBIC']                                         = 'Bank code (BIC)';
$LANG['pay with direct debit']                              = 'I want to pay by direct debit';
$LANG['save data']                                          = 'Save information';
$LANG['debtor general data saved']                          = 'Your client information has been saved';
$LANG['debtor login data saved']                            = 'Your password has been changed. The next time you login, remember to use your new password.';
$LANG['no companyname and no surname are given']            = "No company name or surname entered";
$LANG['no password given']                                  = "No password entered";
$LANG['repeat password not correct']                        = "The password do not match";
$LANG['invalid bic']                                        = "Invalid bank code (BIC) entered";
$LANG['invalid iban']                                       = "Invalid bank account number entered";
$LANG['password should at least be 8 char']                 = "Your password should contain a least 8 characters";
$LANG['invalid current password']                           = "Your current password is incorrect";
$LANG['invalid state']                                      = "Invalid state";
$LANG['warning editgeneral modification awaiting approval'] = 'Your general information has been changed on %s and is waiting for approval.';
$LANG['warning editbilling modification awaiting approval'] = 'Your billing information has been changed on %s and is waiting for approval.';
$LANG['warning editpayment modification awaiting approval'] = 'Your payment information has been changed on %s and is waiting for approval.';
$LANG['two step authentication - text'] 					= '2-step authentication ensures that you have to fill in an additional authentication code besides your username and password. This authenticaiton code will be generated by one of the authentication apps which you can use on your smartphone or computer. Choose a "time based" within your application if it supports multiple types. The additional authentication procedure will increase the protection against intruders.';
$LANG['two step authentication - apps'] 					= 'Example apps that you can use';
$LANG['two step authentication - generate code'] 			= 'Generate code';
$LANG['two step authentication - generate code text'] 		= 'By generating a code you can create an account within your authentication app. Choose a "time based" within your application if it supports multiple types. In the future you will need an authentication code to log in.';
$LANG['two step authentication - generate code link']		= 'Click here to generate your code';
$LANG['two step authentication - activate text']			= '<strong>Note:</strong> You need to confirm the 2-step authentication code to activate it.';
$LANG['two step authentication - auth code']				= 'Authentication code';
$LANG['two step authentication - submit']					= 'Activate';
$LANG['two step authentication - already active']			= '2-step authentication is currently active.';
$LANG['two step authentication - deactivate link']			= 'Click here to deactivate setup 2-step authentication.';
$LANG['two step authentication - success']					= '2-step authentication has been set up.';
$LANG['two step authentication - error']					= 'Activation of the 2-step authentication failed because of an incorrect code.';
$LANG['two step authentication deactivate - success']		= '2-step authentication has been deactivated.';
$LANG['two step authentication deactivate - error']			= 'Deactivation of the 2-step authentication has failed.';
$LANG['two step authentication deactivate - title']			= 'Deactivate 2-step authentication';
$LANG['two step authentication deactivate - text']			= 'Confirm deactivating the 2-step authentication by providing your password?';
$LANG['confirm']											= 'Confirm';

/** INVOICES */
$LANG['invoice']                                         = 'Invoice';
$LANG['invoices']                                        = 'Invoices';
$LANG['invoice nr']                                      = 'Invoice no';
$LANG['due date']                                        = 'Due date';
$LANG['to pay']                                          = 'Payable amount';
$LANG['invoice date']                                    = 'Invoice date';
$LANG['reference number']                                = 'Referene';
$LANG['invoice lines']                                   = 'Invoice lines';
$LANG['outstanding invoices']                            = 'Outstanding invoices';
$LANG['invoice open amount']                             = 'Outstanding amount';
$LANG['pay invoice']                                     = 'Pay online';
$LANG['download invoice']                                = 'Download invoice';
$LANG['no invoices found']                               = 'No invoices found.';
$LANG['invoice does not exist']                          = 'The invoice does not exist (anymore).';
$LANG['copy general data']                               = 'Copy from general information';
$LANG['billing data uses general data']                  = 'Your general information will be used for billing.';
$LANG['use different billing data']                      = 'Do you want to use different information for billing?';
$LANG['you have outstanding invoices']                   = 'You have %d open invoices, click on the invoice to see more details.';
$LANG['you have no outstanding invoices']                = 'You have no open invoices.';
$LANG['payment data - bank transfer or online payments'] = 'By bank or online payment.';
$LANG['payment data - activate authorisation']           = 'If you prefer to pay by direct debit, you can change that here.';
$LANG['payment data - direct debit']                     = 'You have chosen to pay by direct debit. The outstanding amounts will be collected from bank account <strong>%s</strong> attn. <strong>%s</strong>.';
$LANG['direct debit invoice abbr']                       = '- direct debit';
$LANG['direct debit invoice']                            = '- pay by direct debit';
$LANG['direct debit paid invoice']                       = '- paid by direct debit';
$LANG['already paid']                                    = 'Paid';
$LANG['open amount to be paid']                          = 'To be paid';


/** OTHER SERVICES */
$LANG['other services']                      = 'Other services';
$LANG['other service']                       = 'Other service';
$LANG['order new service']                   = 'New service';
$LANG['you have other services']             = 'You have %d other services, click on an other service to see more details.';
$LANG['you have no other services']          = 'You have no other services.';
$LANG['no other services found']             = 'No other services found.';
$LANG['other service does not exist']        = 'The other service does not exist (anymore).';
$LANG['error during termination of service'] = 'An error occurred while terminating the service.';
$LANG['next invoice date']                   = 'Next invoice';
$LANG['terminated']                          = 'terminated';


/** PRICEQUOTES */
$LANG['open pricequotes']                         = 'Open estimates';
$LANG['pricequote']                               = 'Estimate';
$LANG['pricequotes']                              = 'Estimates';
$LANG['pricequote code']                          = 'Estimate number';
$LANG['pricequote nr']                            = 'Estimate no.';
$LANG['pricequote date']                          = 'Estimate date';
$LANG['expiration date']                          = 'Valid till';
$LANG['download pricequote']                      = 'Download estimate';
$LANG['accept pricequote']                        = 'Accept';
$LANG['decline pricequote']                       = 'Decline';
$LANG['pricequote lines']                         = 'Estimate lines';
$LANG['modal title decline pricequote']           = 'Decline estimate';
$LANG['pricequote accepted successfull']          = 'Thank you for accepting estimate %s.';
$LANG['pricequote declined successfull']          = 'Thank you.';
$LANG['please agree to the terms and conditions'] = 'Please agree with our terms and conditions';
$LANG['draw in this field'] 					  = 'Sign in this field';
$LANG['again'] 					  				  = 'Retry';
$LANG['signed by'] 					  			  = 'By';
$LANG['ipaddress'] 					  		  	  = 'IP-address';
$LANG['comment'] 					  		 	  = 'Comment';
$LANG['signature'] 					  		 	  = 'Signature';
$LANG['download signed pricequote'] 			  = 'Download signed estimate';
$LANG['not mandatory'] 			 				  = 'Not mandatory';
$LANG['accept'] 			 				      = 'Accept';
$LANG['see pricequote pdf'] 			 		  = 'See estimate PDF';
$LANG['accept pricequote online'] 			 	  = 'Accept estimate online';
$LANG['pricequote is already accepted']			  = 'Estimate has been accepted';
$LANG['accept the pricequote online'] 				= 'By entering your details, you accept estimate "%s" and agree with the corresponding terms.';
$LANG['ip address']									= 'IP address';
$LANG['contact']									= 'Contact';
$LANG['the pricequote is accepted online on %s'] = 'The estimate has been accepted on %s';
$LANG['pricequote expired title']					= 'Estimate has expired';
$LANG['pricequote expired text']					= 'The estimate has expired.<br />Please contact us if you would still like to accept the estimate.';

/** ORDERS */
$LANG['order']                = 'Order';
$LANG['orders']               = 'Orders';
$LANG['order date']           = 'Ordered on';
$LANG['order lines']          = 'Order lines';
$LANG['order does not exist'] = 'The order does not exist (anymore).';


/** CHANGES */
$LANG['cancel modification']                 = "Cancel change";
$LANG['cancel modification?']                = "Cancel change?";
$LANG['cancel modification are you sure']    = "Are you sure you want to cancel your change?";
$LANG['modification successfully cancelled'] = "Change cancelled.";
$LANG['modification could not be cancelled'] = "The change could not be cancelled. Perhaps your change has already been processed.";
$LANG['view modification']                   = "View the changes";


/** GENERAL */
$LANG['client area']                           = 'Client area';
$LANG['invalid identifier']                    = 'Invalid ID';
$LANG['priceExcl']                             = 'Price excl. tax';
$LANG['priceIncl']                             = 'Price incl. tax';
$LANG['quantity']                              = 'Quantity';
$LANG['date']                                  = 'Date';
$LANG['date and time']                         = 'Date and time';
$LANG['amountExcl']                            = 'Amount excl. tax';
$LANG['amountIncl']                            = 'Amount incl. tax';
$LANG['tax excluded']                          = 'excl. %s%% tax';
$LANG['tax included']                          = 'incl. %s%% tax';
$LANG['till']                                  = 'till';
$LANG['discount on product']                   = '%s product discount';
$LANG['discount on invoice']                   = '%s invoice discount';
$LANG['discount on pricequote']                = '%s estimate discount';
$LANG['period']                                = 'Period';
$LANG['amount']                                = 'Amount';
$LANG['total amount']                          = 'Total amount';
$LANG['description']                           = 'Description';
$LANG['discount']                              = 'Discount';
$LANG['discount amount']                       = 'Discount amount';
$LANG['status']                                = 'Status';
$LANG['actions']                               = 'Actions';
$LANG['page not found title']                  = 'Page not found';
$LANG['page not found text']                   = 'The requested page does not exist.';
$LANG['search']                                = 'Search';
$LANG['search results']                        = 'Search results';
$LANG['and']                                   = 'and';
$LANG['yes']                                   = 'yes';
$LANG['no']                                    = 'no';
$LANG['from']                                  = 'from';
$LANG['at']                                    = 'at';
$LANG['submit']                                = 'Send';
$LANG['check']                                 = 'Check';
$LANG['back']                                  = 'Back';
$LANG['undo']                                  = 'Undo';
$LANG['logout']                                = 'Logout';
$LANG['my account']                            = 'My account';
$LANG['megabytes short']                       = 'Mb';
$LANG['gigabytes short']                       = 'Gb';
$LANG['VAT']                                   = 'tax';
$LANG['terminate']                             = 'Terminate';
$LANG['cancel']                                = 'Cancel';
$LANG['no search results found']               = 'No results found';
$LANG['extended search']                       = 'Search advanced';
$LANG['extended search results']               = 'Advanced results';
$LANG['extended search search again']          = 'Search advanced again';
$LANG['retrieving data']                       = 'Retrieving information';
$LANG['data could not be retrieved']           = 'Information could not be retrieved';
$LANG['comment']                               = 'Comment';
$LANG['i agree with the terms and conditions'] = 'i agree with the %s';
$LANG['terms and conditions']                  = 'terms and conditions';
$LANG['year']                                  = 'year';
$LANG['make your choice']                      = 'Make a choice';
$LANG['unlimited']                             = 'Unlimited';
$LANG['attachment']                            = 'Attachment';
$LANG['attachments']                           = 'Attachments';


/** HOME */
$LANG['my data']                      = 'My information';
$LANG['change your data']             = 'Change information';
$LANG['what would you like to order'] = 'Wat would you like to order';
$LANG['place new service order']      = 'Order new service(s)';
$LANG['pricequote to be accepted']    = 'Open estimates';


/** SUBSCRIPTIONS */
$LANG['subscription']                                  = 'Recurring profile';
$LANG['subscription description']                      = 'Description';
$LANG['subscription current period']                   = 'Current period';
$LANG['terminate subscription']                        = 'Terminate service';
$LANG['terminate subscription?']                       = 'Terminate service?';
$LANG['terminate subscription are you sure']           = 'Are you sure you want to terminate this service?';
$LANG['terminate reason']                              = 'Reason of termination';
$LANG['password is invalid']                           = 'The entered password is invalid';
$LANG['subscription termination successfull']          = 'The service has been terminated successfully and expires on %s.';
$LANG['subscription termination waiting for approval'] = 'The termination for date %s has been sent.';
$LANG['subscription terminated and expires on']        = 'The service has been terminated on %s and expires on %s.';
$LANG['subscription terminated and expires today']     = 'The service has been terminated on %s and expires today.';
$LANG['subscription terminated and is expired']        = 'The service has been terminated on %s and has been expired on %s.';
$LANG['subscription termination waiting for approval'] = 'The termination for this service is in process.';
$LANG['service list termination at']                   = 'Terminated, expires on %s';


/** LOGIN */
$LANG['login']                                  = 'Login';
$LANG['forgot password']                        = 'Password forgotten?';
$LANG['no username or password given']          = 'No username or password entered';
$LANG['username or password invalid']           = 'The combination of your username and password is incorrect.';
$LANG['password reset title']                   = 'Change password';
$LANG['password reset description']             = 'Please enter your own new password for future logins.';
$LANG['password reset successfull']             = 'Your password has been successfully changed.';
$LANG['password forgot title']                  = 'Password forgotten';
$LANG['password forgot description']            = 'Please enter your username and email address. A new password will be sent to your email address.';
$LANG['back to login page']                     = 'Back to login page';
$LANG['no username or email given']             = 'No username or email address entered';
$LANG['user with username email unknown']       = 'No information found with the entered username and email address';
$LANG['password reset successfull, email send'] = 'An email has been sent to %s with an one time only password. After your login you can enter your own desired password.';
$LANG['logout successfull']                     = 'You have successfully logged out.';
$LANG['login from wfh - key invalid']           = 'Invalid login key.';
$LANG['login from wfh - signature invalid']     = 'Invalid login key. Check your database configuration files.';
$LANG['login from wfh - key expired']           = 'Login key expired.';
$LANG['user blocked for x minutes']             = 'You are blocked due to too many incorrect login attempts. In %s minute(s) you can try again.';
$LANG['two factor login title'] 				= '2-step authentication';
$LANG['two factor login description'] 			= 'Fill in the authentication code to continue.';
$LANG['two factor login invalid'] 				= 'You have filled in an incorrect authentication code.';

/** MAIN MENU */
$LANG['mainmenu menu']           = 'Menu';
$LANG['mainmenu home']           = 'Home';
$LANG['mainmenu services']       = 'Services';
$LANG['mainmenu all services']   = 'Services overview';
$LANG['mainmenu other services'] = 'Other services';
$LANG['mainmenu billing']        = 'Billing';
$LANG['mainmenu invoices']       = 'Invoices';
$LANG['mainmenu pricequotes']    = 'Estimates';
$LANG['mainmenu orders']         = 'Orders';
$LANG['mainmenu subscriptions']  = 'Recurring profiles';
$LANG['mainmenu account']        = 'My information';


/** ARRAY VALUES */
$LANG['array_periodic'] = array(
	""  => "once",
	"d" => "day",
	"w" => "week",
	"m" => "month",
	"k" => "quarter",
	"h" => "half year",
	"j" => "year",
	"t" => "two year");

$LANG['array_periodic_multi'] = array(
	"d" => "days",
	"w" => "weeks",
	"m" => "months",
	"k" => "quarters",
	"h" => "half years",
	"j" => "years",
	"t" => "two years");

$LANG['array_pricequotestatus'] = array(
	"2" => "Sent",
	"3" => "Accepted",
	"4" => "Accepted",
	"8" => "Declined");

$LANG['array_orderstatus'] = array(
	"0" => "Received",
	"1" => "In process",
	"2" => "In process",
	"8" => "Processed",
	"9" => "Cancelled");

$LANG['array_invoicestatus'] = array(
	"2" => "Open",
	"3" => "Partly paid",
	"4" => "Paid",
	"8" => "Credit invoice",
	"9" => "Expired");

$LANG['array_authorisation'] = array(
	"yes" => "Yes",
	"no"  => "No");

$LANG['array_mailingopt'] = array(
	"yes" => "Yes",
	"no"  => "No");

$LANG['array_boolean'] = array(
	"1" => "Yes",
	"0" => "No");

$LANG['array_invoicemethod'] = array(
	"0" => "By email",
	"1" => "By post",
	"3" => "By email and post");

$LANG['array_sex'] = array(
	"m" => "Mr.",
	"f" => "Mrs.",
	"d" => "Dep.",
	"u" => "Unknown");

$LANG['array_months'] = array(
	"01" => "January",
	"02" => "February",
	"03" => "March",
	"04" => "April",
	"05" => "May",
	"06" => "June",
	"07" => "July",
	"08" => "August",
	"09" => "September",
	"10" => "Oktober",
	"11" => "November",
	"12" => "December");

$LANG['array_days'] = array(
	"1" => "Monday",
	"2" => "Tuesday",
	"3" => "Wednesday",
	"4" => "Thursday",
	"5" => "Friday",
	"6" => "Saturday",
	"7" => "Sunday");

