<?php
class CustomClientFields_Model extends Base_Model{
	
	
	public static function getCustomFields($type)
	{
		// Default no custom fields
		$custom_fields = array();
				
		// Use cache if available
		if(isset($_SESSION['custom_fields'][$type]) && $_SESSION['custom_fields'][$type])
		{
			$custom_fields = $_SESSION['custom_fields'][$type];
		}
		else
		{
			// Load fields
			$show_column = ($type == 'handle') ? 'ShowHandle': 'ShowDebtor';
			$pdo_statement = Database_Model::getInstance()->prepare("SELECT * FROM `HostFact_Debtor_Custom_Fields` WHERE `".$show_column."`='yes' AND `ShowOrderform`='yes' ORDER BY `OrderID` ASC");
			
			$pdo_statement->execute();
			$pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
			$result = $pdo_statement->fetchAll();
			
			if($result === false)
			{
				// Cache
				$_SESSION['custom_fields'][$type] = false;
				return $custom_fields;
			}
			
			foreach($result as $tmp_field)
			{
				unset($tmp_field['ShowDebtor'], $tmp_field['ShowHandle'], $tmp_field['ShowOrderform'], $tmp_field['ShowInvoice'], $tmp_field['ShowPriceQuote'], $tmp_field['OrderID']);
				$custom_fields[] = $tmp_field;
			}
			
			// Store array in session
			$_SESSION['custom_fields'][$type] = $custom_fields;
		}
				
		return $custom_fields;		
	}
	
	public static function getCustomValues($type, $debtor_id = 0)
	{
		// Load custom client values
		$customfield_values = array();
		$model_type 		= CustomClientFields_Model::getCustomFieldsModelName($type);

		if($type == 'debtor' && $debtor_id > 0)
		{
			$pdo_statement = Database_Model::getInstance()->prepare("SELECT v.*, f.`FieldCode`, f.`LabelType`, f.`LabelOptions` FROM `HostFact_Debtor_Custom_Values` as v, `HostFact_Debtor_Custom_Fields` as f WHERE v.`ReferenceType`='debtor' AND v.`ReferenceID`=:reference_id AND v.`FieldID`=f.`id` AND f.`ShowDebtor`='yes'");
			$pdo_statement->bindValue(':reference_id', $debtor_id);
			$pdo_statement->execute();
			$result = $pdo_statement->fetchAll();
			
			if($result)
			{
				foreach($result as $tmp_value)
				{
					if($tmp_value->LabelType == 'checkbox')
					{
						$customfield_values[$tmp_value->FieldCode] = (array)json_decode($tmp_value->Value);
					}
					else
					{
						$customfield_values[$tmp_value->FieldCode] = $tmp_value->Value;	
					}
				}
			}	
		}
		elseif($type == 'handle' && $debtor_id > 0)
		{
			$pdo_statement = Database_Model::getInstance()->prepare("SELECT v.*, f.`FieldCode`, f.`LabelType`, f.`LabelOptions` FROM `HostFact_Debtor_Custom_Values` as v, `HostFact_Debtor_Custom_Fields` as f WHERE v.`ReferenceType`='handle' AND v.`ReferenceID`=:reference_id AND v.`FieldID`=f.`id` AND f.`ShowHandle`='yes' AND f.`ShowOrderform`='yes'");
			$pdo_statement->bindValue(':reference_id', $debtor_id);
			$pdo_statement->execute();
			$result = $pdo_statement->fetchAll();

			if($result)
			{
				foreach($result as $tmp_value)
				{
					if($tmp_value->LabelType == 'checkbox')
					{
						$customfield_values[$tmp_value->FieldCode] = (array)json_decode($tmp_value->Value);
					}
					else
					{
						$customfield_values[$tmp_value->FieldCode] = $tmp_value->Value;
					}
				}
			}
		}
		else
		{
			if(isset($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom']) && is_array($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom']))
			{
				foreach($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'] as $key=>$value)
				{
					$customfield_values[$key] = (is_array($value)) ? $value : htmlspecialchars($value);
				}
			}
		}
		return $customfield_values;
	}
	
	public static function setCustomFields($type)
	{
		$array_customfields = CustomClientFields_Model::getCustomFields($type);
		if(!empty($array_customfields))
		{
			$model_type = CustomClientFields_Model::getCustomFieldsModelName($type);
			
			foreach($array_customfields as $k=>$custom_field)
			{
				$_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'][$custom_field['FieldCode']] = (isset($_POST['custom'][$type][$custom_field['FieldCode']])) ? $_POST['custom'][$type][$custom_field['FieldCode']] : '';
				
				if(!is_array($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'][$custom_field['FieldCode']]))
				{
					$_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'][$custom_field['FieldCode']] = htmlspecialchars_decode($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'][$custom_field['FieldCode']]);
				}
			}
		}
	}
	
	public static function validateCustomFields($type)
	{		
		$error = array();
		
		$array_customfields = CustomClientFields_Model::getCustomFields($type);
		if(!empty($array_customfields))
		{
			$model_type 	= CustomClientFields_Model::getCustomFieldsModelName($type);
			
			$customvalues = $_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'];
			foreach($array_customfields as $custom_field)
			{
				
				$label_name = (__('label custom client fields - '.$custom_field['FieldCode'])) ? __('label custom client fields - '.$custom_field['FieldCode']) : htmlspecialchars($custom_field['LabelTitle']);
				
				switch($custom_field['LabelType'])
				{
					// rewrite date-object...
					case 'date':
						$original_date_input = $customvalues[$custom_field['FieldCode']];
						$customvalues[$custom_field['FieldCode']] = ($customvalues[$custom_field['FieldCode']] && rewrite_date_site2db($customvalues[$custom_field['FieldCode']])) ? date("Y-m-d",strtotime(rewrite_date_site2db($customvalues[$custom_field['FieldCode']]))) : '';
						
						if($original_date_input && !$customvalues[$custom_field['FieldCode']])
						{
							$error[] = sprintf(__('custom client fields regex'), $label_name);
						}
						break;
					
					// Check if options are valid options
					case 'select':
					case 'radio':
					
						if(!array_key_exists('opt-'.htmlspecialchars_decode($customvalues[$custom_field['FieldCode']]), (array) json_decode($custom_field['LabelOptions'])))
						{
							$error[] = sprintf(__('custom client fields regex'), $label_name);
						}
						break;
					case 'checkbox':
					
						$options = (array) json_decode($custom_field['LabelOptions']);
						$checked = (is_array($customvalues[$custom_field['FieldCode']])) ? $customvalues[$custom_field['FieldCode']] : array();
						
						foreach($checked as $checked_key)
						{
							if(!array_key_exists('opt-'.$checked_key, $options))
							{
								$error[] = sprintf(__('custom client fields regex'), $label_name);
								break;
							}
						}
						break;

				}
				
				// validate regexes
				if($custom_field['Regex'])
				{
					if(!@preg_match($custom_field['Regex'], $customvalues[$custom_field['FieldCode']]))
					{
						$error[] = sprintf(__('custom client fields regex'), $label_name);
					}
				}
				
				
			}
			$_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'] = $customvalues;
		}
		
		return $error;
	}
	
	public static function addCustomFields($type, $reference_id)
	{
		$array_customfields = CustomClientFields_Model::getCustomFields($type);
		if(!empty($array_customfields))
		{
			
			$model_type 	= CustomClientFields_Model::getCustomFieldsModelName($type);
			$reference_type = ($type == 'handle') ? 'handle' : 'newcustomer'; 
			$customvalues 	= (isset($_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'])) ? $_SESSION['OrderForm'.ORDERFORM_ID][$model_type]['custom'] : array();

			foreach($array_customfields as $custom_field)
			{
				$custom_value = (isset($customvalues[$custom_field['FieldCode']])) ? $customvalues[$custom_field['FieldCode']] : '';
				
				// In case of labeltype checkbox, json_encode the array
				if($custom_field['LabelType'] == 'checkbox')
				{
					$custom_value = json_encode($custom_value);
				}
				
				$pdo_statement = Database_Model::getInstance()->prepare("INSERT INTO `HostFact_Debtor_Custom_Values` (`ReferenceType`, `ReferenceID`, `FieldID`, `Value`) VALUES (:reference_type,:reference_id,:field_id,:value) ON DUPLICATE KEY UPDATE `Value`=:value");
				$pdo_statement->bindValue(':value', 			$custom_value);
				$pdo_statement->bindValue(':reference_type', 	$reference_type);
				$pdo_statement->bindValue(':reference_id', 		$reference_id);
				$pdo_statement->bindValue(':field_id', 			$custom_field['id']);
				$pdo_statement->execute();
			}
		}
	}
	
	public static function getCustomFieldsModelName($type)
	{
		return ($type == 'handle') ? 'Handle_Model' : (($type == 'debtor') ? 'Debtor_Model' : 'Customer_Model');
	}
	
	public static function syncCustomFields($from_type, $from_id)
	{
		
		$from = CustomClientFields_Model::getCustomValues($from_type, $from_id);
		$to_custom_fields = CustomClientFields_Model::getCustomFields('handle');
	
		foreach($to_custom_fields as $to_custom_field)
		{
			if(isset($from[$to_custom_field['FieldCode']]))
			{
				$_SESSION['OrderForm'.ORDERFORM_ID]['Handle_Model']['custom'][$to_custom_field['FieldCode']] = $from[$to_custom_field['FieldCode']];
			}
		}
	}
	
}