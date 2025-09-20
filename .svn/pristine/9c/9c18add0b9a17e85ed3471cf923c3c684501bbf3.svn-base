<div class="form-basic p-lg white">
    <div class="row m-b-sm">
	    <div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="business_center" class="selectize" name="business_center" placeholder="Select business center" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($business_centers as $key => $val){
					?>
						<option value="<?php echo $val['org_code']; ?>"><?php echo $val['name']; ?></option>
					<?php } ?>
				</select>
				<label for="business_center" class="active required">Business Center</label>
	      	</div>
	    </div>


		<div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="nomination_type" class="selectize" name="nomination_type" placeholder="Select Nomination Type" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($nomination_types as $key => $val){
					?>
						<option value="<?php echo $val['category_code']; ?>"><?php echo $val['category_name']; ?></option>
					<?php } ?>
				</select>
				<label for="nomination_type" class="active required">Nomination Type</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-sm">
	    <div class="col s12 m12 l12">
	    	<div class="input-field">
	    		<input type="text"  id="suggested_name" name="suggested_name"  placeholder="Enter Suggested Store Name" data-parsley-required="true" />
	      		<label for="suggested_name" class="active required">Suggested Store Name</label>
	      	</div>
	    </div>
	</div>
</div>