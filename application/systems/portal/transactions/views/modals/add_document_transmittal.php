<?php
/* Notes By: Gene | On : 2025-10-24
|------------------------------------------------------------------------------------------
| This is the Add Document Transmittal Modal View
|
|------------------------------------------------------------------------------------------
|
|------------------------------------------------------------------------------------------
*/

	// $regex		= PARSLEY_AMOUNT_REGEX;

	// $disabled		= (!EMPTY($security))? "disabled": "";

	// $soa_date		= (ISSET($soa_details['soa_date']) AND !EMPTY($soa_details['soa_date']))? date('m/d/Y', strtotime($soa_details['soa_date'])): NULL;
	// $soa_num		= (ISSET($soa_details['soa_num']) AND !EMPTY($soa_details['soa_num']))? $soa_details['soa_num']: NULL;
	// $org_code		= (ISSET($soa_details['org_code']) AND !EMPTY($soa_details['org_code']))? $soa_details['org_code']: NULL;
	// $vendor_code	= (ISSET($soa_details['vendor_code']) AND !EMPTY($soa_details['vendor_code']))? $soa_details['vendor_code']: NULL;
	// $date_submitted	= (ISSET($soa_details['submission_date']) AND !EMPTY($soa_details['submission_date']))? date('m/d/Y', strtotime($soa_details['submission_date'])): NULL;
	// $soa_amount		= (ISSET($soa_details['soa_amount']) AND !EMPTY($soa_details['soa_amount']))? $soa_details['soa_amount']: NULL;
	// $date_from		= (ISSET($soa_details['date_from']) AND !EMPTY($soa_details['date_from']))? date('m/d/Y', strtotime($soa_details['date_from'])): NULL;
	// $date_to		= (ISSET($soa_details['date_to']) AND !EMPTY($soa_details['date_to']))? date('m/d/Y', strtotime($soa_details['date_to'])): NULL;
	// $recipient_id	= (ISSET($soa_details['recipient_id']) AND !EMPTY($soa_details['recipient_id']))? $soa_details['recipient_id']: NULL;
	// $doc_recipient	= (ISSET($soa_details['doc_recipient']) AND !EMPTY($soa_details['doc_recipient']))? $soa_details['doc_recipient']: NULL;
?>

<input type="hidden" id="tab_module" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" id="ag_code" name="ag_code" value="<?php echo $ag_code ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="security" value="<?php echo $security; ?>">
<div class="form-basic p-lg white">

	<div class="row m-b-md">
		<div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<input type="text" name="transmittal_date" class="datepicker" placeholder="Enter Transmittal Date" data-parsley-required="true" data-max-date="0" value="<?php echo $transmittal_date; ?>" />
	      		<label for="transmittal_date" class="active required">Transmittal Date</label>
	      	</div>
	    </div>
	   
	    <div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<input type="text" maxlength="100" name="document_tracer_batch_number" class="" placeholder="Enter Document Tracer Batch Number" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" value="<?php echo $document_tracer_batch_number; ?>" />
				<?php else: ?>
					<input type="hidden" name="document_tracer_batch_number" value="<?php echo $document_tracer_batch_number; ?>"/>
					<?php echo $document_tracer_batch_number; ?>
				<?php endif; ?>
	      		<label for="document_tracer_batch_number" class="active required">Document Tracer Batch Number</label>
	      	</div>
	    </div>
	</div>
				
	<div class="row m-b-md">

		<!-- Business Center Field -->
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
				<label for="business_center" class="active required">Business Center Name</label>
	      	</div>
		</div>

		<!-- Vendor Field -->
		<div class="col s12 m6 l6 p-t-sm">
			<div class="input-field">
				<?php if(EMPTY($security)): ?>
				<select id="vendor" class="selectize" name="vendor" placeholder="Select vendor">
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
				<label for="vendor" class="active">Vendor</label>
			</div>
		</div>

	</div>

	<div class="row m-b-md">

		<!-- Document Transmittal Date Field -->
		<div class="col s12 m6 l6 p-t-sm">
			<div class="input-field">
				<input type="text" name="document_transmittal_date" class="datepicker" placeholder="Enter Document Transmittal Date" data-max-date="0" value="<?php echo $transmittal_document_date; ?>" />
	      		<label for="document_transmittal_date" class="active">Document Transmittal Date</label>
	      	</div>
		</div>

		<!-- Number of Documents Field -->
		<div class="col s12 m6 l6 p-t-sm">
			<div class="input-field">
				<input type="text" name="courier_tracking_number" placeholder="Enter Courier/Tracking Number" data-parsley-required="true" value="<?php echo $courier_tracking_number; ?>" />
				<label for="courier_tracking_number" class="active required">Courier/Tracking Number</label>
			</div>				
		</div>
	</div>

	<!-- Period Covered Field -->
	<div class="row m-b-md">
		<div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="date_from" class="datepicker_start" placeholder="Enter period from" value="<?php echo $date_from; ?>" />
	      		<label for="date_from" class="active">Period Covered</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="date_to" class="datepicker_end" placeholder="Enter period to" value="<?php echo $date_to; ?>" />
	      	</div>
	    </div>
	</div>

	<!-- Document Sender Field -->
	<div class="row m-b-md">
		<div class="col s12 m12 l12 p-t-sm">
			<div class="input-field">
				<input type="text" name="transmittal_document_sender" placeholder="Enter Document Sender" data-parsley-required="true" value="<?php echo $transmittal_document_sender; ?>" />
	      		<label for="transmittal_document_sender" class="active required">Document Sender</label>
	      	</div>
		</div>
	</div>

</div>