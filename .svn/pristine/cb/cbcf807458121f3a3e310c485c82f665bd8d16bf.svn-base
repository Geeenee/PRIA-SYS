<?php

/* If this is copy paste please read
	find get_menu($curr_system) change $curr_system to the template's desired system_code
	e.g get_menu(GMMS)
*/
$sys_logo 		 = get_setting(GENERAL, "system_logo");
$system_logo_src = base_url() . PATH_IMAGES . "logo_white.png";

$favicon 		 	= get_setting(GENERAL, "system_favicon");
$system_favicon_src = base_url() . PATH_IMAGES . "favicon.ico";

$avatar_src 	 	= base_url() . PATH_IMAGES . "avatar.jpg";

$org_pic 			= get_system_logo($this->session->current_system);

$change_upload_path	= get_setting(MEDIA_SETTINGS, "change_upload_path");

if( !EMPTY( $sys_logo ) )
{
	$sys_logo_path 		= $ROOT_PATH. PATH_SETTINGS_UPLOADS . $sys_logo;
	$sys_logo_path 		= str_replace(array('\\','/'), array(DS,DS), $sys_logo_path);

	if( file_exists( $sys_logo_path ) )
	{
		$system_logo_src = base_url() . PATH_SETTINGS_UPLOADS . $sys_logo;

		if( !EMPTY( $change_upload_path ) )
		{	
			$system_logo_src = output_image($sys_logo, PATH_SETTINGS_UPLOADS);
		}
		
		$system_logo_src = @getimagesize($sys_logo_path) ? $system_logo_src : base_url() . PATH_IMAGES . "logo_white.png";
	}
}

/* GET SYSTEM FAVICON */
if( !EMPTY( $favicon ) )
{

	$sys_fav_path 		= $ROOT_PATH. PATH_SETTINGS_UPLOADS . $favicon;
	$sys_fav_path 		= str_replace(array('\\','/'), array(DS,DS), $sys_fav_path);
	
	if( file_exists( $sys_fav_path ) )	
	{
		$system_favicon_src = base_url() . PATH_SETTINGS_UPLOADS . $favicon;

		if( !EMPTY( $change_upload_path ) )
		{	
			$system_favicon_src = output_image($favicon, PATH_SETTINGS_UPLOADS);
		}

		$system_favicon_src = @getimagesize($sys_fav_path) ? $system_favicon_src : base_url() . PATH_IMAGES . "favicon.ico";		
		
	}
}



/* GET USER AVATAR */
$avatar_path 	= $ROOT_PATH . PATH_USER_UPLOADS . $this->session->userdata('photo');
$avatar_path 	= str_replace(array('\\','/'), array(DS,DS), $avatar_path);

$avatar_photo 	= $this->session->userdata('photo');

if( !is_dir( $avatar_path ) AND file_exists( $avatar_path ) )
{	
	$avatar_src = base_url() . PATH_USER_UPLOADS . $this->session->userdata('photo');

	if( !EMPTY( $change_upload_path ) )
	{	
		$avatar_src = output_image($this->session->userdata('photo'), PATH_USER_UPLOADS);
	}

	$avatar_src = @getimagesize($avatar_path) ? $avatar_src : base_url() . PATH_IMAGES . "avatar.jpg";	
}
else
{
	$avatar_photo = '';
}

$user_role_sess 	= $this->session->user_roles;
$user_roles 		= '';

if( !EMPTY( $user_role_sess ) )
{
	$user_roles = implode(",",$this->session->user_roles);
}

$sidebar_menu = get_setting(LAYOUT, "sidebar_menu");
$class_compact_header = !empty($sidebar_menu) ? "cd-compact-header" : "";

$auto_log_inactivity 				= get_setting( LOGIN, 'auto_log_inactivity' );
$log_in_dur 						= get_setting(LOGIN, "auto_log_inactivity_duration");

$sess_expiration_warning 			= get_setting( LOGIN, 'sess_expiration_warning' );

// GET MENU POSITION SETTING
$menu_position = get_setting(MENU_LAYOUT, "menu_position");

$body_color = ISSET($body_color) ? $body_color : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo get_setting(GENERAL, "system_title") ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?php echo $system_favicon_src; ?>" id="favico_logo" />
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>materialize.css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>base.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>stylev2.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>stylev3.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>pria.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>/skins/skin_<?php echo get_setting(THEME, "skins") ?>.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>parsley.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().PATH_CSS ?>material_icons.css">
  <link type="text/css" href="<?php echo base_url().PATH_CSS ?>jquery.jscrollpane.css" rel="stylesheet" media="all" />
	<link type="text/css" href="<?php echo base_url().PATH_CSS ?>component.css" rel="stylesheet" media="all" />
	<link type="text/css" href="<?php echo base_url().PATH_CSS ?>custom.css" rel="stylesheet" media="all" />
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>widgets.css">
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>style_responsive.css">
	<link type="text/css" rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>offline-theme-chrome.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>offline-language-english.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo base_url().PATH_CSS.CSS_LOBIBOX ?>.css" />
	
	<!-- ALWAYS ON TOP (nodejs) -->
	<script src="<?php echo base_url(). PATH_JS ?>socket.io/socket.io.min.js"></script>

	<!-- JQUERY 2.1.1+ IS REQUIRED BY MATERIALIZE TO FUNCTION -->
	<script src="<?php echo base_url().PATH_JS ?>jquery-3.1.0.min.js"></script>
	<script src="<?php echo base_url().PATH_JS ?>jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() . PATH_JS ?>offline.js" type="text/javascript"></script>
	<script >

		Offline.options = {
			checkOnLoad: false,
			reconnect: {
		    // How many seconds should we wait before rechecking.
		    initialDelay: 3
		  },
		  checks : {
		  	xhr : {
		  		url : "<?php echo base_url().PATH_IMAGES ?>" + "favicon.ico"
		  		// active : 'image'
		  	}
		  }
		}

		var request_check_offl 	= false;

		var run = function()
		{
			if( request_check_offl == true )
			{
				return;	
			}

	  		if( Offline.state === 'up' )
	  		{	request_check_offl = true;
		    	Offline.check();
		    	request_check_offl = false;
		    }
		}

		setInterval(run, 600000);

	</script>

</head>

<?php
	$clock_onclick = ( ! ISSET($hide_clock) ) ? 'display_time()' : '';
?>

<body class="skin_<?php echo get_setting(THEME, "skins") ?> <?php echo $body_color ?>" onload="<?php echo $clock_onclick; ?>">
	<input type="button" id="download_log_file" class="none" value="Download Log" />
	<div class="se-pre-con" style="display : none !important"></div>
	<input type="hidden" id="base_url" value="<?php echo base_url() ?>">

	<!-- (NODEJS) -->
	<input type="hidden" id="nodejs_server" value="<?php echo NODEJS_SERVER ?>"/>
	<input type="hidden" id="user_id" value="<?php echo $this->session->user_id ?>"/>
	<input type="hidden" id="org_code" value="<?php echo $this->session->org_code ?>"/>
	
	<input type="hidden" id="user_roles" value="<?php echo $user_roles ?>"/>
	<input type="hidden" id="notif_cnt_<?php echo $this->session->user_id ?>"/>
	<!-- (NODEJS) -->

	<input type="hidden" id="path_user_uploads" value="<?php echo PATH_USER_UPLOADS ?>" />
	<input type="hidden" id="path_images" value="<?php echo PATH_IMAGES ?>" />
	<input type="hidden" id="path_settings_upload" value="<?php echo PATH_SETTINGS_UPLOADS ?>">
	<input type="hidden" id="path_file_uploads" value="<?php echo PATH_FILE_UPLOADS ?>">

	<script src="<?php echo base_url().PATH_JS ?>script.js" type="text/javascript"></script>
	
	<header class="cd-main-header <?php echo $class_compact_header ?> <?php echo get_setting(LAYOUT, "header") ?> ">
		<a class="cd-logo" id="org-select">
			<?php 
				if( !EMPTY( $org_sys_owner ) AND $org_sys_owner == ENUM_YES ) :
			?>
			<img src="<?php echo $org_pic ?>" class="org_logo_img" id="app-logo"/>
			<?php 
				else :
			?>
			<img src="<?php echo $system_logo_src ?>" class="org_logo_img" id=""/>
			<?php 
				endif;
			?>
			<!-- <div class="input-field p-n m-n">
				<select id="org-selector" name="org-selector" disabled onchange="">
					<?php //echo get_organization_options(); ?>
				</select>
			</div> -->
		</a>
		<!-- <a href="#0" class="cd-logo"><img src="<?php //echo $system_logo_src ?>" alt="Logo"></a> -->
		
		<!-- div class="cd-search is-hidden">
			<form action="#0">
				<input type="search" placeholder="Search...">
			</form>
		</div--> <!-- cd-search -->
		
		<?php if($menu_position == MENU_TOP_NAV): ?>
		<div class="menu">
		<?php
			$active_sub_menu = ! empty($active_sub_menu) ? $active_sub_menu : '';
			get_system_modules_menu(PORTAL, $active_sub_menu);
		?>
		</div>
		<?php endif; ?>
		
		<a href="#0" class="cd-nav-trigger">Menu<span></span></a>
		<a href="javascript:;" class="none" id="generated_file" name="generated_file" data-target="modal_generated_file">
			<div class="icons" ><i class="material-icons">create</i></div>
		</a>

		<nav class="cd-nav">
			<ul class="cd-top-nav">
				<?php if(ISSET($quick_add_permissions) AND COUNT($quick_add_permissions) > 0): ?>
				<li class="has-children quick-add">
					<a href="javascript:;" class="quick-add-panel tooltipped" data-tooltip="Quick Add" data-position="bottom"><i class="material-icons" style="pointer-events: none;">add_circle</i></a>
					<ul>
						<li>
							<div class="list-quick-add">
								<h5>Quick Add</h5>
								<ul>
									<?php if($per_io_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_IO; ?>', 'Import File')">
											<div class="icons" ><i class="material-icons">create</i></div>
											<div class="center-align">Internal Order (IO) List</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_pr_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_PR; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">assignment</i></div>
											<div class="center-align">Purchase Request (PR) List</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_po_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_PO; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">shopping_cart</i></div>
											<div class="center-align">Purchase Order (PO) List</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_po_batch): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_PO_BATCH; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">create</i></div>
											<div class="center-align">Purchase Order (PO) Batch Files</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_soa_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_SOA; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">shopping_cart</i></div>
											<div class="center-align">Statement of Account (SOA) List</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_soa_batch): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_SOA_BATCH; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">create</i></div>
											<div class="center-align">Statement of Account (SOA) Batch Files</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_apv_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_APV; ?>', 'Import File')">
											<div class="icons"><i class="material-icons">assignment</i></div>
											<div class="center-align">Account Payable Voucher (APV) List</div>
										</a>
									</li>
									<?php endif; ?>
									<?php if($per_gr_list): ?>
									<li class="p-t-n p-b-n">
										<a href="javascript:;" data-target="modal_quick_add" onclick="modal_quick_add_init('<?php echo PORTAL_TMP_QA_GR; ?>', 'Import File')">
										<div class="icons"><i class="material-icons">assignment</i></div>
										<div class="center-align">Good Receipt (GR) List</div>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</div>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				<li class="has-children apps">
					<a href="javascript:;" class="apps-panel tooltipped" data-tooltip="Apps" data-position="bottom"><i class="material-icons" style="pointer-events: none;">apps</i></a>
					<ul>
						<li>
							<div class="list-apps">
								<?php echo get_system_apps() ?>
							</div>
						</li>
					</ul>
				</li>
				<li class="has-children notif">
					<a href="javascript:;" class="notification-panel tooltipped" data-tooltip="Notifications" data-position="bottom"><i class="material-icons" style="pointer-events: none;">notifications</i><span style="<?php echo ( !ISSET( $unread_notif ) OR EMPTY($unread_notif)) ? 'display:none !important;' : ''; ?>" id="noti_red"></span></a>
					<ul>
						<li class="notification-title"><span id = "notif_cnt" class="new badge red" data-badge-caption="" style="<?php echo (!ISSET( $unread_notif ) OR EMPTY($unread_notif)) ? 'display:none;' : ''; ?>" >
							<?php if( ISSET( $unread_notif ) ) : ?>
								<?php echo $unread_notif; ?>
							<?php endif; ?>
							</span>New Notification
						</li>
						<li>
							<div class="scroll-pane scroll-dark" style="height:200px;">
								<ul class="collection collection-notif">
									<?php if( ISSET( $notif_list ) ) : ?>
										<?php echo $notif_list; ?>
									<?php endif; ?>
								</ul>
							</div>
						</li>
						<li class="">
							<a href="<?php echo base_url(). CORE_USER_MANAGEMENT . '/profile#tab_profile_notifications' ?>" target="_blank" >Show all Notifications</a>
						</li>
					</ul>
				</li>
				<li class="has-children account">
					<a href="javascript:;" class="tooltipped" data-tooltip="Account" data-position="bottom">
						<?php 
							if( !EMPTY( $avatar_photo ) ) :	
						?>	
						<img src="<?php echo $avatar_src ?>" class=""  alt="avatar" id="top_bar_avatar" style="pointer-events: none;">
						<?php 
							else :
						?>
						<img src="" class="profile_avatar" data-name="<?php echo $this->session->name ?>" alt="avatar" id="top_bar_avatar" style="pointer-events: none;">
						<?php 
							endif;
						?>
					</a>

					<ul>
						<?php //if($this->permission->check_permission(MODULE_PROFILE, ACTION_VIEW)){ ?>
							<li><a href="<?php echo base_url() . CORE_USER_MANAGEMENT ?>/profile#tab_profile_account">My Profile</a></li>
						<?php //} ?>
						<?php //if($this->permission->check_permission(MODULE_PERMISSION, ACTION_VIEW)){ ?>
							<!-- <li><a href="<?php echo base_url().CORE_SETTINGS ?>/manage_settings#tab_site_settings">Settings</a></li> -->
						<?php //} ?>
						<li><a href="#modal_version_info">About</a></li>
						<li><a href="javascript:;" id="logout">Log Out</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</header> <!-- .cd-main-header -->

	<main class="cd-main-content">
		<?php if($menu_position == MENU_SIDE_NAV): ?>
			<nav class="cd-side-nav <?php echo get_setting(LAYOUT, "sidebar_menu") ?>">
				<div class="cd-side-app">
					<!-- <div class="agency-logo">
					  <img id="org_logo_img" style="cursor: pointer" src="<?php echo $org_pic ?>" />
					  <input type="file" class="hide" id="org_logo_file_inp">
					</div> -->
					<?php get_systems(); ?>
				</div>	
				<?php get_menu(PORTAL); ?>
			</nav>
		<?php endif; ?>
		
		<?php 
			if( ISSET( $sub_nav_left ) ) :
				echo $sub_nav_left;
			endif;
		?>

		<?php $wrapper_class = ISSET($page_title) ? "has-header" : ""; ?>
		<div id="content-wrapper" class="<?php echo $wrapper_class ?>">
		<?php $referer = (ISSET($page_referer) && ($page_referer)) ?  $page_referer : ''; ?>
		
		<?php if(ISSET($page_title)): ?>
		<div id="content-header">
			<div class="row m-b-n">
				<div class="col l8 m8 s12 valign-middle">
					<h5>
						<?php if(!EMPTY($referer)): ?>
							<div class="back-nav"><a href="<?php echo $referer; ?>"><i class="material-icons">keyboard_arrow_left</i></a></div>
						<?php endif; ?>
						<span><?php echo $page_title ?></span>
					</h5>
				</div>
				<div class="col l4 m4 s12" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onmouseover="this.style.overflow='visible';" onmouseout="this.style.overflow='hidden';">
					<?php if( ! ISSET($hide_clock) )
						  { 
					?>
							<div class="welcome-label m-b-xs" style="font-family:'Roboto'">Welcome <span class="red-text"><?php echo ucfirst($this->session->userdata('name')); ?></span>,</div>
							<span id="clock_display"></span>
					<?php } ?>
				</div>
			</div>
			<?php //get_breadcrumbs() ;?>
		</div>
		<?php endif; ?>
		<?php echo $contents ?>
		</div> <!-- .content-wrapper -->
		
		<?php 
			if( ISSET( $sub_nav_right ) ) :
				echo $sub_nav_right;
			endif;
		?>
	</main> <!-- .cd-main-content -->
	
	<!-- NOTIFICATION SECTION -->
	<div class="notify success none">
		<div class="success">
			<h4>
				<span>Success!</span>
			</h4>
			<p></p>
		</div>
	</div>
	<div class="notify error none">
		<div class="error">
			<h4>
				<span>Warning!</span>
			</h4>
			<p></p>
		</div>
	</div>

	<!-- CONFIRMATION SECTION -->
	<div id="confirm_modal" style="display:none">
		<form id="form_confirm_modal">
			<div class="confirmModal_content">
				<h4></h4>	
				
				<h6></h6>

				<p></p>

				<div class="form-basic white additonal"></div>

			</div>
			<div class="confirmModal_footer">
				<button type="button" data-confirmmodal-but="cancel"><?php echo BTN_CANCEL ?></button>
				<button type="button" value="<?php echo BTN_OK ?>" id="confirm_modal_btn" class="btn btn-success" data-confirmmodal-but="ok"><?php echo BTN_OK ?></button>
			</div>
		</form>
	</div>

	<div id="task_confirm_modal" style="display:none">
		<form id="task_form_confirm_modal">
			<div class="confirmModal_content">
				<h4></h4>	
				
				<h6></h6>

				<p></p>

				<div class="form-basic white additonal"></div>

			</div>
			<div class="confirmModal_footer">
				<button type="button" data-confirmmodal-but="cancel"><?php echo BTN_CANCEL ?></button>
				<button type="button" value="<?php echo BTN_OK ?>"  class="btn btn-success" id="btn-task-ok"><?php echo BTN_OK ?></button>
			</div>
		</form>
	</div>
	
	<div id="modal_profile" class="modal modal-materialize modal-fixed-footer md">
		<form id="form_modal_profile">
			<div class="modal-content scroll-pane scroll-dark" style="height:calc(100%-156px)">
				<div id="content"></div>
			</div>
			
			<div class="modal-footer right-align">
				<a href="javascript:;" class="btn-flat modal-action modal-close m-n m-r-xs"><?php echo BTN_CANCEL ?></a>
				<button type="submit" id="submit_modal_profile" class="modal-action waves-effect waves-light btn m-n" data-btn-action="<?php echo BTN_SAVING ?>"><?php echo BTN_SAVE ?></button>
			</div>
			
		</form>
	</div>

	<div id="modal_sess_expired_log_in" class="modal modal-materialize modal-fixed-footer modal-fixed-header sm" style="height:45% !important;max-height: 45% !important;">
		<div class="modal-header">
			Session Expired
			<a href="javascript:;" id="modal_warning_expired_log_in_close" class="modal-action modal-close">&times;</a>
		</div>
		<form id="form_modal_sess_expired_log_in">
			<div class="modal-content scroll-pane scroll-dark" style="height:calc(100%-156px)">
				<div id="content"></div>
			</div>
			<div class="modal-footer right-align">
				<button type="button" class="btn m-n waves-effect waves-light m-r-xs blue lighten-1"id="continue_btn" name="continue_btn" data-btn-action="Getting Started" >Continue &rarr;</button>
			</div>
		</form>
	</div>

	<div id="modal_warning_expired_log_in" class="modal modal-materialize modal-fixed-footer modal-fixed-header xs">
		<div class="modal-header">
			Session Expiration
			<a href="javascript:;" id="modal_warning_expired_log_in_close" class="modal-action modal-close">&times;</a>
		</div>
		<form id="form_modal_warning_expired_log_in">
			<div class="modal-content scroll-pane scroll-dark" style="height:calc(100%-156px)">
				<div id="content"></div>
			</div>
			<div class="modal-footer right-align">
				<a href="javascript:;" class="btn-flat modal-action m-n m-r-xs" id="sess_warning_logout">No, log me out</a>
				<button type="button" class="btn m-n waves-effect waves-light m-r-xs blue lighten-1 hide" id="logged_me_in" data-save="Logged me in" >Go to Login Page</button>
				<button type="button" class="btn m-n waves-effect waves-light m-r-xs blue lighten-1" id="stay_connected" data-save="Stay Connected" >Yes, keep me logged in</button>
			</div>
		</form>
	</div>
	
	<div id="modal_version_info" class="modal modal-materialize modal-fixed-footer xs">
		<a href="javascript:;" class="modal-action modal-close">&times;</a>
		<form id="form_modal_version_info">
			<div class="modal-content scroll-pane scroll-dark" style="height:calc(100%)">
				<div id="content"></div>
			</div>
		</form>
	</div>
	
	<!-- (nodejs) -->
	<!-- since $.get('nodejs/index.html') doesn't work, we need this dummy div - $('alerts_div').load('nodejs/index.html') works -->
	<div id="alerts_div" class="none"></div>
	<!-- (nodejs) -->

	<!-- PLATFORM SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>constants.js"></script>
	<!-- END PLATFORM SCRIPT -->

	<!-- PLATFORM SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>materialize.js"></script>
	<!-- END PLATFORM SCRIPT -->
	
	<!-- SIDEBAR MENU SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>jquery.menu-aim.js"></script>
	<script src="<?php echo base_url().PATH_JS ?>main.js"></script>
	<!-- END SIDEBAR MENU SCRIPT -->
	
	<!-- AUTHENTICATION SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>auth.js"></script>
	<!-- END AUTHENTICATION SCRIPT -->
	
	<!-- PARSLEY FORM VALIDATION SCRIPT -->
	<script src="<?php echo base_url() . PATH_JS ?>parsley_config.js" type="text/javascript"></script>
	<script src="<?php echo base_url().PATH_JS ?>parsley.min.js" type="text/javascript"></script>
	<!-- END PARSLEY FORM VALIDATION SCRIPT -->
  
	<!-- OWL CAROUSEL SCRIPT -->
	<link href="<?php echo base_url().PATH_CSS ?>owl.carousel.css" rel="stylesheet" />
	<link href="<?php echo base_url().PATH_CSS ?>owl.theme.css" rel="stylesheet" />
	<script src="<?php echo base_url().PATH_JS ?>owl.carousel.js"></script>
	<!-- END OWL CAROUSEL SCRIPT -->
	
	<!-- JSCROLLPANE SCRIPT -->
	<script type="text/javascript" src="<?php echo base_url().PATH_JS ?>jquery.mousewheel.js"></script>
	<script type="text/javascript" src="<?php echo base_url().PATH_JS ?>jquery.jscrollpane.js"></script>
	<!-- END JSCROLLPANE SCRIPT -->
	
	<!-- UPLOAD FILE -->
	<link href="<?php echo base_url() . PATH_CSS; ?>uploadfile.css" rel="stylesheet" type="text/css">
	<script src="<?php echo base_url() . PATH_JS ?>jquery.uploadfile.js" type="text/javascript"></script>
	<!-- END UPLOAD FILE -->

	<!-- POPMODAL SCRIPT -->
	<link href="<?php echo base_url().PATH_CSS.CSS_POP_MODAL ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo base_url().PATH_JS.JS_POP_MODAL ?>.js"></script>
	<!-- END POPMODAL SCRIPT -->

	<!-- MODAL SCRIPT -->
	<script type="text/javascript" src="<?php echo base_url().PATH_JS ?>classie.js"></script>
	<script type="text/javascript" src="<?php echo base_url().PATH_JS ?>modalEffects.js"></script>
	<!-- END MODAL SCRIPT -->

	<!-- SEARCH SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>jquery.lookingfor.min.js"></script>
	<!-- END SEARCH SCRIPT -->

	<!-- PAGE LOADER SCRIPT -->
	<script src="<?php echo base_url() . PATH_JS ?>jquery.isloading.js" type="text/javascript"></script>
	<!-- END PAGE LOADER SCRIPT -->

	<!-- BLOCK UI SCRIPT -->
	<script src="<?php echo base_url() . PATH_JS ?>jquery.blockUI.js" type="text/javascript"></script>
	<!-- END BLOCK UI SCRIPT -->

	<!-- SELECTIZE SCRIPT -->
	<script src="<?php echo base_url() . PATH_JS ?>selectize.js" type="text/javascript"></script>
	<!-- END SELECTIZE SCRIPT -->

	<!-- MEGAMENU SCRIPT -->
	<link href="<?php echo base_url() . PATH_CSS . CSS_MEGAMENU; ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo base_url().PATH_JS.JS_MEGAMENU ?>.js"></script>
	<!-- END MEGAMENU SCRIPT -->
	
	<!-- LOBIBOX SCRIPT -->
	<script src="<?php echo base_url() . PATH_JS.JS_LOBIBOX ?>.js" type="text/javascript"></script>
	<!-- END LOBIBOX SCRIPT -->

	<!-- LOBIBOX SCRIPT -->
	<!-- <script src="<?php echo base_url() . PATH_JS ?>push.min.js" type="text/javascript"></script> -->
	<!-- END LOBIBOX SCRIPT -->

	<!-- (NODEJS) USE FOR TIME AND DATE, EX: 5 SECONDS AGO, 2 DAYS AGO, ETC. -->
	<script type="text/javascript" src="<?php echo base_url() . PATH_JS ?>moment.js"></script>
	<!-- (NODEJS) -->

	<script src="<?php echo base_url().PATH_JS ?>common.js" type="text/javascript"></script>
	<script src="<?php echo base_url().PATH_JS ?>initializations.js" type="text/javascript"></script>

	<script src="<?php echo base_url() . PATH_JS ?>idle.min.js" type="text/javascript"></script>

	<script src="<?php echo base_url() . PATH_JS ?>initial.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() . PATH_JS ?>parsley_extend.js" type="text/javascript"></script>
	<script src="<?php echo base_url() . PATH_JS ?>yofinity.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() . PATH_JS ?>systems/infinite-scroll.pkdgd.min.js" type="text/javascript"></script>

	<script src="<?php echo base_url().PATH_JS ?>general.js" type="text/javascript"></script>
	<script>
		$(".ui-progressbar > .ui-widget-header").each(function() {
		  $(this)
		    .data("origWidth", $(this).width())
		    .width(0)
		    .animate({
		      width: $(this).data("origWidth") // or + "%" if fluid
		    }, 1200);
		});
	
		var ci_details 		= {
			ci_base_url 	: '<?php echo base_url() ?>',
			ci_user_id 		: '<?php echo $this->session->user_id ?>',
			ci_user_roles 	: '<?php echo $user_roles ?>',
			// ci_org_code 	: '<?php echo $this->session->org_code ?>',
			ci_nodejs_server: '<?php echo NODEJS_SERVER ?>',
			ci_sess_expiration : '<?php echo $this->config->item('sess_expiration') ?>',
		};
		
		var modal_warn_obj = $('#modal_warning_expired_log_in').modal({
			dismissible: false,
			opacity: .5, // Opacity of modal background
			in_duration: 300, // Transition in duration
			out_duration: 200, // Transition out duration
			ready: function() {
				$("#modal_warning_expired_log_in .modal-content #content").load($base_url+'Unauthorized/warning_expired_sess_modal/');
			}, // Callback for Modal open
			complete: function() { 
			
			} // Callback for Modal close
		});

		var awayCallback = function(){
			if( !$('#modal_warning_expired_log_in').hasClass('open') )
			{
				$.post($base_url + "auth/sign_out/" + $('#user_id').val(), function(result){
					if(result.flag == 1){
						
							window.location = $base_url + 'auth/index/inactivity';
						
					}
				},'json');
			}
		};
		
		var awayBackCallback = function(){
			// console.log(new Date().toTimeString() + ": back");
		};

		<?php 
			if( !EMPTY( $sess_expiration_warning ) ) :
		?>
		var onWarning 		= function()
		{
			if( !$('#modal_warning_expired_log_in').hasClass('open') )
			{
				modal_warn_obj.trigger('openModal');
			}
		}
		
		<?php 
			else :
		?>
		var onWarning 		= function()
		{
		}

		<?php 
			endif;
		?>

		<?php if( !EMPTY( $auto_log_inactivity ) AND !EMPTY( $log_in_dur ) ) : ?>

		var log_in_dur 	= '<?php echo $log_in_dur ?>';

		if( log_in_dur == 30 )
		{
			log_in_dur = parseInt( log_in_dur ) + 10;
		}
		else if( log_in_dur <= 29 ) 
		{
			log_in_dur = parseInt( log_in_dur ) + 30;
		}

		var idle = new Idle({
			onAway: awayCallback,
			onAwayBack: awayBackCallback,
			awayTimeout: parseInt( log_in_dur ) * 1000,
			onWarning : onWarning
		}).start();

		<?php endif; ?>



		$(function(){
			<!-- (nodejs) -->
			// $("#alerts_div").load("<?php echo base_url() ?>nodejs/index.html");
			<!-- (nodejs) -->

			parsley_listener_duplicate();
			
		});

	</script>
	<!-- <script src="<?php //echo base_url().PATH_JS ?>socket_notification.js" type="text/javascript"></script> -->
	<?php 
		if( ISSET( $resources ) )
		{
			$resources['initial'] 		= TRUE;
		
			$this->view('footer', $resources);
		}
	?>
	<div id="overlay-wrapper"></div>
	<!-- <script src="<?php //echo base_url().PATH_JS ?>socket_notification.js" type="text/javascript"></script>-->
	<!-- <script src="<?php //echo base_url().PATH_JS ?>template_common.js" type="text/javascript"></script> -->
</body>
</html>