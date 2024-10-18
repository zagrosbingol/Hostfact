<?php
class Discount_Model extends Base_Model{
	
	
	public function __construct()
	{
		// Load parent constructor
		parent::__construct();
		
		// Default values
		$this->Type 			= 'new';
		$this->DiscountPercentage = 0;
		$this->updateDiscountCounter = false;
		
	}
	
	function check($order_object)
	{		
		// Check if debtor
		if($order_object->get('ExistingCustomer') == 'yes')
		{
			$debtor = new Debtor_Model();
			$debtor->checkLogin();
		}
	
		// Default values
		$elements 						= $order_object->elements;
		$debtor_id 						= (isset($debtor->Identifier)) ? (int)$debtor->Identifier : 0;
		$debtor_auth_discount_allowed 	= false;
		
		// Calculate totals
		$order_object->calculateTotals();

		// Check if we should look for discount for debtors with auth
		if($debtor_id > 0 && $order_object->PaymentMethod == 'auth')
		{
			// Only accept auth. discount if no transaction discount is given.
			$array_paymentmethods = $this->_settings->get('array_paymentmethods');
			
			if(isset($array_paymentmethods['auth']) && !$array_paymentmethods['auth']['PriceExcl'] && !$array_paymentmethods['auth']['Percentage'])
			{
				$debtor_auth_discount_allowed = true;
			}
		}
		
		// Get product ID's from elements
		$product_ids_in_order = array();
		foreach($elements as $tmp_element){
			if($tmp_element['ProductID'] > 0 && !in_array($tmp_element['ProductID'], $product_ids_in_order))
			{
				$product_ids_in_order[] = $tmp_element['ProductID'];
			}
		}
		
		// Get list of all productgroups with id's
		$pdo_statement = $this->_db->prepare("	SELECT `Group`, `Reference` FROM `HostFact_GroupRelations` WHERE `Type`='product'");
		
		// Execute statement
		$pdo_statement->execute();
		$result = $pdo_statement->fetchAll();
		foreach($result as $prod_relation)
		{
			$productgroup_id_list[$prod_relation->Group][] = $prod_relation->Reference;
		}
			
		// Get discount candidates
		$pdo_statement = $this->_db->prepare("	SELECT discount.* 
												FROM (`HostFact_Discount` as discount, `HostFact_GroupRelations` as gr) 
												WHERE (
														(discount.`Debtor`=:debtor_id AND discount.`Debtor` != '')
														OR
														(CONCAT(',',discount.`DebtorGroup`) LIKE CONCAT('%,',gr.`Group`,',%') AND gr.`Reference`=:debtor_id AND gr.`Type`='debtor')
														OR
														(discount.`Debtor`='' AND discount.`DebtorGroup`='') 
														".(($debtor_id > 0) ? " OR (discount.`DebtorGroup`='-2')": "")."
														".(($debtor_id === 0) ? " OR (discount.`DebtorGroup`='-1')": "")."
														".(($debtor_auth_discount_allowed === true) ? " OR (discount.`DebtorGroup`='-3')": "")."
													) 
												AND (discount.`Counter` <".((isset($order_object->Identifier) && $order_object->Identifier > 0) ? "=": "")." discount.`Max` OR discount.`Max`='0')
												AND (
														(discount.`StartDate` <= CURDATE() AND discount.`EndDate` >= CURDATE())
														OR
														(discount.`StartDate` <= CURDATE() AND discount.`EndDate` ='0000-00-00 00:00:00')
														OR
														(discount.`StartDate`='0000-00-00 00:00:00' AND discount.`EndDate`='0000-00-00 00:00:00')
													)
												AND discount.`Status` != 9
												AND discount.`MinAmount` <= :amount_excl
												AND (discount.`Coupon`=:coupon OR discount.`Coupon`='')
												GROUP BY discount.`id`
												ORDER BY discount.`DiscountPercentage` ASC, `Debtor` ASC
												");
		$pdo_statement->bindValue(':debtor_id', $debtor_id);
		$pdo_statement->bindValue(':amount_excl', $order_object->AmountExcl);
		$pdo_statement->bindValue(':coupon', $order_object->Coupon);
		
		// Execute statement
		$pdo_statement->execute();
		$discount_candidate_list = $pdo_statement->fetchAll();
		$discount_list = array();
		
		foreach($discount_candidate_list as $discount_candidate)
		{
			// Check counter
			if($discount_candidate->Max > 0 && $discount_candidate->Counter == $discount_candidate->Max)
			{
				// Max reached, after add...check if this is valid for email
				if(!isset($_SESSION['OrderForm'.ORDERFORM_ID]['Discount_Model']['updatedDiscountCounter'][$discount_candidate->id]))
				{
					// Not set, so not available
					continue;
				}
			}
			
			// If discount has a productID restriction, check for it
			if(	($discount_candidate->Product1 > 0 && !in_array($discount_candidate->Product1, $product_ids_in_order)) ||
				($discount_candidate->Product2 > 0 && !in_array($discount_candidate->Product2, $product_ids_in_order)) || 
				($discount_candidate->Product3 > 0 && !in_array($discount_candidate->Product3, $product_ids_in_order)))
			{
				// Not all productID restrictions are met, so skip this discount candidate
				continue;	
			} 
			
			// If discount has productgroups, check for each restriction if we have at least one product in this group
			if($discount_candidate->ProductGroup1 > 0 && (!isset($productgroup_id_list[$discount_candidate->ProductGroup1]) || count(array_intersect($productgroup_id_list[$discount_candidate->ProductGroup1], $product_ids_in_order)) === 0))
			{
				// ProductGroup1 restrictions not met, so skip this discount candidate
				continue;	
			}
			
			if($discount_candidate->ProductGroup2 > 0 && (!isset($productgroup_id_list[$discount_candidate->ProductGroup2]) || count(array_intersect($productgroup_id_list[$discount_candidate->ProductGroup2], $product_ids_in_order)) === 0))
			{
				// ProductGroup2 restrictions not met, so skip this discount candidate
				continue;	
			}
			
			if($discount_candidate->ProductGroup3 > 0 && (!isset($productgroup_id_list[$discount_candidate->ProductGroup3]) || count(array_intersect($productgroup_id_list[$discount_candidate->ProductGroup3], $product_ids_in_order)) === 0))
			{
				// ProductGroup3 restrictions not met, so skip this discount candidate
				continue;	
			}
			
			// Store product id's in discount
			$discount_candidate->product_ids_in_discount = array($discount_candidate->Product1, $discount_candidate->Product2, $discount_candidate->Product3);
			if(isset($productgroup_id_list[$discount_candidate->ProductGroup1])){ $discount_candidate->product_ids_in_discount = array_merge($discount_candidate->product_ids_in_discount, $productgroup_id_list[$discount_candidate->ProductGroup1]); }
			if(isset($productgroup_id_list[$discount_candidate->ProductGroup2])){ $discount_candidate->product_ids_in_discount = array_merge($discount_candidate->product_ids_in_discount, $productgroup_id_list[$discount_candidate->ProductGroup2]); }
			if(isset($productgroup_id_list[$discount_candidate->ProductGroup3])){ $discount_candidate->product_ids_in_discount = array_merge($discount_candidate->product_ids_in_discount, $productgroup_id_list[$discount_candidate->ProductGroup3]); }
			
			$discount_candidate->product_ids_in_discount = array_unique($discount_candidate->product_ids_in_discount);
			unset($discount_candidate->product_ids_in_discount[array_search(0, $discount_candidate->product_ids_in_discount)]);
			
			$discount_list[] = $discount_candidate;
			
			
		}

		// Process discounts
		foreach($discount_list as $discount_info)
		{
			$is_discount_used = 0;
			
			// Look for discount-type and adjust items
			switch($discount_info->DiscountType){
				case 'TotalAmount': 
                    // Fixed amount of discount
   
                    // Create a temporary object
                    $order_element = new OrderElement_Model();
                    $order_element->newItem('DiscountModule');  		
					$order_element->setAttribute('Description', 		$discount_info->Description);
					$order_element->setAttribute('isCompactView',		true);
					$order_element->setAttribute('PriceExcl', 			($discount_info->Discount > $order_object->AmountExcl) ? - $order_object->AmountExcl : - $discount_info->Discount);
					$order_element->saveItem();
					
					// Add element
					$elements[] = $order_element->getCartItem('DiscountModule', false);
					
					// Delete temporary discount
					$order_element->removeItem('DiscountModule');
					
					$is_discount_used++;
                    break;
                case 'TotalPercentage': 
                    // Percentage of total
                    $this->DiscountPercentage = max($this->DiscountPercentage, $discount_info->DiscountPercentage); 
					
					$is_discount_used++;              
                    break;
				case 'PartialRestrictedPercentage':
					// Discount percentage on all products which meet product requirements
					foreach($elements as $key => $tmp_element){
						
						// Element must have product id, must be one of three productID restrictions or in one of three groups
						if($tmp_element['ProductID'] > 0 && in_array($tmp_element['ProductID'], $discount_info->product_ids_in_discount))
						{						
							$elements[$key]['DiscountPercentage'] = max($tmp_element['DiscountPercentage'], $discount_info->DiscountPart);
							if($discount_info->DiscountPart > $tmp_element['DiscountPercentage'])
							{
								$elements[$key]['DiscountPercentageType'] = $discount_info->DiscountPercentageType;
							}
							
							$is_discount_used++;
							
							// If local limit of this discount is 1, break foreach of elements
							if($discount_info->MaxPerInvoice > 0 && $discount_info->MaxPerInvoice <= $is_discount_used)
							{
								break;
							}
						}
						
					}
					break;
				case 'PartialPercentage': 
					// Discount percentage on products which meet chosen requirements
					foreach($elements as $key => $tmp_element){
						
						$chosen_restriction = $discount_info->DiscountPartRestriction;
						
						// Is product in chosen restricton, give discount
						if($tmp_element['ProductID'] > 0 && ($tmp_element['ProductID'] == $discount_info->{'Product'.$chosen_restriction} || (isset($productgroup_id_list[$discount_info->{'ProductGroup'.$chosen_restriction}]) && in_array($tmp_element['ProductID'], $productgroup_id_list[$discount_info->{'ProductGroup'.$chosen_restriction}]))))
						{						
							$elements[$key]['DiscountPercentage'] = max($tmp_element['DiscountPercentage'], $discount_info->DiscountPart);
							if($discount_info->DiscountPart > $tmp_element['DiscountPercentage'])
							{
								$elements[$key]['DiscountPercentageType'] = $discount_info->DiscountPercentageType;
							}
							
							$is_discount_used++;
							
							// If local limit of this discount is 1, break foreach of elements
							if($discount_info->MaxPerInvoice > 0 && $discount_info->MaxPerInvoice <= $is_discount_used)
							{
								break;
							}
						}
										
					}
					break;
				case 'PartialAmount': 
					// Discount amount on one of the chosen requirements
					foreach($elements as $key => $tmp_element){
						
						$chosen_restriction = $discount_info->DiscountPartRestriction;
						
						// Is product in chosen restricton, give discount
						if(($discount_info->{'Product'.$chosen_restriction} > 0 && $tmp_element['ProductID'] > 0 && $tmp_element['ProductID'] == $discount_info->{'Product'.$chosen_restriction}) || ($discount_info->{'ProductGroup'.$chosen_restriction} > 0 && in_array($tmp_element['ProductID'], $productgroup_id_list[$discount_info->{'ProductGroup'.$chosen_restriction}])))
						{
							// Get product-price, minus discountpart is new price
							$pdo_statement = $this->_db->prepare("SELECT `PriceExcl`, `PricePeriod` FROM `HostFact_Products` WHERE `id`=:product_id LIMIT 1");
							$pdo_statement->bindValue(':product_id', $tmp_element['ProductID']);
							$pdo_statement->execute();	
							$product_info = $pdo_statement->fetch();
							
							// Calculate new price
							$elements[$key]['PriceExcl'] = $this->getPriceForPeriod($tmp_element['Periodic'], $product_info->PricePeriod, $product_info->PriceExcl - $discount_info->DiscountPart);
							
							$is_discount_used++;
							
							// If local limit of this discount is 1, break foreach of elements
							if($discount_info->MaxPerInvoice > 0 && $discount_info->MaxPerInvoice <= $is_discount_used)
							{
								break;
							}

						}

					}
           			break;
			}	
			
			if($is_discount_used > 0 && $this->updateDiscountCounter)
			{
					$pdo_statement = $this->_db->prepare("UPDATE `HostFact_Discount` SET `Counter`=`Counter`+:times_used WHERE `id`=:discount_id LIMIT 1");
					$pdo_statement->bindValue(':times_used', $is_discount_used);
					$pdo_statement->bindValue(':discount_id', $discount_info->id);
					$pdo_statement->execute();
					
					// Store in session, because we need this in the e-mail
					$_SESSION['OrderForm'.ORDERFORM_ID]['Discount_Model']['updatedDiscountCounter'][$discount_info->id] = $is_discount_used;
			}
		}
				
		return $elements;		
	}
	
	function getPriceForPeriod($to_period, $compare_period, $compare_price)
	{
		if($to_period == $compare_period){
			return $compare_price;
		}
	
		$converter = array('t' => 0.5, 'j' => 1, 'h' => 2, 'k' => 4, 'm' => 12, 'w' => 52, 'd' => 365);
			
		// Calculate new price if both periods exist in converter-array
		if(isset($converter[$to_period]) && isset($converter[$compare_period]))
		{
			return $compare_price * ( $converter[$compare_period] / $converter[$to_period] );
		}	
	}
	
	
}		