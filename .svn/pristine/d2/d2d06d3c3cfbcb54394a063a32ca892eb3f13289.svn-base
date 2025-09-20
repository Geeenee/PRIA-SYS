<?php 
$salt = gen_salt();
$token	= in_salt(PROJECT_NAME, $salt);

?>
<div class="m-lg">
	<div class="right-align">
		<!-- <a href="javascript:;" class="modal-action modal-close modal-close-icon" onclick='$("#modal_sign_up").modal("close");'>&times;</a> -->
	</div>
	<div class="modal-title center-align m-t-n">CREATE NEW ACCOUNT</div>

	<input type="hidden" name="salt" value="<?php echo $salt ?>">
	<input type="hidden" name="token" value="<?php echo $token ?>">
	<input class="none" type="password" />
	
	<div class="m-b-sm m-t-lg">
		<div class="row">
			<div class="col s3">&nbsp;</div>
			<div class="col s2 right-align" style="padding:0;">
				<input type="radio" class="labelauty" name="gender" id="gender_male" value="<?php echo MALE ?>" checked />
			</div>
			<div class="col s2 center-align" style="padding-top:20px;"><small style="font-weight:600;">OR</small></div>
			<div class="col s2 left-align" style="padding:0 0 0 8px;">
				<input type="radio" class="labelauty" name="gender" id="gender_female" value="<?php echo FEMALE ?>"/>
			</div>
			<div class="col s3">&nbsp;</div>
		</div>
		<div class="row m-n">
			<div class="col s1">&nbsp;</div>
			<div class="col s4">
				<div class="input-field m-b-xs">
					<i class="material-icons prefix">people_outline</i>
					<input type="text" name="lname" id="lname" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" placeholder="Last Name" class="m-b-n"/>
				</div>
			</div>
			<div class="col s4">
				<div class="input-field m-b-xs">
					<input type="text" name="fname" id="fname" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" placeholder="First Name" class="m-b-n"/>
				</div>
			</div>
			<div class="col s2">
				<div class="input-field m-b-xs">
					<input type="text" name="mname" id="mname" placeholder="M.I." class="m-b-n"/>
				</div>
			</div>		  
			<div class="col s1">&nbsp;</div>
		</div>
		<div class="row m-n">
			<div class="col s1">&nbsp;</div>
			<div class="col s5">
				<div class="input-field m-b-xs">
					<i class="material-icons prefix">account_balance</i>
					<select name="vendor" id="vendor" class="material-select">
						<option value="" disabled selected>Select Vendor</option>
						<?php foreach ($vendors as $vendor): ?>
							<option value="<?php echo $vendor["vendor_code"]?>"><?php echo $vendor["vendor_name"]?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="col s5">
				<div class="input-field m-b-xs">
					<i class="material-icons prefix">work</i>
					<input type="text" name="job_title" id="job_title" placeholder="Position" class="m-b-n" />
				</div>
			</div>
			<div class="col s1">&nbsp;</div>
		</div>

		<div class="row m-n">
			<div class="col s1">&nbsp;</div>

			<div class="col s4">
				<div class="input-field m-b-xs">
					<i class="material-icons prefix">account_circle</i>
					<!-- <label class="active black-text" for="password"><b>Password</b</label> -->
					<input type="text" name="username" id="username" placeholder="User name" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup"/>
				</div>
			</div>

			<div class="col s3">
				<div class="input-field m-b-xs">
					<!-- <label class="active black-text" for="password"><b>Password</b</label> -->
					<input type="password" name="password" id="password" 
					id="password" 
				    data-parsley-required-message="<?php echo $pass_err;?>" 
				    data-parsley-required="true"
				    data-parsley-pass=""
				    data-parsley-trigger="change" 
				    placeholder="Password"/>
				</div>
			</div>

			<div class="col s3">
				<div class="input-field m-b-xs">
					<!-- <label class="active black-text" for="password"><b>Password</b</label> -->
					<input type="password" name="confirm_password" id="password" 
					data-parsley-required-message="<?php echo $pass_err;?>" 
				    data-parsley-required="true" 
				    data-parsley-pass=""
				    data-parsley-equalto="#password"
				    data-parsley-trigger="change"
				    placeholder="Confirm password"/>
				</div>
			</div>
			<div class="col s1">&nbsp;</div>
		</div>

		<div class="row m-n">
			<div class="col s1">&nbsp;</div>
			<div class="col s10">
				<div class="input-field m-b-xs">
					<i class="material-icons prefix">email</i>
					<input type="email" name="email" id="email" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-type="email" data-parsley-trigger="keyup" placeholder="Email Address" class="m-b-n" />
				</div>
				<div class="center-align m-t-lg">
					<button type="submit" class="waves-effect waves-light btn" id="create_account_btn" data-btn-action="<?php echo BTN_CREATING_ACCOUNT ?>"><?php echo BTN_CREATE_ACCOUNT ?></button>
					<a href="javascript:;" onclick='$("#modal_sign_up").modal("close");' class="modal-action modal-close m-t-md inline">&larr; Already have an account?</a>
				</div>	
			</div>	  
			<div class="col s1">&nbsp;</div>
		</div>
	</div>
</div>