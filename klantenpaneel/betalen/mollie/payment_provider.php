<?php
// Require the autoloader for the Mollie API Client.
require_once __DIR__ . "/vendor/autoload.php";

class mollie extends Payment_Provider_Base
{
    /** @var $mollie \Mollie\Api\MollieApiClient */
    private $mollie;

    function __construct()
    {
        $this->conf['PaymentDirectory'] = 'mollie';
        $this->conf['PaymentMethod'] = 'other';

        // Load parent constructor
        parent::__construct();

        // Load configuration
        $this->loadConf();

    }

    public function choosePaymentMethod()
    {
        $this->setMollieObject();
        try {
            $mollie_payment_methods = $this->mollie->methods->allActive(['includeWallets' => 'applepay']);

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            // Return error message for consumer
            return '<br />' . htmlspecialchars($e->getMessage());
        }

        if ($mollie_payment_methods->count == 1) {
            $_SESSION['mollie_payment_method'] = $mollie_payment_methods[0]->id;
            return false;
        } elseif ($mollie_payment_methods->count > 1) {

            // Or get the payment methods and create HTML with options.
            $html = "<select name=\"mollie_payment_method\">";
            $html .= "<option value=\"\" selected=\"selected\">Kies uw betaalmethode</option>";

            foreach ($mollie_payment_methods as $_method) {
                $html .= '<option value=' . htmlspecialchars($_method->id) . '>' . htmlspecialchars($_method->description) . '</option>';
            }
        }
        $html .= "</select>";

        return $html;

    }

    public function validateChosenPaymentMethod()
    {
        $this->setMollieObject();

        // Or check the chosen payment methods and store in session
        if(isset($_POST['mollie_payment_method']) && $_POST['mollie_payment_method'])
        {
            $_SESSION['mollie_payment_method'] = htmlspecialchars($_POST['mollie_payment_method']);
            return true;
        }
        elseif(!isset($_POST['mollie_payment_method']) && isset($_SESSION['mollie_payment_method']) && $_SESSION['mollie_payment_method'])
        {
            return true;
        }
        else
        {
            $this->Error = 'U heeft nog geen betaalmethode geselecteerd.';
            return false;
        }
    }

    public function startTransaction()
    {
        $payment_method_id = $_SESSION['mollie_payment_method'];

        $this->setMollieObject();

        if ($this->Type == 'invoice') {
            $orderID = 'invoice' . $this->InvoiceID;
            $description = __('description prefix invoice') . ' ' . $this->InvoiceCode;
        } else {
            $orderID = 'order' . $this->OrderID;
            $description = __('description prefix order') . ' ' . $this->OrderCode;
        }

        try {
            // Start transaction
            $payment = $this->mollie->payments->create(array(
                "amount" => ['currency' => CURRENCY_CODE, 'value' => number_format($this->Amount, 2, '.', '')],
                "description" => $description,
                "redirectUrl" => IDEAL_EMAIL . "mollie/return.php?id=" . urlencode(base64_encode(passcrypt('mollieid' . $orderID))),
                "method" => $payment_method_id, // ideal, creditcard, mistercash, paypal, paysafecard
                "webhookUrl" => IDEAL_EMAIL . "mollie/notify.php"
            ));

            // If a transaction ID update to database
            $this->updateTransactionID($payment->id);
            $_SESSION['mollie']['transaction_id'] = $payment->id;

            // Redirect
            header("Location: " . $payment->getCheckoutUrl());
            exit;

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            // Return error message for consumer
            $this->paymentStatusUnknown(htmlspecialchars($e->getMessage()));
            exit;
        }
    }

    public function validateTransaction($transactionID)
    {
        $this->setMollieObject();

        // Get the payment data from Mollie, if that failed, let's Mollie retry later.
        try {
            $payment = $this->mollie->payments->get($transactionID);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            if ($this->isNotificationScript !== true) {
                $this->paymentStatusUnknown();
                header("Location: " . IDEAL_EMAIL);
            } else {
                // Return a 503 http code, so Mollie will retry later.
                header('HTTP/1.1 503 Service Temporarily Unavailable');
            }
            exit;
        }

        if ($this->isNotificationScript === true) {
            if ($payment->isPaid() == true) {
                // Update database for successfull transaction
                $this->paymentProcessed($payment->id);
            } elseif ($payment->isOpen() == false) {
                // The payment isn't paid and isn't open anymore. We can assume it was aborted.
                $this->paymentFailed($payment->id);
            }
        } else {
            if ($this->getType($payment->id)) {
                if ($this->Type == 'invoice') {
                    $_SESSION['payment']['type'] = 'invoice';
                    $_SESSION['payment']['id'] = $this->InvoiceID;
                } elseif ($this->Type == 'order') {
                    $_SESSION['payment']['type'] = 'order';
                    $_SESSION['payment']['id'] = $this->OrderID;
                }

            }

            // For consumer (in this case the status is already changed by server-to-server notification script)
            if ($payment->isOpen() == true) {
                $_SESSION['payment']['status'] = 'pending';
                $_SESSION['payment']['paymentmethod'] = $this->conf['PaymentMethod'];
                $_SESSION['payment']['transactionid'] = $payment->id;
                $_SESSION['payment']['date'] = date('Y-m-d H:i:s');
            } elseif ($payment->isPaid() === true || $this->Paid > 0) {
                // Because type is found, we know it is paid
                $_SESSION['payment']['status'] = 'paid';
                $_SESSION['payment']['paymentmethod'] = $this->conf['PaymentMethod'];
                $_SESSION['payment']['transactionid'] = $payment->id;
                $_SESSION['payment']['date'] = date('Y-m-d H:i:s');
            } else {
                $_SESSION['payment']['status'] = 'failed';
                $_SESSION['payment']['paymentmethod'] = $this->conf['PaymentMethod'];
                $_SESSION['payment']['transactionid'] = $payment->id;
                $_SESSION['payment']['date'] = date('Y-m-d H:i:s');
            }

            header("Location: " . IDEAL_EMAIL);
            exit;

        }

    }

    public static function getBackofficeSettings()
    {
        $settings = array();

        $settings['InternalName'] = 'Mollie';

        // Partner ID
        $settings['MerchantID']['Title'] = "Mollie API key";
        $settings['MerchantID']['Value'] = "";

        $settings['Advanced']['Title'] = "Mollie";
        $settings['Advanced']['Image'] = "mollie.jpg";
        $settings['Advanced']['Description'] = "Met Mollie kunt u vertrouwd, veilig en gemakkelijk uw online betalingen verrichten.";

        $settings['Advanced']['FeeType'] = "";
        $settings['Advanced']['FeeAmount'] = "0";
        $settings['Advanced']['FeeDesc'] = "Transactiekosten";

        $settings['Advanced']['Testmode'] = "0";
        $settings['Hint'] = "U dient bij Mollie een websiteprofiel aan te maken. Binnen dit websiteprofiel kunt u bepalen welke betaalmethode(n) u wilt aanbieden. Bij het websiteprofiel vindt u ook de API key.";
        $settings['Advanced']['Extra'] = "Kies uw betaalmethode: ";

        return $settings;
    }

    private function setMollieObject()
    {
        if (isset($this->mollie) && is_object($this->mollie)) {
            return;
        }

        try {
            $this->mollie = new \Mollie\Api\MollieApiClient();
            $this->mollie->setApiKey(trim($this->conf['MerchantID']));
            // Let support of Mollie know which integration is using.
            $this->mollie->addVersionString("MollieHostFact/2.39.0");
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            // Return error message for consumer
            $this->paymentStatusUnknown(htmlspecialchars($e->getMessage()));
            exit;
        }
    }
}
