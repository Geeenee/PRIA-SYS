<div class="form-basic p-lg white">
    <div class="row m-b-sm">
	    <div class="col s12 m12 l12">
	    	<div class="input-field">
	    		<select id="site" class="selectize" name="site" placeholder="Select site" data-parsley-required="true">
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

	<div class="row m-b-sm">
		<div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="boq" class="selectize" name="boq" placeholder="Select boq" data-parsley-required="true">
						<option value=""></option>
				</select>
				<label for="boq" class="active required">Boq</label>
	      	</div>
	    </div>

		<div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="project_type" class="selectize" name="project_type[]" placeholder="Select project type" data-parsley-required="true" multiple>
						<option value=""></option>
			
				</select>
				<label for="project_type" class="active required">Project Type</label>
	      	</div>
	    </div>
	</div>
</div>