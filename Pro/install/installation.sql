SET AUTOCOMMIT=0;
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `HostFact_Actions`;
CREATE TABLE IF NOT EXISTS `HostFact_Actions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `ReferenceType` varchar(25) NOT NULL,
  `ReferenceID` int(10) NOT NULL,
  `ActionType` ENUM( 'manual', 'automatic', 'mail2client', 'mail2user' ) NOT NULL DEFAULT 'manual',
  `Description` varchar(200) NOT NULL,
  `When` enum('direct','before','on','after') NOT NULL DEFAULT 'on',
  `Days` tinyint(5) NOT NULL,
  `Status` enum('pending','executed','error','canceled','removed') NOT NULL DEFAULT 'pending',
  `Created` datetime NOT NULL,
  `Modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Agenda`;
CREATE TABLE IF NOT EXISTS `HostFact_Agenda` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Description` TEXT NOT NULL DEFAULT '',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `TimeFrom` time NOT NULL DEFAULT '00:00:00',
  `TimeTill` time NOT NULL DEFAULT '00:00:00',
  `WholeDay` tinyint(1) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Employee` int(10) NOT NULL DEFAULT '0',
  `EmailNotify` tinyint(4) NOT NULL DEFAULT '-1',
  `ItemType` varchar(16) NOT NULL DEFAULT '',
  `ItemID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_API_Calls`;
CREATE TABLE IF NOT EXISTS `HostFact_API_Calls` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `DateTime` datetime NOT NULL,
  `Controller` varchar(25) NOT NULL,
  `Action` varchar(25) NOT NULL,
  `Input` text NOT NULL,
  `ResponseType` enum('','success','error') NOT NULL,
  `Response` text NOT NULL,
  `IP` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Automation`;
CREATE TABLE IF NOT EXISTS `HostFact_Automation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Variable` varchar(100) NOT NULL DEFAULT '',
  `Value` tinyint(1) NOT NULL DEFAULT '0',
  `Run` enum('cronjob','login','both','create','receive') NOT NULL DEFAULT 'cronjob',
  `Exception` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `HostFact_Automation` (`id`, `Variable`, `Value`, `Run`, `Exception`) VALUES
(1, 'acceptorder', 0, 'cronjob', ''),
(2, 'sentinvoice', 0, 'login', ''),
(3, 'makeinvoice', 1, 'both', ''),
(4, 'registerdomain', 0, 'create', 'transfer'),
(5, 'makeaccount', 0, 'receive', ''),
(6, 'makebackup', 0, 'both', ''),
(7, 'checkticket', 1, 'both', ''),
(8, 'batchmail', 0, 'login', ''),
(9, 'remindersummation', 0, 'both', '');

DROP TABLE IF EXISTS `HostFact_Backups`;
CREATE TABLE IF NOT EXISTS `HostFact_Backups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FileName` varchar(255) NOT NULL DEFAULT '',
  `Version` varchar(10) NOT NULL DEFAULT '',
  `Author` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Company`;
CREATE TABLE IF NOT EXISTS `HostFact_Company` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `CompanyNumber` varchar(20) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `AccountNumber` varchar(50) NOT NULL DEFAULT '',
  `AccountName` varchar(100) NOT NULL DEFAULT '',
  `AccountBank` varchar(100) NOT NULL DEFAULT '',
  `AccountCity` varchar(100) NOT NULL DEFAULT '',
  `AccountBIC` varchar(50) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `FaxNumber` varchar(25) NOT NULL DEFAULT '',
  `MobileNumber` varchar(25) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Website` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `HostFact_Company` (`id`, `Country`) VALUES (1, 'NL');

DROP TABLE IF EXISTS `HostFact_CreditInvoice`;
CREATE TABLE IF NOT EXISTS `HostFact_CreditInvoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CreditInvoiceCode` varchar(25) NOT NULL DEFAULT '',
  `InvoiceCode` varchar(100) NOT NULL DEFAULT '',
  `Creditor` int(10) NOT NULL DEFAULT '0',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Term` int(3) NOT NULL DEFAULT '0',
  `Authorisation` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `PayDate` date NOT NULL DEFAULT '0000-00-00',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `AmountExcl` DECIMAL(10,2) NOT NULL DEFAULT  '0.00',
  `AmountIncl` DECIMAL(10,2) NOT NULL DEFAULT  '0.00',
  `AmountPaid` DECIMAL(10,2) NOT NULL DEFAULT  '0.00',
  `Location` varchar(255) NOT NULL DEFAULT '',
  `Private` DECIMAL(10,2) NOT NULL DEFAULT  '0.00',
  `PrivatePercentage` float NOT NULL DEFAULT '0',
  `ReferenceNumber` varchar(255) NOT NULL,
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `CreditInvoiceCode` (`CreditInvoiceCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_CreditInvoiceElements`;
CREATE TABLE IF NOT EXISTS `HostFact_CreditInvoiceElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CreditInvoiceCode` varchar(25) NOT NULL DEFAULT '',
  `Creditor` int(10) NOT NULL DEFAULT '0',
  `Number` float NOT NULL DEFAULT '0',
  `Description` varchar(255) NOT NULL DEFAULT '',
  `PriceExcl` DOUBLE NOT NULL DEFAULT  '0',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `CreditInvoiceCode` (`CreditInvoiceCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Creditors`;
CREATE TABLE IF NOT EXISTS `HostFact_Creditors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CreditorCode` varchar(50) NOT NULL DEFAULT '',
  `MyCustomerCode` varchar(50) NOT NULL,
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `CompanyNumber` varchar(20) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `Sex` char(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(100) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `BirthDate` date NOT NULL DEFAULT '0000-00-00',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `MobileNumber` varchar(25) NOT NULL DEFAULT '',
  `FaxNumber` varchar(25) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `Term` SMALLINT(5) NOT NULL DEFAULT '0',
  `Authorisation` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `AccountNumber` varchar(50) NOT NULL DEFAULT '',
  `AccountBIC` varchar(50) NOT NULL DEFAULT '',
  `AccountName` varchar(100) NOT NULL DEFAULT '',
  `AccountBank` varchar(100) NOT NULL DEFAULT '',
  `AccountCity` varchar(100) NOT NULL DEFAULT '',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Debtors`;
CREATE TABLE IF NOT EXISTS `HostFact_Debtors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `DebtorCode` varchar(50) NOT NULL DEFAULT '',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Anonymous` ENUM('no','yes') NOT NULL DEFAULT 'no',
  `ActiveLogin` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
  `Username` varchar(100) NOT NULL DEFAULT '',
  `Password` varchar(32) NOT NULL DEFAULT '',
  `OneTimePasswordValidTill` DATETIME NULL,
  `SecurePassword` VARCHAR(255) NOT NULL,
  `TwoFactorAuthentication` ENUM('on','off') NOT NULL DEFAULT 'off',
  `TokenData` VARCHAR(255) NOT NULL,
  `CustomerPanelKey` VARCHAR(255) NOT NULL,
  `LastDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `CompanyNumber` varchar(20) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `LegalForm` varchar(15) NOT NULL DEFAULT '',
  `Sex` char(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(100) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `BirthDate` date NOT NULL DEFAULT '0000-00-00',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Website` VARCHAR(255) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `MobileNumber` varchar(25) NOT NULL DEFAULT '',
  `FaxNumber` varchar(25) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `PeriodicInvoiceDays` SMALLINT(5) NOT NULL DEFAULT '-1',
  `InvoiceMethod` tinyint(2) NOT NULL DEFAULT '0',
  `InvoiceTerm` smallint(5) NOT NULL DEFAULT '-1',
  `InvoiceCompanyName` varchar(100) NOT NULL,
  `InvoiceSex` CHAR(1) NOT NULL DEFAULT '',
  `InvoiceInitials` varchar(25) NOT NULL DEFAULT '',
  `InvoiceSurName` varchar(100) NOT NULL DEFAULT '',
  `InvoiceAddress` varchar(100) NOT NULL DEFAULT '',
  `InvoiceAddress2` varchar(100) NOT NULL DEFAULT '',
  `InvoiceZipCode` varchar(10) NOT NULL DEFAULT '',
  `InvoiceCity` varchar(100) NOT NULL DEFAULT '',
  `InvoiceState` varchar(100) NOT NULL DEFAULT '',
  `InvoiceCountry` varchar(10) NOT NULL DEFAULT '',
  `InvoiceEmailAddress` varchar(255) NOT NULL DEFAULT '',
  `InvoiceDataForPriceQuote` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `InvoiceAuthorisation` enum('yes','no') NOT NULL DEFAULT 'yes',
  `InvoiceTemplate` int(10) NOT NULL DEFAULT '0',
  `PriceQuoteTemplate` int(10) NOT NULL DEFAULT '0',
  `ReminderEmailAddress` varchar(255) NOT NULL DEFAULT '',
  `ReminderTemplate` int(10) NOT NULL DEFAULT '0',
  `SecondReminderTemplate` int(10) NOT NULL DEFAULT '-1',
  `SummationTemplate` int(10) NOT NULL DEFAULT '0',
  `Server` int(10) DEFAULT '0',
  `PaymentMail` varchar(50) NOT NULL DEFAULT '-1',
  `PaymentMailTemplate` int(10) NOT NULL,
  `AccountNumber` varchar(50) NOT NULL DEFAULT '',
  `AccountBIC` varchar(50) NOT NULL DEFAULT '',
  `AccountName` varchar(100) NOT NULL DEFAULT '',
  `AccountBank` varchar(100) NOT NULL DEFAULT '',
  `AccountCity` varchar(100) NOT NULL DEFAULT '',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `InvoiceCollect` tinyint( 1 ) NOT NULL DEFAULT '-1',
  `DefaultLanguage` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `Mailing` enum('yes','no') NOT NULL DEFAULT 'yes',
  `Taxable` enum('auto','yes','no') NOT NULL DEFAULT 'auto',
  `InvoiceEmailAttachments` VARCHAR(25) NOT NULL,
  `DNS1` varchar(255) NOT NULL,
  `DNS2` varchar(255) NOT NULL,
  `DNS3` varchar(255) NOT NULL,
  `ClientareaProfile` int(5) NOT NULL DEFAULT '0',
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `handle` (`DebtorCode`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Debtor_Custom_Fields`;
CREATE TABLE IF NOT EXISTS `HostFact_Debtor_Custom_Fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `FieldCode` varchar(50) NOT NULL,
  `LabelTitle` varchar(255) NOT NULL,
  `LabelType` enum('select','input','checkbox','radio','date','textarea') NOT NULL DEFAULT 'input',
  `LabelOptions` text NOT NULL,
  `LabelDefault` varchar(255) NOT NULL,
  `OrderID` int(10) NOT NULL,
  `ShowDebtor` enum('no','yes') NOT NULL DEFAULT 'no',
  `ShowHandle` enum('no','yes') NOT NULL DEFAULT 'no',
  `ShowOrderform` enum('no','yes') NOT NULL DEFAULT 'no',
  `ShowInvoice`  ENUM(  'no',  'yes' )  NOT NULL DEFAULT  'no',
  `ShowPriceQuote`  ENUM(  'no',  'yes' )  NOT NULL DEFAULT  'no',
  `Regex` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FieldCode` (`FieldCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Debtor_Custom_Values`;
CREATE TABLE IF NOT EXISTS `HostFact_Debtor_Custom_Values` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ReferenceType` enum('debtor','handle','newcustomer','invoice','pricequote') NOT NULL DEFAULT 'debtor',
  `ReferenceID` int(10) NOT NULL,
  `FieldID` int(10) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ReferenceType` (`ReferenceType`,`ReferenceID`,`FieldID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Discount`;
CREATE TABLE IF NOT EXISTS `HostFact_Discount` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(255) NOT NULL DEFAULT '',
  `Coupon` varchar(50) NOT NULL DEFAULT '',
  `Discount` DECIMAL( 10, 2 ) NULL DEFAULT  '0.00',
  `DiscountPercentage` float DEFAULT '0',
  `DiscountPercentageType` ENUM( 'line', 'subscription' ) NOT NULL DEFAULT 'line',
  `DiscountPart` DOUBLE NOT NULL DEFAULT '0',
  `DiscountPartRestriction` tinyint(1) NOT NULL,
  `DiscountType` varchar(30) NOT NULL,
  `DiscountPeriod` varchar(10) NOT NULL,
  `StartDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `EndDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Counter` int(5) NOT NULL DEFAULT '0',
  `Max` int(5) NOT NULL DEFAULT '0',
  `MaxPerInvoice` INT( 5 ) NOT NULL DEFAULT '0',
  `MinAmount`  DOUBLE NOT NULL DEFAULT '0',
  `DocumentType` VARCHAR( 10 ) NOT NULL,
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `DebtorGroup` varchar(50) NOT NULL DEFAULT '0',
  `Product1` int(10) NOT NULL DEFAULT '0',
  `ProductGroup1` int(10) NOT NULL DEFAULT '0',
  `Price1` DOUBLE NULL DEFAULT NULL,
  `Product2` int(10) NOT NULL DEFAULT '0',
  `ProductGroup2` int(10) NOT NULL DEFAULT '0',
  `Price2` DOUBLE NULL DEFAULT NULL,
  `Product3` int(10) NOT NULL DEFAULT '0',
  `ProductGroup3` int(10) NOT NULL DEFAULT '0',
  `Price3` DOUBLE NULL DEFAULT NULL,
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_DNS_Integrations`;
CREATE TABLE IF NOT EXISTS `HostFact_DNS_Integrations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `DNS1` varchar(255) NOT NULL,
  `DNS2` varchar(255) NOT NULL,
  `DNS3` varchar(255) NOT NULL,
  `Type` enum('server','registrar','other','') NOT NULL,
  `IntegrationID` int(10) NOT NULL,
  `Status` enum('active','removed','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_DNS_Platform`;
CREATE TABLE IF NOT EXISTS `HostFact_DNS_Platform` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Platform` varchar(100) NOT NULL,
  `Status` enum('active') NOT NULL DEFAULT 'active',
  `Settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `HostFact_DNS_Templates`;
CREATE TABLE IF NOT EXISTS `HostFact_DNS_Templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `DNSIntegrationID` int(10) NOT NULL,
  `Status` enum('active','removed','') NOT NULL,
  `TemplateType` enum('templates','records','') NOT NULL,
  `TemplateID` varchar(100) NOT NULL,
  `TemplateName` varchar(100) NOT NULL,
  `DNSRecords` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Documents`;
CREATE TABLE IF NOT EXISTS `HostFact_Documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Filename` varchar(255) NOT NULL DEFAULT '',
  `FilenameServer` varchar(255) NOT NULL DEFAULT '',
  `Size` int(11) NOT NULL,
  `Type` enum('','invoice','pricequote','pricequote_accepted','ticket','creditinvoice','debtor','creditor') NOT NULL DEFAULT '',
  `Reference` int(11) NOT NULL,
  `DateTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Domains`;
CREATE TABLE IF NOT EXISTS `HostFact_Domains` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Product` int(10) NOT NULL DEFAULT '0',
  `Domain` varchar(255) NOT NULL DEFAULT '',
  `Tld` varchar(63) NOT NULL DEFAULT '',
  `RegistrationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ExpirationDate` date NOT NULL DEFAULT '0000-00-00',
  `Registrar` tinyint(3) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `DNS1` varchar(255) NOT NULL DEFAULT '',
  `DNS2` varchar(255) NOT NULL DEFAULT '',
  `DNS3` varchar(255) NOT NULL DEFAULT '',
  `DNSTemplate` int(4) NOT NULL,
  `PeriodicID` int(10) NOT NULL DEFAULT '0',
  `HostingID` int(10) NOT NULL,
  `ownerHandle` varchar(20) DEFAULT NULL,
  `adminHandle` varchar(20) DEFAULT NULL,
  `techHandle` varchar(20) DEFAULT NULL,
  `AuthKey` varchar(50) NOT NULL DEFAULT '',
  `Type` varchar(10) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `LastSyncDate` DATETIME NOT NULL,
  `IsSynced` VARCHAR(10) NOT NULL,
  `DomainAutoRenew` ENUM('on','off') NOT NULL DEFAULT 'on',
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Tld` (`Tld`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_DomainsPending`;
CREATE TABLE `HostFact_DomainsPending` (
	`DomainID` int( 10 ) NOT NULL ,
	`Registrar` int( 10 ) NOT NULL ,
	`StatusCode` VARCHAR( 20 ) NOT NULL ,
	`StatusText` VARCHAR( 255 ) NOT NULL ,
	`LastDate` DATETIME NOT NULL ,
	`NextDate` DATETIME NOT NULL ,
	PRIMARY KEY ( `DomainID` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Domain_Extra_Fields`;
CREATE TABLE IF NOT EXISTS `HostFact_Domain_Extra_Fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Registrar` int(10) NOT NULL,
  `Tld` varchar(63) NOT NULL,
  `RegistrarField` varchar(100) NOT NULL,
  `LabelTitle` varchar(255) NOT NULL,
  `LabelType` enum('text','options') NOT NULL,
  `LabelOptions` varchar(255) NOT NULL,
  `LabelDefault` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Domain_Extra_Values`;
CREATE TABLE IF NOT EXISTS `HostFact_Domain_Extra_Values` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `DomainID` int(10) NOT NULL,
  `FieldID` int(10) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `DomainID` (`DomainID`),
  KEY `FieldID` (`FieldID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `HostFact_Emails`;
CREATE TABLE IF NOT EXISTS `HostFact_Emails` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Recipient` tinytext NOT NULL,
  `Debtor` INT( 10 ) NOT NULL DEFAULT '0',
  `CarbonCopy` tinytext NOT NULL,
  `BlindCarbonCopy` tinytext NOT NULL,
  `Sender` varchar(255) NOT NULL DEFAULT '',
  `Subject` varchar(255) NOT NULL DEFAULT '',
  `Message` text NOT NULL,
  `Attachment` varchar(255) NOT NULL DEFAULT '',
  `SentDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_EmailTemplates`;
CREATE TABLE IF NOT EXISTS `HostFact_EmailTemplates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `CarbonCopy` tinytext NOT NULL,
  `BlindCarbonCopy` tinytext NOT NULL,
  `Sender` varchar(255) NOT NULL DEFAULT '',
  `Subject` varchar(255) NOT NULL DEFAULT '',
  `Message` text NOT NULL,
  `Attachment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_EmailTemplates` (`id`, `Name`, `CarbonCopy`, `BlindCarbonCopy`, `Sender`, `Subject`, `Message`, `Attachment`) VALUES
(1, 'Factuur', '', '', '', 'Factuur [invoice->InvoiceCode]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			In de bijlage van dit e-mail bericht vindt u de factuur met factuurnummer [invoice-&gt;InvoiceCode] voor de afgenomen diensten/producten.<br />\r\n			<br />\r\n			[paid]De factuur is reeds betaald.[/paid]<br />\r\n			[unpaid]<br />\r\n			[directdebit]Het te betalen bedrag wordt automatisch op [invoice-&gt;DirectDebitDate] van uw rekening [debtor-&gt;AccountNumber] t.n.v. [debtor-&gt;AccountName] afgeschreven.[/directdebit]<br />\r\n			[transfer]Wij vragen u vriendelijk het bedrag binnen [invoice-&gt;Term] dagen te betalen.[/transfer]<br />\r\n			[/unpaid]<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(2, 'Offerte', '', '', '', 'Offerte [invoice->PriceQuoteCode]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			In de bijlage van dit e-mail bericht ontvangt u van ons een offerte zoals eerder besproken is.<br />\r\n<br />\r\n<a href="[invoice-&gt;AcceptURLRaw]">Indien u akkoord gaat, kunt u de offerte hier online accepteren</a><br />\r\n	<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(3, 'Herinnering', '', '', '', 'Herinnering van factuur [invoice->InvoiceCode]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			Enige tijd geleden heeft u van ons de factuur [invoice-&gt;InvoiceCode] ontvangen voor de afgenomen diensten. Helaas hebben we nog geen betaling van het verschuldigde bedrag ontvangen.<br />\r\n			<br />\r\n			Wij vragen u daarom het totaalbedrag van [invoice-&gt;PartPayment] binnen [invoice-&gt;Term] dagen te voldoen, met vermelding van uw klantnummer en factuurnummer.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(4, 'Aanmaning', '', '', '', 'Aanmaning factuur [invoice->InvoiceCode]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			Enige tijd geleden heeft u van ons de factuur [invoice-&gt;InvoiceCode] ontvangen voor de afgenomen diensten. Ondanks herhaaldelijke herinneringen heeft u het verschuldigde bedrag niet voldaan op onze bankrekening.<br />\r\n			<br />\r\n			Wij vragen u daarom een laatste keer het totaal verschuldigde bedrag van [invoice-&gt;PartPayment] te voldoen, met vermelding van uw klantnummer en factuurnummer. Indien wij de betaling niet binnen [invoice-&gt;Term] dagen hebben ontvangen, zullen wij genoodzaakt zijn de vordering neer te leggen bij een incassobureau.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(5, 'Welkom bij [companyname]', '', '', '', 'Welkom bij [companyname]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Welkom bij [company-&gt;CompanyName]!<br />\r\n			<br />\r\n			We hebben u zojuist toegevoegd aan ons klantenbestand. U heeft vanaf heden de mogelijkheid om in te loggen in ons klantenpaneel&nbsp;om zo uw&nbsp;bestellingen, offertes, facturen etc. te bekijken.<br />\r\n			<br />\r\n			<strong>Uw gegevens</strong><br />\r\n			Adres: <a href="[clientarea_url]" target="_blank">[clientarea_url]</a><br />\r\n			Gebruikersnaam: [debtor-&gt;Username]<br />\r\n			Tijdelijk wachtwoord: [debtor-&gt;Password] (24 uur geldig)<br />\r\n			<br />\r\n			Indien u nog vragen heeft of problemen ondervindt bij het inloggen, neem dan contact met ons op!<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(6, 'Betaling ontvangen', '', '', '', 'Betaling ontvangen', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			We hebben de betaling van de factuur met factuurnummer [invoice-&gt;InvoiceCode] ontvangen.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(8, 'Nieuw ticket naar uzelf', '', '', '', 'Nieuw ticket [ticket->TicketID]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Er is een nieuw ticket of een reactie op een ticket met ticketnummer: [ticket-&gt;TicketID].<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(9, 'Ticket ontvangen (naar klant)', '', '', '', 'Ticket [ticket->TicketID] ontvangen', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte heer/mevrouw,<br />\r\n			<br />\r\n			Wij hebben uw ticket met ticketnummer [ticket-&gt;TicketID] in goede orde ontvangen.<br />\r\n			<br />\r\n			We zullen er naar streven zo spoedig mogelijk een reactie te geven.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(10, 'Gegevens webhostingpakket', '', '', '', 'Gegevens van uw webhostingpakket', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Uw hostingpakket is aangemaakt en gereed voor gebruik!<br />\r\n			<br />\r\n			<strong>Hostingpakket gegevens</strong></span></span></p>\r\n		<table border="0" cellpadding="0" cellspacing="0" width="100%">\r\n			<tbody>\r\n				<tr>\r\n					<td width="150">\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Controle paneel:</span></span></td>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;"><a href="http://[hosting-&gt;Domain]:[server-&gt;Port]/" target="_blank">http://[hosting-&gt;Domain]:[server-&gt;Port]/</a>&nbsp;&nbsp;<i>(indien domeinnaam nog niet beschikbaar is: <a href="http://[server-&gt;IP]:[server-&gt;Port]/" target="_blank">http://[server-&gt;IP]:[server-&gt;Port]/</a>)</i></span></span></td>\r\n				</tr>\r\n				<tr>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Gebruikersnaam:</span></span></td>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">[hosting-&gt;Username]</span></span></td>\r\n				</tr>\r\n				<tr>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Wachtwoord:</span></span></td>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">[hosting-&gt;Password]</span></span></td>\r\n				</tr>\r\n				<tr>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Domeinnaam:</span></span></td>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;"><a href="[hosting-&gt;Domain]" target="_blank">[hosting-&gt;Domain]</a></span></span></td>\r\n				</tr>\r\n				<tr>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">IP-adres:</span></span></td>\r\n					<td>\r\n						<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">[server-&gt;IP]</span></span></td>\r\n				</tr>\r\n			</tbody>\r\n		</table>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Indien u nog vragen heeft of problemen ondervindt bij het inloggen, neem dan contact met ons op!<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(11, 'Herinnering verlengen abonnementen', '', '', '', 'Binnenkort worden de afgenomen diensten opnieuw gefactureerd', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Op [periodic-&gt;NextDate] zullen onderstaande diensten gefactureerd worden:<br />\r\n			[START:periodics]<br />\r\n			&quot;[periodicElement-&gt;Description]&quot; voor een bedrag van [periodicElement-&gt;AmountExcl] excl. BTW ([periodicElement-&gt;AmountIncl] incl. BTW).<br />\r\n			[END:periodics]<br />\r\n			<br />\r\n			Het totaal bedrag van de factuur zal [periodic-&gt;totalAmountExcl] excl. BTW ([periodic-&gt;totalAmountIncl] incl. BTW) bedragen.<br />\r\n			<br />\r\n			Indien u &eacute;&eacute;n of meerdere diensten wilt opzeggen, dient u voortijdig uw opzegging schriftelijk aan ons kenbaar te maken.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(12, 'Creditfactuur', '', '', '', 'Creditfactuur [invoice->InvoiceCode]', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [invoice-&gt;Initials] [invoice-&gt;SurName],<br />\r\n			<br />\r\n			In de bijlage van dit e-mail bericht vindt u de creditfactuur met factuurnummer [invoice-&gt;InvoiceCode].<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(13, 'Antwoord ticket via klantenpaneel', '', '', '', 'Antwoord op uw ticket via het klantenpaneel', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Er is een reactie geplaatst op uw ticket met ticketnummer [ticket-&gt;TicketID].<br />\r\n			<br />\r\n			Log in op het klantenpaneel om deze reactie te bekijken.<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(14, 'Abonnementen verlengd', '', '', '', 'U heeft abonnementen verlengd', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Hierbij bevestigen wij de verlenging van de volgende abonnementen:<br />\r\n			[START:periodics]<br />\r\n			- [periodicElement-&gt;Description] met [periodicElement-&gt;Periods] [periodicElement-&gt;Periodic] ([periodicElement-&gt;StartContract] tot [periodicElement-&gt;EndContract])<br />\r\n			[END:periodics]<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', ''),
(15, 'Notificatie e-mail voor nieuwe incassodatum', '', '', '', 'Nieuwe incassodatum voor factuur [invoice->InvoiceCode]', '<html>\r\n<head>\r\n	<title></title>\r\n</head>\r\n<body>\r\n<p><span style="font-size: 12px;"><span style="font-family: Arial,Helvetica,sans-serif;">Beste [invoice->Initials] [invoice->SurName],<br />\r\n<br />\r\nGraag informeren we u over de nieuwe datum waarop factuur [invoice->InvoiceCode] ge&iuml;ncasseerd zal worden.<br />\r\nHet bedrag van [invoice->PartPayment] zal op [invoice->DirectDebitDate] van uw rekeningnummer [debtor->AccountNumber] worden afgeschreven.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br />\r\n[company->CompanyName]</span></span></p>\r\n</body>\r\n</html>\r\n', ''),
(16, 'Notificatie e-mail voor mislukte incasso', '', '', '', 'Incasso mislukt voor factuur [invoice->InvoiceCode]', '<html>\r\n<head>\r\n	<title></title>\r\n</head>\r\n<body>\r\n<p><span style="font-size: 12px;"><span style="font-family: Arial,Helvetica,sans-serif;">Beste [invoice->Initials] [invoice->SurName],<br />\r\n<br />\r\nHelaas is de poging tot het incasseren van factuur [invoice->InvoiceCode] niet gelukt.<br />\r\n<br />\r\nOp [invoice->DirectDebitDate] zullen wij opnieuw een poging doen om het bedrag van [invoice->PartPayment] af te schrijven van uw rekeningnummer [debtor->AccountNumber].<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br />\r\n[company->CompanyName]</span></span></p>\r\n</body>\r\n</html>\r\n', ''),
(17, 'Wachtwoord vergeten e-mail', '', '', '', 'Wachtwoord vergeten', '<html>\r\n	<head>\r\n		<title></title>\r\n	</head>\r\n	<body>\r\n		<p>\r\n			<span style="font-size:12px;"><span style="font-family:arial,helvetica,sans-serif;">Geachte [debtor-&gt;Initials] [debtor-&gt;SurName],<br />\r\n			<br />\r\n			Zojuist is er een nieuw tijdelijk wachtwoord voor u aangemaakt om in te loggen in ons klantenpaneel. Dit wachtwoord is 24 uur geldig.<br />\r\n			<br />\r\n			<strong>Uw gegevens</strong><br />\r\n			Adres: <a href="[clientarea_url]" target="_blank">[clientarea_url]</a><br />\r\n			Gebruikersnaam: [debtor-&gt;Username]<br />\r\n			Tijdelijk wachtwoord: [debtor-&gt;Password]<br />\r\n			<br />\r\n			Indien u nog vragen heeft of problemen ondervindt bij het inloggen, neem dan contact met ons op!<br />\r\n			<br />\r\n			Met vriendelijke groet,<br />\r\n			<br />\r\n			[company-&gt;CompanyName]</span></span></p>\r\n	</body>\r\n</html>\r\n', '');

DROP TABLE IF EXISTS `HostFact_Employee`;
CREATE TABLE IF NOT EXISTS `HostFact_Employee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `Function` varchar(255) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `MobileNumber` varchar(25) NOT NULL DEFAULT '',
  `UserName` varchar(50) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL DEFAULT '',
  `TokenData` VARCHAR(255) NOT NULL,
  `TwoFactorAuthentication` ENUM('on','off') NOT NULL DEFAULT 'off',
  `Signature` text NOT NULL,
  `Language` varchar(10) NOT NULL DEFAULT 'nl_NL',
  `LastDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Notes` text NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '1',
  `TicketOrder` enum('ASC','DESC') NOT NULL DEFAULT 'DESC',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_EmployeePreferences`;
CREATE TABLE IF NOT EXISTS `HostFact_EmployeePreferences` (
  `Employee` int(10) NOT NULL,
  `Page` varchar(10) NOT NULL,
  `Action` varchar(20) NOT NULL,
  `Value` varchar(10) NOT NULL,
  `Order` tinyint(1) NOT NULL,
  UNIQUE KEY `Employee` (`Employee`,`Page`,`Action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `HostFact_EmployeeRights`;
CREATE TABLE IF NOT EXISTS `HostFact_EmployeeRights` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Employee` int(10) NOT NULL DEFAULT '0',
  `Right` varchar(50) NOT NULL DEFAULT '',
  `Value` set('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_EmployeeWidgets`;
CREATE TABLE IF NOT EXISTS `HostFact_EmployeeWidgets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Employee` int(8) NOT NULL,
  `Widget` int(8) NOT NULL,
  `Option1` varchar(255) NOT NULL,
  `Position` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_ExportHistory`;
CREATE TABLE IF NOT EXISTS `HostFact_ExportHistory` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ExportedAt` datetime NOT NULL,
  `Package` varchar(25) NOT NULL,
  `Type` ENUM(  'sddbatch',  'debtor', 'creditor', 'invoice', 'creditinvoice', 'product',  'payment_invoice', 'payment_purchase' ) NOT NULL,
  `ReferenceID` int(10) NOT NULL,
  `PackageReference` VARCHAR( 255 ) NOT NULL,
  `Status` ENUM(  'success',  'ignore',  'error','paid_diff') NOT NULL,
  `Message` TEXT NOT NULL,
  `LastOpenAmount` DECIMAL(10,2) NULL,
  UNIQUE KEY `unique` (`Package` ,`Type` ,`ReferenceID`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_ExportPaymentTransactions`;
CREATE TABLE IF NOT EXISTS `HostFact_ExportPaymentTransactions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Package` varchar(25) NOT NULL,
  `Journal` varchar(100) NOT NULL,
  `PackageReference` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Amount` decimal(10,2) NOT NULL DEFAULT  '0.00',
  `PackageStatus` enum('draft','final','removed') NOT NULL,
  `InvoiceID` int(10) NOT NULL,
  `Action` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Package` (`Package`,`Journal`,`PackageReference`,`Date`,`Description`,`Amount`,`InvoiceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_ExportSettings`;
CREATE TABLE IF NOT EXISTS `HostFact_ExportSettings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `modified` datetime NOT NULL,
  `package` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `package_unique` (`package` ,`name`),
  KEY `name` (`name`),
  KEY `package` (`package`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `HostFact_ExportTemplateElements`;
CREATE TABLE IF NOT EXISTS `HostFact_ExportTemplateElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Template` int(10) NOT NULL,
  `Status` tinyint(2) NOT NULL,
  `Table` varchar(100) NOT NULL,
  `Field` varchar(100) NOT NULL,
  `Order` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `HostFact_ExportTemplateElements` (`id`, `Template`, `Status`, `Table`, `Field`, `Order`) VALUES
(NULL, 1, 0, 'HostFact_Debtors', 'AccountBIC', 32),
(NULL, 1, 0, 'HostFact_Debtors', 'AccountName', 33),
(NULL, 1, 0, 'HostFact_Debtors', 'AccountNumber', 30),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceAuthorisation', 29),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceEmailAddress', 28),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceCountry', 27),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceCity', 26),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceZipCode', 25),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceAddress', 24),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceSurName', 23),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceInitials', 22),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceCompanyName', 21),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceTerm', 20),
(NULL, 1, 0, 'HostFact_Debtors', 'InvoiceMethod', 19),
(NULL, 1, 0, 'HostFact_Debtors', 'Comment', 18),
(NULL, 1, 0, 'HostFact_Debtors', 'FaxNumber', 17),
(NULL, 1, 0, 'HostFact_Debtors', 'MobileNumber', 16),
(NULL, 1, 0, 'HostFact_Debtors', 'PhoneNumber', 15),
(NULL, 1, 0, 'HostFact_Debtors', 'EmailAddress', 13),
(NULL, 1, 0, 'HostFact_Debtors', 'Country', 12),
(NULL, 1, 0, 'HostFact_Debtors', 'City', 11),
(NULL, 1, 0, 'HostFact_Debtors', 'ZipCode', 10),
(NULL, 1, 0, 'HostFact_Debtors', 'Address', 9),
(NULL, 2, 0, 'HostFact_Invoice', 'InvoiceCode', 0),
(NULL, 2, 0, 'HostFact_Invoice', 'Date', 1),
(NULL, 2, 0, 'HostFact_Invoice', 'Term', 2),
(NULL, 2, 0, 'HostFact_Invoice', 'Discount', 3),
(NULL, 2, 0, 'HostFact_Invoice', 'Coupon', 4),
(NULL, 2, 0, 'HostFact_Invoice', 'ReferenceNumber', 5),
(NULL, 2, 0, 'HostFact_Invoice', 'CompanyName', 6),
(NULL, 2, 0, 'HostFact_Invoice', 'Initials', 7),
(NULL, 2, 0, 'HostFact_Invoice', 'SurName', 8),
(NULL, 2, 0, 'HostFact_Invoice', 'Address', 9),
(NULL, 2, 0, 'HostFact_Invoice', 'ZipCode', 10),
(NULL, 2, 0, 'HostFact_Invoice', 'City', 11),
(NULL, 2, 0, 'HostFact_Invoice', 'Country', 12),
(NULL, 2, 0, 'HostFact_Invoice', 'EmailAddress', 13),
(NULL, 2, 0, 'HostFact_Invoice', 'Authorisation', 14),
(NULL, 2, 0, 'HostFact_Invoice', 'InvoiceMethod', 15),
(NULL, 2, 0, 'HostFact_Invoice', 'Template', 16),
(NULL, 2, 0, 'HostFact_Invoice', 'Status', 17),
(NULL, 2, 0, 'HostFact_Invoice', 'AmountExcl', 18),
(NULL, 2, 0, 'HostFact_Invoice', 'AmountIncl', 19),
(NULL, 2, 0, 'HostFact_Invoice', 'PayDate', 20),
(NULL, 2, 0, 'HostFact_Invoice', 'TransactionID', 21),
(NULL, 2, 0, 'HostFact_Invoice', 'Description', 22),
(NULL, 2, 0, 'HostFact_Invoice', 'Comment', 23),
(NULL, 1, 0, 'HostFact_Debtors', 'SurName', 8),
(NULL, 1, 0, 'HostFact_Debtors', 'Initials', 7),
(NULL, 1, 0, 'HostFact_Debtors', 'Sex', 6),
(NULL, 1, 0, 'HostFact_Debtors', 'LegalForm', 5),
(NULL, 1, 0, 'HostFact_Debtors', 'TaxNumber', 4),
(NULL, 1, 0, 'HostFact_Debtors', 'CompanyNumber', 3),
(NULL, 1, 0, 'HostFact_Debtors', 'CompanyName', 2),
(NULL, 1, 0, 'HostFact_Debtors', 'Username', 1),
(NULL, 1, 0, 'HostFact_Debtors', 'DebtorCode', 0),
(NULL, 1, 0, 'HostFact_Debtors', 'AccountBank', 34),
(NULL, 1, 0, 'HostFact_Debtors', 'AccountCity', 35),
(NULL, 1, 0, 'HostFact_Debtors', 'Mailing', 36),
(NULL, 1, 0, 'HostFact_Debtors', 'Taxable', 37);

DROP TABLE IF EXISTS `HostFact_ExportTemplates`;
CREATE TABLE IF NOT EXISTS `HostFact_ExportTemplates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ExportData` varchar(100) NOT NULL,
  `Status` tinyint(2) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` enum('intern','extern') NOT NULL,
  `Date` date NOT NULL,
  `Filename` VARCHAR( 100 ) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `HostFact_ExportTemplates` (`id`, `ExportData`, `Status`, `Name`, `Type`, `Date`) VALUES
(1, 'HostFact_Debtors', 0, 'Alle debiteurgegevens', 'intern', '2010-03-01'),
(2, 'HostFact_Invoice', 0, 'Alle factuurgegevens', 'intern', '2010-03-01');

DROP TABLE IF EXISTS `HostFact_FailedLoginAttempts`;
CREATE TABLE IF NOT EXISTS `HostFact_FailedLoginAttempts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `DateTime` datetime NOT NULL,
  `IP` varchar(40) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Type` enum('backoffice','api','clientarea') NOT NULL DEFAULT 'backoffice',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Group`;
CREATE TABLE IF NOT EXISTS `HostFact_Group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Type` enum('product','debtor','creditor') NOT NULL DEFAULT 'product',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `GroupName` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `HostFact_Group` (`id`, `Type`, `Status`, `GroupName`) VALUES
(1, 'product', 1, 'Domeinnamen bestelformulier'),
(2, 'product', 1, 'Hosting bestelformulier'),
(3, 'product', 1, 'Opties bestelformulier');

DROP TABLE IF EXISTS `HostFact_GroupRelations`;
CREATE TABLE IF NOT EXISTS `HostFact_GroupRelations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Group` int(10) NOT NULL DEFAULT '0',
  `Type` enum('product','debtor','creditor') NOT NULL DEFAULT 'product',
  `Reference` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Reference` (`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Handles`;
CREATE TABLE IF NOT EXISTS `HostFact_Handles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Handle` varchar(30) NOT NULL DEFAULT '',
  `Registrar` varchar(20) NOT NULL,
  `RegistrarHandle` varchar(50) NOT NULL,
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `PreSurName` varchar(10) NOT NULL DEFAULT '',
  `SurName` varchar(100) NOT NULL DEFAULT '',
  `Address` varchar(255) NOT NULL DEFAULT '',
  `Address2` varchar(255) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(255) NOT NULL DEFAULT '',
  `State` varchar(255) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `FaxNumber` varchar(25) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Sex` char(1) NOT NULL DEFAULT '',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `LegalForm` varchar(15) NOT NULL DEFAULT '',
  `RegType` varchar(20) NOT NULL DEFAULT '',
  `CompanyNumber` varchar(20) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `HandleType` varchar(10) NOT NULL DEFAULT '',
  `Status` tinyint(1) NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Debtor` (`Debtor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Hosting`;
CREATE TABLE IF NOT EXISTS `HostFact_Hosting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Product` int(10) NOT NULL DEFAULT '0',
  `PeriodicID` int(10) NOT NULL DEFAULT '0',
  `Package` varchar(255) NOT NULL DEFAULT '',
  `Username` varchar(255) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL DEFAULT '',
  `Domain` varchar(255) NOT NULL DEFAULT '',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Server` int(10) NOT NULL DEFAULT '1',
  `Comment` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Interactions`;
CREATE TABLE IF NOT EXISTS `HostFact_Interactions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Category` int(10) NOT NULL DEFAULT '0',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Type` varchar(15) NOT NULL DEFAULT '',
  `Author` int(10) NOT NULL DEFAULT '0',
  `Message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Invoice`;
CREATE TABLE IF NOT EXISTS `HostFact_Invoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `InvoiceCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Term` int(5) NOT NULL DEFAULT '0',
  `Discount` float NOT NULL DEFAULT '0',
  `IgnoreDiscount` tinyint(1) NOT NULL DEFAULT '0',
  `Coupon` varchar(50) NOT NULL DEFAULT '',
  `ReferenceNumber` varchar(255) NOT NULL DEFAULT '',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `Sex` CHAR(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(111) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Authorisation` enum('yes','no') NOT NULL DEFAULT 'yes',
  `InvoiceMethod` tinyint(2) NOT NULL DEFAULT '0',
  `Template` int(10) NOT NULL DEFAULT '0',
  `SentDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Sent` int(3) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `SubStatus` VARCHAR( 50 ) NOT NULL,
  `Reminders` int(3) NOT NULL DEFAULT '0',
  `ReminderDate` date NOT NULL DEFAULT '0000-00-00',
  `Summations` int(3) NOT NULL DEFAULT '0',
  `SummationDate` date NOT NULL DEFAULT '0000-00-00',
  `TaxRate` float(7,6) NOT NULL DEFAULT '0.00',
  `Compound` enum('yes','no') NOT NULL DEFAULT 'no',
  `AmountExcl` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `AmountIncl` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `AmountPaid` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `PaymentMethod` varchar(10) NOT NULL DEFAULT 'wire',
  `PaymentMethodID` INT( 10 ) NOT NULL,
  `Paid` tinyint(1) NOT NULL DEFAULT '0',
  `PayDate` date NOT NULL,
  `TransactionID` varchar(50) NOT NULL DEFAULT '',
  `SDDBatchID` VARCHAR( 15 ) NOT NULL DEFAULT '',
  `AuthTrials` int(2) NOT NULL DEFAULT '0',
  `VatCalcMethod` ENUM( 'excl', 'incl' ) NOT NULL DEFAULT 'excl',
  `VatShift` VARCHAR( 5 ) NOT NULL DEFAULT '',
  `Description` text NOT NULL,
  `Comment` text NOT NULL,
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `CorrespondingInvoice` INT(10) NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `InvoiceCode` (`InvoiceCode`),
  KEY `Debtor` (`Debtor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_InvoiceElements`;
CREATE TABLE IF NOT EXISTS `HostFact_InvoiceElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `InvoiceCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Number` float NOT NULL DEFAULT '0',
  `NumberSuffix` VARCHAR( 20 ) NOT NULL DEFAULT  '',
  `ProductCode` varchar(50) NOT NULL DEFAULT '',
  `Description` text NOT NULL DEFAULT '',
  `PriceExcl` double NOT NULL DEFAULT '0',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `DiscountPercentage` float NOT NULL,
  `DiscountPercentageType` ENUM( 'line', 'subscription' ) NOT NULL DEFAULT 'line',
  `Periods` int(3) NOT NULL DEFAULT '0',
  `Periodic` char(1) NOT NULL DEFAULT '',
  `PeriodicID` int(10) NOT NULL DEFAULT '0',
  `StartPeriod` date NOT NULL DEFAULT '0000-00-00',
  `EndPeriod` date NOT NULL DEFAULT '0000-00-00',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Ordering` int(3) NOT NULL DEFAULT '0',
  `LineAmountExcl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `LineAmountIncl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `ProductType` VARCHAR( 25 ) NOT NULL, 
  `Reference` INT( 10 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `InvoiceCode` (`InvoiceCode`),
  KEY `ProductCode` (`ProductCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Log`;
CREATE TABLE IF NOT EXISTS `HostFact_Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Type` VARCHAR( 25 ) NOT NULL DEFAULT 'debtor',
  `Reference` int(10) NOT NULL DEFAULT '0',
  `Who` VARCHAR(10) NOT NULL DEFAULT '0',
  `Action` varchar(255) NOT NULL DEFAULT '',
  `Values` varchar(255) NOT NULL DEFAULT '',
  `Translate` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `Page` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `Type` (`Type`,`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_MessageLog`;
CREATE TABLE IF NOT EXISTS `HostFact_MessageLog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Type` enum('error','warning','success') NOT NULL,
  `Message` varchar(100) NOT NULL DEFAULT '',
  `Values` varchar(255) NOT NULL DEFAULT '',
  `ObjectType` varchar(100) NOT NULL DEFAULT '',
  `Reference` int(10) NOT NULL DEFAULT '0',
  `Who` VARCHAR(10) NOT NULL DEFAULT '0',
  `Page` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Modules`;
CREATE TABLE IF NOT EXISTS `HostFact_Modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ModuleType` VARCHAR( 25 ) NOT NULL DEFAULT 'other',
  `Module` varchar(25) NOT NULL,
  `Language` varchar(10) NOT NULL,
  `Active` enum('active','disabled') NOT NULL DEFAULT 'disabled',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `HostFact_Modules` (`id`, `ModuleType`, `Module`, `Language`, `Active`) VALUES (1, 'product', 'ssl', 'nl_NL', 'active');
INSERT INTO `HostFact_Modules` (`id`, `ModuleType`, `Module`, `Language`, `Active`) VALUES (NULL, 'product', 'vps', 'nl_NL', 'disabled');
INSERT INTO `HostFact_Modules` (`id`, `ModuleType`, `Module`, `Language`, `Active`) VALUES (NULL, 'dns', 'dnsmanagement', 'nl_NL', 'disabled');

DROP TABLE IF EXISTS `HostFact_NewCustomers`;
CREATE TABLE IF NOT EXISTS `HostFact_NewCustomers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Username` varchar(100) NOT NULL DEFAULT '',
  `Password` varchar(32) NOT NULL DEFAULT '',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `CompanyNumber` varchar(20) NOT NULL DEFAULT '',
  `TaxNumber` varchar(20) NOT NULL DEFAULT '',
  `LegalForm` varchar(15) NOT NULL DEFAULT '',
  `Sex` char(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(100) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `BirthDate` date NOT NULL DEFAULT '0000-00-00',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(25) NOT NULL DEFAULT '',
  `MobileNumber` varchar(25) NOT NULL DEFAULT '',
  `FaxNumber` varchar(25) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `InvoiceMethod` tinyint(2) NOT NULL DEFAULT '0',
  `InvoiceCompanyName` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `InvoiceSex` CHAR(1) NOT NULL DEFAULT '',
  `InvoiceInitials` varchar(25) NOT NULL DEFAULT '',
  `InvoiceSurName` varchar(100) NOT NULL DEFAULT '',
  `InvoiceAddress` varchar(100) NOT NULL DEFAULT '',
  `InvoiceAddress2` varchar(100) NOT NULL DEFAULT '',
  `InvoiceZipCode` varchar(10) NOT NULL DEFAULT '',
  `InvoiceCity` varchar(100) NOT NULL DEFAULT '',
  `InvoiceState` varchar(100) NOT NULL DEFAULT '',
  `InvoiceCountry` varchar(10) NOT NULL DEFAULT '',
  `InvoiceEmailAddress` varchar(255) NOT NULL DEFAULT '',
  `InvoiceAuthorisation` enum('yes','no') NOT NULL DEFAULT 'yes',
  `InvoiceTemplate` int(10) NOT NULL DEFAULT '0',
  `PriceQuoteTemplate` int(10) NOT NULL DEFAULT '0',
  `AccountNumber` varchar(50) NOT NULL DEFAULT '',
  `AccountBIC` varchar(50) NOT NULL DEFAULT '',
  `AccountName` varchar(100) NOT NULL DEFAULT '',
  `AccountBank` varchar(100) NOT NULL DEFAULT '',
  `AccountCity` varchar(100) NOT NULL DEFAULT '',
  `DefaultLanguage` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_NewOrder`;
CREATE TABLE IF NOT EXISTS `HostFact_NewOrder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `OrderCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Customer` int(10) NOT NULL DEFAULT '0',
  `Type` enum('new','debtor') NOT NULL DEFAULT 'new',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Term` int(5) NOT NULL DEFAULT '0',
  `Discount` float NOT NULL DEFAULT '0',
  `IgnoreDiscount` tinyint(1) NOT NULL DEFAULT '0',
  `Coupon` varchar(50) NOT NULL DEFAULT '',
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `Sex` CHAR(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(111) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Authorisation` enum('yes','no') NOT NULL DEFAULT 'yes',
  `InvoiceMethod` tinyint(2) NOT NULL DEFAULT '0',
  `Template` int(10) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `TaxRate` float(7,6) NOT NULL DEFAULT '0.00',
  `Compound` enum('yes','no') NOT NULL DEFAULT 'no',
  `AmountExcl` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
  `AmountIncl` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
  `VatCalcMethod` ENUM( 'excl', 'incl' ) NOT NULL DEFAULT 'excl',
  `VatShift` VARCHAR( 5 ) NOT NULL DEFAULT '',
  `PaymentMethod` varchar(10) NOT NULL DEFAULT 'wire',
  `PaymentMethodID` INT( 10 ) NOT NULL,
  `Paid` tinyint(1) NOT NULL DEFAULT '0',
  `TransactionID` varchar(50) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `IPAddress` varchar(40) NOT NULL DEFAULT '',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Employee` int(10) NULL DEFAULT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `OrderCode` (`OrderCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_NewOrderElements`;
CREATE TABLE IF NOT EXISTS `HostFact_NewOrderElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `OrderCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Number` float NOT NULL DEFAULT '0',
  `NumberSuffix` VARCHAR( 20 ) NOT NULL DEFAULT  '',
  `ProductCode` varchar(50) NOT NULL DEFAULT '',
  `Description` text NOT NULL DEFAULT '',
  `PriceExcl` DOUBLE NOT NULL DEFAULT '0',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `DiscountPercentage` float NOT NULL,
  `DiscountPercentageType` ENUM( 'line', 'subscription' ) NOT NULL DEFAULT 'line',
  `Periods` tinyint(2) NOT NULL DEFAULT '0',
  `Periodic` char(1) NOT NULL DEFAULT '',
  `StartPeriod` date NOT NULL DEFAULT '0000-00-00',
  `EndPeriod` date NOT NULL DEFAULT '0000-00-00',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Ordering` int(3) NOT NULL DEFAULT '0',
  `LineAmountExcl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `LineAmountIncl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `ProductType` VARCHAR( 25 ) NOT NULL, 
  `Reference` INT( 10 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `OrderCode` (`OrderCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_OrderForms`;
CREATE TABLE IF NOT EXISTS `HostFact_OrderForms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Language` VARCHAR( 10 ) NOT NULL,
  `Available` enum('yes','no') NOT NULL DEFAULT 'yes',
  `ProductGroups` TEXT NOT NULL,
  `ShowPrices` enum('yes','no') NOT NULL DEFAULT 'yes',
  `ShowDiscountCoupon` enum('yes','no') NOT NULL DEFAULT 'yes',
  `VatCalcMethod` ENUM( 'default', 'excl', 'incl' ) NOT NULL DEFAULT 'default',
  `PeriodChoice` enum('yes','default','no') NOT NULL DEFAULT 'no',
  `PeriodDefaultPeriods` int(2) NOT NULL,
  `PeriodDefaultPeriodic` char(1) NOT NULL,
  `PeriodChoiceOptions` TEXT NOT NULL,
  `OtherSettings` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Packages`;
CREATE TABLE IF NOT EXISTS `HostFact_Packages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `PackageName` varchar(255) NOT NULL DEFAULT '',
  `PackageType` enum('normal','reseller') NOT NULL DEFAULT 'normal',
  `Product` int(10) NOT NULL DEFAULT '0',
  `Server` int(10) DEFAULT '0',
  `Template` varchar(5) NOT NULL DEFAULT '',
  `TemplateName` varchar(50) NOT NULL,
  `Status` tinyint(2) NOT NULL DEFAULT '1',
  `BandWidth` varchar(15) NOT NULL DEFAULT '0',
  `uBandWidth` tinyint(1) NOT NULL DEFAULT '0',
  `DiscSpace` varchar(15) NOT NULL DEFAULT '0',
  `uDiscSpace` tinyint(1) NOT NULL DEFAULT '0',
  `Domains` varchar(15) NOT NULL DEFAULT '0',
  `uDomains` tinyint(1) NOT NULL DEFAULT '0',
  `SubDomains` varchar(15) NOT NULL DEFAULT '0',
  `uSubDomains` tinyint(1) NOT NULL DEFAULT '0',
  `IPs` varchar(15) NOT NULL DEFAULT '0',
  `uIPs` tinyint(1) NOT NULL DEFAULT '0',
  `EmailAccounts` varchar(15) NOT NULL DEFAULT '0',
  `uEmailAccounts` tinyint(1) NOT NULL DEFAULT '0',
  `EmailForwarders` varchar(15) NOT NULL DEFAULT '0',
  `uEmailForwarders` tinyint(1) NOT NULL DEFAULT '0',
  `MailingLists` varchar(15) NOT NULL DEFAULT '0',
  `uMailingLists` tinyint(1) NOT NULL DEFAULT '0',
  `Autoresponders` varchar(15) NOT NULL DEFAULT '0',
  `uAutoresponders` tinyint(1) NOT NULL DEFAULT '0',
  `MySQLDatabases` varchar(15) NOT NULL DEFAULT '0',
  `uMySQLDatabases` tinyint(1) NOT NULL DEFAULT '0',
  `Domainpointers` varchar(15) NOT NULL DEFAULT '0',
  `uDomainpointers` tinyint(1) NOT NULL DEFAULT '0',
  `FTPAccounts` varchar(15) NOT NULL DEFAULT '0',
  `uFTPAccounts` tinyint(1) NOT NULL DEFAULT '0',
  `AnonFTP` tinyint(1) NOT NULL DEFAULT '0',
  `CGIAccess` tinyint(1) NOT NULL DEFAULT '0',
  `PHPAccess` tinyint(1) NOT NULL DEFAULT '0',
  `SpamAssasin` tinyint(1) NOT NULL DEFAULT '0',
  `SSLAccess` tinyint(1) NOT NULL DEFAULT '0',
  `SSHAccess` tinyint(1) NOT NULL DEFAULT '0',
  `SSHUserAccess` tinyint(1) NOT NULL DEFAULT '0',
  `Cronjobs` tinyint(1) NOT NULL DEFAULT '0',
  `Sysinfo` tinyint(1) NOT NULL DEFAULT '0',
  `DNSControl` tinyint(1) NOT NULL DEFAULT '0',
  `PersonalDNS` tinyint(1) NOT NULL DEFAULT '0',
  `SharedIP` tinyint(1) NOT NULL DEFAULT '0',
  `EmailTemplate` int(10) NOT NULL DEFAULT '0',
  `EmailAuto` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  `PdfTemplate` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_PaymentMethods`;
CREATE TABLE IF NOT EXISTS `HostFact_PaymentMethods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `InternalName` varchar(50) NOT NULL,
  `Directory` varchar(255) NOT NULL,
  `Availability` tinyint(1) NOT NULL DEFAULT '0',
  `Image` varchar(255) NOT NULL,
  `Extra` varchar(255) NOT NULL,
  `FeeType` varchar(10) NOT NULL,
  `FeeAmount` DOUBLE NOT NULL,
  `FeeDesc` varchar(255) NOT NULL,
  `PaymentType` ENUM( 'auth', 'wire', 'other', 'ideal', 'paypal') NOT NULL,
  `MerchantID` varchar(255) NOT NULL,
  `Password` text NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Ordering` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


INSERT INTO `HostFact_PaymentMethods` (`id`, `Title`, `Directory`, `Availability`, `Image`, `Extra`, `FeeType`, `FeeAmount`, `FeeDesc`, `PaymentType`, `MerchantID`, `Password`, `Description`, `Ordering`) VALUES
(1, 'Betaling via bankoverschrijving', '', 1, 'overboeking.jpg', '', '', 0, '', 'wire', '', '', '', 1);
INSERT INTO `HostFact_PaymentMethods` (`id`, `Title`, `Directory`, `Availability`, `Image`, `Extra`, `FeeType`, `FeeAmount`, `FeeDesc`, `PaymentType`, `MerchantID`, `Password`, `Description`, `Ordering`) VALUES
(2, 'Betaling via automatische incasso', 'payment.auth', 1, 'automatischeincasso.jpg', 'Vul hieronder uw gegevens in', '', 0, 'Transactiekosten machtiging', 'auth', '', '', '', 2);

DROP TABLE IF EXISTS `HostFact_PeriodicElements`;
CREATE TABLE IF NOT EXISTS `HostFact_PeriodicElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ProductCode` varchar(50) NOT NULL DEFAULT '0',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Description` text NOT NULL DEFAULT '',
  `PeriodicType` VARCHAR( 25 ) NOT NULL DEFAULT 'other',
  `Reference` int(10) NOT NULL DEFAULT '0',
  `StartPeriod` date NOT NULL DEFAULT '0000-00-00',
  `EndPeriod` date NOT NULL DEFAULT '0000-00-00',
  `Periods` tinyint(2) NOT NULL DEFAULT '0',
  `Periodic` char(1) NOT NULL DEFAULT '',
  `LastDate` date NOT NULL DEFAULT '0000-00-00',
  `NextDate` date NOT NULL DEFAULT '0000-00-00',
  `PriceExcl` double NOT NULL DEFAULT '0',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `DiscountPercentage` FLOAT NOT NULL DEFAULT '0',
  `Number` float NOT NULL DEFAULT '0',
  `NumberSuffix` VARCHAR( 20 ) NOT NULL DEFAULT  '',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `AutoRenew` ENUM( 'yes', 'no', 'once' ) NOT NULL DEFAULT 'yes',
  `TerminationDate` date NOT NULL DEFAULT '0000-00-00',
  `Reminder` char(3) NOT NULL DEFAULT '',
  `ReminderDate` date NOT NULL DEFAULT '0000-00-00',
  `InvoiceAuthorisation` enum( 'yes', 'no' ) NOT NULL DEFAULT 'yes',
  `StartContract` date NOT NULL DEFAULT '0000-00-00',
  `EndContract` date NOT NULL DEFAULT '0000-00-00',
  `ContractRenewalDate` date NOT NULL DEFAULT '0000-00-00',
  `ContractPeriods` tinyint(2) NOT NULL DEFAULT '0',
  `ContractPeriodic` char(1) NOT NULL DEFAULT '',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Debtor` (`Debtor`),
  KEY `ProductCode` (`ProductCode`),
  KEY `PeriodicType` (`PeriodicType`,`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_PeriodicElements_Renewals`;
CREATE TABLE IF NOT EXISTS `HostFact_PeriodicElements_Renewals` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `PeriodicID` int(10) NOT NULL,
  `Date` datetime NOT NULL,
  `IPAddress` varchar(40) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Role` varchar(100) NOT NULL,
  `StartContract` date NOT NULL,
  `EndContract` date NOT NULL,
  `Periods` tinyint(2) NOT NULL,
  `Periodic` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `PeriodicID` (`PeriodicID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `HostFact_PriceQuote`;
CREATE TABLE IF NOT EXISTS `HostFact_PriceQuote` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `PriceQuoteCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Term` int(5) NOT NULL DEFAULT '0',
  `Discount` float NOT NULL DEFAULT '0',
  `IgnoreDiscount` tinyint(1) NOT NULL DEFAULT '0',
  `Coupon` varchar(50) NOT NULL DEFAULT '',
  `ReferenceNumber` varchar(255) NOT NULL,
  `CompanyName` varchar(100) NOT NULL DEFAULT '',
  `Sex` CHAR(1) NOT NULL DEFAULT '',
  `Initials` varchar(25) NOT NULL DEFAULT '',
  `SurName` varchar(111) NOT NULL DEFAULT '',
  `Address` varchar(100) NOT NULL DEFAULT '',
  `Address2` varchar(100) NOT NULL DEFAULT '',
  `ZipCode` varchar(10) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `State` varchar(100) NOT NULL DEFAULT '',
  `Country` varchar(10) NOT NULL DEFAULT '',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Authorisation` enum('yes','no') NOT NULL DEFAULT 'no',
  `PriceQuoteMethod` tinyint(2) NOT NULL DEFAULT '0',
  `Template` int(10) NOT NULL DEFAULT '0',
  `SentDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Sent` int(3) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `TaxRate` float(7,6) NOT NULL DEFAULT '0.00',
  `Compound` enum('yes','no') NOT NULL DEFAULT 'no',
  `AmountExcl` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
  `AmountIncl` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
  `VatCalcMethod` ENUM( 'excl', 'incl' ) NOT NULL DEFAULT 'excl',
  `VatShift` VARCHAR( 5 ) NOT NULL DEFAULT '',
  `Description` text NOT NULL DEFAULT '',
  `Comment` TEXT NOT NULL,
  `Reason` TEXT NOT NULL,
  `IPAddress` VARCHAR( 40 ) NOT NULL,
  `AcceptName`  varchar(100) NOT NULL DEFAULT '',
  `AcceptEmailAddress`  varchar(255) NOT NULL DEFAULT '',
  `AcceptComment`  text NOT NULL DEFAULT '',
  `AcceptSignatureBase64`  text NOT NULL DEFAULT '',
  `AcceptDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `AcceptIPAddress`  varchar(100) NOT NULL DEFAULT '',
  `AcceptUserAgent`  varchar(255) NOT NULL DEFAULT '',
  `AcceptPDF`  int(10) NOT NULL DEFAULT '0',
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Debtor` (`Debtor`),
  UNIQUE KEY `PriceQuoteCode` (`PriceQuoteCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_PriceQuoteElements`;
CREATE TABLE IF NOT EXISTS `HostFact_PriceQuoteElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `PriceQuoteCode` varchar(50) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Number` float NOT NULL DEFAULT '0',
  `NumberSuffix` VARCHAR( 20 ) NOT NULL DEFAULT  '',
  `ProductCode` varchar(50) NOT NULL DEFAULT '',
  `Description` text NOT NULL DEFAULT '',
  `PriceExcl` DOUBLE NOT NULL DEFAULT '0',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `DiscountPercentage` float NOT NULL,
  `DiscountPercentageType` ENUM( 'line', 'subscription' ) NOT NULL DEFAULT 'line',
  `Periods` tinyint(2) NOT NULL DEFAULT '0',
  `Periodic` char(1) NOT NULL DEFAULT '',
  `PeriodicID` int(10) NOT NULL DEFAULT '0',
  `StartPeriod` date NOT NULL DEFAULT '0000-00-00',
  `EndPeriod` date NOT NULL DEFAULT '0000-00-00',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Ordering` int( 3 ) NOT NULL DEFAULT '0',
  `LineAmountExcl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `LineAmountIncl` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  PRIMARY KEY (`id`),
  KEY `PriceQuoteCode` (`PriceQuoteCode`),
  KEY `ProductCode` (`ProductCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Products`;
CREATE TABLE IF NOT EXISTS `HostFact_Products` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ProductCode` varchar(50) NOT NULL DEFAULT '',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `ParentIdentifier` int(10) NOT NULL DEFAULT '0',
  `ProductName` varchar(100) NOT NULL DEFAULT '',
  `ProductKeyPhrase` text NOT NULL DEFAULT '',
  `ProductDescription` text NOT NULL,
  `NumberSuffix` varchar(20) NOT NULL DEFAULT '',
  `HasCustomPrice` ENUM( 'no', 'period' ) NOT NULL DEFAULT 'no',
  `PriceExcl` double NOT NULL DEFAULT '0',
  `PricePeriod` varchar(10) NOT NULL DEFAULT '',
  `TaxPercentage` float NOT NULL DEFAULT '0',
  `Cost` float NOT NULL DEFAULT '0',
  `Ordered` int(10) NOT NULL DEFAULT '0',
  `Sold` int(10) NOT NULL DEFAULT '0',
  `ProductType` VARCHAR(25) NOT NULL DEFAULT 'other',
  `ProductTld` varchar(63) NOT NULL,
  `PackageID` int(10) NOT NULL DEFAULT '0',
  `Free1` int(10) NOT NULL DEFAULT '0',
  `Free2` int(10) NOT NULL DEFAULT '0',
  `Free3` varchar(255) NOT NULL DEFAULT '',
  `Free4` varchar(255) NOT NULL DEFAULT '',
  `Free5` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ProductCode` (`ProductCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Product_Custom_Prices`;
CREATE TABLE IF NOT EXISTS `HostFact_Product_Custom_Prices` (
  `ProductID` int(10) NOT NULL,
  `PriceType` enum('period') NOT NULL DEFAULT 'period',
  `Periods` int(3) NOT NULL,
  `Periodic` char(1) NOT NULL,
  `PriceExcl` double NOT NULL DEFAULT '0',
  UNIQUE KEY `ProductID` (`ProductID`,`PriceType`,`Periods`,`Periodic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Registrar`;
CREATE TABLE IF NOT EXISTS `HostFact_Registrar` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) NOT NULL DEFAULT '',
  `Class` varchar(50) NOT NULL DEFAULT '',
  `License` varchar(40) NOT NULL DEFAULT '',
  `User` varchar(100) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL DEFAULT '',
  `DNS1` varchar(255) NOT NULL DEFAULT '',
  `DNS2` varchar(255) NOT NULL DEFAULT '',
  `DNS3` varchar(255) NOT NULL DEFAULT '',
  `Testmode` tinyint(1) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `AutoRenew` tinyint(1) NOT NULL DEFAULT '0',
  `AdminCustomer` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes',
  `AdminHandle` INT( 10 ) NOT NULL ,
  `TechCustomer` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes',
  `TechHandle` INT( 10 ) NOT NULL, 
  `Setting1` text NOT NULL,
  `Setting2` text NOT NULL,
  `Setting3` text NOT NULL,
  `DomainEnabled` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes', 
  `SSLEnabled` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
  `DefaultDNSTemplate` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_SDD_BatchElements`;
CREATE TABLE IF NOT EXISTS `HostFact_SDD_BatchElements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `BatchID` varchar(15) NOT NULL,
  `Debtor` int(10) NOT NULL,
  `InvoiceID` int(10) NOT NULL,
  `Amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `MandateID` varchar(35) NOT NULL,
  `MandateDate` date NOT NULL,
  `MandateType` enum('','FRST','RCUR','FNAL','OOFF') NOT NULL,
  `IBAN` varchar(34) NOT NULL,
  `BIC` varchar(11) NOT NULL,
  `Status` enum('draft','success','failed','cancelled') NOT NULL DEFAULT 'draft',
  `Reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_SDD_Batches`;
CREATE TABLE IF NOT EXISTS `HostFact_SDD_Batches` (
  `BatchID` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Status` enum('draft','downloadable','downloaded','processed','rejected','cancelled') NOT NULL,
  `DownloadDate` date NOT NULL,
  `SDD_ID` varchar(50) NOT NULL,
  `SDD_IBAN` varchar(34) NOT NULL,
  `SDD_BIC` varchar(11) NOT NULL,
  `Count` int(10) NOT NULL,
  `Amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00',
  `ProcessingTime` tinyint(2) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_SDD_Mandates`;
CREATE TABLE IF NOT EXISTS `HostFact_SDD_Mandates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL,
  `MandateID` varchar(35) NOT NULL,
  `MandateDate` date NOT NULL,
  `MandateType` enum('','FRST','RCUR','FNAL','OOFF') NOT NULL,
  `Status` enum('active','suspended') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `MandateID` (`MandateID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Servers`;
CREATE TABLE IF NOT EXISTS `HostFact_Servers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL DEFAULT '',
  `OS` varchar(50) NOT NULL DEFAULT '',
  `Panel` varchar(50) NOT NULL DEFAULT '',
  `Location` varchar(255) NOT NULL DEFAULT '',
  `Port` varchar(10) NOT NULL DEFAULT '',
  `Username` varchar(255) NOT NULL DEFAULT '',
  `Password` text NOT NULL,
  `IP` varchar(40) NOT NULL DEFAULT '',
  `AdditionalIP` VARCHAR( 40 ) NOT NULL DEFAULT '',
  `DomainType` ENUM( 'additional', 'pointer', 'alias' ) NOT NULL DEFAULT 'additional',
  `DNS1` varchar(255) NOT NULL DEFAULT '',
  `DNS2` varchar(255) NOT NULL DEFAULT '',
  `DNS3` varchar(255) NOT NULL DEFAULT '',
  `DefaultDNSTemplate` int(3) NOT NULL,
  `Settings` TEXT NOT NULL DEFAULT '',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_ServersPleskClients`;
CREATE TABLE `HostFact_ServersPleskClients` (
	`ServerID` int(10) NOT NULL, 
	`DebtorID` int(10) NOT NULL, 
	`ClientID` int(10) NOT NULL, 
	UNIQUE KEY `ServerID` (`ServerID`,`DebtorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Services`;
CREATE TABLE IF NOT EXISTS `HostFact_Services` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL,
  `ServiceType` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Settings`;
CREATE TABLE IF NOT EXISTS `HostFact_Settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Variable` varchar(255) NOT NULL DEFAULT '',
  `Value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=232 ;

INSERT INTO `HostFact_Settings` (`id`, `Variable`, `Value`) VALUES
(1, 'LICENSE', ''),
(2, 'LICENSE_UPDATE', ''),
(3, 'LICENSE_DATE', ''),
(4, 'LICENSE_UPDATE_DATE', ''),
(5, 'LICENSE_UPDATE_EXPIRE_DATE', ''),
(6, 'INSTALL_DATE', ''),
(7, 'UPDATE_DATE', ''),
(8, 'SOFTWARE_NAME', 'HostFact'),
(9, 'SOFTWARE_VERSION', ''),
(10, 'DEBTORCODE_PREFIX', 'DB'),
(11, 'DEBTORCODE_NUMBER', '10000'),
(12, 'CREDITORCODE_PREFIX', 'CD'),
(13, 'CREDITORCODE_NUMBER', '50000'),
(14, 'PRODUCTCODE_PREFIX', 'P'),
(15, 'PRODUCTCODE_NUMBER', '001'),
(16, 'INVOICECODE_PREFIX', 'F'),
(17, 'INVOICECODE_NUMBER', '0001'),
(18, 'PRICEQUOTECODE_PREFIX', 'OF'),
(19, 'PRICEQUOTECODE_NUMBER', '0001'),
(20, 'CREDITINVOICECODE_PREFIX', 'CF'),
(21, 'CREDITINVOICECODE_NUMBER', '0001'),
(22, 'ORDERCODE_PREFIX', 'B'),
(23, 'ORDERCODE_NUMBER', '0001'),
(24, 'TICKETID_PREFIX', 'T[jaar]'),
(25, 'TICKETID_NUMBER', '0001'),
(26, 'DATE_FORMAT', '%d-%m-%Y'),
(27, 'MAX_RESULTS_LIST', '25'),
(32, 'DOMAINWARNING', '15'),
(33, 'BCC_EMAILADDRESS', ''),
(34, 'PRICEQUOTE_TERM', '30'),
(35, 'INVOICE_TERM', '14'),
(36, 'TICKET_POP3_SERVER', ''),
(37, 'TICKET_POP3_PORT', '110'),
(38, 'TICKET_SMTP_SERVER', ''),
(39, 'TICKET_SMTP_PORT', '25'),
(40, 'TICKET_EMAILADDRESS', ''),
(41, 'TICKET_PASSWORD', '7'),
(42, 'BACKUP_EMAILADDRESS', ''),
(43, 'BACKUP_DAYS', '7'),
(44, 'BACKUP_DIR', 'backup/'),
(45, 'CONTROLPANEL_LOCATION', ''),
(46, 'CONTROLPANEL_USER', ''),
(47, 'CONTROLPANEL_PASS', ''),
(48, 'CONTROLPANEL', ''),
(49, 'SERVER_OS', 'linuxfedora'),
(50, 'BACKUP_LASTDATE', ''),
(52, 'SIDN_POP3_SERVER', ''),
(53, 'SIDN_POP3_EMAIL', ''),
(54, 'SIDN_POP3_PASS', ''),
(55, 'SIDN_POP3_PORT', '110'),
(56, 'GROUP_DOMAIN', '1'),
(57, 'GROUP_HOSTING', '2'),
(64, 'WELCOME_MAIL', '5'),
(65, 'REMINDER_MAIL', '3'),
(66, 'SUMMATION_MAIL', '4'),
(67, 'CUSTOMER_ALLOW_EDIT', '2'),
(69, 'SMTP_ON', '0'),
(70, 'SMTP_HOST', ''),
(71, 'SMTP_AUTH', '0'),
(72, 'SMTP_USERNAME', ''),
(73, 'SMTP_PASSWORD', '#'),
(96, 'PAYMENT_MAIL', '6'),
(95, 'TEMPLATE_PREVIEW', 'yes'),
(74, 'CONTROLPANEL_PORT', '8443'),
(75, 'ORDERACCEPT_STATUS', '0'),
(77, 'CONTROLPANEL_IP2', ''),
(81, 'INVOICE_ELEMENTS_ON_PDF', '10'),
(82, 'PERIODIC_INVOICE_DAYS', '14'),
(83, 'ORDER_ACCEPT_WELCOME_MAIL', '0'),
(84, 'AUTOMATIC_RECOGNITION', '1'),
(99, 'DOMAIN_HOME_WARNING', 'on'),
(98, 'STANDARD_SERVER', ''),
(97, 'INVOICE_DIRECT_SENT', '0'),
(105, 'PAYMENT_MAIL_WHEN', ''),
(106, 'CREDIT_TEXT', 'Corresponderende factuur: [invoice->InvoiceCode]'),
(107, 'CREDIT_AUTO_SENT', '0'),
(108, 'CREDIT_TEMPLATE', '5'),
(109, 'CREDIT_SHOW_NOT_SENT', '7'),
(112, 'CUSTOMER_IDEAL', '0'),
(113, 'CUSTOMER_PAYPAL', '0'),
(114, 'MAX_SENT_BATCHES', '25'),
(116, 'SENT_BATCHES', '0'),
(117, 'SIDN_202_REPLY', 'no'),
(118, 'TICKET_NOTIFY', '0'),
(119, 'PERIODIC_REMINDER_SENT', '0'),
(120, 'PERIODIC_REMINDER_DAYS', '14'),
(121, 'PERIODIC_REMINDER_MAIL', '11'),
(122, 'TICKET_NOTIFY_CUSTOMER', '0'),
(123, 'TICKET_NOTIFY_CUSTOMER_EMAIL', ''),
(125, 'SIDN_COPY_MAIL', '0'),
(126, 'SIDN_COPY_MAILADDRESS', ''),
(127, 'INVOICECODE_CONCEPT', 'yes'),
(128, 'INVOICE_REMINDER_SENT_PDF', '1'),
(129, 'INVOICE_SUMMATION_SENT_PDF', '1'),
(131, 'REMINDER_MAIL_SECOND', ''),
(132, 'TICKET_NOTIFY_EMAILADDRESS', ''),
(140, 'DOMAINAUTOTRANSFER', 'false'),
(141, 'DOMAINAUTOREGISTER', 'true'),
(142, 'INVOICE_REMINDER_LETTER', '6'),
(144, 'SIDN_250_REPLY', 'no'),
(145, 'INVOICE_SUMMATION_LETTER', '7'),
(147, 'TICKET_NOTIFY_EMAIL_TEMPLATE', ''),
(148, 'IDEAL_EMAIL', ''),
(189, 'INVOICE_REMINDER_NUMBER', '2'),
(190, 'INVOICE_REMINDER_TERM', '14'),
(191, 'INVOICE_SUMMATION_NUMBER', '1'),
(192, 'INVOICE_SUMMATION_TERM', '14'),
(196, 'ACCOUNTCODE_PREFIX', 'id'),
(197, 'ACCOUNTCODE_NUMBER', '00001'),
(199, 'GROUP_OPTIES', '3'),
(198, 'ACCOUNT_GENERATION', '1'),
(200, 'MULTIPLE_DOMAIN', 'no'),
(201, 'STANDARD_INVOICEMETHOD', '0'),
(202, 'ORDERMAIL_SENT_BCC', ''),
(203, 'ORDERMAIL_SENT', 'no'),
(204, 'COMPANY_AV_PDF', ''),
(205, 'COMPANY_AV_HTML', 'av.html'),
(206, 'COMPANY_LOGO', ''),
(207, 'COMPANY_EMAIL', ''),
(208, 'COMPANY_NAME', ''),
(209, 'CUSTOMER_WIRE', '1'),
(210, 'CUSTOMER_AUTH', '0'),
(234, 'INVOICE_REMINDER_WARNINGS', ''),
(NULL, 'SIDN_QUEUE', 'no'),
(NULL, 'PERIODIC_REMINDER_MERGE', '1'),
(NULL, 'INVOICE_COLLECT_ENABLED', 'no'),
(NULL, 'LANGUAGE', 'nl_NL'),
(NULL, 'BACKOFFICE_URL', ''),
(NULL, 'TICKET_REACTION_EMAIL_TEMPLATE', '0'),
(NULL, 'LOGIN_COOKIE', 'yes'),
(NULL, 'API_KEY', ''),
(NULL, 'API_ACCESS', ''),
(NULL, 'API_ACTIVE', 'no'),
(NULL, 'INVOICE_COLLECT_TPM', '2'),
(NULL, 'CRONJOB_LASTDATE', ''),
(NULL, 'CONTRACT_RENEW_FOR', 'none'),
(NULL, 'CONTRACT_RENEW_DAYS_BEFORE', '60'),
(NULL, 'CONTRACT_RENEW_MIN_PERIOD', '2'),
(NULL, 'CONTRACT_RENEW_CONFIRM_MAIL', '14'),
(NULL, 'CRONJOB_NOTIFY_MAILADDRESS', ''),
(NULL, 'CRONJOB_NOTIFY_ORDER', ''),
(NULL, 'CRONJOB_NOTIFY_DOMAIN', ''), 
(NULL, 'CRONJOB_NOTIFY_HOSTING', ''),
(NULL, 'TICKET_USE', '0'),
(NULL, 'TICKET_USE_MAIL', '0'),
(NULL, 'TICKET_SENDERNAME', ''),
(NULL, 'ORDERFORM_CSS_COLOR', '#4F94BA'),
(NULL, 'DOMAIN_AUTH_KEY_REQUIRED', 'no'),
(NULL, 'DEFAULT_ORDERFORM', ''),
(NULL, 'ORDERFORM_URL', ''),
(NULL, 'ORDERFORM_TO_PAYMENTDIR', ''),
(NULL, 'PDF_MODULE', 'tcpdf'),
(NULL, 'PDF_PAGE_WIDTH', '210'),
(NULL, 'PDF_PAGE_HEIGHT', '297'),
(NULL, 'CURRENCY_SIGN_LEFT', '€'),
(NULL, 'CURRENCY_SIGN_RIGHT', ''),
(NULL, 'CURRENCY_CODE', 'EUR'),
(NULL, 'AMOUNT_DEC_PLACES', '2'),
(NULL, 'AMOUNT_DEC_SEPERATOR', ','),
(NULL, 'AMOUNT_THOU_SEPERATOR', '.'),
(NULL, 'LICENSE_HASH', ''),
(NULL, 'DIR_CREDIT_INVOICES', 'documents/purchasinginvoices/'),
(NULL, 'DIR_EMAIL_ATTACHMENTS', 'documents/email/'),
(NULL, 'DIR_PDF_FILES', 'pdf/'),
(NULL, 'DIR_TICKET_ATTACHMENTS', 'documents/tickets/'),
(NULL, 'TICKET_NOTIFY_TO_EMPLOYEE', '0'),
(NULL, 'PERIODIC_REMINDER_SENT_FOR', 'all'),
(NULL, 'SDD_ID', ''),
(NULL, 'SDD_TYPE', 'CORE'),
(NULL, 'SDD_DAYS', '1'),
(NULL, 'SDD_NOTICE', '14'),
(NULL, 'SDD_MAIL_NOTIFY', 'yes'),
(NULL, 'SDD_PROCESSING_RCUR', '2'),
(NULL, 'SDD_IBAN', ''),
(NULL, 'SDD_BIC', ''),
(NULL, 'SDD_LIMIT_TRANSACTION', ''),
(NULL, 'SDD_LIMIT_BATCH', ''),
(NULL, 'SDD_MOVED_MAIL', ''),
(NULL, 'SDD_FAILED_MAIL', ''),
(NULL, 'DIR_INVOICE_ATTACHMENTS', 'documents/'),
(NULL, 'CRONJOB_IS_RUNNING', ''),
(NULL, 'VAT_CALC_METHOD', 'excl'),
(NULL, 'API_CLEAN_LOG_AFTER_DAYS', '90'),
(NULL, 'API_LOG_TYPE', 'error'),
(NULL, 'CRONJOB_NOTIFY_SSL', ''),
(NULL, 'SSL_WARN_BEFORE_EXPIRING', '30'),
(NULL, 'SEND_TICKET_BCC', 'no'),
(NULL, 'PASSWORD_GENERATION', 'strong'),
(NULL, 'BACKUP_DELETE_AFTER_DAYS', '180'),
(NULL, 'TERMINATION_NOTICE_PERIOD', '30'),
(NULL, 'TERMINATION_NOTICE_PERIOD_WVD', 'yes'),
(NULL, 'CRONJOB_NOTIFY_TERMINATIONS', ''),
(NULL, 'DOMAIN_SYNC', 'yes'),
(NULL, 'DOMAIN_SYNC_DAYS', '30'),
(NULL, 'DOMAIN_SYNC_EXPDATE', 'yes'),
(NULL, 'DOMAIN_SYNC_NAMESERVERS', 'yes'),
(NULL, 'CUSTOMERPANEL_PASSWORD_PROTECTION', 'secure_passwords'),
(NULL, 'DIR_DEBTOR_ATTACHMENTS', 'documents/debtor/'),
(NULL, 'DIR_CREDITOR_ATTACHMENTS', 'documents/creditor/'),
(NULL, 'HOSTING_UPGRADE_FINANCIAL_PROCESSING', 'existing_period'),
(NULL, 'HOSTING_UPGRADE_PREFIX_UPGRADE', 'Overstap naar [hosting->NewPackageName]'),
(NULL, 'HOSTING_UPGRADE_PREFIX_REFUND', 'Verrekening [hosting->OldPackageName]'),
(NULL, 'HOSTING_UPGRADE_CREATE_INVOICE', 'always'),
(NULL, 'SHOW_PRODUCT_SEARCH_GROUPS', 'no'),
(NULL, 'SHOW_DEBTOR_SEARCH_GROUPS', 'no'),
(NULL, 'SHOW_CREDITOR_SEARCH_GROUPS', 'no'),
(NULL, 'IS_INTERNATIONAL', ''),
(NULL, 'DKIM_DOMAINS',  ''),
(NULL, 'IP_WHITELIST',  ''),
(NULL, 'IP_BLACKLIST',  ''),
(NULL, 'INVOICE_EMAIL_ATTACHMENTS', 'pdf'),
(NULL, 'CRONJOB_ACCOUNTING_IS_RUNNING',  ''),
(NULL, 'CRONJOB_ACCOUNTING_LASTDATE', ''),

(NULL, 'CLIENTAREA_HEADER_TITLE',  'Klantenpaneel'),
(NULL, 'CLIENTAREA_LOGO_URL',  ''),
(NULL, 'CLIENTAREA_DEFAULT_LANG',  'nl_NL'),
(NULL, 'CLIENTAREA_PASSWORDFORGOT_EMAIL',  '17'),
(NULL, 'CLIENTAREA_LOGOUT_URL',  ''),
(NULL, 'CLIENTAREA_NOTIFICATION_EMAILADDRESS',  ''),
(NULL, 'CLIENTAREA_TERMS_URL',  ''),
(NULL, 'CLIENTAREA_USE_TICKETSYSTEM',  'no'),
(NULL, 'CLIENTAREA_URL',  ''),
(NULL, 'CLIENTAREA_PRIMARY_COLOR',  '#4F94BA'),
(NULL, 'SUBSCRIPTIONS_EXTEND_SCHEDULED', 'no'),
(NULL, 'ANONYMOUS_FEEDBACK', 'no'),
(NULL, 'ANONYMOUS_FEEDBACK_HASH', ''),
(NULL, 'ANONYMOUS_FEEDBACK_LASTDATE', ''),
(NULL, 'BACKUP_IS_RUNNING', ''),
(NULL, 'SECURITY_HEADERS', '{"x-content-type-options":"1","x-xss-protection":"1","x-frame-options":"1"}'),
(NULL, 'DISABLE_CURLOPT_SSL_VERIFICATION', ''),
(NULL, 'COOKIE_EXPIRATION_TIME', '1'),
(NULL, 'TICKET_POP3_AUTH_TYPE', '');

DROP TABLE IF EXISTS `HostFact_Settings_Countries`;
CREATE TABLE IF NOT EXISTS `HostFact_Settings_Countries` (
  `CountryCode` varchar(10) NOT NULL,
  `EUCountry` enum('yes','no') NOT NULL DEFAULT 'no',
  `Visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  `OrderID` int(10) NOT NULL,
  `nl_NL` varchar(100) NOT NULL,
  `en_EN` varchar(100) NOT NULL,
  PRIMARY KEY (`CountryCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `HostFact_Settings_Countries` (`CountryCode`, `EUCountry`, `Visible`, `OrderID`, `nl_NL`, `en_EN`) VALUES
('AD', 'no', 'yes', 32, 'Andorra', 'Andorra'),
('AE', 'no', 'yes', 190, 'Verenigde Arabische Emiraten', 'United Arab Emirates'),
('AF', 'no', 'yes', 29, 'Afghanistan', 'Afghanistan'),
('AG', 'no', 'yes', 34, 'Antigua en Barbuda', 'Antigua and Barbuda'),
('AL', 'no', 'yes', 30, 'Albanië', 'Albania'),
('AM', 'no', 'yes', 36, 'Armenië', 'Armenia'),
('AO', 'no', 'yes', 33, 'Angola', 'Angola'),
('AR', 'no', 'yes', 35, 'Argentinië', 'Argentina'),
('AT', 'yes', 'yes', 19, 'Oostenrijk', 'Austria'),
('AU', 'no', 'yes', 38, 'Australië', 'Australia'),
('AW', 'no', 'yes', 37, 'Aruba', 'Aruba'),
('AZ', 'no', 'yes', 39, 'Azerbeidzjan', 'Azerbaijan'),
('BA', 'no', 'yes', 49, 'Bosnië en Herzegovina', 'Bosnia and Herzegovina'),
('BB', 'no', 'yes', 43, 'Barbados', 'Barbados'),
('BD', 'no', 'yes', 42, 'Bangladesh', 'Bangladesh'),
('BE', 'yes', 'yes', 1, 'België', 'Belgium'),
('BF', 'no', 'yes', 53, 'Burkina Faso', 'Burkina Faso'),
('BG', 'yes', 'yes', 2, 'Bulgarije', 'Bulgaria'),
('BH', 'no', 'yes', 41, 'Bahrein', 'Bahrain'),
('BI', 'no', 'yes', 54, 'Burundi', 'Burundi'),
('BJ', 'no', 'yes', 45, 'Benin', 'Benin'),
('BM', 'no', 'yes', 46, 'Bermuda', 'Bermuda'),
('BN', 'no', 'yes', 52, 'Brunei', 'Brunei Darussalam'),
('BO', 'no', 'yes', 48, 'Bolivia', 'Bolivia'),
('BQ', 'no', 'yes', 57, 'Caribisch Nederland', 'Caribbean Netherlands'),
('BR', 'no', 'yes', 51, 'Brazilië', 'Brazil'),
('BS', 'no', 'yes', 40, 'Bahama\'s', 'Bahamas'),
('BT', 'no', 'yes', 47, 'Bhutan', 'Bhutan'),
('BW', 'no', 'yes', 50, 'Botswana', 'Botswana'),
('BY', 'no', 'yes', 193, 'Wit-Rusland', 'Belarus'),
('BZ', 'no', 'yes', 44, 'Belize', 'Belize'),
('CA', 'no', 'yes', 56, 'Canada', 'Canada'),
('CD', 'no', 'yes', 64, 'Congo-Kinshasa', 'Congo, the Democratic Republic of the'),
('CF', 'no', 'yes', 58, 'Centraal-Afrikaanse republiek', 'Central African Republic'),
('CG', 'no', 'yes', 63, 'Congo-Brazzaville', 'Congo'),
('CH', 'no', 'yes', 198, 'Zwitserland', 'Switzerland'),
('CI', 'no', 'yes', 96, 'Ivoorkust', 'Côte d\'Ivoire'),
('CL', 'no', 'yes', 59, 'Chili', 'Chile'),
('CM', 'no', 'yes', 102, 'Kameroen', 'Cameroon'),
('CN', 'no', 'yes', 60, 'China', 'China'),
('CO', 'no', 'yes', 61, 'Colombia', 'Colombia'),
('CR', 'no', 'yes', 65, 'Costa Rica', 'Costa Rica'),
('CU', 'no', 'yes', 66, 'Cuba', 'Cuba'),
('CV', 'no', 'yes', 101, 'Kaapverdië', 'Cape Verde'),
('CW', 'no', 'yes', 67, 'Curaçao', 'Curaçao'),
('CY', 'yes', 'yes', 3, 'Cyprus', 'Cyprus'),
('CZ', 'yes', 'yes', 26, 'Tsjechië', 'Czech Republic'),
('DE', 'yes', 'yes', 5, 'Duitsland', 'Germany'),
('DJ', 'no', 'yes', 68, 'Djibouti', 'Djibouti'),
('DK', 'yes', 'yes', 4, 'Denemarken', 'Denmark'),
('DM', 'no', 'yes', 69, 'Dominica', 'Dominica'),
('DO', 'no', 'yes', 70, 'Dominicaanse Republiek', 'Dominican Republic'),
('DZ', 'no', 'yes', 31, 'Algerije', 'Algeria'),
('EC', 'no', 'yes', 71, 'Ecuador', 'Ecuador'),
('EE', 'yes', 'yes', 6, 'Estland', 'Estonia'),
('EG', 'no', 'yes', 72, 'Egypte', 'Egypt'),
('ER', 'no', 'yes', 75, 'Eritrea', 'Eritrea'),
('ES', 'yes', 'yes', 25, 'Spanje', 'Spain'),
('ET', 'no', 'yes', 76, 'Ethiopië', 'Ethiopia'),
('FI', 'yes', 'yes', 7, 'Finland', 'Finland'),
('FJ', 'no', 'yes', 77, 'Fiji', 'Fiji'),
('FM', 'no', 'yes', 125, 'Micronesië', 'Micronesia, Federated States of'),
('FR', 'yes', 'yes', 8, 'Frankrijk', 'France'),
('GA', 'no', 'yes', 79, 'Gabon', 'Gabon'),
('GB', 'no', 'yes', 27, 'Verenigd Koninkrijk', 'United Kingdom'),
('GD', 'no', 'yes', 83, 'Grenada', 'Grenada'),
('GE', 'no', 'yes', 81, 'Georgië', 'Georgia'),
('GH', 'no', 'yes', 82, 'Ghana', 'Ghana'),
('GM', 'no', 'yes', 80, 'Gambia', 'Gambia'),
('GN', 'no', 'yes', 85, 'Guinee', 'Guinee'),
('GQ', 'no', 'yes', 74, 'Equatoriaal-Guinea', 'Equatorial Guinea'),
('GR', 'yes', 'yes', 9, 'Griekenland', 'Greece'),
('GT', 'no', 'yes', 84, 'Guatemala', 'Guatemala'),
('GW', 'no', 'yes', 86, 'Guinee-Bissau', 'Guinee-Bissau'),
('GY', 'no', 'yes', 87, 'Guyana', 'Guyana'),
('HN', 'no', 'yes', 89, 'Honduras', 'Honduras'),
('HR', 'yes', 'yes', 13, 'Kroatië', 'Croatia'),
('HT', 'no', 'yes', 88, 'Haïti', 'Haïti'),
('HU', 'yes', 'yes', 10, 'Hongarije', 'Hungary'),
('ID', 'no', 'yes', 92, 'Indonesië', 'Indonesia'),
('IE', 'yes', 'yes', 11, 'Ierland', 'Ireland'),
('IL', 'no', 'yes', 95, 'Israël', 'Israel'),
('IN', 'no', 'yes', 91, 'India', 'India'),
('IQ', 'no', 'yes', 93, 'Irak', 'Iraq'),
('IR', 'no', 'yes', 94, 'Iran', 'Iran, Islamic Republic of'),
('IS', 'no', 'yes', 90, 'Ijsland', 'Iceland'),
('IT', 'yes', 'yes', 12, 'Italië', 'Italy'),
('JM', 'no', 'yes', 97, 'Jamaica', 'Jamaica'),
('JO', 'no', 'yes', 100, 'Jordanië', 'Jordan'),
('JP', 'no', 'yes', 98, 'Japan', 'Japan'),
('KE', 'no', 'yes', 104, 'Kenia', 'Kenya'),
('KG', 'no', 'yes', 105, 'Kirgizië', 'Kyrgyzstan'),
('KH', 'no', 'yes', 55, 'Cambodja', 'Cambodia'),
('KI', 'no', 'yes', 106, 'Kiribati', 'Kiribati'),
('KM', 'no', 'yes', 62, 'Comoren', 'Comoros'),
('KN', 'no', 'yes', 155, 'Saint Kitts en Nevis', 'Saint Kitts and Nevis'),
('KP', 'no', 'yes', 139, 'Noord-Korea', 'Korea, Democratic People\'s Republic of'),
('KR', 'no', 'yes', 197, 'Zuid-Korea', 'Korea, Republic of'),
('KW', 'no', 'yes', 107, 'Koeweit', 'Kuwait'),
('KZ', 'no', 'yes', 103, 'Kazachstan', 'Kazakhstan'),
('LA', 'no', 'yes', 108, 'Laos', 'Lao People\'s Democratic Republic'),
('LB', 'no', 'yes', 110, 'Libanon', 'Lebanon'),
('LC', 'no', 'yes', 156, 'Saint Lucia', 'Saint Lucia'),
('LI', 'no', 'yes', 113, 'Liechtenstein', 'Liechtenstein'),
('LK', 'no', 'yes', 171, 'Sri Lanka', 'Sri Lanka'),
('LR', 'no', 'yes', 111, 'Liberia', 'Liberia'),
('LS', 'no', 'yes', 109, 'Lesotho', 'Lesotho'),
('LT', 'yes', 'yes', 16, 'Litouwen', 'Lithuania'),
('LU', 'yes', 'yes', 17, 'Luxemburg', 'Luxembourg'),
('LV', 'yes', 'yes', 14, 'Letland', 'Latvia'),
('LY', 'no', 'yes', 112, 'Libië', 'Libyan Arab Jamahiriya'),
('MA', 'no', 'yes', 120, 'Marokko', 'Morocco'),
('MC', 'no', 'yes', 127, 'Monaco', 'Monaco'),
('MD', 'no', 'yes', 126, 'Moldavië', 'Moldova, Republic of'),
('ME', 'no', 'yes', 129, 'Montenegro', 'Montenegro'),
('MG', 'no', 'yes', 115, 'Madagaskar', 'Madagascar'),
('MH', 'no', 'yes', 121, 'Marshalleilanden', 'Marshall Islands'),
('MK', 'no', 'yes', 114, 'Macedonië', 'Macedonia, the former Yugoslav Republic of'),
('ML', 'no', 'yes', 119, 'Mali', 'Mali'),
('MM', 'no', 'yes', 131, 'Myanmar', 'Myanmar'),
('MN', 'no', 'yes', 128, 'Mongolië', 'Mongolia'),
('MR', 'no', 'yes', 122, 'Mauritanië', 'Mauritania'),
('MT', 'yes', 'yes', 18, 'Malta', 'Malta'),
('MU', 'no', 'yes', 123, 'Mauritius', 'Mauritius'),
('MV', 'no', 'yes', 117, 'Maldiven', 'Maldives'),
('MW', 'no', 'yes', 116, 'Malawi', 'Malawi'),
('MX', 'no', 'yes', 124, 'Mexico', 'Mexico'),
('MY', 'no', 'yes', 118, 'Maleisië', 'Malaysia'),
('MZ', 'no', 'yes', 130, 'Mozambique', 'Mozambique'),
('NA', 'no', 'yes', 132, 'Namibië', 'Namibia'),
('NE', 'no', 'yes', 137, 'Niger', 'Niger'),
('NG', 'no', 'yes', 138, 'Nigeria', 'Nigeria'),
('NI', 'no', 'yes', 135, 'Nicaragua', 'Nicaragua'),
('NL', 'yes', 'yes', 0, 'Nederland', 'Netherlands'),
('NO', 'no', 'yes', 140, 'Noorwegen', 'Norway'),
('NP', 'no', 'yes', 134, 'Nepal', 'Nepal'),
('NR', 'no', 'yes', 133, 'Nauru', 'Nauru'),
('NZ', 'no', 'yes', 136, 'Nieuw-Zeeland', 'New Zealand'),
('OM', 'no', 'yes', 144, 'Oman', 'Oman'),
('PA', 'no', 'yes', 148, 'Panama', 'Panama'),
('PE', 'no', 'yes', 151, 'Peru', 'Peru'),
('PG', 'no', 'yes', 149, 'Papoea-Nieuw-Guinea', 'Papua New Guinea'),
('PH', 'no', 'yes', 78, 'Filipijnen', 'Philippines'),
('PK', 'no', 'yes', 146, 'Pakistan', 'Pakistan'),
('PL', 'yes', 'yes', 20, 'Polen', 'Poland'),
('PT', 'yes', 'yes', 21, 'Portugal', 'Portugal'),
('PW', 'no', 'yes', 147, 'Palau', 'Palau'),
('PY', 'no', 'yes', 150, 'Paraguay', 'Paraguay'),
('QA', 'no', 'yes', 152, 'Qatar', 'Qatar'),
('RO', 'yes', 'yes', 22, 'Roemenië', 'Roemenië'),
('RS', 'no', 'yes', 164, 'Servië', 'Serbia'),
('RU', 'no', 'yes', 153, 'Rusland', 'Russian Federation'),
('RW', 'no', 'yes', 154, 'Rwanda', 'Rwanda'),
('SA', 'no', 'yes', 162, 'Saoedi-Arabië', 'Saudi Arabia'),
('SB', 'no', 'yes', 158, 'Salomonseilanden', 'Solomon Islands'),
('SC', 'no', 'yes', 165, 'Seychellen', 'Seychelles'),
('SD', 'no', 'yes', 169, 'Soedan', 'Sudan'),
('SE', 'yes', 'yes', 28, 'Zweden', 'Sweden'),
('SG', 'no', 'yes', 167, 'Singapore', 'Singapore'),
('SI', 'yes', 'yes', 23, 'Slovenië', 'Slovenia'),
('SK', 'yes', 'yes', 24, 'Slowakije', 'Slovakia'),
('SL', 'no', 'yes', 166, 'Sierra Leone', 'Sierra Leone'),
('SM', 'no', 'yes', 160, 'San Marino', 'San Marino'),
('SN', 'no', 'yes', 163, 'Senegal', 'Senegal'),
('SO', 'no', 'yes', 170, 'Somalië', 'Somalia'),
('SR', 'no', 'yes', 172, 'Suriname', 'Suriname'),
('ST', 'no', 'yes', 161, 'Sao Tomé en Principe', 'Sao Tome and Principe'),
('SV', 'no', 'yes', 73, 'El Salvador', 'El Salvador'),
('SX', 'no', 'yes', 168, 'Sint Maarten', 'Sint Maarten'),
('SY', 'no', 'yes', 174, 'Syrië', 'Syrian Arab Republic'),
('SZ', 'no', 'yes', 173, 'Swaziland', 'Swaziland'),
('TD', 'no', 'yes', 181, 'Tsjaad', 'Chad'),
('TG', 'no', 'yes', 178, 'Togo', 'Togo'),
('TH', 'no', 'yes', 177, 'Thailand', 'Thailand'),
('TJ', 'no', 'yes', 175, 'Tadzjikistan', 'Tajikistan'),
('TL', 'no', 'yes', 145, 'Oost-Timor', 'Timor-Leste'),
('TM', 'no', 'yes', 184, 'Turkmenistan', 'Turkmenistan'),
('TN', 'no', 'yes', 182, 'Tunesië', 'Tunisia'),
('TO', 'no', 'yes', 179, 'Tonga', 'Tonga'),
('TR', 'no', 'yes', 183, 'Turkije', 'Turkey'),
('TT', 'no', 'yes', 180, 'Trinidad en Tobago', 'Trinidad and Tobago'),
('TV', 'no', 'yes', 185, 'Tuvalu', 'Tuvalu'),
('TZ', 'no', 'yes', 176, 'Tanzania', 'Tanzania, United Republic of'),
('UA', 'no', 'yes', 142, 'Oekraïne', 'Ukraine'),
('UG', 'no', 'yes', 141, 'Oeganda', 'Uganda'),
('US', 'no', 'yes', 191, 'Verenigde Staten', 'United States'),
('UY', 'no', 'yes', 186, 'Uruguay', 'Uruguay'),
('UZ', 'no', 'yes', 143, 'Oezbekistan', 'Uzbekistan'),
('VA', 'no', 'yes', 188, 'Vaticaan', 'Holy See (Vatican City State)'),
('VC', 'no', 'yes', 157, 'Saint Vincent en de Grenadines', 'Saint Vincent and the Grenadines'),
('VE', 'no', 'yes', 189, 'Venezuela', 'Venezuela, Bolivarian Republic of'),
('VN', 'no', 'yes', 192, 'Vietnam', 'Viet Nam'),
('VU', 'no', 'yes', 187, 'Vanuatu', 'Vanuatu'),
('WS', 'no', 'yes', 159, 'Samoa', 'Samoa'),
('YE', 'no', 'yes', 99, 'Jemen', 'Yemen'),
('ZA', 'no', 'yes', 196, 'Zuid-Afrika', 'South Africa'),
('ZM', 'no', 'yes', 194, 'Zambia', 'Zambia'),
('ZW', 'no', 'yes', 195, 'Zimbabwe', 'Zimbabwe');


DROP TABLE IF EXISTS `HostFact_Settings_LegalForms`;
CREATE TABLE `HostFact_Settings_LegalForms` (
`LegalForm` VARCHAR( 20 ) NOT NULL ,
`Title` VARCHAR( 100 ) NOT NULL ,
`OrderID` INT( 10 ) NOT NULL ,
PRIMARY KEY ( `LegalForm` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_Settings_LegalForms` (`LegalForm` ,`Title` ,`OrderID`) VALUES
('BV', 'Besloten Vennootschap', '1'), 
('BVI/O', 'BV in oprichting', '2'), 
('COOP', 'Cooperatie', '3'), 
('CV', 'Commanditaire Vennootschap', '4'), 
('EENMANSZAAK', 'Eenmanszaak', '5'), 
('KERK', 'Kerkgenootschap', '6'), 
('MAATSCHAP', 'Maatschap', '7'),
('NV', 'Naamloze Vennootschap', '8'), 
('OWM', 'Onderlinge Waarborg Maatschappij', '9'), 
('REDR', 'Rederij', '10'), 
('STICHTING', 'Stichting', '11'), 
('VERENIGING', 'Vereniging', '12'), 
('VOF', 'Vennootschap onder firma', '13'), 
('BEG', 'Buitenlandse EG vennootschap', '14'), 
('BRO', 'Buitenlandse rechtsvorm/onderneming', '15'), 
('EESV', 'Europees Economisch Samenwerkingsverband', '16'), 
('BE-COMMVA', 'Commanditaire VA', '17'), 
('BE-BVBA', 'BVBA', '18'), 
('BE-CVBA', 'CVBA', '19'), 
('BE-CVOA', 'CVOA', '20'), 
('ANDERS', 'Anders of onbekend', '0');
DROP TABLE IF EXISTS `HostFact_Settings_States`;
CREATE TABLE IF NOT EXISTS `HostFact_Settings_States` (
  `CountryCode` varchar(10) NOT NULL,
  `StateCode` varchar(10) NOT NULL,
  `State` varchar(100) NOT NULL,
  UNIQUE KEY `CountryCode` (`CountryCode`,`StateCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_Settings_States` (`CountryCode`, `StateCode`, `State`) VALUES ('US', 'AL', 'Alabama'),('US', 'AK', 'Alaska'),('US', 'AZ', 'Arizona'),('US', 'AR', 'Arkansas'),('US', 'CA', 'California'),('US', 'CO', 'Colorado'),('US', 'CT', 'Connecticut'),('US', 'DE', 'Delaware'),('US', 'FL', 'Florida'),('US', 'GA', 'Georgia'),('US', 'HI', 'Hawai\'i'),('US', 'ID', 'Idaho'),('US', 'IL', 'Illinois'),('US', 'IN', 'Indiana'),('US', 'IA', 'Iowa'),('US', 'KS', 'Kansas'),('US', 'KY', 'Kentucky'),('US', 'LA', 'Louisiana'),('US', 'ME', 'Maine'),('US', 'MD', 'Maryland'),('US', 'MA', 'Massachusetts'),('US', 'MI', 'Michigan'),('US', 'MN', 'Minnesota'),('US', 'MS', 'Mississippi'),('US', 'MO', 'Missouri'),('US', 'MT', 'Montana'), ('US', 'NE', 'Nebraska'),('US', 'NV', 'Nevada'),('US', 'NH', 'New Hampshire'),('US', 'NJ', 'New Jersey'),('US', 'NM', 'New Mexico'),('US', 'NY', 'New York'),('US', 'NC', 'North Carolina'),('US', 'ND', 'North Dakota'),('US', 'OH', 'Ohio'),('US', 'OK', 'Oklahoma'),('US', 'OR', 'Oregon'),('US', 'PA', 'Pennsylvania'),('US', 'RI', 'Rhode Island'),('US', 'SC', 'South Carolina'),('US', 'SD', 'South Dakota'),('US', 'TN', 'Tennessee'),('US', 'TX', 'Texas'),('US', 'UT', 'Utah'),('US', 'VT', 'Vermont'),('US', 'VA', 'Virginia'),('US', 'WA', 'Washington'),('US', 'WV', 'WestVirginia'),('US', 'WI', 'Wisconsin'),('US', 'WY', 'Wyoming');

DROP TABLE IF EXISTS `HostFact_Settings_Taxrates`;
CREATE TABLE `HostFact_Settings_Taxrates` (
	`Rate` FLOAT( 7, 6 ) NOT NULL ,
	`Default` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
    `Label` VARCHAR(100) NOT NULL,
    `TaxType` ENUM('line', 'total') NOT NULL DEFAULT 'line', 
    `Compound` ENUM('yes', 'no') NOT NULL DEFAULT 'no',
	UNIQUE KEY `Rate` (`Rate`,`TaxType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_Settings_Taxrates` (`Rate`, `Default`, `Label`, `TaxType`, `Compound`) VALUES
(0.000, 'no','0% BTW','line','no'),
(0.090, 'no','9% BTW','line','no'),
(0.210, 'yes','21% BTW','line','no');

DROP TABLE IF EXISTS `HostFact_Settings_TaxRules`;
CREATE TABLE IF NOT EXISTS `HostFact_Settings_TaxRules` (
  `CountryCode` varchar(10) NOT NULL,
  `StateCode` varchar(10) NOT NULL,
  `TaxLevel1` float( 7, 6 ) DEFAULT NULL,
  `TaxLevel2` float( 7, 6 ) DEFAULT NULL,
  `Compound` enum('yes','no') NOT NULL DEFAULT 'no',
  `Restriction` enum('all','company','company_vat','individual') NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_Settings_TaxRules` (`CountryCode`, `StateCode`, `TaxLevel1`, `TaxLevel2`, `Compound`, `Restriction`) VALUES ('otherEU', 'all', 0.000000, NULL, 'no', 'company_vat'), ('nonEU', 'all', 0.000000, NULL, 'no', 'all');

DROP TABLE IF EXISTS `HostFact_SSL_Certificates`;
CREATE TABLE IF NOT EXISTS `HostFact_SSL_Certificates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL,
  `SSLTypeID` int(10) NOT NULL,
  `Registrar` int(10) NOT NULL,
  `Status` varchar(25) NOT NULL,
  `CommonName` varchar(250) NOT NULL,
  `Type` enum('domain','extended','organization') NOT NULL DEFAULT 'domain',
  `Wildcard` enum('no','yes') NOT NULL DEFAULT 'no',
  `MultiDomain` enum('no','yes') NOT NULL DEFAULT 'no',
  `MultiDomainRecords` text NOT NULL,
  `ApproverEmail` varchar(250) NOT NULL,
  `CSR` text NOT NULL,
  `ServerSoftware` enum('linux','windows') NOT NULL DEFAULT 'linux',
  `Period` tinyint(3) NOT NULL,
  `RequestDate` date NOT NULL,
  `RenewDate` date NOT NULL,
  `ownerHandle` int(10) NOT NULL,
  `adminHandle` int(10) NOT NULL,
  `techHandle` int(10) NOT NULL,
  `RegistrarReference` varchar(200) NOT NULL,
  `RegistrarReference2` varchar(200) NOT NULL,
  `RegistrarCheckDate` datetime NOT NULL,
  `Comment` text NOT NULL,
  `Created` DATETIME NOT NULL,
  `Modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_SSL_Types`;
CREATE TABLE IF NOT EXISTS `HostFact_SSL_Types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Product` int(10) NOT NULL,
  `Registrar` int(10) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Brand` varchar(50) NOT NULL,
  `TemplateName` varchar(100) NOT NULL,
  `Periods` varchar(50) NOT NULL,
  `Type` enum('domain','extended','organization') NOT NULL DEFAULT 'domain',
  `Wildcard` enum('no','yes') NOT NULL DEFAULT 'no',
  `MultiDomain` enum('no','yes') NOT NULL DEFAULT 'no',
  `MultiDomainIncluded` int(4) NOT NULL,
  `MultiDomainMax` int(4) NOT NULL,
  `Status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_TemplateBlocks`;
CREATE TABLE IF NOT EXISTS `HostFact_TemplateBlocks` (
    `id` int(10) NOT NULL AUTO_INCREMENT, 
    `template_id` int(10) NOT NULL, 
    `type` enum('text','image','table','invoicelines','totals','qrcode') NOT NULL DEFAULT 'text',
    `value` text NOT NULL, 
    `positioning_x` float NOT NULL, 
    `positioning_y` float NOT NULL, 
    `positioning_w` float NOT NULL, 
    `positioning_h` float NOT NULL, 
    `visibility` enum('all','first','last','none') NOT NULL DEFAULT 'all', 
    `text_family` varchar(50) NOT NULL,  
    `text_size` tinyint(2) NOT NULL, 
    `text_color` varchar(7) NOT NULL, 
    `text_align` enum('left','center','right') NOT NULL DEFAULT 'left', 
    `text_lineheight` float NOT NULL DEFAULT '1.25', 
    `text_style` varchar(4) NOT NULL, 
    `borders_top` enum('yes','no') NOT NULL DEFAULT 'no', 
    `borders_right` enum('yes','no') NOT NULL DEFAULT 'no', 
    `borders_bottom` enum('yes','no') NOT NULL DEFAULT 'no', 
    `borders_left` enum('yes','no') NOT NULL DEFAULT 'no', 
    `borders_thickness` int(1) NOT NULL, 
    `borders_color` varchar(7) NOT NULL, 
    `borders_type` varchar(10) NOT NULL, 
    `style_bgcolor` varchar(7) NOT NULL, 
    `cols` text NOT NULL, 
    `rows` text NOT NULL, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_TemplateBlocks` (`id`, `template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES
(1, 1, 'invoicelines', '[["Aantal","Omschrijving","Prijs per stuk","Bedrag"],["[invoiceElement->Number]","[invoiceElement->Description]\\r\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]","[invoiceElement->PriceExcl]","[invoiceElement->NoDiscountAmountExcl]"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"20"}},{"text":{"align":"left"},"positioning":{"w":""}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}}]', '[{"text":{"family":"","size":"10","style":"B","color":"","lineheight":"1.5"},"borders":{"bottom":"yes","type":"solid","thickness":"1","color":""},"style":{"bgcolor":""}},{"text":{"family":"","size":"10","style":"","color":"","lineheight":"1.5"},"borders":{"type":"solid","thickness":"","color":""},"style":{"bgcolor":"","bgcolor_even":""}}]'),
(2, 1, 'text', 'Factuur', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(3, 1, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nIBAN: [company->AccountNumber]\r\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(4, 1, 'totals', '[["Paginatotaal excl","[invoice->PageTotalExcl]"],["Totaal excl. BTW","[invoice->AmountExcl]"],[""],["Totaal incl. BTW","[invoice->AmountIncl]"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"33"}},{"style":{"format":"money"}}]', '[{"style":{"totaltype":"pagetotalexcl","bgcolor":""}},{"style":{"totaltype":"amountexcl","bgcolor":""}},{"style":{"totaltype":"amounttax","bgcolor":""}},{"style":{"totaltype":"amountincl","bgcolor":""},"borders":{"top":"yes","bottom":"yes"}}]'),
(5, 1, 'text', '[reversecharge]BTW verlegd: [invoice->TaxNumber][/reversecharge]', 20, 220, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(6, 1, 'text', '[invoice->CompanyName]\r\n[invoice->Initials] [invoice->SurName]\r\n[invoice->Address]\r\n[invoice->ZipCode]  [invoice->City]\r\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(7, 1, 'table', '[["Klantnummer:","[debtor->DebtorCode]"],["Factuurnummer:","[invoice->InvoiceCode]"],["Factuurdatum:","[invoice->Date]"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"30"}},{"text":{"align":"right"},"positioning":{"w":""}}]', '[]'),
(8, 1, 'text', '[paid]De factuur is reeds betaald.[/paid]\r\n[unpaid]\r\n[directdebit]Het te betalen bedrag wordt automatisch op [invoice->DirectDebitDate] van uw rekening [debtor->AccountNumber] t.n.v. [debtor->AccountName] afgeschreven.[/directdebit]\r\n[transfer]Te betalen binnen [invoice->Term] dagen na de factuurdatum (voor [invoice->PayBefore]) op rekeningnummer [company->AccountNumber] t.n.v. [company->CompanyName] onder vermelding van klantnummer en factuurnummer.[/transfer]\r\n[/unpaid]', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(9, 5, 'invoicelines', '[["Aantal","Omschrijving","Prijs per stuk","Bedrag"],["[invoiceElement->Number]","[invoiceElement->Description]\\r\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]","[invoiceElement->PriceExcl]","[invoiceElement->NoDiscountAmountExcl]"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"20"}},{"text":{"align":"left"},"positioning":{"w":""}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}}]', '[{"text":{"family":"","size":"10","style":"B","color":"","lineheight":"1.5"},"borders":{"bottom":"yes","type":"solid","thickness":"1","color":""},"style":{"bgcolor":""}},{"text":{"family":"","size":"10","style":"","color":"","lineheight":"1.5"},"borders":{"type":"solid","thickness":"","color":""},"style":{"bgcolor":"","bgcolor_even":""}}]'),
(10, 5, 'text', 'Creditfactuur', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(11, 5, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nBankrekening: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(12, 5, 'totals', '[["Paginatotaal excl","[invoice->PageTotalExcl]"],["Totaal excl. BTW","[invoice->AmountExcl]"],[""],["Totaal incl. BTW","[invoice->AmountIncl]"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"33"}},{"style":{"format":"money"}}]', '[{"style":{"totaltype":"pagetotalexcl","bgcolor":""}},{"style":{"totaltype":"amountexcl","bgcolor":""}},{"style":{"totaltype":"amounttax","bgcolor":""}},{"style":{"totaltype":"amountincl","bgcolor":""},"borders":{"top":"yes","bottom":"yes"}}]'),
(13, 5, 'text', '[reversecharge]BTW verlegd: [invoice->TaxNumber][/reversecharge]', 20, 222, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(14, 5, 'text', '[invoice->CompanyName]\r\n[invoice->Initials] [invoice->SurName]\r\n[invoice->Address]\r\n[invoice->ZipCode]  [invoice->City]\r\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(15, 5, 'table', '[["Klantnummer:","[debtor->DebtorCode]"],["Factuurnummer:","[invoice->InvoiceCode]"],["Factuurdatum:","[invoice->Date]"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"30"}},{"text":{"align":"right"},"positioning":{"w":""}}]', '[]'),
(16, 5, 'text', 'Het bedrag wordt binnen [invoice->Term] dagen op uw bankrekening overgemaakt.', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(17, 2, 'invoicelines', '[["Aantal","Omschrijving","Prijs per stuk","Bedrag"],["[invoiceElement->Number]","[invoiceElement->Description]\\r\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]","[invoiceElement->PriceExcl]","[invoiceElement->NoDiscountAmountExcl]"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"20"}},{"text":{"align":"left"},"positioning":{"w":""}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}},{"text":{"align":"right"},"positioning":{"w":"27"},"style":{"format":"money"}}]', '[{"text":{"family":"","size":"10","style":"B","color":"","lineheight":"1.5"},"borders":{"bottom":"yes","type":"solid","thickness":"1","color":""},"style":{"bgcolor":""}},{"text":{"family":"","size":"10","style":"","color":"","lineheight":"1.75"},"borders":{"type":"solid","thickness":"","color":""},"style":{"bgcolor":"","bgcolor_even":""}}]'),
(18, 2, 'text', 'Offerte', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(19, 2, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nBankrekening: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(20, 2, 'totals', '[["Paginatotaal excl","[invoice->PageTotalExcl]"],["Totaal excl. BTW","[invoice->AmountExcl]"],[""],["Totaal incl. BTW","[invoice->AmountIncl]"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"33"}},{"style":{"format":"money"}}]', '[{"style":{"totaltype":"pagetotalexcl","bgcolor":""}},{"style":{"totaltype":"amountexcl","bgcolor":""}},{"style":{"totaltype":"amounttax","bgcolor":""}},{"style":{"totaltype":"amountincl","bgcolor":""},"borders":{"top":"yes","bottom":"yes"}}]'),
(21, 2, 'text', '[reversecharge]BTW verlegd: [debtor->TaxNumber][/reversecharge]', 20, 222, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(22, 2, 'text', '[invoice->CompanyName]\r\n[invoice->Initials] [invoice->SurName]\r\n[invoice->Address]\r\n[invoice->ZipCode]  [invoice->City]\r\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(23, 2, 'table', '[["Klantnummer:","[debtor->DebtorCode]"],["Offertenummer:","[invoice->PriceQuoteCode]"],["Offertedatum:","[invoice->Date]"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"30"}},{"text":{"align":"right"},"positioning":{"w":""}}]', '[]'),
(24, 2, 'text', 'De offerte is [invoice->Term] dagen geldig na offertedatum.', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(25, 7, 'text', 'Aanmaning', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(26, 7, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nBankrekening: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(27, 7, 'text', '[invoice->CompanyName]\r\n[invoice->Initials] [invoice->SurName]\r\n[invoice->Address]\r\n[invoice->ZipCode]  [invoice->City]\r\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(28, 7, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\r\n\r\nEnige tijd geleden heeft u van ons de factuur [invoice->InvoiceCode] ontvangen voor de afgenomen diensten. Ondanks herhaaldelijke herinneringen heeft u het verschuldigde bedrag niet voldaan op onze bankrekening.\r\n\r\nWij vragen u daarom een laatste keer het totaal verschuldigde bedrag van € [invoice->PartPayment] te voldoen, met vermelding van uw klantnummer en factuurnummer. Indien wij de betaling niet binnen [invoice->Term] dagen hebben ontvangen, zullen wij genoodzaakt zijn de vordering neer te leggen bij een incassobureau.\r\n\r\nMet vriendelijke groet,\r\n\r\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(29, 6, 'text', 'Herinnering', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(30, 6, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nBankrekening: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(31, 6, 'text', '[invoice->CompanyName]\r\n[invoice->Initials] [invoice->SurName]\r\n[invoice->Address]\r\n[invoice->ZipCode]  [invoice->City]\r\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(32, 6, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\r\n\r\nEnige tijd geleden heeft u van ons de factuur [invoice->InvoiceCode] ontvangen voor de afgenomen diensten. Helaas hebben we nog geen betaling van het verschuldigde bedrag ontvangen.\r\n\r\nWij vragen u daarom het totaalbedrag van € [invoice->PartPayment] binnen [invoice->Term] dagen te voldoen, met vermelding van uw klantnummer en factuurnummer. \r\n\r\nMet vriendelijke groet,\r\n\r\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(33, 3, 'text', 'Gegevens webhostingpakket', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(34, 3, 'text', '[company->CompanyName]\r\n[company->Address]\r\n[company->ZipCode]  [company->City]\r\n[company->CountryLong]\r\n\r\nE-mail: [company->EmailAddress]\r\nWebsite: [company->Website]\r\n\r\nKvK nummer: [company->CompanyNumber]\r\nBTW nummer: [company->TaxNumber]\r\nBankrekening: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(35, 3, 'text', '[debtor->CompanyName]\r\n[debtor->Initials] [debtor->SurName]\r\n[debtor->Address]\r\n[debtor->ZipCode]  [debtor->City]\r\n[debtor->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),
(36, 3, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\r\n\r\nUw hostingpakket is aangemaakt en gereed voor gebruik!', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(37, 3, 'text', 'Indien u nog vragen heeft of problemen ondervindt bij het inloggen, neem dan contact met ons op!\r\n\r\nMet vriendelijke groet,\r\n\r\n[company->CompanyName]', 20, 174, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(38, 3, 'text', 'Hostingpakket gegevens', 20, 148, 0, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),
(39, 3, 'table', '[["Controle paneel:","http:\\/\\/[hosting->Domain]:[server->Port]\\/"],["Gebruikersnaam:","[hosting->Username]"],["Wachtwoord:","[hosting->Password]"],["Domeinnaam:","[hosting->Domain]"]]', 20, 153, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{"text":{"align":"left"},"positioning":{"w":"35"}},{"text":{"align":"left"},"positioning":{"w":""}}]', '[]');


DROP TABLE IF EXISTS `HostFact_Templates`;
CREATE TABLE IF NOT EXISTS `HostFact_Templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Location` varchar(255) NOT NULL DEFAULT '',
  `Standard` tinyint(1) NOT NULL DEFAULT '0',
  `Author` varchar(255) NOT NULL DEFAULT '',
  `Title` varchar(255) NOT NULL DEFAULT '',
  `FileName` varchar(255) NOT NULL,
  `Type` enum('invoice','pricequote','other') NOT NULL DEFAULT 'invoice',
  `EmailTemplate` int(10) NOT NULL DEFAULT '0',
  `PostLocation` varchar(255) NOT NULL DEFAULT '',
  `ElementsPerPage` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `HostFact_Templates` (`id`, `Name`, `Location`, `Standard`, `Author`, `Title`, `FileName`, `Type`, `EmailTemplate`, `PostLocation`, `ElementsPerPage`) VALUES
(1, 'Factuur', '', 1, 'HostFact', 'Factuur', 'Factuur_[invoice->InvoiceCode]', 'invoice', 1, '', 10),
(2, 'Offerte', '', 1, 'HostFact', 'Offerte', 'Offerte_[invoice->PriceQuoteCode]', 'pricequote', 2, '', 10),
(3, 'Gegevens webhostingpakket', '', 0, 'HostFact', 'Gegevens webhostingpakket', 'Gegevens.webhostingpakket', 'other', 0, '', 10),
(5, 'Creditfactuur', '', 0, 'HostFact', 'Creditfactuur', 'Creditfactuur_[invoice->InvoiceCode]', 'invoice', 12, '', 10),
(6, 'Herinneringsbrief', '', 0, 'HostFact', 'Herinneringsbrief', 'Herinneringsbrief', 'other', 0, '', 10),
(7, 'Aanmaningsbrief', '', 0, 'HostFact', 'Aanmaningsbrief', 'Aanmaningsbrief', 'other', 0, '', 10);

DROP TABLE IF EXISTS `HostFact_TerminationProcedures`;
CREATE TABLE IF NOT EXISTS `HostFact_TerminationProcedures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `ServiceType` varchar(25) NOT NULL,
  `Default` enum('yes','no') NOT NULL DEFAULT 'no',
  `TermPreference` enum('direct','date','contract') NOT NULL DEFAULT 'direct',
  `Status` enum('active','removed') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_TerminationProcedures` (`id`, `Name`, `ServiceType`, `Default`, `TermPreference`, `Status`) VALUES
(1, 'Overige diensten', 'other', 'yes', 'contract', 'active'), 
(2, 'Domeinnamen', 'domain', 'yes', 'contract', 'active'), 
(3, 'Hostingaccounts', 'hosting', 'yes', 'contract', 'active'), 
(4, 'SSL certificaten', 'ssl', 'yes', 'contract', 'active');

DROP TABLE IF EXISTS `HostFact_TerminationProceduresActions`;
CREATE TABLE IF NOT EXISTS `HostFact_TerminationProceduresActions` (
  `ProcedureID` int(10) NOT NULL,
  `ActionType` ENUM( 'manual', 'automatic', 'mail2client', 'mail2user' ) NOT NULL DEFAULT 'manual',
  `Description` varchar(200) NOT NULL,
  `When` enum('direct','before','on','after') NOT NULL DEFAULT 'on',
  `Days` tinyint(5) NOT NULL,
  PRIMARY KEY (`ProcedureID`,`ActionType`,`Description`,`When`,`Days`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_TerminationProceduresActions` (`ProcedureID`, `ActionType`, `Description`, `When`, `Days`) VALUES
(1, 'manual', 'Dienst wordt vandaag opgezegd', 'on', 0),
(2, 'manual', 'Dienst wordt vandaag opgezegd', 'on', 0),
(2, 'automatic', 'domain:cancelend', 'before', 14),
(3, 'manual', 'Dienst wordt vandaag opgezegd', 'on', 0),
(3, 'automatic', 'hosting:suspendhosting', 'on', 0),
(4, 'manual', 'Dienst wordt vandaag opgezegd', 'on', 0);

DROP TABLE IF EXISTS `HostFact_Terminations`;
CREATE TABLE IF NOT EXISTS `HostFact_Terminations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Debtor` int(10) NOT NULL,
  `ServiceType` varchar(25) NOT NULL,
  `ServiceID` int(10) NOT NULL,
  `Date` date NOT NULL,
  `ProcedureID` int(10) NOT NULL,
  `Term` enum('direct','date','contract') NOT NULL DEFAULT 'direct',
  `Reason` text NOT NULL,
  `Status` enum('approval','rejected','pending','processed','canceled') NOT NULL DEFAULT 'pending',
  `Created` datetime NOT NULL,
  `Who` VARCHAR( 10 ) NOT NULL,
  `IP` VARCHAR( 40 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ServiceType` (`ServiceType`,`ServiceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_TicketMessage`;
CREATE TABLE IF NOT EXISTS `HostFact_TicketMessage` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `TicketID` varchar(255) NOT NULL DEFAULT '',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `Subject` varchar(255) NOT NULL DEFAULT '',
  `Attachments` text NOT NULL,
  `Message` text NOT NULL,
  `SenderID` int(10) NOT NULL DEFAULT '0',
  `SenderName` varchar(100) NOT NULL DEFAULT '',
  `SenderEmail` varchar(255) NOT NULL DEFAULT '',
  `Tstatus` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `TicketID` (`TicketID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `HostFact_Tickets`;
CREATE TABLE IF NOT EXISTS `HostFact_Tickets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `TicketID` varchar(255) NOT NULL DEFAULT '',
  `Debtor` int(10) NOT NULL DEFAULT '0',
  `EmailAddress` varchar(255) NOT NULL DEFAULT '',
  `CC` TEXT NOT NULL DEFAULT '',
  `Type` enum('ticket','email') NOT NULL DEFAULT 'ticket',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Subject` varchar(255) NOT NULL DEFAULT '',
  `Owner` int(10) NOT NULL DEFAULT '0',
  `Priority` tinyint(2) NOT NULL DEFAULT '0',
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Comment` text NOT NULL,
  `Number` tinyint(10) NOT NULL DEFAULT '1',
  `LastDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastName` varchar(100) NOT NULL DEFAULT '',
  `LockDate` DATETIME NOT NULL, 
  `LockEmployee` INT( 10 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Debtor` (`Debtor`),
  KEY `TicketID` (`TicketID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_TopLevelDomain`;
CREATE TABLE IF NOT EXISTS `HostFact_TopLevelDomain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Tld` varchar(63) NOT NULL DEFAULT '',
  `Registrar` int(10) NOT NULL DEFAULT '0',
  `OwnerChangeCost` int(10) NOT NULL DEFAULT '0',
  `WhoisServer` VARCHAR( 250 ) NOT NULL,
  `WhoisNoMatch` VARCHAR( 250 ) NOT NULL,
  `AskForAuthKey` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
  `AllowedIDNCharacters` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Tld` (`Tld`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `HostFact_Transactions`;
CREATE TABLE IF NOT EXISTS `HostFact_Transactions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `BankAccount` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Type` varchar(25) NOT NULL,
  `Amount` DECIMAL( 10, 2 ) NOT NULL,
  `ShortDescription` varchar(255) NOT NULL,
  `ExtendedDescription` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `AccountNumber` varchar(50) NOT NULL,
  `AccountBIC` varchar(50) NOT NULL,
  `BankReference` varchar(50) NOT NULL,
  `Status` varchar(25) NOT NULL,
  `MatchCount` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `BankReference` (`BankReference`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Transaction_Import`;
CREATE TABLE IF NOT EXISTS `HostFact_Transaction_Import` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `BankAccount` varchar(50) NOT NULL,
  `ImportDate` datetime NOT NULL,
  `Counter` smallint(6) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Transaction_Matches`;
CREATE TABLE IF NOT EXISTS `HostFact_Transaction_Matches` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `TransactionID` int(10) NOT NULL,
  `ReferenceType` enum('invoice','creditinvoice') NOT NULL DEFAULT 'invoice',
  `ReferenceID` int(10) NOT NULL,
  `RelationID` int(10) NOT NULL,
  `MatchedAmount` DECIMAL( 10, 2 ) NOT NULL,
  `MatchedBy` int(10) NOT NULL,
  `MatchType` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Widgets`;
CREATE TABLE IF NOT EXISTS `HostFact_Widgets` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `File` varchar(100) NOT NULL,
  `Period` varchar(100) NOT NULL,
  `Width` int(8) NOT NULL,
  `Type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `HostFact_Widgets` (`id`, `Name`, `File`, `Period`, `Width`, `Type`) VALUES
(1, 'widget turnover', 'widget.turnover.php', 'day,week,month,quarter,year', 160, 'internal'),
(2, 'widget orders', 'widget.orders.php', 'day,week,month,quarter,year', 160, 'internal'),
(3, 'widget graph turnover', 'widget.graph_turnover.php', 'last3m,last6m,last12m', 358, 'internal'),
(4, 'widget invoices', 'widget.invoices.php', 'day,week,month,quarter,year', 160, 'internal'),
(5, 'widget subscriptions', 'widget.subscriptions.php', 'day,week,month,quarter,year', 160, 'internal'),
(NULL ,  'widget revenue',  'widget.revenue.php',  'month,quarter,year',  '160',  'internal'),
(NULL ,  'widget expenses',  'widget.expenses.php',  'month,quarter,year',  '160',  'internal'),
(NULL ,  'widget revenue from orders', 'widget.revenue_orders.php', 'day,week,month,quarter,year', '160', 'internal');

DROP TABLE IF EXISTS `HostFact_UpgradeGroups`;
CREATE TABLE IF NOT EXISTS `HostFact_UpgradeGroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Status` tinyint(2) NOT NULL DEFAULT '0',
  `Products` varchar(255) NOT NULL,
  `ServiceType` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `HostFact_Clientarea_Changes`;
CREATE TABLE IF NOT EXISTS `HostFact_Clientarea_Changes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ReferenceType` varchar(25) NOT NULL,
  `ReferenceID` int(10) NOT NULL,
  `Action` varchar(25) NOT NULL,
  `Data` text NOT NULL,
  `Debtor` int(10) NOT NULL,
  `Approval` enum('notused','approved','rejected','pending') NOT NULL DEFAULT 'notused',
  `Status` enum('pending','executed','error','canceled','removed') NOT NULL DEFAULT 'pending',
  `CreatorType` varchar(25) NOT NULL,
  `CreatorID` int(10) NOT NULL,
  `Created` datetime NOT NULL,
  `Modified` datetime NOT NULL,
  `IP` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `HostFact_Clientarea_Profiles`;
CREATE TABLE IF NOT EXISTS `HostFact_Clientarea_Profiles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `WelcomeTitle` varchar(255) NOT NULL,
  `WelcomeMessage` text NOT NULL,
  `Rights` longtext NOT NULL,
  `Orderforms` TEXT NOT NULL,
  `Default` varchar(5) NOT NULL,
  `TwoFactorAuthentication` ENUM('on','off') NOT NULL DEFAULT 'on',
  `Status` enum('active','removed','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `HostFact_Clientarea_Profiles` (`id`, `Name`, `WelcomeTitle`, `WelcomeMessage`, `Rights`, `Orderforms`, `Default`, `Status`) VALUES
(NULL, 'Standaard profiel', 'Welkom in het klantenpaneel', 'Dit paneel geeft u een overzicht van alle diensten die u bij ons afneemt. Daarnaast kunt u uw facturen terug zien en openstaande facturen betalen.', '{"CLIENTAREA_DEBTOR_DATA_CHANGE":"approve","CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE":"approve","CLIENTAREA_SERVICE_TERMINATE":"approve","CLIENTAREA_PRICEQUOTE_ACCEPT":"yes","CLIENTAREA_DOMAIN_TOKEN":"no","CLIENTAREA_DOMAIN_WHOIS_CHANGE":"no","CLIENTAREA_DOMAIN_NAMESERVER_CHANGE":"no","CLIENTAREA_DOMAIN_NAMESERVER_CHANGE_NOTIFICATION":"email","CLIENTAREA_DOMAIN_DNSZONE_CHANGE":"no","CLIENTAREA_VPS_ACTIONS":"no","CLIENTAREA_DEBTOR_DATA_CHANGE_NOTIFICATION":"email","CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE_NOTIFICATION":"email","CLIENTAREA_DEBTOR_PAYMENTDATA_AUTHORISATION":"no","CLIENTAREA_PRICEQUOTE_ACCEPT_NOTIFICATION":"email","CLIENTAREA_PRICEQUOTE_ACCEPT_TERMS":"no","CLIENTAREA_SERVICE_TERMINATE_NOTIFICATION":"email","CLIENTAREA_DOMAIN_WHOIS_CHANGE_NOTIFICATION":"email","CLIENTAREA_DOMAIN_DNSZONE_CHANGE_NOTIFICATION":"email","CLIENTAREA_HOSTING_SINGLE_SIGN_ON":"yes", "CLIENTAREA_HOSTING_PASSWORD_RESET":"no"}', '{"CLIENTAREA_DOMAIN_ORDERFORM":"","CLIENTAREA_HOSTING_ORDERFORM":"","CLIENTAREA_SSL_ORDERFORM":"","CLIENTAREA_VPS_ORDERFORM":"","CLIENTAREA_OTHER_ORDERFORM":"","CLIENTAREA_SSL_DOWNLOAD":"yes","CLIENTAREA_SSL_REISSUE":"no"}', 'yes', 'active');

COMMIT;
SET AUTOCOMMIT=1;