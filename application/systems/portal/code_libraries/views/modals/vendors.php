<?php

$region_code 			= ( ISSET($info['region_code']) && ! EMPTY($info['region_code']) ) ? $info['region_code'] : '';
$province_code 			= ( ISSET($info['province_code']) && ! EMPTY($info['province_code']) ) ? $info['province_code'] : '';
//$vendor_type 			= ( ISSET($info['vendor_type']) && ! EMPTY($info['vendor_type']) ) ? $info['vendor_type'] : '';
$municity_code 			= ( ISSET($info['muni_city_code']) 	&& ! EMPTY($info['muni_city_code']) ) ? $info['muni_city_code'] : '';
$barangay_code 			= ( ISSET($info['barangay_code']) && ! EMPTY($info['barangay_code']) ) ? $info['barangay_code'] : '';
$org_code 				= ( ISSET($info['org_code']) && ! EMPTY($info['org_code']) ) ? $info['org_code'] : '';
$account_group_codes 	= ( ISSET($info['account_group_codes']) && ! EMPTY($info['account_group_codes']) ) ? $info['account_group_codes'] : '';
$vendor_account_group 	= (ISSET($vendor_account_group)) && !EMPTY($vendor_account_group) ? $vendor_account_group : array();
$vendor_business_centers 	= (ISSET($vendor_business_centers)) && !EMPTY($vendor_business_centers) ? $vendor_business_centers : array();

$orgs 					= (ISSET($orgs)) && !EMPTY($orgs) ? $orgs : array();

$cont_fname 			= ( ISSET($info['cont_fname']) && ! EMPTY($info['cont_fname']) ) ? $info['cont_fname'] : '';
$cont_lname 			= ( ISSET($info['cont_lname']) && ! EMPTY($info['cont_lname']) ) ? $info['cont_lname'] : '';
$cont_mname 			= ( ISSET($info['cont_mname']) && ! EMPTY($info['cont_mname']) ) ? $info['cont_mname'] : '';
$cont_email 			= ( ISSET($info['cont_email']) && ! EMPTY($info['cont_email']) ) ? $info['cont_email'] : '';
$cont_mobile 			= ( ISSET($info['cont_mobile']) && ! EMPTY($info['cont_mobile']) ) ? $info['cont_mobile'] : '';

$selected = '';

?>

<input type="hidden" name="security" value="<?php echo $security ?>">
<input type="hidden" name="btn_action" value="save">
<div class="form-basic p-lg white">	
	<div class="row m-b-lg">

		<?php if(empty($info['vendor_code'])): ?>
		
	    <div class="col s4">
	    	<div class="input-field">
	    		<input type="text" name="vendor_code" class="vendor_code" placeholder="Enter vendor code" <?php echo !empty($info['vendor_code']) ? 'readonly="readonly"' : ''; ?> value="<?php echo isset($info['vendor_code']) ? $info['vendor_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="vendor_code" class="active required">Vendor Code</label>
	      	</div>
	    </div>

	    <?php else: ?>
	    <div class="col s4 p-b-lg">
	    	<div class="input-field">
	    		<h6><b><?php echo $info['vendor_code']; ?></b></h6>
	      		<label for="vendor_code" class="active required">Vendor Code</label>
	      	</div>
	    </div>

	    <input type="hidden" maxlength="45" name="vendor_code" class="vendor_code" value="<?php echo isset($info['vendor_code']) ? $info['vendor_code'] : ''; ?>" />

		<?php endif; ?>

	    <div class="col <?php echo !empty($info['vendor_code']) ? 's12' : 's8' ?>">
	    	<div class="input-field">
	    		<input type="text" name="vendor_name" class="vendor_name" placeholder="Enter vendor name" value="<?php echo isset($info['vendor_name']) ? $info['vendor_name'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="vendor_name" class="active required">Vendor Name</label>
	      	</div>
	    </div>
	</div>	

	<div class="row m-b-lg">
		<!-- <div class="col s3">
	    	<div class="input-field">

	    		<select id="vendor_type" class="selectize white" name="vendor_type" placeholder="Select vendor type" data-parsley-required="true">
						<option value=""></option>
						<option <?php echo ($vendor_type == VT_VENDOR) ? 'selected' : ''; ?> value="<?php echo VT_VENDOR ?>">Vendor</option>
						<option <?php echo ($vendor_type == VT_LESSOR) ? 'selected' : ''; ?> value="<?php echo VT_LESSOR ?>">Lessor</option>
				</select>
				<label for="vendor_type" class="active required">Vendor Type</label>
	      	</div>
	    </div> -->
	    
	    <div class="col s6">
	    	<div class="input-field">
	    
	    		<select id="business_center" class="selectize white" multiple name="business_center[]" placeholder="Select business center" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($business_centers as $val){
							// $selected = ($val['org_code'] == $org_code) ? 'selected' : '';
						$selected = in_array($val['org_code'],$vendor_business_centers) ? 'selected' : '';
					?>
						<option value="<?php echo $val['org_code']; ?>" <?php echo $selected; ?>><?php echo $val['name']; ?></option>
					<?php } ?>
				</select>
				<label for="business_center" class="active required">Business Center</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<div class="input-field">

	    		<select id="account_groups" class="selectize white" multiple name="account_groups[]" placeholder="Select account group" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($account_groups as $key => $val){
							$selected = in_array($val['account_group_code'], $vendor_account_group) ? 'selected' : '';
					?>
						<option value="<?php echo $val['account_group_code']; ?>" <?php echo $selected; ?>><?php echo $val['account_group_name']; ?></option>
					<?php } ?>
				</select>
				<label for="account_groups" class="active required">Account Groups</label>

	    		<!-- <input type="text" name="cost_center_code" class="cost_center_code" placeholder="Enter cost_center" value="<?php //echo isset($info['cost_center_code']) ? $info['cost_center_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="cost_center_code" class="active">Cost Center</label> -->
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s12">
	    	<div class="input-field">
	    		<input type="text" name="description" class="description" placeholder="Enter description" value="<?php echo isset($info['description']) ? $info['description'] : ''; ?>" />
	      		<label for="description" class="active">Description</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s6 m-t-xs">
	    	<div class="input-field">
	    		<input type="text" name="bldg_st" class="bldg_st" placeholder="Enter building street" value="<?php echo isset($info['bldg_st']) ? $info['bldg_st'] : ''; ?>"  data-parsley-required="false" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="bldg_st" class="active">Building Street</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<!-- <div class="input-field">
	    		<input type="text" name="region_code" class="region_code" placeholder="Enter region" value="<?php //echo isset($info['region_code']) ? $info['region_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="region_code" class="active">Region</label>
	      	</div> -->

	      	<div class="input-field">
				<select id="region_code" class="selectize white" name="region_code" placeholder="Select region" data-parsley-required="false">
						<option value=""></option>
					<?php foreach($regions as $val){  
							$selected = ($val['region_code'] == $region_code) ? 'selected' : '';
					?>
						<option value="<?php echo $val['region_code']; ?>" <?php echo $selected; ?>><?php echo $val['region_name']; ?></option>
					<?php } ?>
				</select>
				<label for="region_code" class="active">Region</label>
			</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    
	    <!-- <div class="input-field">
	    		<input type="text" name="province_code" class="province_code" placeholder="Enter province" value="<?php //echo isset($info['province_code']) ? $info['province_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="province_code" class="active">Province</label>
	      	</div>
	    </div> -->
	    <div class="col s6">
		    <div class="input-field">
				<select id="province_code" class="selectize white" name="province_code" placeholder="Select province" data-parsley-required="false">
						<option value=""></option>
					<?php foreach($provinces as $val){  
							$selected = ($val['province_code'] == $province_code) ? 'selected' : '';
					?>
						<option value="<?php echo $val['province_code']; ?>" <?php echo $selected; ?>><?php echo $val['province_name']; ?></option>
					<?php } ?>				
				</select>
				<label for="province_code" class="active">Province</label>
			</div>
		</div>

	    <div class="col s6">
	    	<!-- <div class="input-field">
	    		<input type="text" name="muni_city_code" class="muni_city_code" placeholder="Enter municipality/city" value="<?php //echo isset($info['muni_city_code']) ? $info['muni_city_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="muni_city_code" class="active">Municipality/City</label>
	      	</div> -->
	      	<div class="input-field">
				<select id="muni_city_code" class="selectize white" name="muni_city_code" placeholder="Select city" data-parsley-required="false">
						<option value=""></option>
					<?php foreach($municities as $val){  
							//$id = $val['district_code'].'-'.$val['muni_city_code'];
							$selected = ($val['id'] == $municity_code) ? 'selected' : '';
					?>
						<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?>><?php echo $val['name']; ?></option>
					<?php } ?>
				</select>
				<label for="muni_city_code" class="active">City</label>
			</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s6 m-t-xs">
	    	<div class="input-field">
	    		<input type="text" name="district_code" class="district_code" placeholder="Enter district" value="<?php echo isset($info['district_code']) ? $info['district_code'] : ''; ?>" />
	      		<label for="district_code" class="active">District</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<div class="input-field">
	    		<select id="barangay_code" class="selectize white" name="barangay_code" placeholder="Select barangay" data-parsley-required="false">
						<option value=""></option>
					<?php foreach($barangays as $val){
						/* $combined_code = $val['district_code'] . '-' . $val['muni_city_code'];
						$selected = 
						(
							$val['region_code'] == $region_code && 
							$val['province_code'] == $province_code && 
							$combined_code == $municity_code && 
							$val['barangay_code'] == $barangay_code
						) ? 'selected' : ''; */

						$selected = ($val['barangay_code'] == $barangay_code) ? 'selected' : '';
					?>
						<option value="<?php echo $val['barangay_code']; ?>" <?php echo $selected; ?> > <?php echo $val['barangay_name']; ?></option>
					<?php } ?>
				</select>
				<label for="barangay_code" class="active">Barangay</label>
	      	</div>
	    </div>
	</div>

	<b class="p-l-xs m-b-lg">Contact Person Information</b>
	<hr>
	<br>
	<div class="row m-b-lg">
	    <div class="col s5">
	    	<div class="input-field">
	    		<input type="text" name="contact_last_name" class="contact_last_name" placeholder="Enter Last Name" value="<?php echo isset($cont_lname) ? $cont_lname : ''; ?>" />
	      		<label for="contact_last_name" class="active">Last Name</label>
	      	</div>
	    </div>

	    <div class="col s5">
	    	<div class="input-field">
	    		<input type="text" name="contact_first_name" class="contact_first_name" placeholder="Enter First Name" value="<?php echo isset($cont_lname) ? $cont_lname : ''; ?>" />
	      		<label for="contact_first_name" class="active">First Name</label>
	      	</div>
	    </div>

	    <div class="col s2">
	    	<div class="input-field">
	    		<input type="text" name="contact_middle_initial" class="contact_middle_initial" placeholder="Enter M.I." value="<?php echo isset($cont_mname) ? $cont_mname : ''; ?>" />
	      		<label for="contact_middle_initial" class="active">Middle Initial</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
		
	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="contact_mobile_number" class="contact_mobile_number" placeholder="Enter Mobile Number" value="<?php echo isset($cont_mobile) ? $cont_mobile : ''; ?>" />
	      		<label for="contact_mobile_number" class="active">Mobile Number</label>
	      	</div>
	    </div>

	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="email" name="contact_email" class="contact_email" placeholder="Enter Contact Email" value="<?php echo isset($cont_email) ? $cont_email : ''; ?>" />
	      		<label for="contact_email" class="active">Email</label>
	      	</div>
	    </div>
	</div>
	<!--
	<div class="row">
	    <div class="col s12">
			<div class="input-field">				
				<input type="checkbox" <?php //echo isset($active) ? $active : ''; ?> class="labelauty status" name="status" data-labelauty="Inactive|Active" value="<?php //echo ACTIVE_FLAG ?>" />
				<label for="status" class="active">Status</label>
			</div>
	    </div>
	</div>	-->	
</div>