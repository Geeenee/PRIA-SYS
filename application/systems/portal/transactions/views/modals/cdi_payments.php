


<div class="form-basic p-lg white">
	<div class="row m-b-md">
		<div class="col s12 m12 l8">
	    	<div class="input-field">
	    		<select id="workflow" class="selectize" name="workflow" placeholder="Select Category" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($workflows as $key => $val){
					?>
						<option value="<?php echo $val['workflow_id']; ?>"><?php echo $val['workflow_name']; ?></option>
					<?php } ?>
				</select>
				<label for="workflow" class="active required">Category</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s12 m12 l4">
	    	<div class="input-field">
	    		<select id="bom_id" class="selectize" name="bom_id" placeholder="Select Store" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($boms as $key => $val){
					?>
						<option value="<?php echo $val['bom_id']; ?>"><?php echo $val['official_store_name']; ?></option>
					<?php } ?>
				</select>
				<label for="bom_id" class="active required">Store</label>
	      	</div>
	    </div>

		<div class="col s12 m12 l4 p-t-sm">
	    	<div class="input-field ">
				<label class="active">Business Center</label>
				<input type="hidden" name="business_center" id="business_center_field">
				<div class="font-md font-semibold" id="business_center">N/A</div>
	      	</div>
	    </div>

		<div class="col s12 m12 l4 p-t-sm">
	    	<div class="input-field">
				<div class="font-md font-semibold" id="bom_approval_reference_no">N/A</div>
	      		<label class="active">BOM Approval Reference No.</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-sm">
	    <div class="col s12 m12 l4">
			<div class="input-field">
				<input type="text"  id="soa_num" name="soa_num" placeholder="Enter SOA Num" data-parsley-required="true" />
				<label for="soa_num" class="active required">SOA Num</label>
			</div>
	    </div>

	    <div class="col s12 m12 l4">
	    	<div class="input-field">
	    		<input type="text" name="soa_document_date" class="datepicker" placeholder="Enter SOA Document Date"  data-parsley-required="true" value="" />
	      		<label for="soa_document_date" class="active required">SOA Document Date</label>
	      	</div>

	    </div>

	    <div class="col s12 m12 l4">
	    	<div class="input-field">
	    		<input type="text" name="soa_submission_date" class="datepicker" placeholder="Enter SOA Submission Date"  data-parsley-required="true" value="" />
	      		<label for="soa_submission_date" class="active required">SOA Submission Date</label>
	      	</div>

	    </div>

	</div>
</div>