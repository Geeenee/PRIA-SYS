<?php 
	$pending_ctr 		= $statistics['pending_count'];
	$active_ctr 		= $statistics['active_count'];
	$approved_ctr 		= $statistics['approved_count'];
	$disapproved_ctr 	= $statistics['disapproved_count'];
	
	$visitor 			= get_setting(ACCOUNT, "account_creator");


	if( !EMPTY( $roles ) )
	{
		$role_key 				= array_column( $roles, 'role_code' );
		$role_name 				= array_column( $roles, 'role_name' );
		
		$roles_json 			= json_encode( array_combine( $role_key, $role_name ) );
	}
?>

<div class="page-title">
  <div class="row m-b-n">
	<div class="col s12 p-r-n">
	  <h5>Sign up</h5>
	</div>
  </div>
</div>

<input type="hidden" id="sign_up_role_json" value='<?php echo $roles_json ?>'/>

<?php if($visitor == VISITOR){ ?>
	<div class="bg-white m-b-lg box-shadow">
	  <div class="table-display">
		
		<div class="table-cell p-md b-r valign-middle" style="width:50%; border-style:dashed!important; border-right-color:#eee!important;">
		  <div class="row m-n center-align">
			<div class="col s2 left-align font-semibold"><h6 class="m-n">Statistics</h6></div>
			<ul class="list link-tab inline m-l-sm">
				<li><a href="javascript:;" class="link-filter active" id="ctr_pending">new  <span><?php echo $pending_ctr ?></span></a></li>
				<li><a href="javascript:;" class="link-filter" id="ctr_disapproved">rejected <span><?php echo $disapproved_ctr ?></span></a></li>
				<li><a href="javascript:;" class="link-filter" id="ctr_approved">approved <span><?php echo $approved_ctr ?></span></a></li>
			</ul>
		  </div>
		</div>
		
		<div class="table-cell p-md valign-middle" style="width:15%;">
		  <label class="active block m-b-sm">Status</label>
		  <select name="filter_user_status" id="filter_user_status" class="selectize">
			<option value="0">All</option>
			<?php foreach($status_arr as $key => $value): ?>
			  <option value="<?php echo base64_url_encode($key); ?>"><?php echo $value; ?></option>
			<?php endforeach; ?>
		  </select>
		</div>
	  </div>
	</div>

	<div>
	  <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="user_approval_table">
	  <thead>
		<tr>
		  <th width="20%" class="center-align">Name</th>
		  <th width="20%" class="center-align">Agency</th>
		  <th width="15%" class="center-align">Position/Job Title</th>
		  <th width="15%" class="center-align">Email</th>
		  <th width="15%" class="center-align">Status</th>
		  <th width="15%" class="center-align">Actions</th>
		</tr>
		 <tr class="table-filters">
	 	 	<td width="20%" ><input name="full_name" class="form-filter" /></td>
          	<td width="20%" ><input name="agency" class="form-filter"/></td>
			<td width="15%" ><input name="job_title" class="form-filter"/></td>
          	<td width="15%" ><input name="email" class="form-filter"/></td>
          	<td width="15%" >
          		<select name="status" class="material-select form-filter">
          			<option value=""></option>
          			<?php foreach($status_arr as $key => $value): ?>
					  <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
					<?php endforeach; ?>
          		</select>
          	</td>
		 	 <td width="15%" class="table-actions">
	            <a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Submit" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
	            <a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
	          </td>
		 </tr>
	  </thead>
	  </table>
	</div>

	<div class="none">
		<div id="reject_content">
		  <form id="reject_user_form" class="form-basic">
			<input type="hidden" name="id" id="reject_id" value="" />
			<p class="p-n m-b-md font-bold font-sm">Are you sure you want to reject this registration?</p>
			<div class="input-field">
			  <textarea class="materialize-textarea" name="reject_reason" id="reject_reason" placeholder="Write a reject reason here..."></textarea>
			</div>
			<div class="popModal_footer">
				<button type="button" class="btn red darken-3 waves-effect waves-light" data-popmodal-but="ok" onclick="Sign_up.updateStatus('reject_user_form', '<?php echo DISAPPROVED ?>')">Reject</button>
				<button type="button" class="btn-flat" data-popmodal-but="cancel">Cancel</button>
			</div>
		  </form>
		</div>
	</div>
	<div class="none">
		<div id="approve_content" class="approve_content">
		  <form id="approve_user_form" class="form-basic">
			<input type="hidden" name="id" id="approve_id" value="" />
			<p class="p-n m-b-md font-bold font-sm">Are you sure you want to approve this registration?</p>
			<label class="active font-sm font-bold" for="main_role">Assign main role to this account</label>
			<div class="input-field m-t-xs">
			  <select class="selectize main_role" id="main_role" name="main_role[]" placeholder="Select Roles">
				<option value="">Select Role</option>
				<?php foreach($roles as $role): ?>
				<option value="<?php echo $role['role_code'] ?>"><?php echo $role['role_name'] ?></option>
				<?php endforeach; ?>
			  </select>
			</div>
			<label class="active font-sm font-bold" for="approve_user_roles">Assign other role/s to this account</label>
			<div class="input-field m-t-xs">
			  <select class="selectize other_role" multiple id="approve_user_roles" name="role[]" placeholder="Select Roles">
				<option value="">Select Roles</option>
				<?php foreach($roles as $role): ?>
				<option value="<?php echo $role['role_code'] ?>"><?php echo $role['role_name'] ?></option>
				<?php endforeach; ?>
			  </select>
			</div>
			<div class="popModal_footer">
				<button type="button" class="btn green darken-3 waves-effect waves-light" data-popmodal-but="ok" onclick="Sign_up.updateStatus('approve_user_form', '<?php echo APPROVED ?>')">Approve</button>
				<button type="button" class="btn-flat" data-popmodal-but="cancel">Cancel</button>
			</div>
		  </form>
		</div>
	</div>
<?php 	
	
	}
?>