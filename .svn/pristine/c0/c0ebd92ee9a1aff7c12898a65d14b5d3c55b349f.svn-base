<?php 
	$group_name 		= "";
	$group_desc 		= "";
	$group_color 		= "";

	$active_fl 			= NULL;

	$check_active 		= "checked";

	$disable 			= "";

	$class 				= "";

	if( $group_action == ACTION_VIEW )
	{
		$disable 		= "disabled";
	}

	if( !EMPTY( $details ) )
	{
		$group_name 	= ( ISSET( $details['group_name'] ) ) ? $details['group_name'] : '';
		$group_desc 	= ( ISSET( $details['group_description'] ) ) ? $details['group_description'] : '';
		$group_color 	= ( ISSET( $details['group_color'] ) ) ? $details['group_color'] : '';

		$active_fl  	= ( ISSET( $details['active_flag'] ) ) ? $details['active_flag'] : ENUM_NO;

		$class 			= "active";
	}

	if( $active_fl == ENUM_YES )
	{
		$check_active 	= "checked";
	}
	else if( $active_fl == ENUM_NO )
	{
		$check_active 	= "";
	}
?>
<input type="hidden" value="<?php echo $group_id ?>" name="group_id" id="group_id">
<input type="hidden" value="<?php echo $group_salt ?>" name="group_salt" id="group_salt">
<input type="hidden" value="<?php echo $group_token ?>" name="group_token" id="group_token">
<input type="hidden" value="<?php echo $group_action ?>" name="group_action" id="group_action">
<div class="form-float-label">
	<div class="row m-n">
		<div class="col s8">
			<div class="input-field">
				<input type="text" <?php echo $disable ?> data-parsley-required="true" data-parsley-maxlength="100" data-parsley-trigger="keyup" id="group_name" value="<?php echo $group_name ?>" name="group_name">
				<label for="group_name" class="required <?php echo $class ?>">Group Name</label>
			</div>
		</div>
		<div class="col s4">
			<div class="input-field">
				<input type="text" id="group_color" name="group_color" value="<?php echo $group_color ?>" <?php echo $disable ?> class="color-picker m-t-sm m-b-sm center" readonly placeholder="Select color" data-parsley-trigger="keyup" data-parsley-maxlength="45" style="height: 35px;">
				<label for="group_color" class="active">Group Color</label>
			</div>
		</div>
	</div>
	<div class="row m-n">
		<div class="col s12">
			<div class="input-field">
				<textarea id="group_description" class="materialize-textarea" data-parsley-trigger="keyup" data-parsley-maxlength="255" name="group_description" placeholder="" <?php echo $disable ?>><?php echo $group_desc ?></textarea>
				<label for="group_description" class="<?php echo $class ?>">Group Description</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col s4">
			<div class="input-field p-b-sm">					
			 	<input type="checkbox" class="labelauty" <?php echo $disable ?> <?php echo $check_active ?> name="active_flag" id="active_flag_id" value="" data-labelauty="Inactive|Active" />
			</div>
		</div>
	</div>
	<div class="row m-n"></div>
	<div class="table_user_group_div">
		<div class="row m-n white lighten-3 p-t-md">
			<div class="col s8 b-n">
				<h5 class="form-header">Group Members</h5>
			</div>
		</div>
		<div class="row m-n white lighten-3 p-t-md">
			<div class="form-basic">
				<div class="col s4">
					<div class="input-field">
						<div class="left-align m-t-sm">
							<input class="search-box" id="search_box_mod" type="text" value="" placeholder="Search" />
						</div>
					</div>
				</div>
				<div class="col s8">
					<div class="right-align m-t-sm">
						<?php 
							if( EMPTY( $disabled ) ) :
						?>
						<button type="button" id="add_group_member" class="btn btn-secondary">Add Member</button>
						<?php 
							endif;
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="row m-n p-b-sm">
			<div class="col b-n">
				<table class="table table-default form-basic" id="tbl_group_members" >
					<thead>
						<tr>
							<th width="65%">User</th>
							<th width="25%">Role</th>
							<th width="10%">&nbsp;</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>				
			</div>
		</div>
	</div>
</div>