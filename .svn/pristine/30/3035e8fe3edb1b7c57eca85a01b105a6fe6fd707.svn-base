<div class="form-basic p-lg white">
    <div class="row m-b-lg">
	    <div class="col s12 m12 l12 p-t-sm">
	    	<div class="input-field">
	    		<select id="site" class="selectize" name="site" placeholder="Select official store name" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($sites as $key => $val){
					?>
						<option value="<?php echo $val['site_id']; ?>"><?php echo $val['official_store_name']; ?></option>
					<?php } ?>
				</select>
				<label for="site" class="active required">Official Store Name</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
        <div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<select class="selectize" id="additional_flag" name="additional_flag" placeholder="Select Project Type" data-parsley-required="true">
					<option value=""></option>
					<option value="<?php echo INITIAL_NO; ?>">New Project</option>
					<option value="<?php echo INITIAL_YES; ?>">Additional Works</option>
				</select>
				<label for="additional_flag" class="active required">Project Type</label>
			</div>
	    </div>
        <div class="col s6 m6 l4">
	    	<div class="input-field">
                <input type="checkbox" class="labelauty" name="third_party" id="third_party" value="<?php echo ACTIVE ?>" data-labelauty="No|Yes"/>

				<label for="third_party" class="active required">3rd Party Contractor</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
        <div class="col s12 m12 l12">
	    	<div class="input-field">
	    		<select id="recommended_contractor" class="selectize" name="recommended_contractor" placeholder="Select Recommended Contractor" data-parsley-required="false">
						<option value=""></option>
						<option value="new">New Contractor</option>
					<?php foreach($contractors as $key => $val){
					?>
						<option value="<?php echo $val['vendor_code']; ?>"><?php echo $val['vendor_name']; ?></option>
					<?php } ?>
				</select>
				<label for="recommended_contractor" class="active required">Recommended Contractor</label>
	      	</div>
	    </div>

        <div class="col s12 m12 l12">
	    	<div class="input-field">
                <input type="text" name="new_contractor" id="new_contractor" placeholder="Enter New Contractor" value="" data-parsley-required="false" disabled/>
	      	</div>
	    </div>
	</div>
</div>