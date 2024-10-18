<?php
class payment_auth extends Payment_Provider_Base
{
	
	function __construct()
	{
		$this->conf['PaymentDirectory'] = 'payment.auth';
		$this->conf['PaymentMethod'] 	= 'auth';
		
		// Load parent constructor
		parent::__construct();
	}

	public function choosePaymentMethod()
	{
		$data = isset($_SESSION['payment_auth']) ? $_SESSION['payment_auth']: array();
        
        $company_data = Setting_Model::getInstance()->get('company_data');
        
        $html  = '<table id="table_auth_payment" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td width="150">'.__('auth accountnumber').':</td>
						<td><input type="text" name="AccountNumber" value="'.((isset($data['AccountNumber'])) ? htmlspecialchars($data['AccountNumber']) : '').'" /></td>
					</tr>
					<tr>
						<td>'.__('auth accountbic').':</td>
						<td><input type="text" name="AccountBIC" value="'.((isset($data['AccountBIC'])) ? htmlspecialchars($data['AccountBIC']) : '').'" /></td>
					</tr>
					<tr>
						<td>'.__('auth accountname').':</td>
						<td><input type="text" name="AccountName" value="'.((isset($data['AccountName'])) ? htmlspecialchars($data['AccountName']) : '').'" /></td>
					</tr>
					<tr>
						<td>'.__('auth accountcity').':</td>
						<td><input type="text" name="AccountCity" value="'.((isset($data['AccountCity'])) ? htmlspecialchars($data['AccountCity']) : '').'" /></td>
					</tr>
				</table>
				<br />';
				
		$html  .= '<label><input type="checkbox" name="Auth_Agree" value="agree" '.((isset($data['Auth_Agree']) && $data['Auth_Agree'] == 'agree') ? 'checked="checked"' : '').'/> '.sprintf(__('i authorize company to collect this invoice'),htmlspecialchars($company_data->CompanyName)).'</label><br /><br />
					<strong>'.__('other open invoices').'</strong><br />
					<label><input type="checkbox" name="Auth_All" value="all" '.((isset($data['Auth_All']) && $data['Auth_All'] == 'all') ? 'checked="checked"' : '').'/> '.sprintf(__('i authorize company to collect all invoices'),htmlspecialchars($company_data->CompanyName)).'</label><br />';


		return $html;
	}
	
	public function validateChosenPaymentMethod()
	{
		// Store data in session
		$_SESSION['payment_auth']['AccountNumber'] 	= $_POST['AccountNumber'];
		$_SESSION['payment_auth']['AccountBIC'] 	= $_POST['AccountBIC'];
		$_SESSION['payment_auth']['AccountName'] 	= $_POST['AccountName'];
		$_SESSION['payment_auth']['AccountCity'] 	= $_POST['AccountCity'];
		$_SESSION['payment_auth']['Auth_Agree'] 	= (isset($_POST['Auth_Agree']) && $_POST['Auth_Agree'] == 'agree') ? 'agree' : '';
		$_SESSION['payment_auth']['Auth_All'] 		= (isset($_POST['Auth_All']) && $_POST['Auth_All'] == 'all') ? 'all' : '';
        
        
		// Check data
		if(!$_SESSION['payment_auth']['AccountNumber'])
		{
			$this->Error = __('auth error, accountnumber needed');
			return false;
		}
        elseif($_SESSION['payment_auth']['AccountNumber'] != '' && !checkIBAN($_SESSION['payment_auth']['AccountNumber']))
        {
            $this->Error = __('auth error, accountnumber invalid');
            return false;
        }
        elseif($_SESSION['payment_auth']['AccountBIC'] != '' && !checkBIC($_SESSION['payment_auth']['AccountBIC']))
        {
            $this->Error = __('auth error, accountbic invalid');
            return false;
        }
		elseif(!$_SESSION['payment_auth']['AccountName'])
		{
			$this->Error = __('auth error, accountname needed');
			return false;
		}
		elseif($_SESSION['payment_auth']['Auth_Agree'] != "agree")
		{
			$this->Error = __('auth error, please agree');
			return false;
		}
		
		return true;
	}
	
	public function startTransaction()
	{		
		// Get debtor data
		$pdo_statement = $this->db->prepare("SELECT `id`, `AccountNumber`, `AccountBIC`, `AccountName`, `AccountCity`, `InvoiceAuthorisation`, `DebtorCode` FROM `HostFact_Debtors` WHERE `id`=:debtor_id LIMIT 1");
		$pdo_statement->bindValue(':debtor_id', 	$this->Debtor);	
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetch();

		// If failed, redirect
		if(!$result || !$result->id)
		{
			$_SESSION['payment']['status'] 	= 'failed';
			$_SESSION['payment']['type'] 	= 'invoice';
			$_SESSION['payment']['id'] 		= $this->InvoiceID;
			$this->paymentStatusUnknown(__('auth error, we cannot process your authorization'));
			return false;
		}
		
		// Compare new and old data
		$old_data = $new_data = array();
		
		if($result->AccountNumber || $result->AccountBIC || $result->AccountName || $result->AccountCity)
		{
			$old_data['AccountNumber']	= $result->AccountNumber;	
			$old_data['AccountBIC']		= $result->AccountBIC;
			$old_data['AccountName']	= $result->AccountName;
			$old_data['AccountCity']	= $result->AccountCity;
					
			if($result->InvoiceAuthorisation == 'no')
			{
				$old_data['InvoiceAuthorisation']	= $result->InvoiceAuthorisation;
			}		
		}
		
		$new_data['AccountNumber']	= $_SESSION['payment_auth']['AccountNumber'];	
		$new_data['AccountBIC']		= $_SESSION['payment_auth']['AccountBIC'];
		$new_data['AccountName']	= $_SESSION['payment_auth']['AccountName'];
		$new_data['AccountCity']	= $_SESSION['payment_auth']['AccountCity'];
		
		$pdo_statement = $this->db->prepare("UPDATE `HostFact_Debtors` SET `AccountNumber`=:account_number, `AccountBIC`=:account_bic, `AccountName`=:account_name, `AccountCity`=:account_city, `Modified`=NOW() WHERE `id`=:debtor_id LIMIT 1");
		$pdo_statement->bindValue(':account_number',$_SESSION['payment_auth']['AccountNumber']);	
		$pdo_statement->bindValue(':account_bic', 	$_SESSION['payment_auth']['AccountBIC']);	
		$pdo_statement->bindValue(':account_name', 	$_SESSION['payment_auth']['AccountName']);	
		$pdo_statement->bindValue(':account_city', 	$_SESSION['payment_auth']['AccountCity']);	
		$pdo_statement->bindValue(':debtor_id', 	$this->Debtor);	
		
		// Execute statement
		$query_result = $pdo_statement->execute();

		// Also adjust setting for new invoices
        $pdo_statement = $this->db->prepare("UPDATE `HostFact_Debtors` SET `InvoiceAuthorisation`='yes' WHERE `id`=:debtor_id LIMIT 1");
        $pdo_statement->bindValue(':debtor_id', 	$this->Debtor);

        // Execute statement
        $pdo_statement->execute();
		
		if(!$query_result)
		{
			$_SESSION['payment']['status'] 	= 'failed';
			$_SESSION['payment']['type'] 	= 'invoice';
			$_SESSION['payment']['id'] 		= $this->InvoiceID;
			$this->paymentStatusUnknown(__('auth error, we cannot process your authorization'));
			return false;
		}

		//Update invoices
		$pdo_statement = $this->db->prepare("UPDATE `HostFact_Invoice` SET `Authorisation`='yes', `PaymentMethod`='', `TransactionID`='', `Modified`=NOW() WHERE `id`=:invoice_id AND `Paid`='0' LIMIT 1");
		$pdo_statement->bindValue(':invoice_id', 	$this->InvoiceID);	
		
		// Execute statement
		$pdo_statement->execute();

		$all = false;
		
		if($_SESSION['payment_auth']['Auth_All'] == "all")
		{
			// Change invoice status all other open invoices
			$pdo_statement = $this->db->prepare("UPDATE `HostFact_Invoice` SET `Authorisation`='yes', `PaymentMethod`='', `TransactionID`='' WHERE `Debtor`=:debtor_id AND `Status` <='2' AND `Paid`='0'");
			$pdo_statement->bindValue(':debtor_id', 	$this->Debtor);	
			
			// Execute statement
			$pdo_statement->execute();
		
			$all = true;
		}
		
		// SEPA Direct Debit
		if(SDD_ID)
		{
			// Get mandate
			$pdo_statement = $this->db->prepare("SELECT * FROM `HostFact_SDD_Mandates` WHERE `Debtor`=:debtor_id AND `Status`='active' LIMIT 1");
			$pdo_statement->bindValue(':debtor_id', 	$this->Debtor);	
			
			// Execute statement
			$pdo_statement->execute();
			$mandate = $pdo_statement->fetch();
			
			if(isset($mandate->MandateType) && $mandate->MandateType == 'OOFF')
			{
				// Set OOFF to suspended and create new mandate
				$pdo_statement = $this->db->prepare("UPDATE `HostFact_SDD_Mandates` SET `Status`='suspended' WHERE `id`=:mandate_id AND `Status`='active'");
				$pdo_statement->bindValue(':mandate_id', 	$mandate->id);	
				$pdo_statement->execute();
				
				$mandate = false;
			}
			
			if(!$mandate)
			{
                $company_data = Setting_Model::getInstance()->get('company_data');
                
				// Create new mandate
				$prefix = preg_replace("/[^a-z0-9]/i","", $result->DebtorCode).'-';
				$prefix = strtoupper($prefix.substr(preg_replace("/[^a-z0-9]/i","", $company_data->CompanyName) ,0, (35-strlen($prefix)-3)).'-');
				
				$pdo_statement = $this->db->prepare("SELECT `MandateID` FROM `HostFact_SDD_Mandates` WHERE `MandateID` LIKE :mandate_id ORDER BY `MandateID` DESC LIMIT 1");
				$pdo_statement->bindValue(':mandate_id', 	$prefix.'%');	
				$pdo_statement->execute();
				$mandate_var = $pdo_statement->fetch();
				
				$mandateID = '';
				
				if($mandate_var->MandateID)
				{
					$tmp_mandate_id = intval(substr($mandate_var->MandateID,-2,2));
					$mandateID = $prefix.str_pad($tmp_mandate_id+1,2,'0',STR_PAD_LEFT);
				}
				else
				{
					$mandateID = $prefix.'01';
				}
				
				// Create mandate
				$pdo_statement = $this->db->prepare("INSERT INTO `HostFact_SDD_Mandates` (`Debtor`, `MandateID`, `MandateDate`, `MandateType`, `Status`) VALUES (:debtor_id, :mandate_id, CURDATE(), :mandate_type, 'active')");
				$pdo_statement->bindValue(':debtor_id', 	$this->Debtor);
				$pdo_statement->bindValue(':mandate_id', 	$mandateID);	
				$pdo_statement->bindValue(':mandate_type',  'RCUR');
				$pdo_statement->execute();
			}
			else
			{
				$mandateID = $mandate->MandateID;
			}
		}

		// E-mail notification  
	    $email = new Email_Model;
	    						
		$email->Sender 		= getFirstMailAddress(Setting_Model::getInstance()->get('CLIENTAREA_NOTIFICATION_EMAILADDRESS'));
		$email->Recipient 	= Setting_Model::getInstance()->get('CLIENTAREA_NOTIFICATION_EMAILADDRESS');
		$email->Subject 	= __('auth notification mail subject');
		$email->Message 	= file_get_contents('payment.auth/auth_payment.phtml');

		$invoice = (object) array();
		$invoice->id = $this->InvoiceID;
		$invoice->InvoiceCode = $this->InvoiceCode;
		
		$email->set_variables(array('invoice'=>$invoice, 'all' => $all, 'old' => $old_data, 'new' => $new_data));

		$result = $email->send();
				
		// Set redirect location
		$_SESSION['payment']['status'] 			= 'paid';
		$_SESSION['payment']['type'] 			= 'invoice';
		$_SESSION['payment']['id'] 				= $this->InvoiceID;
		if(SDD_ID)
		{ 
			$_SESSION['payment']['mandate_id']	= $mandateID;
		}
		$_SESSION['payment']['paymentmethod'] 	= $this->conf['PaymentMethod'];
			
		header("Location: ".IDEAL_EMAIL);
		exit;
		
	}
	
}
