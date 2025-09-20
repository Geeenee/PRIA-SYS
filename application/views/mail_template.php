<?php
$pria_logo				= base_url() . PATH_IMAGES . "pria-logo-new.png";

$favicon 		 		= get_setting(GENERAL, "system_favicon");
$system_favicon_src		= base_url() . PATH_IMAGES . "favicon.ico";

list($width, $height) 	= getimagesize($pria_logo);
$logo_class				= ($width > $height) ? "landscape-logo": "portrait-logo";

$root_path 			= get_root_path();

/* GET SYSTEM FAVICON */
if( !EMPTY( $favicon ) )
{
	
	$sys_fav_path 		= $root_path. PATH_SETTINGS_UPLOADS . $favicon;
	$sys_fav_path 		= str_replace(array('\\','/'), array(DS,DS), $sys_fav_path);
	
	if( file_exists( $sys_fav_path ) )	
	{
		$system_favicon_src = output_image($favicon, PATH_SETTINGS_UPLOADS);
		$system_favicon_src = @getimagesize($sys_fav_path) ? $system_favicon_src : base_url() . PATH_IMAGES . "favicon.ico";		
		
	}
}

$maintenance_mode 				= get_setting(GENERAL, "maintenance_mode");

$show_title_on_login 			= get_setting(GENERAL, "show_title_on_login");
$show_tagline_on_login 			= get_setting(GENERAL, "show_tagline_on_login");

$sys_title 						= get_setting(GENERAL, "system_title");
$sys_tagline 					= get_setting(GENERAL, "system_tagline");

$title 							= "";
$tagline 						= "";

if( !EMPTY( $show_title_on_login ) )
{
	$title 						= $sys_title;
}
else
{
	$title 						= "ANI";
}

if( !EMPTY( $show_tagline_on_login ) )
{
	$tagline 					= $sys_tagline;
}
else
{
	$tagline 					= "PHP Core";
}

?>
<html>
<head>
  <title><?php echo get_setting(GENERAL, "system_title") ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="shortcut icon" href="<?php echo $system_favicon_src; ?>" id="favico_logo" />
  <link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>login.css">
  <link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>skins.css">
  <link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>component.css">
  <link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>materialize.css"  media="screen,projection"/>
  <link rel="stylesheet" href="<?php echo base_url().PATH_CSS ?>parsley.css" type="text/css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url().PATH_CSS ?>material_icons.css">
  <link type="text/css" href="<?php echo base_url().PATH_CSS ?>jquery.jscrollpane.css" rel="stylesheet" media="all" />

  <link type="text/css" rel="stylesheet" href="<?php echo base_url().PATH_CSS.CSS_LOBIBOX ?>.css" />
  <script src="<?php echo base_url().PATH_JS ?>less.min.js" type="text/javascript"></script>
  
  <!-- JQUERY 2.1.1+ IS REQUIRED BY MATERIALIZE TO FUNCTION -->
  <script src="<?php echo base_url().PATH_JS ?>jquery-3.1.0.min.js"></script>
  <script src="<?php echo base_url().PATH_JS ?>jquery-ui.min.js" type="text/javascript"></script>
  <style>
    
  	.panel-section
  	{
		    margin:auto;
        background: #FFF;
        border-radius:3px;
        width:90%;
	  }

  	.btn{
  		box-shadow: none;
  		height:45px;
  		line-height:45px;
  		font-size:11px;
  	} 

  	#wrapper{
  		align-items:initial !important;
  	}

  	.blockMsg{
  		background: 0 0!important;
      	border: none!important;
  	}

    @media only screen and (min-width: 993px) {
      .panel-section {
        flex: 2;   
        margin: auto 20%;
      }
    }
  </style>
</head>
<body class="default">
  <input type="hidden" id="base_url" value="<?php echo base_url() ?>">

  
  <script src="<?php echo base_url().PATH_JS ?>script.js" type="text/javascript"></script>
	

	
	<div id="wrapper">
		<div class="panel">
			<div class="panel-section mail">
				<form id="mail_action_form" name="mail_action_form">
            <div class="row">
                <div class="col s12 form-basic">
                    <center><img src="<?php echo $pria_logo ?>" class="center logo <?php echo $logo_class ?>"/></center>
                    <?php echo $view_page; ?>
                </div>    
            </div> 
				</form>
			</div>
		</div>
	</div>


  
	<!-- CONFIRMATION SECTION -->
	<div id="confirm_modal" style="display:none">
		<form id="form_confirm_modal">
			<div class="confirmModal_content">
				<h4></h4>	
				
				<h6></h6>

				<p></p>

			</div>
			<div class="confirmModal_footer">
				<button type="button" data-confirmmodal-but="cancel"><?php echo BTN_CANCEL ?></button>
				<button type="button" value="<?php echo BTN_OK ?>" id="confirm_modal_btn" class="btn btn-success red" data-confirmmodal-but="ok"><?php echo BTN_OK ?></button>
			</div>
		</form>
	</div>	

  <!-- NOTIFICATION SECTION -->
  <div class="notify success none"><div class="success"><h4><span>Success</span></h4><p></p></div></div>
  <div class="notify error none"><div class="error"><h4><span>Error</span></h4><p></p></div></div>
  
  <!-- PLATFORM SCRIPT -->
	<script src="<?php echo base_url().PATH_JS ?>constants.js"></script>
	<!-- END PLATFORM SCRIPT -->

  <!-- PLATFORM SCRIPT -->
  <script src="<?php echo base_url().PATH_JS ?>materialize.js"></script>
  <!-- END PLATFORM SCRIPT -->

  <!-- LOBIBOX SCRIPT -->
  <script src="<?php echo base_url() . PATH_JS.JS_LOBIBOX ?>.js" type="text/javascript"></script>
  <!-- END LOBIBOX SCRIPT -->
  
  <script src="<?php echo base_url().PATH_JS ?>script.js"></script>
  <script src="<?php echo base_url().PATH_JS ?>common.js"></script>
  <script src="<?php echo base_url().PATH_JS ?>auth.js"></script>
 
 	<!-- PARSLEY FORM VALIDATION SCRIPT -->
  <script src="<?php echo base_url() . PATH_JS ?>parsley_config.js" type="text/javascript"></script>
	<script src="<?php echo base_url().PATH_JS ?>parsley.min.js" type="text/javascript"></script>
  <script src="<?php echo base_url() . PATH_JS ?>parsley_extend.js" type="text/javascript"></script>
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

  <!-- BLOCK UI SCRIPT -->
  <script src="<?php echo base_url() . PATH_JS ?>jquery.blockUI.js" type="text/javascript"></script>
  <!-- END BLOCK UI SCRIPT -->
  
  <!-- POPMODAL SCRIPT -->
  <link type="text/css" href="<?php echo base_url().PATH_CSS ?>popModal.css" rel="stylesheet" media="all" />
  <script type="text/javascript" src="<?php echo base_url().PATH_JS ?>popModal.min.js"></script>
  <!-- END POPMODAL SCRIPT -->
  
  <script src="<?php echo base_url().PATH_JS ?>initializations.js" type="text/javascript"></script>

  <script src="<?php echo base_url() . PATH_JS ?>initial.min.js" type="text/javascript"></script>
  <script src="<?php echo base_url() . PATH_JS ?>parsley_extend.js" type="text/javascript"></script>

  <script src="<?php echo base_url().PATH_JS ?>general.js" type="text/javascript"></script>
</body>
</html>