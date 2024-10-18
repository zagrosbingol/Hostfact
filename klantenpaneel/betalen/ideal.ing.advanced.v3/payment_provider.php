<?php
class ideal_ing_advanced_v3 extends Payment_Provider_Base
{
	public $IssuerID;
	
	private $connectorHelper;
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'ideal.ing.advanced.v3';
		$this->conf['PaymentMethod'] 	= 'ideal';
		
		// Load parent constructor
		parent::__construct();
	
		// Specific configuration for this integration
		if(!defined("SECURE_PATH")){ define("SECURE_PATH", $this->conf['PaymentDirectory']."/security"); }
		$this->IssuerID = (isset($_SESSION['ing_advanced_issuer_id']['IssuerID'])) ? $_SESSION['ing_advanced_issuer_id']['IssuerID'] : 0;
		
		// Load configuration
		$this->loadConf();
		
		// Include files and instantiate connectorHelper
		require_once "ConnectorHelper.php";
			
		$this->connectorHelper = new ConnectorHelper();
		
		// Overwrite configuration settings
		$this->connectorHelper->setConfiguration('MERCHANTID', 			$this->conf['MerchantID']);
		$this->connectorHelper->setConfiguration('PRIVATEKEYPASS', 		$this->conf['Password']);
		$this->connectorHelper->setConfiguration('MERCHANTRETURNURL', 	IDEAL_EMAIL.'ideal.ing.advanced.v3/notify.php');
	}

	public function choosePaymentMethod()
	{
		//TODO: [low] cache dirreq
		
		// For iDEAL, we want to return the issuerList
		$xmlMsg = $this->connectorHelper->CreateDirectoryMessage();

        // Post the XML to the server.
        $response = $this->connectorHelper->DirectoryRequest($xmlMsg);
      
        // If the response did not work out, return an ErrorResponse object.
        $error = $this->connectorHelper->DirectoryResponseHasErrors($response);
        if($error != false)
		{
			$errorCode 			= $error->getErrorCode();
	        $errorMsg 			= $error->getErrorMessage();
	        $consumerMessage 	= $error->getConsumerMessage();
		
			// Return error message for consumer
            return $consumerMessage;
        }

		// Directory request was succesfull, create select-field
        $response = new DirectoryResponse($response);
        
        $html  = "<select name=\"ing_advanced_issuer_id\" class=\"issuerID\">";
		$html .= "<option value=\"0\">- maak uw keuze -</option>";
        
        $countries = $response->getCountries();
        foreach($countries as $countryNames => $country)
		{
            $html .= "<optgroup label=\"" . $countryNames . "\">";

            foreach ($country->getIssuers() as $issuer)
			{
                $html .= "<option value=\"" . $issuer->getIssuerID() . "\">" . $issuer->getIssuerName() . "</option>";
            }
            $html .= "</optgroup>";
        }
        
        $html .= "</select>";

		return $html;
	}
	
	public function validateChosenPaymentMethod()
	{
		if(isset($_POST['ing_advanced_issuer_id']) && $_POST['ing_advanced_issuer_id'])
		{
			$this->IssuerID = $_POST['ing_advanced_issuer_id'];
			$_SESSION['ing_advanced_issuer_id']['IssuerID'] = $this->IssuerID;
			return true;
		}
		elseif(!isset($_POST['ing_advanced_issuer_id']) && isset($_SESSION['ing_advanced_issuer_id']['IssuerID']) && $_SESSION['ing_advanced_issuer_id']['IssuerID'])
		{
			$this->IssuerID = $_SESSION['ing_advanced_issuer_id']['IssuerID'];
			return true;
		}
		else
		{
			$this->Error = 'U heeft geen bank geselecteerd.';
		}
		
		return false;
		
	}
	
	public function startTransaction()
	{		
		$issuerId 			= $this->IssuerID;
		
		if($this->Type == 'invoice')
		{
			$purchaseId 	= substr(preg_replace("/[^0-9a-z]/i", "", $this->InvoiceCode), 0, 35); //A-Z0-9  max 35
			$description	= $this->InvoiceCode; // max 32
			$entranceCode	= md5($this->InvoiceCode); // a code determined by the online shop with which the purchase can be authenticated upon redirection to the online shop
		}
		else
		{
			$purchaseId 	= substr(preg_replace("/[^0-9a-z]/i", "", $this->OrderCode), 0, 35); //A-Z0-9  max 35
			$description	= $this->OrderCode; // max 32
			$entranceCode	= md5($this->OrderCode); // a code determined by the online shop with which the purchase can be authenticated upon redirection to the online shop
		}

		$amount	= number_format($this->Amount,2,'.','');
		
		$xmlMsg = $this->connectorHelper->CreateTransactionMessage($issuerId, $purchaseId, $amount, $description, $entranceCode);

        // Post the request to the server.
        $response = $this->connectorHelper->TransactionRequest($xmlMsg);

		// If the response did not work out, return an ErrorResponse object.
        $error = $this->connectorHelper->TransactionResponseHasErrors($response);
        if($error != false)
		{
			$errorCode 			= $error->getErrorCode();
	        $errorMsg 			= $error->getErrorMessage();
	        $consumerMessage 	= $error->getConsumerMessage();
		
			// Return error message for consumer
            $this->paymentStatusUnknown($consumerMessage);
            return false;
        }

		// Transaction request was succesfull
        $response = new AcquirerTransactionResponse($response);
        
        $acquirerID = $response->getAcquirerID();
        $issuerAuthenticationURL = $response->getIssuerAuthenticationURL();
        $transactionID = $response->getTransactionID();
        
        // Update database
        $this->updateTransactionID($transactionID);
        
        // Redirect
		header("Location: " . $issuerAuthenticationURL);
		exit;
	}
	
	public function validateTransaction($transactionID)
	{
		// Only keep numbers from transaction ID
		$transactionID = preg_replace("/[^0-9]/", "", $transactionID);
		
		$xmlMsg = $this->connectorHelper->CreateStatusMessage($transactionID);

        // Post the request to the server.
        $response = $this->connectorHelper->StatusRequest($xmlMsg);

        // If the response did not work out, return an ErrorResponse object.
        $error = $this->connectorHelper->StatusResponseHasErrors($response);
        if($error != false)
		{
			$errorCode 			= $error->getErrorCode();
	        $errorMsg 			= $error->getErrorMessage();
	        $consumerMessage 	= $error->getConsumerMessage();
		
			// Return error message for consumer
            $this->paymentStatusUnknown($consumerMessage);
            return false;
        }

		// Status request was succesfull
        $response = new AcquirerStatusResponse($response);
      
        $acquirerID = $response->getAcquirerID();
        $consumerName = $response->getConsumerName();
        $consumerIBAN = $response->getConsumerIBAN();
        $consumerBIC = $response->getConsumerBIC();
        $amount = $response->getAmount();
        $currency = $response->getCurrency();
        $statusDateTime = $response->getStatusDateTime();
        $transactionID = $response->getTransactionID();
        $status = $response->getStatus();

        // Interpret status
        switch($status)
        {
        	case IDEAL_TX_STATUS_SUCCESS:
        		// Update database for successfull transaction
        		$this->paymentProcessed($transactionID);
        		break;
       		default:
       			// Update database for failed transaction
        		$this->paymentFailed($transactionID);
        		break;
        }	
	}
	
	public static function getBackofficeSettings()
	{
		$settings = array();
		
		$settings['InternalName'] = 'ING iDEAL Advanced';
		
		// Partner ID
		$settings['MerchantID']['Title'] = "Merchant ID";
		$settings['MerchantID']['Value'] = "";
		
		$settings['Password']['Title'] = "Wachtwoord van certificaat";
		$settings['Password']['Value'] = "";
		
		$settings['Advanced']['Title'] = "iDEAL";
		$settings['Advanced']['Image'] = "ideal.jpg";
		$settings['Advanced']['Description'] = "Met iDEAL kunt u vertrouwd, veilig en gemakkelijk uw online aankopen afrekenen.";
		
		$settings['Advanced']['FeeType'] = "";
		$settings['Advanced']['FeeAmount'] = "0";
		$settings['Advanced']['FeeDesc'] = "Transactiekosten";
		
		$settings['Advanced']['Testmode'] = "0";
		//$settings['Hint'] = "";
		$settings['Advanced']['Extra'] = "Kies een bank: ";
		
		return $settings;
	}
}
