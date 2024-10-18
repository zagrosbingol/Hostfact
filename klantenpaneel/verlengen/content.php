<?php if($error_message){ ?>
	<div id="error_message" class="box ui-state-error">	
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		<strong>Foutmelding:</strong> <?php echo $error_message; ?></p>
	</div>
<?php } ?>

<?php if($success_message){ ?>
	<div id="error" class="box ui-state-highlight">
	
		<p><span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span> 
		<strong>Melding</strong><br />
		<br />
		<?php echo $success_message; ?>
		</p>

	</div>
<?php } ?>	
			
<form method="post" action="?key=<?php echo htmlspecialchars($_GET['key']); ?>" name="subscription_form">
	
	<div class="noborder_box">
		<strong>Online uw abonnementen verlengen</strong><br />
		Onderstaande abonnementen zijn bijna afgelopen. Middels deze pagina kunt u eenvoudig uw abonnementen verlengen, zodat u ook voor de komende periode weer zeker bent van onze dienstverlening.
	</div>
	
	<div id="error" class="box ui-state-error" style="display: none;">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		<strong>Foutmelding:</strong> U dient akkoord te gaan met de voorwaarden en zowel uw naam als functie in te voeren.</p>
	</div>
	
	<div class="noborder_box">
		<table class="table1" cellpadding="0" cellspacing="0">
		<tbody>
		<tr class="trtitle">
			<th width="20"><input name="check_all" value="yes" type="checkbox" /></th>
			<th>Omschrijving abonnement</th>
			<th>Contractperiode</th>
			<th>Verlengen tot</th>
			<th colspan="3">Prijs excl. BTW</th>
		</tr>
		<?php
		foreach($subscriptions as $k=>$item){ 
			?>
			<tr <?php if($k%2 != 0){ echo "class=\"tr1\""; } ?>>
				<td width="20"><input name="subscription[]" value="<?php echo $item['id']; ?>" type="checkbox" class="checkbox"/></td>
				<td><?php echo htmlspecialchars($item['Description']); ?></td>
				<td><?php echo $item['ContractPeriods']; ?> <?php echo ($item['ContractPeriods'] > 1) ? $array_periodic_multi[$item['ContractPeriodic']] : $array_periodic[$item['ContractPeriodic']]; ?></td>
				<td><?php echo rewrite_date_db2site($item['EndContract']); ?></td>
				<td><?php echo CURRENCY_SIGN_LEFT; ?></td>
				<td align="right"><?php echo money(round(1-$item['DiscountPercentage'],8) * $item['PriceExcl'] * $item['Number'] * $item['Periods'],false); ?> per <?php echo $array_periodic[$item['Periodic']]; ?></td>
				<td><?php echo CURRENCY_SIGN_RIGHT; ?></td>
			</tr>
			<?php 
		}
		if(empty($subscriptions)){
			?>
			<tr>
				<td colspan="7">Er zijn geen abonnementen gevonden die op dit moment verlengd kunnen worden.</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		</table>
		<br />
		
		<?php if(!empty($subscriptions)){ ?>
			<input name="agree" value="yes" type="checkbox" <?php if(isset($_POST['agree']) && $_POST['agree'] == "yes"){ echo "checked=\"checked\""; } ?>/>				
			Ik ben bevoegd om de abonnementen te verlengen en ga akkoord met de <?php if(COMPANY_AV_PDF){ ?><a href="<?php echo COMPANY_AV_PDF; ?>" target="_blank" title="Download onze algemene voorwaarden" style="color: #666;"><u>algemene voorwaarden</u></a><?php }else{ ?>algemene voorwaarden<?php } ?>.<br />
			<br />
			<br />
			<strong>Uw gegevens</strong><br />
			<label class="user">Naam:</label> <input type="text" name="Name" class="user" value="<?php if(isset($_POST['Name'])){ echo htmlspecialchars(esc($_POST['Name'])); } ?>" maxlength="100"/><br />
			<?php if($debtor_is_company){ ?><label class="user">Functie:</label> <input type="text" name="Role" class="user" value="<?php if(isset($_POST['Role'])){ echo htmlspecialchars(esc($_POST['Role'])); } ?>" maxlength="100"/><br /><?php } ?>
		<?php } ?>
	</div>
	
	<?php if(!empty($subscriptions)){ ?>
	<div class="noborder_box" style="text-align: left;">
		<button id="submit_btn">Verleng de geselecteerde abonnementen <span class="counter">(0)</span></button>
		<br />
		<?php if(CONTRACT_RENEW_CONFIRM_MAIL > 0){ ?><br /><center style="color: #999;">Na het verlengen van de abonnementen zult u per e-mail een bevestiging ontvangen.</center><?php } ?>
	</div>
	<?php } ?>		
</form>	