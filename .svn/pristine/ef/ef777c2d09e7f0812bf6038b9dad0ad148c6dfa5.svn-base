<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="ag_code" value="<?php echo $ag_code ?>">
<input type="hidden" name="btn_action" value="save">
<div class="form-basic p-lg white">

	<div class="row m-b-sm">
		<div class="col s12 m12 l6">
	    	<div class="input-field">
	    		<b><div class="font-md" id="contract_reference" name="contract_reference"><?php echo 'N/A'; /*$next_num;*/ ?></div></b>
	      		<label for="contract_reference" class="active required">Contract Reference Number</label>
	      	</div>
	    </div>
	   
	   	<div class="col s12 m12 l6">
	    </div>
	</div>

	<div class="row m-b-sm">
	    <div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="store_name" class="selectize" name="store_name" placeholder="Select Store Name" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($store_names as $store_name): ?>
					<option value="<?php echo $store_name['site_id']; ?>"><?php echo $store_name['official_store_name'] ?></option>
					<?php endforeach; ?>
				</select>
	      		<label for="store_name" class="active required">Store Name</label>
	      	</div>
	    </div>

	    <div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="lessor" class="selectize" name="lessor" placeholder="Select Lessor" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($lessors as $lessor): ?>
					<option value="<?php echo $lessor['vendor_code'] ?>"><?php echo $lessor['vendor_name'] ?></option>
					<?php endforeach; ?>
				</select>
	      		<label for="lessor" class="active required">Lessor</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-sm">
		<div class="col s12 m12 l4 p-t-sm">
	    	<div class="input-field">
	    		<select id="payment_terms" class="selectize" name="payment_terms" placeholder="Select Payment Terms" data-parsley-required="true">
					<option value=""></option>
					<?php foreach($payment_terms as $payment_term):?>
					<option value="<?php echo $payment_term['payment_term_code'] ?>"><?php echo $payment_term['payment_term_name'] ?></option>
					<?php endforeach; ?>
				</select>
	      		<label for="payment_terms" class="active required">Payment Terms</label>
	      	</div>
	    </div>
	   
	    <div class="col s6 m12 l4 p-t-md">
	    	<div class="input-field">
	    		<input type="text" class="datepicker_start" name="effectivity_date" class="effectivity_date" placeholder="Enter Effectivity Date" data-parsley-required="true" />
	      		<label for="effectivity_date" class="active required">Effectivity Date</label>
	      	</div>
	    </div>

	    <div class="col s6 m12 l4 p-t-md">
	    	<div class="input-field">
	    		<input type="text" class="datepicker_end" name="expiration_date" class="expiration_date" placeholder="Enter Expiration Date" data-parsley-required="true" />
	      		<label for="expiration_date" class="active required">Expiration Date</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s12 m12 l12 p-t-sm">
	    	<!-- <div class="input-field">
	    		<input type="file" class="file" name="contract_file" class="contract_file" placeholder="Enter Contract File" data-parsley-required="true" />

	      		<label for="contract_file" class="active required">Approved Contract File</label>
	      	</div> -->
	      	

	      	<div class="input-field">
				<a href="#" id="contract_file_upload" class="center m-r-sm">Browse file</a>
				
				<input type="hidden" name="contract_file" id="contract_file" value=""/>
				<label for="contract_file_upload" class="active required">Approved Contract File</label>
			</div>
	    </div>
	</div>
</div>