<?php
class PaymentMethod_Model
{
	// Protected variables
	protected $db;
	
	function __construct()
	{
		$this->db = Database_Model::getInstance();	
	}
	
	
	public function listPaymentMethods()
	{
		$payment_method = array();
		
		// Get all payment methods
		$pdo_statement = $this->db->prepare("SELECT * FROM `HostFact_PaymentMethods` WHERE `PaymentType`!='wire' AND `Availability` >= 2 ORDER BY `Ordering` ASC");
			    	
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetchAll();

		foreach($result as $tmp_result)
		{

			$key = $tmp_result->PaymentType;
			if(isset($payment_method[$key])){
				$tmp_result->PaymentType = $tmp_result->PaymentType.''.$tmp_result->id;
			}
			$key = $tmp_result->PaymentType;
			
			// Payment provider
			$payment_method[$key]['TITLE'] 		= $tmp_result->Title;
			$payment_method[$key]['CLASS'] 		= strtolower(str_replace('.','_',$tmp_result->Directory));
			
			if(is_numeric(substr($payment_method[$key]['CLASS'],0,1))){
				$payment_method[$key]['CLASS']	= str_replace(array(1,2,3,4,5,6,7,8,9,0),array('one_','two_','three_','four_','five_','six_','seven_','eight_','nine_','zero_'),substr($payment_method[$key]['CLASS'],0,1)) . substr($payment_method[$key]['CLASS'],1);	
			}
			$payment_method[$key]['DIRECTORY']	= $tmp_result->Directory;
			$payment_method[$key]['IMAGE'] 		= $tmp_result->Image;
			$payment_method[$key]['IMAGEALT'] 	= '';
			
			// Extra text for bank/payment choice
			$payment_method[$key]['EXTRA'] 		= $tmp_result->Extra;
			
			// Calculate fee?
			$payment_method[$key]['FEETYPE'] 	= $tmp_result->FeeType;
			$payment_method[$key]['FEEAMOUNT'] 	= $tmp_result->FeeAmount;
			$payment_method[$key]['FEEDESC'] 	= $tmp_result->FeeDesc;
		
		}
		
		return $payment_method;
	}
	
	public function getPaymentMethodByID($id)
	{
		$pdo_statement = $this->db->prepare("SELECT `Title` FROM `HostFact_PaymentMethods` WHERE `id`=:payment_id AND `Availability` >= 2");
		$pdo_statement->bindValue(':payment_id', $id);	
			    	
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if(isset($result->Title) && $result->Title)
		{
			return $result->Title;
		}
		else
		{
			return false;
		}
	}
	
	public function getInvoice($invoicecode, $hash)
	{
		
		$pdo_statement = $this->db->prepare("SELECT i.`id`, i.`Debtor`, i.`InvoiceCode`, i.`Date`, i.`AmountIncl`, i.`AmountPaid`, d.`DebtorCode`, d.`Initials`, d.`SurName`, d.`EmailAddress`, d.`PhoneNumber`, d.`Address`, d.`City`, d.`ZipCode`, d.`Country`, i.`Authorisation`, d.`AccountNumber`, d.`AccountName`, d.`AccountCity`, i.`Status`, i.`TransactionID` FROM `HostFact_Invoice` as i LEFT JOIN `HostFact_Debtors` as d ON (i.`Debtor`=d.`id`) WHERE LOWER(i.`InvoiceCode`)=:invoice_code AND MD5(CONCAT(i.`id`, i.`InvoiceCode`, i.`Date`, i.`AmountIncl`))=:hash AND i.`Status` <= '4' LIMIT 1");
		$pdo_statement->bindValue(':invoice_code', $invoicecode);	
		$pdo_statement->bindValue(':hash', $hash);	

		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();

		// Format date
		if($result)
		{
			$result->Date = rewrite_date_db2site($result->Date); 
			
			// Check for language and override
			$this->checkLanguagePreference($result->Debtor);									
		}
		
		return $result;
	}
	
	public function getInvoiceByID($invoice_id)
	{
		
		$pdo_statement = $this->db->prepare("SELECT i.`id`, i.`Debtor`, i.`InvoiceCode`, i.`Date`, i.`AmountIncl`, i.`AmountPaid`, d.`DebtorCode`, d.`Initials`, d.`SurName`, d.`EmailAddress`, d.`PhoneNumber`, d.`Address`, d.`City`, d.`ZipCode`, d.`Country`, i.`Authorisation`, d.`AccountNumber`, d.`AccountName`, d.`AccountCity`, i.`PaymentMethodID` FROM `HostFact_Invoice` as i LEFT JOIN `HostFact_Debtors` as d ON (i.`Debtor`=d.`id`) WHERE i.`id`=:invoice_id LIMIT 1");
		$pdo_statement->bindValue(':invoice_id', $invoice_id);	

		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		// Format date
		if($result)
		{
			$result->Date = rewrite_date_db2site($result->Date); 
		}
		
		return $result;
	}
	
	public function getOrderByID($order_id)
	{
		
		$pdo_statement = $this->db->prepare("SELECT o.`id`, o.`OrderCode`, o.`Date`, o.`AmountIncl`, o.`PaymentMethodID` FROM `HostFact_NewOrder` as o WHERE o.`id`=:order_id LIMIT 1");
		$pdo_statement->bindValue(':order_id', $order_id);	

		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		// Format date
		if($result)
		{
			$result->Date = rewrite_date_db2site($result->Date); 
		}
		
		return $result;
	}
	
	public function checkLanguagePreference($debtor_id)
	{
		if(isset($_SESSION['client_language']))
		{
			return;
		}
		
		$pdo_statement = $this->db->prepare("SELECT `DefaultLanguage` FROM `HostFact_Debtors` WHERE `id`=:debtor_id LIMIT 1");
		$pdo_statement->bindValue(':debtor_id', $debtor_id);	
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();
		
		if($result->DefaultLanguage && $result->DefaultLanguage != LANGUAGE)
		{
			// Load file
			if(@file_exists("includes/language/".htmlspecialchars($result->DefaultLanguage).".php"))
			{
				global $_LANG;
				require_once "includes/language/".htmlspecialchars($result->DefaultLanguage).".php";
				
				$_SESSION['client_language'] = $result->DefaultLanguage;
			}
		}
	}		
}