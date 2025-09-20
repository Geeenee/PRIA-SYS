<?php 
  $salt = gen_salt();
  $token = in_salt($this->session->userdata('user_id'), $salt);

  $change_password_initial_login 	= get_setting(LOGIN, "change_password_initial_login");
  $repeating_pass 					= get_setting(PASSWORD_CONSTRAINTS, "constraint_repeating_characters");

  $log_in_deactivation 				= get_setting( LOGIN, 'log_in_deactivation' );

  $auto_log_inactivity 				= get_setting( LOGIN, 'auto_log_inactivity' );

  $apply_username_constraints 		= get_setting( USERNAME, 'apply_username_constraints' );
  $constraint_pass_diff_username 	= get_setting( PASSWORD_CONSTRAINTS, 'constraint_pass_diff_username' );

  $username_case_sensitivity 		= get_setting( USERNAME, 'username_case_sensitivity' );

  $sess_expiration_warning 			= get_setting( LOGIN, 'sess_expiration_warning' );

  $checked_password 	= ( !EMPTY( $change_password_initial_login ) ) ? 'checked' : '';
  $checked_repeating 	= ( !EMPTY( $repeating_pass ) ) ? 'checked' : '';
  $checked_log_deac 	= ( !EMPTY( $log_in_deactivation ) ) ? 'checked' : '';
  $checked_auto_log_out = ( !EMPTY( $auto_log_inactivity ) ) ? 'checked' : '';
  $checked_user_cons 	= ( !EMPTY( $apply_username_constraints ) ) ? 'checked' : '';

  $checked_pass_diff 	= ( !EMPTY( $constraint_pass_diff_username ) ) ? 'checked' : '';
  $checked_user_case 	= ( !EMPTY( $username_case_sensitivity ) ) ? 'checked' : '';

  $system_name 			= get_setting(GENERAL, "system_title");


  $single_session 				= get_setting(LOGIN, "single_session");

  $checked_single_session 		= ( !EMPTY( $single_session ) ) ? 'checked' : '';

  $checked_sess_expiration_warning = ( !EMPTY( $sess_expiration_warning ) ) ? 'checked' : '';
?>

<div class="row">
  <div class="col l10 m12 s12">
	<form id="account_settings_form" class="m-t-lg">
	  <input type="hidden" name="id" value="<?php echo $this->session->userdata('user_id') ?>"/>
	  <input type="hidden" name="salt" value="<?php echo $salt ?>">
	  <input type="hidden" name="token" value="<?php echo $token ?>">
	  
	  <input type="hidden" name="system_logo" id="system_logo" value="<?php echo get_setting(GENERAL, "system_logo") ?>"/>
	  <input type="hidden" name="system_favicon" id="system_favicon" value="<?php echo get_setting(GENERAL, "system_favicon") ?>"/>
	  
	  <div class="form-basic">
		<div id="account" class="scrollspy table-display white box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Account</label>
			<p class="caption m-t-sm white-text">Control the methods of adding new users to <?php echo $system_name ?>.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<div class="row m-b-n">
			  <div class="col s12">
				<div>
				  <h6>Who can register accounts?</h6>
				  <div class="help-text">Allow the users to sign-up for an account with/without administrator’s approval or require only the administrator to manually create all the user accounts for the site.</div>
				</div>
				
				<div class="row">
				  <div class="col l4 m4 s12">
					<input type="radio" class="labelauty label-icon-side" checked name="account_creator" id="account_administrator" value="<?php echo ADMINISTRATOR ?>" data-labelauty="Only the administrator can register accounts"/>
				  </div>
				  <div class="col l4 m4 s12">
					<input type="radio" class="labelauty label-icon-side" name="account_creator" id="account_visitor" value="<?php echo VISITOR ?>" data-labelauty="Visitors can self-register with admin approval"/>
				  </div>
				  <div class="col l4 m4 s12">
					<input type="radio" class="labelauty label-icon-side" name="account_creator" id="account_visitor_not_approval" value="<?php echo VISITOR_NOT_APPROVAL ?>" data-labelauty="Visitors can self-register without admin approval"/>
				  </div>
				</div>				
			  </div>
			</div>
		  </div>
		</div>
		
		<div id="login_security" class="scrollspy table-display m-t-lg white box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Login</label>
			<p class="caption m-t-sm white-text">Protect your system from the usual login attacks.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<div class="row m-b-n">
			  <div class="col s9 p-l-n p-r-sm">
				<div class="p-b-md">
				  <h6>Login Attempts</h6>
				  <div class="help-text">The maximum number of login failures a user is allowed before blocking their account from the site.</div>
				</div>
			  </div>
			  <div class="col s3">
				<input type="text" name="login_attempts" id="login_attempts" value="<?php echo get_setting(LOGIN, "login_attempts") ?>" />
			  </div>
			</div>
				
			<div class="p-b-md">
			  <h6>Login Via</h6>
			  <div class="help-text">Users can use either their email address or their username when logging in.</div>
			  
			  <div class="row">
				<div class="col s6">
				  <input type="radio" class="labelauty" checked name="login_via" id="login_via_username" value="<?php echo VIA_USERNAME ?>" data-labelauty="Never login using username|Login via username"/>
				  <input type="radio" class="labelauty" checked name="login_via" id="login_via_email" value="<?php echo VIA_EMAIL ?>" data-labelauty="Never login using email address|Login via email address"/>
				  <input type="radio" class="labelauty" checked name="login_via" id="login_via_username_email" value="<?php echo VIA_USERNAME_EMAIL ?>" data-labelauty="Never login using username & email|Login via username & email address"/>
				</div>
			  </div>
			</div>

			<div class="p-b-md">
			  	<h6>Initial Login</h6>
				<div class="help-text">Force user to change password at initial login. (Note: applicable only when the administrator manually created the user’s account)</div>

				<div class="row">
					<div class="col s6">
				  		<input type="checkbox" class="labelauty" name="change_password_initial_login" id="change_initial_log_in" value="" data-labelauty="Change at initial login" <?php echo $checked_password ?> />
					</div>
				</div>

			</div>

			<div class="p-b-md">
			  <h6>Inactive Users</h6>
			  <div class="help-text">Automatically deactivate user accounts of those who didn’t log in for a specific period.</div>
			</div>

		  	<div class="row p-md p-t-sm p-b-n m-b-n bg-light-blue">
			    <div class="col s9">
			      <label class="label m-b-sm">User Account Expiration</label>
			      <div class="help-text">Allow system to automatically deactivate user account.</div>
			    </div>
			    <div class="col s3">
			      <input type="checkbox" <?php echo $checked_log_deac ?> class="labelauty" name="log_in_deactivation" id="log_in_deactivation" value="" data-labelauty="Disabled|Enabled" onclick="toggle('log_in_deactivation', 'log_in_deactivation_duration')"/>
			    </div>
		  	</div>

		  	<div id="log_in_deactivation_duration" style="display:none">
			    <div class="row p-md p-b-n m-b-n">
			      <div class="col s9">
			        <label class="label m-b-sm">Duration</label>
			        <div class="help-text">Length of time for which the system will automatically deactivate user accounts.</div>
			      </div>
			      <div class="col s2">
			        <input type="text" name="log_in_deactivation_duration" id="log_in_deactivation_duration_input" value="<?php echo get_setting(LOGIN, "log_in_deactivation_duration") ?>"/>
			      </div>
			      <div class="col s1 p-n p-t-md">
			        <span class="font-bold">days</span>
			      </div>
			    </div>
			</div>

		  </div>
		</div>

		<div id="session_handling_div" class="scrollspy table-display m-t-lg white box-shadow">
			<div class="table-cell bg-dark p-lg valign-top" style="width:25%">
				<label class="label mute">Session Handing</label>
				<p class="caption m-t-sm white-text">Set your preference for the user sessions.</p>
			</div>
		 	<div class="table-cell p-lg valign-top">
		 		<div class="p-b-md">
				  <h6>Single Session</h6>
				  <div class="help-text">Allow only one active session per user account.</div>
				</div>

				<div class="row p-md p-t-sm p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Enable Single Session</label>
				      <div class="help-text">
				      	Allow system to restrict log in of one user account to multiple devices. 
				      </div>
				    </div>
				    <div class="col s3">
				      <input type="checkbox" <?php echo $checked_single_session ?> class="labelauty" name="single_session" id="single_session" value="" data-labelauty="Disabled|Enabled" />
				    </div>
			  	</div>

			  	<div class="p-b-md m-t-lg p-t-sm">
				  <h6>Session Expiration</h6>
				  <div class="help-text">Automatically log out a user account due to inactivity.</div>
				</div>

				<div class="row p-md p-t-sm p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Expire User Session due to Inactivity</label>
				      <div class="help-text">Allow system to expire user session due to inactivity.</div>
				    </div>
				    <div class="col s3">
				      <input type="checkbox" <?php echo $checked_auto_log_out ?> class="labelauty" name="auto_log_inactivity" id="auto_log_inactivity" value="" data-labelauty="Disabled|Enabled" onclick="toggle('auto_log_inactivity', 'auto_log_inactivity_duration')"/>
				    </div>
			  	</div>

			  	<div id="auto_log_inactivity_duration" style="display:none">
				    <div class="row p-md p-b-n m-b-n">
				      <div class="col s9">
				        <label class="label m-b-sm">Duration</label>
				        <div class="help-text">Length of time for which the system will log out a user account due to inactivity.</div>
				      </div>
				      <div class="col s2">
				        <input type="text" name="auto_log_inactivity_duration" id="auto_log_inactivity_duration_input" value="<?php echo get_setting(LOGIN, "auto_log_inactivity_duration") ?>"/>
				      </div>
				      <div class="col s1 p-n p-t-md">
				        <span class="font-bold">seconds</span>
				      </div>
				    </div>

				    <div class="row p-md p-t-sm p-b-n m-b-n bg-light-blue">
					    <div class="col s9 p-l-lg p-r-lg">
					      <label class="label m-b-sm">Session Expiration Warning</label>
					      <div class="help-text">Allow system to warn user 30s before expiration and ask user to choose if he/she wants to stay logged in. If disabled, system will automatically log out idle user accounts.</div>
					    </div>
					    <div class="col s3">
					      <input type="checkbox" <?php echo $checked_sess_expiration_warning ?> class="labelauty" name="sess_expiration_warning" id="sess_expiration_warning" value="" data-labelauty="Disabled|Enabled"/>
					    </div>
				  	</div>
				</div>
			</div>
		</div>

		<div id="username_div" class="scrollspy m-t-lg table-display white box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Username</label>
			<p class="caption m-t-sm white-text">Set criteria for valid username.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">

			<div>
			  	<h6>Case Sensitivity of Username</h6>
				<div class="help-text">Set preferred case sensitivity of a username.</div>
			</div>
			<div class="row">
				<div class="col s12">
			  		<input type="checkbox" class="labelauty label-icon-side" name="username_case_sensitivity" id="username_case_sensitivity" value="" data-labelauty="Make username case sensitive" <?php echo $checked_user_case ?> />
				</div>
			</div>
				
			<div class="p-t-md">
				<h6>Username Constraints</h6>
				<div class="help-text">Set constraints for usernames.</div>	
			</div>
			<div class="row m-b-lg">
				<div class="col s12">
			  		<input type="checkbox" class="labelauty" name="apply_username_constraints" id="apply_username_constraints" value="" data-labelauty="Apply username constraints" <?php echo $checked_user_cons ?> onclick="toggle('apply_username_constraints', 'apply_username_constraints_div')" />
				</div>
			</div>

			<div id="apply_username_constraints_div" style="display:none">

			 <div class="row p-md p-b-n m-b-n bg-light-blue">
			    <div class="col s9">
			      <label class="label m-b-sm">Username Minimum Length</label>
			      <div class="help-text">Password must contain the specified minimum length.</div>
			    </div>
			    <div class="col s3">
			      <input id="constraint_username_min_length" name="constraint_username_min_length" type="text" class="validate bg-white" value="<?php echo get_setting(USERNAME_CONSTRAINTS, USERNAME_MIN_LENGTH) ?>"/>
			    </div>
			  </div>

		   	  <div class="row p-md p-b-n m-b-n">
			    <div class="col s9">
			      <label class="label m-b-sm">Username Maximum Length</label>
			      <div class="help-text">Password must contain the specified maximum length.</div>
			    </div>
			    <div class="col s3">
			      <input id="constraint_username_max_length" name="constraint_username_max_length" type="text" class="validate bg-white" value="<?php echo get_setting(USERNAME_CONSTRAINTS, USERNAME_MAX_LENGTH) ?>"/>
			    </div>
			  </div>

			   <div class="row p-md p-b-n m-b-n bg-light-blue">
			    <div class="col s9">
			      <label class="label m-b-sm">Digit</label>
			      <div class="help-text">Username must contain the specified minimum number of digits.</div>
			    </div>
			    <div class="col s3">
			      <input class="validate white" id="constraint_username_digit" data-parsley-validation-threshold="0" data-parsley-type="integer" data-parsley-trigger="keyup" name="constraint_username_digit" type="text" value="<?php echo get_setting(USERNAME_CONSTRAINTS, USERNAME_DIGIT) ?>"/>
			    </div>
			  </div>
			 </div>
		  </div>
		</div>
		
		<div id="password" class="scrollspy table-display m-t-lg white box-shadow">
		  <div>
		    <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			  <label class="label mute">Password</label>
			  <p class="caption m-t-sm white-text">Set password expiration and constraints for users’ passwords.</p>
		    </div>
		    
			<div class="table-cell p-lg valign-top">
			  <div class="row">
			    <div class="col s12">
			      
			      <div>
				    <h6>Who can set password for newly created user?</h6>
				    <div class="help-text">Allow the user to set his/her own password,automatically generate a random password, or let the administrator set password for the new user.</div>
				  </div>
				  <div class="row">
					<div class="col l4 m4 s12">
					  <input type="radio" class="labelauty label-icon-side" checked name="password_creator" id="set_system_generated" value="<?php echo SET_SYSTEM_GENERATED ?>" data-labelauty="System provides auto-generated password"/>
					</div>
					<div class="col l4 m4 s12">
					  <input type="radio" class="labelauty label-icon-side" name="password_creator" id="set_account_owner" value="<?php echo SET_ACCOUNT_OWNER ?>" data-labelauty="Allow user to set his/her own password"/>
					</div>
					<div class="col l4 m4 s12">
					  <input type="radio" class="labelauty label-icon-side" name="password_creator" id="set_administrator" value="<?php echo SET_ADMINISTRATOR ?>" data-labelauty="Allow administrator to manually set user password"/>
					</div>
				  </div>
					
				  <div class="p-t-md p-b-md">
				    <h6>Password Constraints</h6>
				    <div class="help-text">Enforce restrictions on user passwords by defining password policies.</div>
				  </div>
				
				  <div class="row p-md p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Uppercase</label>
				      <div class="help-text">Password must contain the specified minimum number of uppercase letters.</div>
				    </div>
				    <div class="col s3">
				      <input id="constraint_uppercase" name="constraint_uppercase" type="text" class="validate bg-white" value="<?php echo get_setting(PASSWORD_CONSTRAINTS, "constraint_uppercase") ?>"/>
				    </div>
				  </div>
				  <div class="row p-md p-b-n m-b-n">
				    <div class="col s9">
				      <label class="label m-b-sm">Digit</label>
				      <div class="help-text">Password must contain the specified minimum number of digits.</div>
				    </div>
				    <div class="col s3">
				      <input class="validate" id="constraint_digit" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-type="integer" data-parsley-trigger="keyup" name="constraint_digit" type="text" value="<?php echo get_setting(PASSWORD_CONSTRAINTS, "constraint_digit") ?>"/>
				    </div>
				  </div>
				  <div class="row p-md p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Length</label>
				      <div class="help-text">Password length must be equal to or longer than the specified minimum length.</div>
				    </div>
				    <div class="col s3">
				      <input id="constraint_length" name="constraint_length" type="text" class="validate bg-white" value="<?php echo get_setting(PASSWORD_CONSTRAINTS, "constraint_length") ?>"/>
				    </div>
				  </div>
				  <div class="row p-md p-b-n m-b-n">
				    <div class="col s9">
				      <label class="label m-b-sm">History</label>
				      <div class="help-text">Password must not match any of the user's previous <small class="font-bold text-underline">X</small> passwords.</div>
				    </div>
				    <div class="col s3">
				      <input id="constraint_history" name="constraint_history" type="text" class="validate" value="<?php echo get_setting(PASSWORD_CONSTRAINTS, "constraint_history") ?>"/>
				    </div>
				  </div>

				  <div class="row p-md p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Password and Username</label>
				      <div class="help-text">Password must be different from the username.</div>
				    </div>
				    <div class="col s3">
				      <input type="checkbox" class="labelauty label-icon-side" name="constraint_pass_diff_username" id="constraint_pass_diff_username" value="" data-labelauty="Disabled|Enabled" <?php echo $checked_pass_diff ?> />
				    </div>
				  </div>

				   <div class="row p-md p-b-n m-b-n">
				    <div class="col s9">
				      <label class="label m-b-sm">Repeatable Characters</label>
				      <div class="help-text">Password must not contain 3 repeated characters in chronological order.</div>
				    </div>
				    <div class="col s3">
				      <input type="checkbox" class="labelauty label-icon-side" name="constraint_repeating_characters" id="constraint_repeating_characters" value="" data-labelauty="Disabled|Enabled" <?php echo $checked_repeating ?> />
				    </div>
				  </div>
				
				  <div class="p-b-md m-t-lg p-t-sm">
				    <h6>Password Expiry Settings</h6>
				    <div class="help-text">Password expiration is disabled by default. It must be enabled to force password expiration periodically.</div>
				  </div>
				
				  <div class="row p-md p-b-n m-b-n bg-light-blue">
				    <div class="col s9">
				      <label class="label m-b-sm">Enable Password Expiration</label>
				      <div class="help-text">Allow passwords to expire after a specified time.</div>
				    </div>
				    <div class="col s3">
				      <input type="checkbox" class="labelauty" name="password_expiry" id="password_expiry" value="1" data-labelauty="Disabled|Enabled" onclick="toggle('password_expiry', 'password_expiry_duration')"/>
				    </div>
				  </div>
				
				  <div id="password_expiry_duration" style="display:none">
				    <div class="row p-md p-b-n m-b-n">
				      <div class="col s9">
				        <label class="label m-b-sm">Duration</label>
				        <div class="help-text">Length of time for which a password is valid.</div>
				      </div>
				      <div class="col s2">
 				        <input type="text" name="password_duration" id="password_duration" value="<?php echo get_setting(PASSWORD_EXPIRY, "password_duration") ?>"/>
				      </div>
				      <div class="col s1 p-n p-t-md">
				        <span class="font-bold">days</span>
				      </div>
				    </div>
				    <div class="row p-md p-b-n m-b-n bg-light-blue">
				      <div class="col s9">
				        <label class="label m-b-sm">Reminder</label>
				        <div class="help-text">Notifications will be sent out <small class="font-bold text-underline">X</small> days before the expiration of password. Leaving this field empty won't send any reminders to the user.</div>
				      </div>
				      <div class="col s2">
				        <input type="text" class="white" name="password_reminder" id="password_reminder" value="<?php echo get_setting(PASSWORD_EXPIRY, "password_reminder") ?>"/>
				      </div>
				      <div class="col s1 p-n p-t-md">
				        <span class="font-bold">days</span>
				      </div>
				    </div>
				  </div>
			    </div>
			  </div>
		    </div>
		  </div>
		
		  <div class="panel-footer right-align">
		    <div class="input-field inline m-n">
		    	<?php 
		    		if( $permission ) :
		    	?>
			  <button class="btn waves-effect waves-light bg-success" type="submit" id="save_account_settings" value="<?php echo BTN_SAVING ?>" data-btn-action="<?php echo BTN_SAVING; ?>"><?php echo BTN_SAVE ?></button>
			  <?php 
			  	endif;
			  ?>
		    </div>
		  </div>
		</div>
	  </div>
	</form>
  </div>
  
  <div class="col l2 hide-on-med-and-down">
	<div class="pinned m-t-lg">
	  <ul class="section table-of-contents">
		<li><a href="#account">Account</a></li>
		<li><a href="#login_security">Login Security</a></li>
		<li><a href="#session_handling_div">Session Handling</a></li>
		<li><a href="#password">Password</a></li>
	  </ul>
	</div>
  </div>
</div>

<script>
/*$(function(){
	$("#account_<?php //echo strtolower(get_setting(ACCOUNT, "account_creator")) ?>").prop("checked", true);
	$("#login_via_<?php //echo strtolower(get_setting(LOGIN, "login_via")) ?>").prop("checked", true);

	<?php //if(get_setting(PASSWORD_EXPIRY, "password_expiry") == 1){ ?>
		$("#password_expiry").prop("checked", true);
	<?php //} ?>
	
	toggle('password_expiry', 'password_expiry_duration');
	
	$('#account_settings_form').parsley();
	
	$('#account_settings_form').submit(function(e) {
        e.preventDefault();
        if ( $(this).parsley().isValid() ) {
		  var data = $(this).serialize();
	  
		  button_loader('save_account_settings', 1);
		  $.post("<?php //echo base_url() . PROJECT_CORE ?>/account_settings/process", data, function(result){
			Materialize.toast(result.msg, 3000, '', function(){
			  button_loader('save_account_settings', 0);
			  location.reload(); 
			});
		  }, 'json');       
        }
    });
});

function toggle(id, content_id){
  if($("#" + id).is(':checked')){
    $('#' + content_id).fadeIn('slow').show();
  } else {
    $('#' + content_id).fadeOut('slow').hide();
  }
}*/
</script>