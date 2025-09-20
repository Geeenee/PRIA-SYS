<script type="text/javascript">
	var modal_height = {};
</script>

<?php if(ISSET($resources['load_modal']) and ! EMPTY($resources['load_modal'])):
	foreach($resources['load_modal'] as $modal_id => $options):
		$size = '';
		$title = '';
		$id = '';

		$controller = '';
		$method = '';
		$module = '';

		$multiple = '';
		$scroll = '';
		$height = '';

		$modal_cus_style = '';
		$ajax 			= false;

		if(is_numeric($modal_id))
		{
			$id = $options;
			$clean = str_replace(
				array('modal_', '_'), 
				array('',' '), 
				$options
				);

			$title = ucfirst(strtolower($clean));

			$controller = $clean;
			$method = strtolower($clean) . '_modal';
		}
		else
		{
			$id = $modal_id;
			$clean = str_replace(
				array('modal_', '_'), 
				array('', ' '), 
				$modal_id
				);

			if(! EMPTY($options) and is_array($options))
			{
				$size = (ISSET($options['size'])) ? $options['size'] : '';

				if(ISSET($options['title'])){ $title = $options['title']; }
				else{ $title = ucfirst(strtolower($clean)); }

				if(ISSET($options['controller'])){ $controller = $options['controller']; }
				else{ $controller = $clean; }

				if(ISSET($options['method'])){ $method = $options['method']; }
				else
				{
					if(ISSET($options['multiple'])){ $method = strtolower($clean) . '_modal'; }
					else{ $method = 'modal'; }
				}

				if(ISSET($options['module'])){ $module = $options['module']; }

				if(ISSET($options['actions_elem'])){ $actions_elem = $options['actions_elem']; }

				if(ISSET($options['actions_event'])){ $actions_event = $options['actions_event']; }

				if(ISSET($options['height'])){ $height = $options['height']; }

				if(ISSET($options['multiple'])){ $multiple = $options['multiple']; }

				if(ISSET($options['modal_style'])){ $modal_cus_style = $options['modal_style']; }

				if(ISSET($options['ajax'])){ $ajax = $options['ajax']; }

			}
		}
		?>

		<div id="<?php echo $id ?>" class="md-modal md-effect-<?php echo MODAL_EFFECT ?> " <?php echo $modal_cus_style ?> >
			<div class="md-content <?php echo $size ?>">
				<a class="md-close icon">&times;</a>
				<h3 class="md-header"><?php echo $title ?></h3>
				<div id="<?php echo $id ?>_content"></div>
			</div>
		</div>

		<script type="text/javascript">

			var modal_options = {}, modalObj, <?php echo $id ?>;

			modal_options.controller = '<?php echo $controller ?>';
			modal_options.modal_id   = '<?php echo $modal_id ?>';
			modal_options.method     = '<?php echo $method ?>';

			<?php if( !EMPTY( $ajax ) ): ?>
				modal_options.ajax 	 = '<?php echo $ajax ?>'
			<?php endif; ?>

			<?php if(! EMPTY($module)): ?>
				modal_options.module = '<?php echo $module ?>';
			<?php endif; ?>

			<?php if(! EMPTY($height)): ?>
				modal_options.height 	= '<?php echo $height ?>';
				modal_height['<?php echo $id ?>'] = '<?php echo $height ?>';
			<?php endif; ?>

			<?php if(! EMPTY($multiple)): ?>
				<?php echo $id ?> = new handleModal( modal_options );

				function <?php echo $id.'_init' ?>( data_id )
				{
					var data_id  = data_id || '',
					options  = {};

					options.id 	 	= data_id;

					<?php if( !EMPTY( $height ) ) : ?>
						<?php echo $id ?>.loadViewJscroll( options );
					<?php else : ?>
						<?php echo $id ?>.loadView( options );
					<?php endif; ?>
				}
			<?php else: ?>
				modalObj = new handleModal( modal_options );
			<?php endif; ?>

		</script>
	<?php endforeach; ?>
<?php endif; ?>

<div class="md-overlay"></div>

<!-- For Delete -->
<?php if( ISSET( $resources['load_delete'] ) AND !EMPTY( $resources['load_delete'] ) ): ?>
	<script>
		<?php foreach( $resources['load_delete'] as $key => $options ) :
			$delete_cntrl 		= '';
			$delete_method 		= 'delete';
			$delete_module 		= CORE_USER_MANAGEMENT;

			if( is_numeric( $key ) ) :
				$delete_cntrl 		= ISSET( $resources['load_delete'][0] ) ? $resources['load_delete'][0] : '';
				$delete_method 		= ISSET( $resources['load_delete'][1] ) ? $resources['load_delete'][1] : 'delete';
				$delete_module 		= ISSET( $resources['load_delete'][2] ) ? $resources['load_delete'][2] : CORE_USER_MANAGEMENT;

			if( $key == 0 ) :
			?>
				var options_delete = {};

				options_delete.controller 	= '<?php echo $delete_cntrl ?>';
				options_delete.method 		= '<?php echo $delete_method ?>';
				options_delete.module 		= '<?php echo $delete_module ?>';

				var deleteObj 	= new handleData( options_delete );
			<?php endif; ?>
			<?php else : ?>
				var options_delete = {},
				<?php echo $key.'Obj' ?>;

				<?php 
				if( ISSET( $options['delete_cntrl'] ) ) :
					$delete_cntrl 	= $options['delete_cntrl'];
				endif;
				if( ISSET( $options['delete_method'] ) ) :
					$delete_method 	= $options['delete_method'];
				endif;
				if( ISSET( $options['delete_module'] ) ) :
					$delete_module 	= $options['delete_module'];
				endif;
				?>

				options_delete.controller  = "<?php echo $delete_cntrl ?>";
				options_delete.method	   = "<?php echo $delete_method ?>";
				options_delete.module 	   = "<?php echo $delete_module ?>";

				<?php echo $key.'Obj' ?> 	= new handleData( options_delete );

				function <?php echo 'content_'.$key.'_delete' ?>( alert_text, param_1, param_2, obj, succ_callback, msg_detail, event )
				{
					if( event !== undefined )
					{
						event.stopImmediatePropagation();
						event.preventDefault();
					}
					
					var param_2 		= param_2 || "";
					var extra_data 		= {};
					var self 			= obj;
					var msg_obj 		= {
						'h4' : 'Are you sure you want to delete this ' + alert_text + '?',
						'p'  : 'This action will permanently delete this record from the database and cannot be undone.'
					}

					$('#confirm_modal').confirmModal({
						topOffset : 0,
						onOkBut : function() {
							
							if( obj !== undefined )
							{
								var extra 	  = $(obj).attr("data-delete_post");
								
								if( extra != null && extra != '' )
								{
									extra_data 	  = JSON.parse(extra);
								}
							}

							var post_data 		= { 
								param_1 : param_1, 
								param_2 : param_2 
							};

							post_data 			= $.extend( post_data, extra_data )

							<?php echo $key.'Obj' ?>.removeData(post_data, succ_callback, self);
						},
						onCancelBut : function() {},
						onLoad : function() {
							if( msg_detail !== undefined )
							{
								if( typeof( msg_detail ) === 'string' )
								{
									msg_detail = JSON.parse(msg_detail);
								}

								msg_obj 	= $.extend( msg_obj, msg_detail );
							}

							for( var key in msg_obj )
							{
								$('.confirmModal_content '+key).html( msg_obj[ key ] );
							}
						},
						onClose : function() {}
					});
				}
			<?php endif; ?>
		<?php endforeach; ?>
	</script>
<?php endif; ?>