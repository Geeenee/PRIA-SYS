<?php 
	$recom_date_from 		= ISSET($contract_info['recommended_date_from']) ? $contract_info['recommended_date_from'] : '';
	$recom_date_to 			= ISSET($contract_info['recommended_date_to']) ? $contract_info['recommended_date_to'] : '';
	$recom_payment_term_code= ISSET($contract_info['recommended_payment_term_code']) ? $contract_info['recommended_payment_term_code'] : '';
	$recom_payment_term_name= ISSET($contract_info['recommended_payment_term_name']) ? $contract_info['recommended_payment_term_name'] : '';
	$contract_code 			= ISSET($contract_info['contract_code']) ? $contract_info['contract_code'] : '';
	$contract_status_name 	= ISSET($contract_info['contract_status_name']) ? $contract_info['contract_status_name'] : '';
	$contract_status_code 	= ISSET($contract_info['contract_status_code']) ? $contract_info['contract_status_code'] : '';
	$date_from 				= ISSET($contract_info['date_from']) ? date('m/d/Y', strtotime($contract_info['date_from'])) : '';
	$date_to 				= ISSET($contract_info['date_to']) ? date('m/d/Y', strtotime($contract_info['date_to'])) : '';
	$payment_term_code 		= ISSET($contract_info['payment_term_code']) ? $contract_info['payment_term_code'] : '';
	$official_store_name 	= ISSET($contract_info['official_store_name']) ? $contract_info['official_store_name'] : '';
	$payment_term_name 		= ISSET($contract_info['payment_term_name']) ? $contract_info['payment_term_name'] : '';
	$contract_file 			= ISSET($contract_info['contract_file']) ? $contract_info['contract_file'] : '';

	$date_from				= ($contract_info['saved_flag'] == MAINTAINER_YES)? $date_from: $recom_date_from;
	$date_to				= ($contract_info['saved_flag'] == MAINTAINER_YES)? $date_to: $recom_date_to;
	$payment_term_code		= ($contract_info['saved_flag'] == MAINTAINER_YES)? $payment_term_code: $recom_payment_term_code;
	$payment_term_name		= ($contract_info['saved_flag'] == MAINTAINER_YES)? $payment_term_name: $recom_payment_term_name;

	$force_version			= ($edit) ? TRUE: FALSE;
?>
<input type="hidden" id="tab_module" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" id="ag_code" name="ag_code" value="<?php echo $ag_code ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="security" value="<?php echo $security; ?>">
<input type="hidden" id="store_name" name="store_name" value="<?php echo $contract_info['site_id']; ?>">
<input type="hidden" id="lessor" name="lessor" value="<?php echo $contract_info['vendor_code']; ?>">
<div class="form-basic p-lg white">
    <div class="row m-b-md">
	    <div class="col s8">
	    	<div class="input-field">
	    		<div class="div-task-values p-t-n"><?php echo $contract_code; ?></div>
				<label for="contract_code" class="active">Contract Number </label>
	      	</div>
	    </div>

        <div class="col s4">
	    	<div class="input-field">
                <div class="div-task-values p-t-n"><?php echo $contract_status_name; ?></div>
				<label for="contract_status" class="active">Contract Status </label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
	    <div class="col s8">
	    	<div class="input-field">
	    		<?php if($edit AND $contract_info['saved_flag'] == MAINTAINER_YES AND !in_array($contract_status_code, [CONTRACT_FOR_RENEWAL, CONTRACT_RENEWED])): ?>
	    		<input type="text" class="datepicker_start" name="effectivity_date" class="effectivity_date" placeholder="Enter Contract Start" data-parsley-required="true" value="<?php echo $date_from; ?>" />
				<?php else: ?>
	    		<div class="div-task-values p-t-n"><?php echo $date_from; ?></div>
				<?php endif; ?>
				<label for="site" class="active">Contract Start </label>
	      	</div>
	    </div>

        <div class="col s4">
	    	<div class="input-field">
	    		<?php if($edit AND $contract_info['saved_flag'] == MAINTAINER_YES AND !in_array($contract_status_code, [CONTRACT_FOR_RENEWAL, CONTRACT_RENEWED])): ?>
	    		<input type="text" class="datepicker_end" name="expiration_date" class="expiration_date" placeholder="Enter Expiration Date" data-parsley-required="true" value="<?php echo $date_to; ?>" />
				<?php else: ?>
                <div class="div-task-values p-t-n"><?php echo $date_to; ?></div>
				<?php endif; ?>
				<label for="site" class="active">Contract Expiration </label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
	    <div class="col s8">
	    	<div class="input-field">
	    		<div class="div-task-values p-t-n"><?php echo $official_store_name; ?></div>
				<label for="site" class="active">Store Name </label>
	      	</div>
	    </div>

        <div class="col s4">
	    	<div class="input-field">
	    		<?php if($edit AND $contract_info['saved_flag'] == MAINTAINER_YES AND !in_array($contract_status_code, [CONTRACT_FOR_RENEWAL, CONTRACT_RENEWED])): ?>
	    		<select id="payment_terms" class="selectize" name="payment_terms" placeholder="Select Payment Terms" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($payment_terms as $payment_term): 
						$selected	= ($payment_term_code == $payment_term['payment_term_code']) ? "selected": ""; ?>
					<option value="<?php echo $payment_term['payment_term_code'] ?>" <?php echo $selected; ?>><?php echo $payment_term['payment_term_name'] ?></option>
					<?php endforeach; ?>
				</select>
				<?php else: ?>
                <div class="div-task-values p-t-n"><?php echo $payment_term_name; ?></div>
				<?php endif; ?>
				<label for="site" class="active">Payment Terms</label>
	      	</div>
	    </div>
	</div>

	<?php if(is_array($documents) AND count($documents) > 0): ?>
	<div class="row m-b-md">
	    <div class="col s12">
	    	<div class="input-field">
	    		<div class="div-task-values p-t-n">
	    			<!-- <a class="waves-effect waves-light btn-small" target="_blank" href="<?php echo base_url().'pria_file/view?file='.$documents['sys_file_name'] ?>"><?php echo $documents['file_name']; ?></a> 	 -->
					<?php create_document_tag($documents, FALSE, NULL, FALSE, FALSE, $force_version, $force_version); ?>			
	    		</div>
				<label for="site" class="active">Contract File </label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>
</div>