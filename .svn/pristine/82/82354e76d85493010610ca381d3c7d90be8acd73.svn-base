<?php
	$regex 					= PARSLEY_AMOUNT_REGEX;
?>
<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="btn_action" value="save">
<div class="form-basic p-lg white">

	<div class="row m-b-md">
		<div class="col s12 m6 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="account_group" class="selectize" name="account_group" placeholder="Select Account Group" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($account_groups as $key => $account_group){
					?>
						<option value="<?php echo $account_group['account_group_code']; ?>"><?php echo $account_group['account_group_name']; ?></option>
					<?php } ?>
				</select>
	      		<label for="account_group" class="active required">Account Group</label>
	      	</div>
	    </div>

		<?php if($show_project_type){ ?>
		<div class="col s12 m6 l6 p-t-sm">
	    	<div class="input-field">
	    		<select class="selectize" id="additional_flag" name="additional_flag" placeholder="Select Project Type" data-parsley-required="true">
					<option value=""></option>
					<option value="<?php echo INITIAL_NO; ?>">New Project</option>
					<option value="<?php echo INITIAL_YES; ?>">Additional Works</option>
				</select>
				<label for="additional_flag" class="active required">Project Type</label>
			</div>
		</div>
		<?php } ?>
	</div>

	<?php if($show_boq_ref): ?>
	<div class="row m-b-md">
		<div class="col s12 m12 l12">
	    	<div class="input-field">
	    		<select id="boq_ref" class="selectize" name="boq_ref" placeholder="Select BOQ" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($boqs as $boq ){ ?>
						<option value="<?php echo $boq['boq_id']; ?>"><?php echo $boq['boq_code']; ?></option>
					<?php } ?>
				</select>
	      		<label for="boq_ref" class="active required">BOQ number</label>
	      	</div>
	    </div>
	</div>
	<?php endif; ?>

	<div class="row m-b-md">
		<?php
			if($show_pr_field)
			{
				if($show_pr_text)
				{
		?>
						<div class="col s12 m12 l6 p-t-sm">
							<div class="input-field ">
								<input type="text" name="purchase_request" value="" id="purchase_request" data-parsley-required="true">
								<label for="purchase_request" class="active required">PR Ref Number</label>
							</div>
						</div>
		<?php
				}
				else
				{
		?>
						<div class="col s12 m12  p-t-sm">
							<div class="input-field ">
								<select id="purchase_request" class="selectize" name="purchase_request[]" placeholder="Select PR Reference Number" data-parsley-required="true" multiple>

								<?php //foreach($purchase_requisitions as $key => $purchase_request){ ?>
									<!-- <option value="<?php echo $purchase_request['pr_id']; ?>"><?php echo $purchase_request['pr_num']; ?></option> -->
								<?php // } ?>
								</select>
								<label for="purchase_request" class="active required">PR Ref Number</label>
							</div>
						</div>
		<?php
				}
			}
		?>
	</div>

	<div class="row m-b-md">
		<div class="col s6 m6 l6">
	    	<div class="input-field">
				<input type="text" name="po_num" value="" id="po_num" data-parsley-required="true">
	      		<label for="po_num" class="active required">PO Number</label>
	      	</div>
	    </div>

	   	<div class="col s6 m6 l6">
	    	<div class="input-field">
				<input type="text" class="datepicker" name="po_date" value="" id="po_date" data-parsley-required="true" data-max-date="0">
	      		<label for="po_date" class="active required">PO Date</label>
	      	</div>
	    </div>
	</div>



	<div class="row m-b-md">
	    <div class="col s12 m12 l6 p-t-xs">
	    	<div class="input-field">
	    		<select id="business_center" class="selectize" name="business_center" placeholder="Select Business Center" data-parsley-required="true">
					<option value=""></option>
					<?php
						//$selected = ( COUNT($organizations) == 1 ) ? 'selected' : '';
						$selected = '';
						foreach($organizations as $o)
						{
							echo <<<EOS
							<option value="{$o['org_code']}" $selected>{$o['name']}</option>
EOS;
						}
					?>
				</select>
				<label for="business_center" class="active required">Business Center</label>
	      	</div>
	    </div>

	    <div class="col s12 m12 l6 p-t-xs">
	    	<div class="input-field">
	    		<select id="vendor" class="selectize" name="vendor" placeholder="Select Vendor" data-parsley-required="true">
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
	      		<label for="vendor" class="active required">Vendors</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<input type="text" name="amount" value="" id="amount" data-parsley-required="true" data-parsley-pattern="<?php echo $regex; ?>" class="number right-align">
	      		<label for="amount" class="active required">Amount</label>
	      	</div>
	    </div>

	    <div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<input type="text" class="datepicker" name="released_date" value="" id="released_date" data-parsley-required="true" data-max-date="0">
	      		<label for="released_date" class="active required">Released Date</label>
	      	</div>
	    </div>
	</div>
</div>