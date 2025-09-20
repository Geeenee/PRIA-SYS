<?php
$id       = (ISSET($id)) ? $id : '';
$salt     = (ISSET($salt)) ? $salt : '';
$token    = (ISSET($token)) ? $token : '';
$org_code = (ISSET($org_details['org_code']) && ! EMPTY($org_details['org_code'])) ? $org_details['org_code'] : '';
$disabled = (ISSET($org_details['org_code']) && ! EMPTY($org_details['org_code'])) ? 'disabled' : '';
$org_name = (ISSET($org_details['name']) && ! EMPTY($org_details['name'])) ? $org_details['name'] : '';
$website  = (ISSET($org_details['website']) && ! EMPTY($org_details['website'])) ? $org_details['website'] : '';
$email    = (ISSET($org_details['email']) && ! EMPTY($org_details['email'])) ? $org_details['email'] : '';
$phone    = (ISSET($org_details['phone']) && ! EMPTY($org_details['phone'])) ? $org_details['phone'] : '';
$fax      = (ISSET($org_details['fax']) && ! EMPTY($org_details['fax'])) ? $org_details['fax'] : '';
$org_parent_code = (ISSET($org_details['org_parent']) && ! EMPTY($org_details['org_parent'])) ? $org_details['org_parent'] : '';
$org_short_name  = (ISSET($org_details['short_name']) && ! EMPTY($org_details['short_name'])) ? $org_details['short_name'] : '';

$organization_type 		= ( ISSET($portal_org_details['org_type_code']) && ! EMPTY($portal_org_details['org_type_code']) ) ? $portal_org_details['org_type_code'] : '';

$org_logo 			= (ISSET($org_details['logo']) && ! EMPTY($org_details['logo'])) ? $org_details['logo'] : '';
$org_logo_orig_name  = (ISSET($org_details['logo_orig_name']) && ! EMPTY($org_details['logo_orig_name'])) ? $org_details['logo_orig_name'] : '';

$org_checked 	= "";

if( !EMPTY( $org_parents ) )
{
	$org_checked = "checked";
}

$checked_sys 	= '';

if( ISSET( $org_details['system_owner'] ) AND $org_details['system_owner'] == ENUM_YES )
{
	$checked_sys = 'checked';
}

?>

<input type="hidden" name="id" id="id_organizations" value="<?php echo $id; ?>"/>
<input type="hidden" name="salt"  id="salt" value="<?php echo $salt; ?>"/>
<input type="hidden" name="token" id="token" value="<?php echo $token; ?>"/>

<div class="form-float-label">

	<div class="row m-n">
		<div class="col s6">
			<div class="input-field">
				
				<input type="text" data-parsley-required="true" name="org_code"  id="org_code" value="<?php echo $org_code; ?>" <?php echo $disabled ?>  />
				<label for="org_code" class="required">Organization Code</label> 
			</div>
		</div>
		<div class="col s6">
			<div class="input-field">
				<label for="org_short_name" class="required">Organization Short Name</label>
				<input type="text" name="org_short_name" data-parsley-required="true" id="org_short_name" value="<?php echo $org_short_name; ?>" /> 
			</div>
		</div>
	</div>

	<div class="row m-n">
		<div class="col s12">
			<div class="input-field">
				<label for="org_name" class="required">Organization Name</label>
				<input type="text" data-parsley-required="true" name="org_name"  id="org_name" value="<?php echo $org_name; ?>"  /> 
			</div>
		</div>

		
	</div>


	<div class="row m-n">
		<div class="col s6">
			<div class="input-field">
				<select id="org_type_code" class="selectize white org_type_code" name="org_type_code" placeholder="Select organization type code" data-parsley-required="true">
						<option value=""></option>
						<?php foreach($org_type_code as $val){  
							$selected = ($val['org_type_code'] == $organization_type) ? 'selected' : '';
					?>
						<option value="<?php echo $val['org_type_code']; ?>" <?php echo $selected; ?>><?php echo $val['org_type_name']; ?></option>
					<?php } ?>
				</select>
				<label for="org_type_code" class="active required">Organization Type Code</label>
			</div>
		</div>

		<div class="col s12">
			<div class="input-field">
				<label for="parent_org_code" class="active">Parent Organization</label>
				<select name="parent_org_code" id="parent_org_code" class="selectize-orgs"  placeholder="Select sector">
						<option value=""></option>
						<?php foreach($org_parents as $key => $val): 
								$selected = ($org_details['org_parent'] == $val['value']) ? 'selected' : '';	
						?>
							<option value="<?php echo $val['value']; ?>" <?php echo $selected; ?>><?php echo $val['text']; ?></option>
						<?php endforeach; ?>
				</select> 
			</div>
		</div>
	</div>	
	
	<div class="row m-n">
		<div class="col s6">
			<div class="input-field">
				<label for="website" class="active">Website</label>
				<input type="text" name="website" placeholder="e.g. https://www.google.com" id="website" value="<?php echo $website; ?>"  /> 
			</div>
		</div>
		<div class="col s6">
			<div class="input-field">
				<label for="email" class="active">Email</label>
				<input type="email" data-parsley-type="email" name="email" placeholder="e.g. jaundelacruz@gmail.com" id="email" value="<?php echo $email; ?>"  /> 
			</div>
		</div>
	</div>	
		
	<div class="row m-n">
		<div class="col s6">
			<div class="input-field">
				<label for="tel_no">Telephone No.</label>
				<input type="text" data-parsley-type="integer" name="tel_no"  id="tel_no" value="<?php echo $phone; ?>"  /> 
			</div>
		</div>
		<div class="col s6">
			<div class="input-field">
				<label for="fax_no">Fax No.</label>
				<input type="text" data-parsley-type="integer" name="fax_no"  id="fax_no" value="<?php echo $fax; ?>"  /> 
			</div>
		</div>
	</div>	
	<div class="row m-n" style="display: none !important">
		<div class="col s12">
			<div class="input-field m-b-md">
				<label for="logo_attachment_upload" class="active">Logo</label>
				<div class="field-multi-attachment p-t-md">
					<input type="hidden" data-parsley-errors-container=".my_error_container_award" name="org_logo" id="org_logo" value="<?php echo $org_logo;?>" class="form_dynamic_upload" data-origfile="<?php echo $org_logo_orig_name;?>" >
					<input type="hidden" name="org_logo_orig_filename" id="org_logo_orig_filename" value="<?php echo $org_logo_orig_name;?>" class="form_dynamic_upload_origfilename">
					<a href="#" id="org_logo_upload" class="tooltipped m-r-sm" data-position="bottom" data-delay="50" data-tooltip="Upload">Upload</a>
				</div>
			</div>
		</div>
	</div>	
	<div class="row m-n">
		
		<div class="col s4">
			<div class="input-field m-t-lg m-b-md">
				<input type="checkbox" class="labelauty" name="system_owner" value="" data-labelauty="No|Yes" id="system_owner" <?php echo $checked_sys ?> />
				<label for="system_owner" class="active m-t-n-md">System Owner ?</label>
			</div>
		</div>
		<!-- <div class="col l3 m3 s4">
			<div class="input-field m-t-lg m-b-md">
				<input type="checkbox" class="labelauty " name="has_parent" value="1" data-labelauty="No|Yes" id="has_parent" <?php echo $org_checked ?> />
				<label for="has_parent" class="active m-t-n-md">Has Parent ?</label>
			</div>
		</div> -->
	</div>


	<div class="row m-n white lighten-3 p-t-md par_div_tbl" style="display : none !important;">
		<div class="col s8 b-n">
			<h5 class="form-header">Org Parents</h5>
		</div>
		<div class="col s4 right-align">
			<button type="button" id="add_parent" class="btn btn-secondary">Add Parents</button>
		</div>
	</div>
	<div class="row m-n par_div_tbl" style="display : none !important;">
		<div class="col b-n">
			<table class="table table-default form-basic" id="tbl_org_parent" >
				<thead>
					<tr>
						<!-- <th width="35%">Group Type</th> -->
						<th width="45%">Organization</th>
						<th width="10%">&nbsp;</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>				
		</div>
	</div>	

	<div class="row m-n par_div_tbl" style="display : none !important;">
		<div class="col s12">
		&nbsp;
		</div>
	</div>		
</div>