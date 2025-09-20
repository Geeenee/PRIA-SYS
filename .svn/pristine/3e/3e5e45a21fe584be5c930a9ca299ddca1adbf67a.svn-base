<?php
$contract_num		= (ISSET($contract['contract_code']))? $contract['contract_code']: "N/A";
$site_id			= (ISSET($contract['site_id']))? $contract['site_id']: "";
$ref_contract_num	= (ISSET($ref_contract['contract_code']))? $ref_contract['contract_code']: "N/A";
$vendor_name		= (ISSET($vendor['vendor_name']))? $vendor['vendor_name']: "N/A";

$recommended_payment_term_code	= (ISSET($contract['recommended_payment_term_code']))? $contract['recommended_payment_term_code']: "";
$recommended_date_from			= (ISSET($contract['recommended_date_from']))? date('m/d/Y', strtotime($contract['recommended_date_from'])): "";
$recommended_date_to			= (ISSET($contract['recommended_date_to']))? date('m/d/Y', strtotime($contract['recommended_date_to'])): "";
?>
<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="security" value="<?php echo $security ?>">
<div class="form-basic p-lg white">

	<input type="hidden" name="contract_id" id="contract_id" value="">
	<div class="row m-b-md">
		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<select id="store_name" class="selectize" name="store_name" placeholder="Select Store Name" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($store_names as $store_name): ?>
					<option value="<?php echo $store_name['site_id'] ?>"><?php echo $store_name['official_store_name'] ?></option>
					<?php endforeach; ?>
				</select>
				<?php else: ?>
					<?php foreach($store as $store_name):
						if($store_name['site_id'] == $site_id):
							echo $store_name['official_store_name'];
						endif;
					endforeach;
				endif; ?>
	      		<label for="store_name" class="active required">Store Name</label>
	      	</div>
	    </div>

		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<div class="font-md font-semibold" id="contract_reference" name="contract_reference"><b><?php echo 'N/A'; /*00000003R*/ ?></b></div>
				<?php else:
					echo $ref_contract_num;
				endif; ?>
	      		<label for="contract_reference" class="active">Expiring Contract Ref Number</label>
	      	</div>
	    </div>	   
	</div>

	<div class="row m-b-md">
		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<div class="font-md font-semibold" id="lessor" name="lessor">N/A</div>
				<?php else:
					echo $vendor_name;
				endif; ?>
	      		<label for="lessor" class="active">Lessor</label>
	      	</div>
	    </div>

		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<?php if(EMPTY($security)): ?>
	    		<div class="font-md font-semibold" id="reference_contract" name="reference_contract">N/A</div>
				<?php else:
					echo $contract_num;
				endif; ?>
	      		<label for="reference_contract" class="active">New Contract Ref Number</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-xs">
		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<select id="payment_terms" class="selectize" name="payment_terms" placeholder="Select Recommended Payment Terms" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($payment_terms as $payment_term):
						$selected	= ($payment_term['payment_term_code'] == $recommended_payment_term_code) ? "selected": ""; ?>
					<option value="<?php echo $payment_term['payment_term_code'] ?>" <?php echo $selected; ?>><?php echo $payment_term['payment_term_name'] ?></option>
					<?php endforeach; ?>
				</select>
	      		<label for="payment_terms" class="active required">Recommended Payment Terms</label>
	      	</div>
	    </div>
	   

	    <!-- <div class="col s3">
	    	<div class="input-field">
	    		<input type="text" class="datepicker_start" name="effectivity_date" class="effectivity_date" placeholder="Enter Effectivity Date" data-parsley-required="true" />
	      		<label for="effectivity_date" class="active required">Effectivity Date</label>
	      	</div>
	    </div>

	    <div class="col s3">
	    	<div class="input-field">
	    		<input type="text" class="datepicker_end" name="expiration_date" class="expiration_date" placeholder="Enter Expiration Date" data-parsley-required="true" />
	      		<label for="expiration_date" class="active required">Expiration Date</label>
	      	</div>
	    </div> -->
	</div>

	<div class="row m-b-md">
		<div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	      		<input type="text" name="recommended_date_from" class="datepicker_start" placeholder="Enter Recommended Contract Start Date" data-parsley-required="true" value="<?php echo $recommended_date_from; ?>" />
	      		<label for="recommended_date_from" class="active required">Recommended contract period from</label>
	      	</div>
	    </div>

	    <div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<input type="text" name="recommended_date_to" class="datepicker_end" placeholder="Enter Recommended Contract Expiry Date"  data-parsley-required="true" value="<?php echo $recommended_date_to; ?>" />
	      		<label for="recommended_date_to" class="active required">Recommended contract period to</label>
	      	</div>
	    </div>
	</div>
</div>


	
