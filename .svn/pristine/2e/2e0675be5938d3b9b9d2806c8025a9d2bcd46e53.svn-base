<?php
$site_id 			= ( ISSET($info['site_id']) && ! EMPTY($info['site_id']) ) ? base64_url_encode($info['site_id']) : '';
$site_num 			= ( ISSET($info['site_num']) && ! EMPTY($info['site_num']) ) ? $info['site_num'] : '';
$site_code 			= ( ISSET($info['site_code']) && ! EMPTY($info['site_code']) ) ? $info['site_code'] : '';
$site_type_code 	= ( ISSET($info['site_type_code']) 	&& ! EMPTY($info['site_type_code']) ) ? $info['site_type_code'] : '';
$nomination_type_code 	= ( ISSET($info['nomination_type_code']) 	&& ! EMPTY($info['nomination_type_code']) ) ? $info['nomination_type_code'] : '';

$status_code 		= ( ISSET($info['status_code']) && ! EMPTY($info['status_code']) ) ? $info['status_code'] : '';
$org_code 			= ( ISSET($info['org_code']) && ! EMPTY($info['org_code']) ) ? $info['org_code'] : '';
$cost_center_code 	= ( ISSET($info['cost_center_code']) && ! EMPTY($info['cost_center_code']) ) ? $info['cost_center_code'] : '';

$suggested_store_name 	= ( ISSET($info['suggested_store_name']) && ! EMPTY($info['suggested_store_name']) ) ? $info['suggested_store_name'] : '';
$official_store_name 	= ( ISSET($info['official_store_name']) && ! EMPTY($info['official_store_name']) ) ? $info['official_store_name'] : '';
$sn_recommendation 		= ( ISSET($info['sn_recommendation']) && ! EMPTY($info['sn_recommendation']) ) ? $info['sn_recommendation'] : '';
$created_by 			= ( ISSET($info['created_by']) && ! EMPTY($info['created_by']) ) ? $info['created_by'] : '';
$created_date 			= ( ISSET($info['created_date']) && ! EMPTY($info['created_date']) ) ? $info['created_date'] : '';


$bc_vend_req 			= ( $site_type_code == SITE_TYPE_COST_CENTER ) ? 'false' : 'true';
$bc_vend_display 		= ( $site_type_code == SITE_TYPE_COST_CENTER) ? 'hide' : '';

$vendor_req 			= ( $site_type_code == SITE_TYPE_OFFICE ) ? 'false' : $bc_vend_req;
$vendor_display 		= ( $site_type_code == SITE_TYPE_OFFICE ) ? 'hide' : '';
?>

<input type="hidden" name="security" value="<?php echo $security ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="site_id" value="<?php echo $site_id; ?>">

<div class="form-basic p-lg white">

	<div class="row m-b-lg">
	    <div class="col s6 p-t-xs">
	    	<div class="input-field">
				<input type="text" name="site_code" class="site_code" placeholder="Enter Site Code"  value="<?php echo isset($site_details['site_code']) ? $site_details['site_code'] : ''; ?>"  data-parsley-required="true"/>	
				<label for="site_code" class="active required">Site Code ( SLOC ) </label>
	      	</div>
	    </div>

		<div class="col s6">
	    	<div class="input-field">
				<select id="site_type_code" class="selectize" name="site_type_code" placeholder="Select site type" data-parsley-required="true">
						<option value=""></option>
						<?php 
							$site_type_code = ISSET($site_details['site_type_code']) ? $site_details['site_type_code'] : '';
							foreach($site_types as $key => $val)
							{
								$selected = ($site_type_code == $val['site_type_code']) ? 'selected' : '';
								echo <<<EOS
									<option value="{$val['site_type_code']}" $selected>{$val['site_type_name']}</option>
EOS;
							} 
						?>
				</select>

				<label for="site_type_code" class="active required">Site Type</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s6">
	    	<div class="input-field">
	    		<select id="business_center" class="selectize" name="business_center" placeholder="Select business center" data-parsley-required="true">
						<option value=""></option>
					<?php foreach($business_centers as $key => $val){
					?>
						<option <?php echo ($org_code == $val['org_code']) ? 'selected' : ''; ?> value="<?php echo $val['org_code']; ?>"><?php echo $val['name']; ?></option>
					<?php } ?>
				</select>
				<label for="business_center" class="active required">Business Center</label>
	      	</div>
	    </div>

		<div class="col s6 <?php echo $vendor_display; ?> <?php echo $bc_vend_display; ?>">
	    	<div class="input-field">
				<select id="vendor_code" class="selectize" name="vendor_code[]" placeholder="Select vendor" data-parsley-required="<?php echo $vendor_req; ?>" multiple>
						<option value=""></option>
						<?php 
							$vendor_code = ISSET($site_details['vendor_codes']) ? explode(',', $site_details['vendor_codes']) : '';
							foreach($vendors as $key => $val)
							{
								$selected = (in_array($val['vendor_code'], $vendor_code)) ? 'selected' : '';
								echo <<<EOS
									<option value="{$val['vendor_code']}" $selected>{$val['vendor_name']}-{$val['vendor_code']}</option>
EOS;
							} 
						?>
				</select>

				<label for="vendor_code" class="active required">Vendor</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s6">
	    	<div class="input-field">
	    		<input type="text" name="official_store_name" class="official_store_name" placeholder="Enter Official Store Name"  value="<?php echo isset($info['official_store_name']) ? $info['official_store_name'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="official_store_name" class="active required">Site Name</label>
	      	</div>
	    </div>

		<div class="col s6">
	    	<div class="input-field">
				<input type="text" id="cost_center_code" name="cost_center_code" class="cost_center_code" placeholder="Enter Cost Center Code"  value="<?php echo isset($site_details['cost_center_code']) ? $site_details['cost_center_code'] : ''; ?>" data-parsley-required="true"/>	
				<label for="cost_center_code" class="active">Cost Center Code</label>
	      	</div>
	    </div>
	</div>

</div>
