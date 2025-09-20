<?php 
$id	= (!EMPTY($user["user_id"]))? $user["user_id"] : "";
$salt = gen_salt();
$token	= in_salt($id, $salt);
?>
<form id="form_profile_pass">
	<div class="row m-md">
		<div class="col l10 m12 s12 p-n">
			<div class="white box-shadow">
				<div class="table-display">
					<div class="table-cell s3 bg-dark p-lg valign-top">
						<label class="label mute">Change Password</label>
						<p class="caption m-t-sm white-text">Change your password associated with your account.</p>
					</div>
					<div class="table-cell s9 p-lg valign-top">
						<input type="hidden" name="user_id" value="<?php echo $id ?>">
						<input type="hidden" name="salt" value="<?php echo $salt ?>">
						<input type="hidden" name="token" value="<?php echo $token ?>">
						
						<div class="form-basic">
							<div class="row">
						  		<div class="col s12">
									<div class="input-field">
										<input type="password" name="current_password" id="current_password" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
										<label for="current_password" class="active required">Current Password</label>
										<div class="help-text">Enter your current password to allow setting of new password.</div>
									</div>
						  		</div>
						  	</div>
						  	<div class="row">
						  		<div class="col s6">
									<div class="input-field">
										<input type="password" name="password" data-parsley-pass="true" disabled id="new_password" data-parsley-trigger="keyup" />
										<label for="password" class="active required">New Password</label>
									</div>
						  		</div>
						  		<div class="col s6">
									<div class="input-field">
										<input type="password" name="confirm_password" disabled id="confirm_password" data-parsley-equalto="#new_password" data-parsley-trigger="keyup" data-parsley-equalto-message="The passwords you entered do not match."  />
										<label for="confirm_password" class="active required">Confirm Password</label>
									</div>
						  		</div>
						  	</div>
						</div>
					</div>
				</div>
				<?php if($this->permission->check_permission(MODULE_PROFILE, ACTION_SAVE)){ ?>
				<div class="panel-footer right-align">
				    <div class="input-field inline m-n">
						<button class="btn waves-effect bg-success" type="button" id="submit_profile_pass" value="Save" data-btn-action="<?php echo BTN_SAVING ?>" ><?php echo BTN_SAVE ?></button>
				    </div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</form>