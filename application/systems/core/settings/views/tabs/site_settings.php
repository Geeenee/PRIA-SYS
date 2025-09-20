<?php 
  $salt = gen_salt();
  $token = in_salt($this->session->userdata('user_id'), $salt);

  $maintenance_mode 			= get_setting(GENERAL, "maintenance_mode");

  $checked_maintenance_mode 	= ( !EMPTY( $maintenance_mode ) ) ? 'checked' : '';

  $show_title_on_login 			= get_setting(GENERAL, "show_title_on_login");

  $checked_show_title_on_login 	= ( !EMPTY( $show_title_on_login ) ) ? 'checked' : '';

  $show_tagline_on_login 		= get_setting(GENERAL, "show_tagline_on_login");

  $checked_show_tagline_on_login = ( !EMPTY( $show_tagline_on_login ) ) ? 'checked' : '';

  $has_agreement_text 				= get_setting( AGREEMENT, 'has_agreement_text' );

  $checked_has_agreement_text 		= ( !EMPTY( $has_agreement_text ) ) ? 'checked' : '';

  $agreement_text_value 			= get_setting( AGREEMENT, 'agremment_text' );

  $agreement_text_value 			= ( !EMPTY( $agreement_text_value ) ) ? html_entity_decode( $agreement_text_value ) : '';

  $agreement_uploads 				= get_setting( AGREEMENT, 'agreement_uploads' );
?>

<div class="row m-md">
  <div class="col l10 m12 s12 p-n">
	<form id="site_settings_form">
	  <input type="hidden" name="id" value="<?php echo $this->session->userdata('user_id') ?>"/>
	  <input type="hidden" name="salt" value="<?php echo $salt ?>">
	  <input type="hidden" name="token" value="<?php echo $token ?>">
	  
	  <input type="hidden" name="system_logo" id="system_logo" value="<?php echo get_setting(GENERAL, "system_logo") ?>"/>
	  <input type="hidden" name="system_favicon" id="system_favicon" value="<?php echo get_setting(GENERAL, "system_favicon") ?>"/>
	  <input type="hidden" name="agreement_uploads" id="terms_conditions" value="<?php echo $agreement_uploads ?>"/>
	  
	  <div class="form-basic">
		<div id="site-info" class="scrollspy table-display white box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Site Information</label>
			<p class="caption m-t-sm white-text">Control how your site is displayed, such as the title, tagline, description, and system email address.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<div class="row">
			  <div class="col s12">
				<div class="p-b-md">
					<div class="row m-n p-n">
						<div class="col s9">
						  <div class="input-field">
							<input id="system_title" name="system_title" type="text" class="validate" value="<?php echo get_setting(GENERAL, "system_title") ?>"/>
							<label for="system_title">Site Title</label>
							<div class="help-text">The site title is the name of your site or business. It generally appears in the title bar of a web browser, login page, or in the header located at the upper left corner of your site.</div>
						  </div>
					 	</div>
					 	<div class="col s3">
					 		<div class="input-field">
						 		<!-- <label for="system_title">Display on login page?</label> -->
							 	<input type="checkbox" class="labelauty" name="show_title_on_login" id="show_title_on_login" value="" data-labelauty="Display Off|Display On" <?php echo $checked_show_title_on_login ?> />
							 	<div class="help-text"></div>
						 	</div>
	  					</div>
					</div>
				</div>
				<div class="p-b-md">
					<div class="row m-n p-n">
					  <div class="col s9">
						  <div class="input-field">
							<input id="system_tagline" name="system_tagline" type="text" class="validate" value="<?php echo get_setting(GENERAL, "system_tagline") ?>"/>
							<label for="system_tagline">Tagline</label>
							<div class="help-text">The tag line is a secondary heading that displays near the site title or logo of your login page.</div>
						  </div>
						</div>
						<div class="col s3">
					 		<div class="input-field">
						 		<!-- <label for="system_title">Display on login page?</label> -->
							 	<input type="checkbox" class="labelauty" name="show_tagline_on_login" id="show_tagline_on_login" value="" data-labelauty="Display Off|Display On" <?php echo $checked_show_tagline_on_login ?> />
							 	<div class="help-text"></div>
						 	</div>
	  					</div>
					</div>
				</div>
				<div class="p-b-md">
				  <div class="input-field">
					<textarea id="system_description" name="system_description" class="materialize-textarea"><?php echo get_setting(GENERAL, "system_description") ?></textarea>
					<label for="system_description">Site Description</label>
					<div class="help-text">The site description is a short bio or information about your site.</div>
				  </div>
				</div>
				<div class="p-b-md">
				  <div class="input-field">
					<input id="system_email" name="system_email" type="email" class="validate" value="<?php echo get_setting(GENERAL, "system_email") ?>"/>
					<label for="system_email">System Email</label>
					<div class="help-text">The system email is the "from" and "reply-to" address in automated e-mails sent during registration, password requests, and other notifications.</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		
		<div id="maintenance-mode" class="scrollspy table-display white box-shadow m-t-lg">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Maintenance Mode</label>
			<p class="caption m-t-sm white-text">Make your site temporarily unavailable to public.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<div class="row m-b-n">
			  <div class="col s8">
				<div class="p-b-md">
				  <h6>Activate / Deactivate Maintenance Mode</h6>
				  <div class="help-text">Put the site into maintenance mode and only the system administrator will have access to it.</div>
				</div>
			  </div>
			  <div class="col s4 right-align">
				<input type="checkbox" class="labelauty" name="maintenance_mode" id="maintenance_mode" value="" data-labelauty="Disabled|Enabled" <?php echo $checked_maintenance_mode ?> />
			  </div>
			</div>
		  </div>
		</div>
		
		<div id="site-logo" class="scrollspy table-display white m-t-lg box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Logo &amp; Favicon</label>
			<p class="caption m-t-sm white-text">Set your logo and favicon to add branding to your site.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<div class="row">
			  <div class="col s12">
				<div class="p-b-md">
				  <h6>Site Logo</h6>
				  <div class="help-text">It is recommended that you use a logo with a transparent background <small>(.png file extension)</small>. This logo will appear above the menu on the left sidebar.</div>
				  
				  <div class="avatar-container lg" style="width:100%;">
					<div class="avatar-action">
					  <a href="#" id="system_logo_upload" class="tooltipped m-r-sm" data-position="bottom" data-delay="50" data-tooltip="Upload"><i class="material-icons">file_upload</i></a>
					</div>
					<img id="system_logo_src" src="<?php echo base_url() . PATH_SETTINGS_UPLOADS . get_setting(GENERAL, "system_logo") ?>" class="m-b-md">
				  </div>
				</div>
				<div class="p-b-md">
				  <h6>Favicon</h6>
				  <div class="help-text">Image file type must be .ico. This icon will appear on your web browser's tab.</div>
				  
				  <div class="avatar-container md p-t-lg p-b-lg" style="width:100%;">
					<div class="avatar-action">
					  <a href="#" id="system_favicon_upload" class="tooltipped m-r-sm" data-position="bottom" data-delay="50" data-tooltip="Upload"><i class="material-icons">file_upload</i></a>
					</div>
					<div class="truncate" style="width:150px; margin:0 auto; background:#fff; padding:10px; border-radius:10px 10px 0 0; box-shadow: 0 -3px 4px 0 #e3e3e3;">
					  <img id="system_favicon_src" src="<?php echo base_url() . PATH_SETTINGS_UPLOADS . get_setting(GENERAL, "system_favicon") ?>">
					  <span id="favico_preview_title" class="m-l-xs font-thin">Asiagate Networks, Inc.</span>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>

		<div id="site-terms" class="scrollspy table-display white m-t-lg box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Site Terms and Conditions</label>
			<p class="caption m-t-sm white-text">Control if the app has Terms and Conditions of use</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
		  	<div class="row m-b-n">
			  <div class="col s8">
				<div class="p-b-md">
				  <h6>Activate / Deactivate Terms and Conditions</h6>
				  <div class="help-text">Authenticated User must agree to terms and conditions before users can proceed to the system.</div>
				</div>
			  </div>
			  <div class="col s4 right-align">
				<input type="checkbox" class="labelauty" name="has_agreement_text" id="has_agreement_text" value="" data-labelauty="Disabled|Enabled" <?php echo $checked_has_agreement_text ?>  onclick="toggle('has_agreement_text', 'has_agreement_text_value')"/>
			  </div>
			</div>
			<div id="has_agreement_text_value" style="display:none">
				<div class="row p-md p-b-n m-b-n">
				  <div class="col s12">
					<div class="p-b-md">
					  <div class="input-field">
					  	<div class="help-text">What are the Terms and conditions</div>
					  	<!-- data-parsley-required="true" data-parsley-maxlength="60000" data-parsley-trigger="keyup" -->
					  	<textarea style="min-height : 200px !important;"  id="agremment_text" class="editor materialize-textarea" name="agremment_text"><?php echo $agreement_text_value ?></textarea>
						<label for="system_title">Terms and Conditions</label>
					  </div>
					</div>
				  </div>
				</div>
				<div class="row p-md p-b-n m-b-n field-multi-attachment">
				 	<div class="col s12">
						<div class="p-b-md">
					  		<div class="input-field">
					  			<div class="help-text">Upload the terms and conditions file.</div>
					  			<div id="terms_conditions_upload">Select File</div>
					  			<label for="system_title">Terms and Conditions Uploads</label>
					  			
					  		</div>
					  	</div>
					</div>
				</div>
			</div>
		  </div>
		</div>
		<div id="site-layout" class="scrollspy table-display white m-t-lg box-shadow">
		  <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			<label class="label mute">Layout</label>
			<p class="caption m-t-sm white-text">Manage how your header and sidebar menu are displayed.</p>
		  </div>
		  <div class="table-cell p-lg valign-top">
			<h5>Header</h5>
			<div class="row">
			  <div class="col l6 m6 s12">
				<input type="radio" class="labelauty" name="header" id="header_default" value="default" data-labelauty="Normal"/>
				<ul class="list square">
				  <li>Default theme color <strong>(Logo section)</strong></li>
				  <li>White header</li>
				</ul>
			  </div>
			  <div class="col l6 m6 s12">
				<input type="radio" class="labelauty" name="header" id="header_inverse" value="inverse" data-labelauty="Inverted"/>
				<ul class="list square">
				  <li>Default theme color <strong>(Logo section and header)</strong></li>
				</ul>
			  </div>
			</div>
			<h5 class="m-t-lg">Navigation Position</h5>
			<div class="row">
			  <div class="col l6 m6 s12">
				<input type="radio" class="labelauty" name="menu_position" id="menu_<?php echo MENU_TOP_NAV ?>" value="<?php echo MENU_TOP_NAV ?>" data-labelauty="Top Navigation"/>
				<ul class="list square">
				  <li>Located at the top of the header</li>
				</ul>
			  </div>
			  <div class="col l6 m6 s12">
				<input type="radio" class="labelauty" name="menu_position" id="menu_<?php echo MENU_SIDE_NAV ?>" value="<?php echo MENU_SIDE_NAV ?>" data-labelauty="Side Navigation"/>
				<ul class="list square">
				  <li>Located at the left side of the content</li>
				</ul>
			  </div>
			</div>
			
			<div id="side_nav_elem">
				<h5 class="m-t-lg">Sidebar Menu</h5>
				<div class="row">
				  <div class="col l6 m6 s12">
					<input type="radio" class="labelauty" name="sidebar_menu" id="layout_collapsed" value="<?php echo SIDEBAR_MENU_CLASS ?>" data-labelauty="Collapsed"/>
					<ul class="list square">
					  <li>Collapsed menubar</li>
					  <li>Full-height menubar</li>
					  <li>Fixed header</li>
					</ul>
				  </div>
				  <div class="col l6 m6 s12">
					<input type="radio" class="labelauty" name="sidebar_menu" id="layout_expanded" value="" data-labelauty="Expanded"/>
					<ul class="list square">
					  <li>Expanded menubar</li>
					  <li>Full-height menubar</li>
					  <li>Fixed header</li>
					</ul>
				  </div>
				</div>
			</div>

			<div id="top_nav_elem">
				<h5 class="m-t-lg">Navigation Type</h5>
				<div class="row">
				  <div class="col l6 m6 s12">
					<input type="radio" class="labelauty" name="menu_type" id="type_<?php echo MENU_CLASSIC ?>" value="<?php echo MENU_CLASSIC ?>" data-labelauty="Classic"/>
					<ul class="list square">
					  <li>Options is listed vertically</li>
					</ul>
				  </div>
				  <div class="col l6 m6 s12">
					<input type="radio" class="labelauty" name="menu_type" id="type_<?php echo MENU_MEGAMENU ?>" value="<?php echo MENU_MEGAMENU ?>" data-labelauty="Megamenu"/>
					<ul class="list square">
					  <li>Options is displayed horizontally in one large panel</li>
					</ul>
				  </div>
				</div>
				<div class="m-t-lg">
					  <h6>Navigation Links Display</h6>
					  <div class="help-text m-t-xs">Select how you want your links to be displayed in the menu.</div>
				</div>
				<ul class="list-group-btn m-t-sm">
				  <li><input type="radio" class="labelauty" name="menu_child_display" id="display_<?php echo DISPLAY_TITLE ?>" value="<?php echo DISPLAY_TITLE ?>" data-labelauty="Title"/></li><!--
				  --><li><input type="radio" class="labelauty" name="menu_child_display" id="display_<?php echo DISPLAY_TITLE_ICON ?>" value="<?php echo DISPLAY_TITLE_ICON ?>" data-labelauty="Title and Icon"/></li><!--
				  --><li><input type="radio" class="labelauty" name="menu_child_display" id="display_<?php echo DISPLAY_TITLE_DESC ?>" value="<?php echo DISPLAY_TITLE_DESC ?>" data-labelauty="Title and Description"/></li><!--
				  --><li><input type="radio" class="labelauty" name="menu_child_display" id="display_<?php echo DISPLAY_TITLE_ICON_DESC ?>" value="<?php echo DISPLAY_TITLE_ICON_DESC ?>" data-labelauty="Title, Icon, and Description"/></li>
				</ul>
			</div>
		  </div>
		</div>
		<div id="site-skin" class="scrollspy table-display white m-t-lg box-shadow">
		  <div>
		    <div class="table-cell bg-dark p-lg valign-top" style="width:25%">
			  <label class="label mute">Skins</label>
			  <p class="caption m-t-sm white-text">Customize your site's colour scheme to fit the styling and branding you desire.</p>
		    </div>
		    <div class="table-cell p-lg valign-top">
			  <div class="row" style="width:95%; margin:0 auto;">
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_default" value="default" data-labelauty="Default"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_red" value="red" data-labelauty="Red"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_green" value="green" data-labelauty="Green"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_orange" value="orange" data-labelauty="Orange"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_lime" value="lime" data-labelauty="Lime"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_violet" value="violet" data-labelauty="Violet"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_blue" value="blue" data-labelauty="Blue"/>
			    </div>
			    <div class="col l3 m4 s6">
				  <input type="radio" class="labelauty" name="skins" id="skin_pink" value="pink" data-labelauty="Pink"/>
			    </div>
			  </div>
		    </div>
		  </div>
		  <div class="panel-footer right-align">
		    <div class="input-field inline m-n">
		    	<?php 
		    		if( $permission ) :
		    	?>
			  <button class="btn waves-effect waves-light bg-success" type="button" id="save_site_settings" value="Save" data-btn-action="<?php echo BTN_SAVING ?>"><?php echo BTN_SAVE ?></button>
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
		<li><a href="#site-info">Site Information</a></li>
		<li><a href="#maintenance-mode">Maintenance Mode</a></li>
		<li><a href="#site-terms">Terms and Conditions</a></li>		
		<li><a href="#site-logo">Logo &amp; Favicon</a></li>
		<li><a href="#site-layout">Layout</a></li>
		<li><a href="#site-skin">Skins</a></li>
	  </ul>
	</div>
  </div>
</div>