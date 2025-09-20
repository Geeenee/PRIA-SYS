<?php 
$sp_status = get_sys_param_val(SYS_PARAM_STATUS, $user["status"]);

$id	= (!EMPTY($user["user_id"]))? $user["user_id"] : "";
$lname = (!EMPTY($user["lname"]))? $user["lname"] : "";
$fname = (!EMPTY($user["fname"]))? $user["fname"] : "";
$mname = (!EMPTY($user["mname"]))? $user["mname"] : "";
$nickname = (!EMPTY($user["nickname"]))? $user["nickname"] : "";
$female = ($user["gender"] == GENDER_FEMALE)? "checked" : "";
$male = ($user["gender"] == GENDER_MALE)? "checked" : "";
$email = (!EMPTY($user["email"]))? $user["email"] : "";
$job_title = (!EMPTY($user["job_title"]))? $user["job_title"] : "";
$contact_no = (!EMPTY($user["contact_no"]))? $user["contact_no"] : "";
$mobile_no = (!EMPTY($user["mobile_no"]))? $user["mobile_no"] : "";
//$username = (!EMPTY($user["username"]))? $user["username"] : "";
$photo = (!EMPTY($user["photo"]))? $user["photo"] : "";
$img_src = (!EMPTY($user["photo"]))? PATH_USER_UPLOADS . $user["photo"] : PATH_IMAGES . "avatar.jpg";

$root_path 	= get_root_path();

$photo_path = $root_path . $img_src;
$photo_path = str_replace(array('\\','/'), array(DS,DS), $photo_path);

$check_upl 	= check_custom_path();


$status = $sp_status["sys_param_value"];
$org_name = $user["org_name"];
$contact_flag = (ISSET($user["contact_flag"])) ? $user["contact_flag"] : "";

$pw_note = "Type in a new password below to reset / change current password.";

$salt = gen_salt();
$token	= in_salt($id, $salt);
$img_src_custom 	= output_image( $this->session->photo, PATH_USER_UPLOADS );

?>
<form id="form_profile_account">
	<div class="row m-md">
		<div class="col l10 m12 s12 p-n">
			<div class="white box-shadow">
				<div class="table-display">
					<div class="table-cell s3 bg-dark p-lg valign-top profile-banner">
								
						<div class="avatar">
							<div class="avatar-wrapper">
						    	<?php if( !EMPTY( $photo ) AND file_exists( $photo_path ) ) : ?>

						    		<?php 
						    			if( !EMPTY( $check_upl ) ) :


						    		?>
									<img id="profile_img" src="<?php echo $img_src_custom ?>" />
						    		<?php 
						    			else :
						    		?>
									<img id="profile_img" src="<?php echo base_url() . PATH_USER_UPLOADS . $this->session->photo ?>" />
									<?php 
										endif;
									?>
						  		<?php else : ?>
						  			<img id="profile_img" class="profile_avatar" data-name="<?php echo $this->session->name ?>" />
							  	<?php endif; ?>
							  	<a href="#" id="profile_photo" class="m-r-sm">Edit Photo</a>
						    </div>
						</div>
						<div class="left-align m-t-lg">
							<label class="label mute">Profile Picture</label>
							<p class="caption m-t-sm white-text">This is the avatar the users see when they are viewing your account. Image file type must be jpeg, jpg, png, gif.</p>
						</div>	
					</div>
					<div class="table-cell s9 p-lg valign-top">
						<input type="hidden" name="user_id" value="<?php echo $id ?>">
						<input type="hidden" name="salt" value="<?php echo $salt ?>">
						<input type="hidden" name="token" value="<?php echo $token ?>">
						<input type="hidden" name="status" value="<?php echo $status ?>">
						<input type="hidden" name="contact_type" value="<?php echo $contact_flag ?>">
						<input type="hidden" name="image" id="user_image" value="<?php echo $photo ?>"/>
						<input type="hidden" id="user_upload_path" value="<?php echo PATH_USER_UPLOADS ?>"/>
						
						<div class="form-basic">
							<div class="p-b-md">
						  		<h6>Personal Information</h6>
						  		<div class="help-text">Manage your account personal information.</div>
							</div>
							<div class="row">
						  		<div class="col s4" id="lname_wrapper">
									<div class="input-field">
										<input type="text" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" name="lname" id="lname" value="<?php echo $lname ?>"/>
										<label for="lname" class="active required">Last Name</label>
									</div>
						  		</div>
						  		<div class="col s4" id="fname_wrapper">
									<div class="input-field">
										<input type="text" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" name="fname" id="fname" value="<?php echo $fname ?>"/>
										<label for="fname" class="active required">First Name</label>
									</div>
						  		</div>
						  		<div class="col s4" id="mname_wrapper">
									<div class="input-field">
										<input type="text" name="mname" id="mname" value="<?php echo $mname ?>"/>
										<label for="mname" class="active">Middle Name</label>
									</div>
						  		</div>
							</div>
							
							<div class="row">
								<div class="col s8" id="nickname_wrapper">
									<div class="input-field">
								  		<input type="text" name="nickname" id="nickname" value="<?php echo $nickname ?>"/>
								  		<label for="lname" class="active">Nickname</label>
									</div>
							  	</div>
							  	<div class="col s4" id="gender_wrapper">
							  		<div class="input-field">
							  			<label class="active required">Sex</label>
										<div class="row m-b-n">
									  		<div class="col s4 p-l-n">
									  			<p>
											      <input type="radio" name="gender" id="profile_gender_male" value="<?php echo MALE ?>" <?php echo $male ?>/>
											      <label for="profile_gender_male">Male</label>
											    </p>
									  		</div>
									  		<div class="col s4">
									  			<p>
											      <input type="radio" name="gender" id="profile_gender_female" value="<?php echo FEMALE?>" <?php echo $female ?>/>
											      <label for="profile_gender_female">Female</label>
											    </p>
									  		</div>
										</div>
									</div>
								</div>   
							</div>
							
							<?php 
								if( !EMPTY( $user_groups ) ) :
							?>
							<div class="row">
								<div class="col s12" id="groups_wrapper">
									<div class="input-field">
										
										<div class="p-t-md">
											<?php 
												foreach( $user_groups as $u_g ) : 

													$color 	= ( !EMPTY( $u_g['group_color'] ) ) ? $u_g['group_color'] : '#F8F8F8';

											?>
											<span class="b-radius-2 p-xs m-r-xs black-text font-md font-spacing-05" style="background-color: <?php echo $color ?>"><?php echo $u_g['group_name'] ?></span>
											<?php 
												endforeach;
											?>
										</div>
								  		
								  		<label for="groups_user_sel" class="active">Groups</label>
									</div>
							  	</div>
							</div>
							<?php 
								endif;
							?>
							
							<div class="p-b-md p-t-md">
						  		<h6>Contact Information</h6>
						  		<div class="help-text">Manage your account personal information.</div>
							</div>
							
							<div class="row">
								<div class="col s6" id="contact_no_wrapper">
									<div class="input-field">
								  		<input type="text" name="contact_no" id="contact_no" value="<?php echo $contact_no ?>"/>
								  		<label for="contact_no" class="active">Telephone No.</label>
									</div>
							  	</div>
							  	<div class="col s6" id="mobile_no_wrapper">
									<div class="input-field">
								  		<input type="text" name="mobile_no" id="mobile_no" value="<?php echo $mobile_no ?>"/>
								  		<label for="mobile_no" class="active">Mobile No.</label>
									</div>
							  	</div>
							</div>
							
							<div class="row">
								<div class="col s12" id="email_wrapper">
									<div class="input-field">
								  		<input type="email" name="email" id="email" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-type="email" data-parsley-trigger="keyup" value="<?php echo $email ?>"/>
								  		<label for="email" class="active required">Email Address</label>
									</div>
							  	</div>
							</div>
							
							<div class="p-b-md p-t-md">
						  		<h6>Company Details</h6>
						  		<div class="help-text">Manage your account personal information.</div>
							</div>
							
							<div class="row">
								<div class="col s6">
									<div class="input-field">
								  		<?php echo $org_name; ?>
								  		<label for="organization" class="active">Department/Agency</label>
									</div>
							  	</div>
							  	<div class="col s6" id="job_title_wrapper">
									<div class="input-field">
								  		<input type="text" name="job_title" id="job_title" value="<?php echo $job_title ?>"/>
								  		<label for="job_title" class="active">Job Title</label>
									</div>
							  	</div>
							</div>
						</div>
					</div>
				</div>
				<?php if($this->permission->check_permission(MODULE_PROFILE, ACTION_SAVE)){ ?>
				<div class="panel-footer right-align">
				    <div class="input-field inline m-n">
						<button class="btn waves-effect bg-success" type="button" id="submit_profile_account" value="Save" data-btn-action="<?php echo BTN_SAVING ?>"><?php echo BTN_SAVE ?></button>
				    </div>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="col l2 hide-on-med-and-down"></div>
	</div>
</form>