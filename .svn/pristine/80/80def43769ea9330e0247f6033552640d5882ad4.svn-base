<?php 
	//current file info
	$doc_type 				= ISSET($current_doc_info['document_type_code']) ? $current_doc_info['document_type_code'] : '';
	$curr_file_name 		= ISSET($current_doc_info['file_name']) ? $current_doc_info['file_name'] : '';
	$curr_sys_file_name 	= ISSET($current_doc_info['sys_file_name']) ? $current_doc_info['sys_file_name'] : '';
	$curr_version 			= ISSET($current_doc_info['version']) ? $current_doc_info['version'] : '';
	$curr_created_date 		= (ISSET($current_doc_info['modified_date'])) ? std_db_datetime_format($current_doc_info['modified_date']) : std_db_datetime_format($current_doc_info['created_date']);
	$curr_created_by 		= (ISSET($current_user_info['fname']) AND $current_user_info['lname']) ? $current_user_info['fname'].' '.$current_user_info['lname'] : '';
	
	$path = PATH_UPLOADED_FILES.$curr_sys_file_name;
	$path = str_replace(array('\\','/'), array(DS,DS), $path);

    if( file_exists( $path ) )
        {
            $curr_file_size         = file_size_convert( filesize( $path ) );
            $curr_file_size_num     = filesize( $path );
    }else{
    	$curr_file_size 		= '0 KB';
		$curr_file_size_num 	= '0 KB';
    }

	$curr_file_ext 			= explode('.', $curr_file_name);

	$dl_path 		= base_url().'pria_file/download?file='.$curr_sys_file_name;
	$vw_path 		= base_url().'pria_file/view?file='.$curr_sys_file_name;
?>

<div class="m-t-sm m-lg">
	<div class="row right-align m-b-n">
		<div class="col s6"></div>
		<div class="col s6">
			<?php if($upload_per){ ?>
			<button type="button" id="upload" onclick="modal_upload_file_init('<?php echo $document_id ?>/<?php echo $doc_type; ?>','ADD FILE')" data-target="modal_upload_file" class="tooltipped btn" data-position="top" data-tooltip="New Version"><i class="material-icons valign-middle white-text">add</i>upload a new file</button>
			<?php } ?>
		</div>
	</div>
	<h4 class="p-sm flow-text">Current File</h4>
	<div class="file-wrapper <?php echo end($curr_file_ext) ?> teal lighten-5">
		<div class="type" data-file-type="<?php echo end($curr_file_ext) ?>"></div>
		<div class="contents valign-top">
			<div class="filename truncate"><?php echo $curr_file_name ?></div>
			<span class="red-text"><b><?php echo 'v'.$curr_version ?></b></span><br>
			uploaded <?php echo $curr_created_date; ?> by <?php echo $curr_created_by; ?> <?php echo $curr_file_size ?>
		</div>
		<div class="actions valign-middle right-align">
			<!-- <button type="button" id="view" onclick="modal_file_version_init()" data-target="modal_file_version"><i class="material-icons valign-middle">search</i> View</button> -->
			<a target="_blank" href="<?php echo $vw_path; ?>">
				<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="view"><i class="material-icons valign-middle">search</i></button>
			</a>

			<?php if($download_per){ ?>
			<a target="_blank" href="<?php echo $dl_path; ?>" download>
				<button type="button" id="download" class="tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons valign-middle">file_download</i> </button>
			</a>
			<?php } ?>

			<?php if($upload_per){ ?>
			<!-- <button type="button" id="upload" onclick="modal_upload_file_init('<?php //echo $document_id ?>/<?php //echo $doc_type; ?>','ADD FILE')" data-target="modal_upload_file" class="tooltipped" data-position="top" data-tooltip="New Version"><i class="material-icons valign-middle black-text">add</i></button> -->
			<?php } ?>
		</div>
	</div>

	<?php IF(!EMPTY($other_doc_info) ): ?>
	<h4 class="p-sm flow-text" >Other Versions</h4>
	<?php foreach ($other_doc_info as $other_doc): ?>

		<?php

			//other file info
			$other_file_name 		= ISSET($other_doc['file_name']) ? $other_doc['file_name'] : '';
			$other_sys_file_name 	= ISSET($other_doc['sys_file_name']) ? $other_doc['sys_file_name'] : '';
			$other_version 			= ISSET($other_doc['version']) ? $other_doc['version'] : '';
			$other_created_date 	= (ISSET($other_doc['modified_date'])) ? std_db_datetime_format($other_doc['modified_date']) : std_db_datetime_format($other_doc['created_date']);

			if($other_doc['created_by']){
				if(!EMPTY($other_doc['modified_by'])){
					$other_user_info = $this->users_model->get_user_details($other_doc['modified_by']);
				}else{
					$other_user_info = $this->users_model->get_user_details($other_doc['created_by']);
				}   
			}

			// $other_user_info 		= $this->users_model->get_user_details($other_doc['created_by']);

			$other_created_by 		= (ISSET($other_user_info['fname']) AND $other_user_info['lname']) ? $other_user_info['fname'].' '.$other_user_info['lname'] : '';

			$path = PATH_UPLOADED_FILES.$other_sys_file_name;
			$path = str_replace(array('\\','/'), array(DS,DS), $path);


			$dl_path 		= base_url().'pria_file/download?file='.$other_sys_file_name;
			$vw_path 		= base_url().'pria_file/view?file='.$other_sys_file_name;

		    if( file_exists( $path ) )
		        {
		            $other_file_size         = file_size_convert( filesize( $path ) );
		            $other_file_size_num     = filesize( $path );
		    }else{
		    		$other_file_size 		= '0 KB';
		    		$other_file_size_num 	= '0 KB';
		    }

			$other_file_ext			= explode('.', $other_file_name);

		?>

		<div class="file-wrapper <?php echo end($other_file_ext) ?> m-b-xs">
			<div class="type" data-file-type="<?php echo end($other_file_ext) ?>"></div>
			<div class="contents valign-top">
				<div class="filename truncate"><?php echo $other_file_name ?></div>
				<span class="red-text"><b>v<?php echo $other_version ?></b></span><br>
				uploaded <?php echo $other_created_date; ?> by <?php echo $other_created_by; ?> <?php echo $other_file_size ?>
			</div>

			<div class="actions valign-middle right-align">

				<a target="_blank" href="<?php echo $vw_path; ?>">
					<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="view"><i class="material-icons valign-middle">search</i></button>
				</a>

				<a target="_blank" href="<?php echo $dl_path; ?>" download>
					<button type="button" id="download" class="tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons valign-middle">file_download</i> </button>
				</a>

			</div>
		</div>

	<?php endforeach; ?>
	<?php ENDIF; ?>


</div>