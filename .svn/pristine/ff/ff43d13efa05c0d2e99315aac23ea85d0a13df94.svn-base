<?php 
  $salt 	= gen_salt();
  $token 	= in_salt($this->session->userdata('user_id'), $salt);

  $change_upload_path	= get_setting(MEDIA_SETTINGS, "change_upload_path");

  $checked_upload_path 	= ( !EMPTY( $change_upload_path ) ) ? 'checked' : '';

  $placeholder_path 	= FCPATH.'uploads';
  $placeholder_path 	= str_replace(array('/', '\\'), array(DS, DS), $placeholder_path);
?>
<div class="row">
	<div class="col l10 m12 s12">
		<form id="media_settings_form" class="m-t-lg">
			<input type="hidden" name="id" value="<?php echo $this->session->userdata('user_id') ?>"/>
		  	<input type="hidden" name="salt" value="<?php echo $salt ?>">
		  	<input type="hidden" name="token" value="<?php echo $token ?>">

		  	<div class="form-basic">
				<div id="media" class="scrollspy table-display white box-shadow">
					<div class="table-cell bg-dark p-lg valign-top" style="width:25%">
						<label class="label mute">Upload path</label>
						<p class="caption m-t-sm white-text">Control where the uploaded media files will be stored.</p>
					</div>
					<div class="table-cell p-lg valign-top">
						<div class="row m-b-n">
							<h6>File Upload Path</h6>
							<div class="help-text">Change path where the uploaded files will be stored. The directory must be writeable by the system and not accessible over the web. (Note: System saves file in the "application_folder_name/uploads/“ by default)</div>

							<div class="row">
								<div class="col s6">
									<input type="checkbox" class="labelauty" name="change_upload_path" id="change_upload_path" value="" data-labelauty="Change Path" onclick="toggle('change_upload_path', 'custom_upload_path_div', Settings.custom_path_toggle)" <?php echo $checked_upload_path ?> />
								</div>
							</div>
	
							<div id="custom_upload_path_div" style="display:none">
								<div class="row p-md p-b-n m-b-n">
									<div class="col s9">
										 <label class="label m-b-sm">New Upload path</label>
										 <div class="help-text">Specify new path where the uploaded files will be stored. (e.g. C:\uploads)</div>
									</div>
								</div>
								<div class="row p-md p-t-n m-t-n p-b-n m-b-n">
									<div class="col s9">
										 <input type="text" data-parsley-trigger="keyup" placeholder="<?php echo $placeholder_path ?>" name="new_upload_path" id="new_upload_path" value="<?php echo get_setting(MEDIA_SETTINGS, "new_upload_path") ?>"/>
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
					  <button class="btn waves-effect waves-light bg-success" type="submit" id="save_media_settings" value="<?php echo BTN_SAVING ?>" data-btn-action="<?php echo BTN_SAVING; ?>"><?php echo BTN_SAVE ?></button>
					   <?php 
					  		endif;
					  	?>
				    </div>
			  	</div>
		  	</div>
		</form>
	</div>

	<div class="col l2 hide-on-med-and-down">
		<div class="pinned m-t-lg">
		  <ul class="section table-of-contents">
			<li><a href="#media">Media</a></li>
		  </ul>
		</div>
	  </div>
	</div>

</div>

