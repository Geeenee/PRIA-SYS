<?php
	$regex		= PARSLEY_AMOUNT_REGEX;

	$disabled		= (!EMPTY($security))? "disabled": "";

	$soa_date		= (ISSET($soa_details['soa_date']) AND !EMPTY($soa_details['soa_date']))? date('m/d/Y', strtotime($soa_details['soa_date'])): NULL;
	$soa_num		= (ISSET($soa_details['soa_num']) AND !EMPTY($soa_details['soa_num']))? $soa_details['soa_num']: NULL;
	$org_code		= (ISSET($soa_details['org_code']) AND !EMPTY($soa_details['org_code']))? $soa_details['org_code']: NULL;
	$vendor_code	= (ISSET($soa_details['vendor_code']) AND !EMPTY($soa_details['vendor_code']))? $soa_details['vendor_code']: NULL;
	$date_submitted	= (ISSET($soa_details['submission_date']) AND !EMPTY($soa_details['submission_date']))? date('m/d/Y', strtotime($soa_details['submission_date'])): NULL;
	$soa_amount		= (ISSET($soa_details['soa_amount']) AND !EMPTY($soa_details['soa_amount']))? $soa_details['soa_amount']: NULL;
	$date_from		= (ISSET($soa_details['date_from']) AND !EMPTY($soa_details['date_from']))? date('m/d/Y', strtotime($soa_details['date_from'])): NULL;
	$date_to		= (ISSET($soa_details['date_to']) AND !EMPTY($soa_details['date_to']))? date('m/d/Y', strtotime($soa_details['date_to'])): NULL;
	$recipient_id	= (ISSET($soa_details['recipient_id']) AND !EMPTY($soa_details['recipient_id']))? $soa_details['recipient_id']: NULL;
	$doc_recipient	= (ISSET($soa_details['doc_recipient']) AND !EMPTY($soa_details['doc_recipient']))? $soa_details['doc_recipient']: NULL;
?>

<input type="hidden" id="tab_module" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" id="ag_code" name="ag_code" value="<?php echo $ag_code ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="security" value="<?php echo $security; ?>">
<div class="form-basic p-lg white">

	<div class="row m-b-md">
		<div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<input type="text" name="soa_date" class="datepicker" placeholder="Enter SOA Date" data-parsley-required="true" data-max-date="0" value="<?php echo $soa_date; ?>" />
	      		<label for="soa_date" class="active required">SOA Date</label>
	      	</div>
	    </div>
	   
	    <div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<input type="text" maxlength="100" name="soa_num" class="soa_num" placeholder="Enter SOA Number" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" value="<?php echo $soa_num; ?>" />
				<?php else: ?>
					<input type="hidden" name="soa_num" value="<?php echo $soa_num; ?>"/>
					<?php echo $soa_num; ?>
				<?php endif; ?>
	      		<label for="soa_num" class="active required">SOA Number</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s12 m6 l6 p-t-sm">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<select id="business_center" class="selectize" name="business_center" placeholder="Select Business Center" data-parsley-required="true">
					<option value=""></option>
				<?php
					$selected = ( COUNT($organizations) == 1) ? 'selected' : '';
							
					foreach($organizations as $o)
					{
						echo <<<EOS
						<option value="{$o['org_code']}" $selected>{$o['name']}</option>
EOS;
					}
				?>
				</select>
				<?php else: ?>
					<input type="hidden" name="business_center" value="<?php echo $org_code; ?>"/>
				<?php foreach($organizations as $o)
					{
						if($o['org_code'] == $org_code):
							echo <<<EOS
								{$o['name']}
EOS;
						endif;
					}
				endif; ?>
				<label for="business_center" class="active required">Business Center</label>
	      	</div>
	    </div>

	    <div class="col s12 m6 l6 p-t-sm">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<select id="vendor" class="selectize" name="vendor" placeholder="Select vendor" data-parsley-required="true">
						<option value=""></option>
						<?php
							$selected = ( COUNT($vendors) == 1 ) ? 'selected' : '';
							foreach($vendors as $vendor)
							{
								echo <<<EOS
								<option value="{$vendor['vendor_code']}" $selected>[{$vendor['vendor_code']}] {$vendor['vendor_name']}</option>
EOS;
							}
						?>
				</select>
				<?php else: ?>
					<input type="hidden" name="vendor" value="<?php echo $vendor_code; ?>"/>
				<?php foreach($vendors as $vendor)
					{
						if($vendor['vendor_code'] == $vendor_code):
						echo <<<EOS
							[{$vendor['vendor_code']}] {$vendor['vendor_name']}
EOS;
						endif;
					}
				endif; ?>
				<label for="vendor" class="active required">Vendor</label>
	      	</div>
	    </div>
	</div>

	<?php if($show_po): ?>
	<div class="row m-b-md">
	    <div class="col s12">
	    	<div class="input-field">
	    		<select id="po_reference_number" class="selectize" name="po_reference_number[]" placeholder="Select PO Reference Number" data-parsley-required="true" multiple>
						<option value=""></option>
					<?php foreach($po_references as $key => $po_reference){
					?>
						<option value="<?php echo $po_reference['po_id']; ?>"><?php echo $po_reference['po_num']; ?></option>
					<?php } ?>
				</select>
				<label for="po_reference_number" class="active required">PO Reference Number</label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>

	<?php if($show_receipt): ?>
	<div class="row m-b-md">
	   <div class="col s12" style="max-height: 100px; overflow-y: scroll;" >
	    	<div class="input-field">
	    		<select id="delivery_receipt_number" class="selectize" name="delivery_receipt_number[]" placeholder="Select Delivery Receipt Number" data-parsley-required="true" multiple>
						<option value=""></option>
					<?php 
						foreach($delivery_receipts as $key => $delivery_receipt)
						{
							$selected	= ( ISSET($w_selected_dr) && $w_selected_dr == TRUE AND ISSET($selected_drs) AND in_array($delivery_receipt['dr_gr_id'], $selected_drs)) ? 'selected' : '';
							$new_selected	= (!EMPTY($security) AND in_array($delivery_receipt['dr_gr_id'], $soa_drs))? "selected": $selected;
					?>
							<option value="<?php echo $delivery_receipt['dr_gr_id']; ?>" <?php echo $new_selected; ?> ><?php echo $delivery_receipt['dr_num']; ?></option>
					<?php 
						} 
					?>
				</select>
				<label for="delivery_receipt_number" class="active required">For Delivery Receipt Number</label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>
	
	<div class="row m-b-md">
		<div class="col s6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<input type="text" name="soa_date_submitted" class="datepicker" placeholder="Enter Date Submitted" data-parsley-required="true" data-max-date="0" value="<?php echo $date_submitted; ?>" />
				<?php else: ?>
					<input type="hidden" name="soa_date_submitted" value="<?php echo $date_submitted; ?>"/>
					<?php echo $date_submitted; ?>
				<?php endif; ?>
	      		<label for="soa_date_submitted" class="active required">Date Submitted</label>
	      	</div>
	    </div>

	    <?php if($show_normal): ?>
	    <input type="hidden" name="soa_type" class="" value="<?php echo SOA_NORMAL; ?>" />
		<?php endif; ?>

		<?php if($show_central): ?>
	   	<input type="hidden" name="soa_type" class="" value="<?php echo SOA_CENTRAL; ?>" />
	    <?php endif; ?>

	    <!--
	    <div class="col s6">
	    	<div class="input-field">
	    		<select id="soa_type" class="selectize" name="soa_type" placeholder="Select SOA Type" data-parsley-required="true">
	    			<?php //if($show_normal): ?>
					<option value="<?php //echo SOA_NORMAL ?>">Normal</option>
					<?php //endif; ?>

					<?php //if($show_central): ?>
					<option value="<?php //echo SOA_CENTRAL ?>">Central</option>
					<?php //endif; ?>
				</select>
	      		<label for="soa_type" class="active required">SOA Type</label>
	      	</div>
	    </div> -->

	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="soa_amount" class="number right-align" value="<?php echo $soa_amount; ?>" data-parsley-required="true" data-parsley-pattern="<?php echo $regex; ?>" />
	      		<label for="soa_amount" class="active required">SOA Amount</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="date_from" required="" data-parsley-required="true" class="datepicker_start" placeholder="Enter period from" value="<?php echo $date_from; ?>" />
	      		<label for="date_from" class="active required">Period Covered</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="date_to" required="" data-parsley-required="true" class="datepicker_end" placeholder="Enter period to" value="<?php echo $date_to; ?>" />
	      	<!-- 	<label for="date_to" class="active required"></label> -->
	      	</div>
	    </div>
	</div>

	<!-- <div class="row m-b-md">
	    <div class="col s12">
	    	<div class="input-field">
	    		<select id="soa_document_recipient" class="selectize" name="soa_document_recipient" placeholder="Select SOA Document Recipient" data-parsley-required="true">
						<option value=""></option>
					<?php //foreach($recipient_select as $key => $recipient){
					?>
						<option value="<?php //echo $recipient['user_id'] ?>"><?php //echo $recipient['fullname']; ?></option>
					<?php //} ?>
				</select>
				<label for="soa_document_recipient" class="active required">SOA Document Recipient</label>
	      	</div>
	    </div>
	</div> -->

	<?php if($show_soa_receipt OR (in_array(decrypt_id($ag_code), array(AG_INBOUND_CENTRAL, AG_INBOUND_NORMAL)) AND $show_normal)): ?>
	<div class="row m-b-md">
	    <div class="col s12">
	    	<div class="input-field">
	    		<select id="soa_document_recipient" class="selectize" name="soa_document_recipient" placeholder="Select Finance In-charge" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($recipient_select as $key => $recipient){
						$selected	= ($recipient['user_id'] == $recipient_id)? "selected": "";
					?>
						<option value="<?php echo $recipient['user_id'] ?>" <?php echo $selected; ?>><?php  echo '['.$recipient['role_names'].']-'.$recipient['fullname']; ?></option>
					<?php } ?>
				</select>
				<label for="soa_document_recipient" class="active required">Finance in-charge</label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>

	<?php if($show_soa_doc_receipt OR (in_array(decrypt_id($ag_code), array(AG_INBOUND_CENTRAL, AG_INBOUND_NORMAL)) AND $show_normal)): ?>
	<div class="row m-b-md">
	    <div class="col s12">
	    	<div class="input-field">
	    		<input type="text" name="doc_recipient" value="<?php echo $doc_recipient; ?>" placeholder="Enter SOA Document Recipient">
				<label for="doc_recipient" class="active required">SOA Document Recipient</label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>
</div>