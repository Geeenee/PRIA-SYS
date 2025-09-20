<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="account_group" value="<?php echo $account_groups[0]['account_group_code']; ?>">
<div class="form-basic p-lg white">
	<div class="row m-b-md">
        <div class="col s12 m7 l7">
	    	<div class="input-field">
	    		<select class="selectize" id="additional_flag" name="additional_flag" placeholder="Select Project Type" data-parsley-required="true">
					<option value=""></option>
					<option value="<?php echo INITIAL_NO; ?>">New Project</option>
					<option value="<?php echo INITIAL_YES; ?>">Additional Works</option>
				</select>
				<label for="additional_flag" class="active required">Project Type</label>
			</div>
	    </div>
		<div class="col s12 m5 l5 m-t-xs">
	    	<div class="input-field">
				<input type="text" id="pr_num" name="pr_num" data-parsley-required="true">
	      		<label for="pr_num" class="active required">PR Number</label>
	      	</div>
	    </div>
	</div>
	<div class="row m-b-md">
	    <div class="col s12 m12 l12">
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
	</div>
	<div class="row m-b-md">
		<div class="col s12 m6 l6">
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
		<div class="col s12 m6 l6">
	    	<div class="input-field">
	    		<select id="project_ref" class="selectize" name="project_ref" placeholder="Select Project Reference Number" data-parsley-required="false">
						<option value=""></option>
					<?php foreach($boqs as $boq ){ ?>
						<option value="<?php echo $boq['boq_id']; ?>"><?php echo $boq['boq_code']; ?></option>
					<?php } ?>
				</select>
	      		<label for="project_ref" class="active">Project Reference Number</label>
	      	</div>
	    </div>
	</div>
	<div class="row m-b-md">
	    <div class="col s12 m12 l12">
	    	<div class="input-field">
	    		<div class="font-md font-semibold" id="store" name="store"><b><?php echo 'N/A'; ?></b></div>
	      		<label for="store" class="active">Store</label>
	      	</div>
	    </div>
	</div>
	<div class="row m-b-md">
	    <div class="col s12 m12 l12">
	    	<div class="input-field">
				<input type="text" name="requested_by" value="" id="requested_by" data-parsley-required="true">
	      		<label for="requested_by" class="active required">Requested by</label>
	      	</div>
	    </div>
	</div>
</div>