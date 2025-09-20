<?php

	
	$date_submitted  		= ( ISSET($harvest_details['harvest_rep_submit_date']) ) ? std_datepicker_format($harvest_details['harvest_rep_submit_date']) : '';
	

	IF(ISSET($harvest_file_details)){

		$version_id 			= (ISSET($harvest_file_details['io_id'])) ? base64_url_encode($harvest_file_details['io_id']) : '';
		$harvest_report_file  	= ( ISSET($harvest_file_details['file_name']) ) ? ($harvest_file_details['file_name']) : '';
		$sys_file_name  		= ( ISSET($harvest_file_details['sys_file_name']) ) ? ($harvest_file_details['sys_file_name']) : '';
		$created_date  			= ( ISSET($harvest_file_details['modified_date']) ) ? std_db_datetime_format($harvest_file_details['modified_date']) : std_db_datetime_format($harvest_file_details['created_date']);
		$created_by 			= (ISSET($user_info['fname']) AND $user_info['lname']) ? $user_info['fname'].' '.$user_info['lname'] : '';
		$file_ext 				= explode('.', $harvest_file_details['file_name']);

		$path = PATH_UPLOADED_FILES.$sys_file_name;
		$path = str_replace(array('\\','/'), array(DS,DS), $path);

	    if( file_exists( $path ) )
	    {
	            $file_size         = file_size_convert( filesize( $path ) );
	            $file_size_num     = filesize( $path );
	    }
	}
?>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Date Submitted by DP</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$date_submitted</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="date_submitted" id="date_submitted" placeholder="Enter Date Submitted" data-parsley-required="true" value="$date_submitted" data-max-date="0" />
EOS;
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
			<label class="<?php echo $class_label ?>">Harvest Report File</label>
		</div>
		<div class="table-cell l9 m7 s7 valign-middle">

		<?php 
/*             if(EMPTY($harvest_form))
                echo <<<EOS
                    <div class="input-field">
                        <a href="#" id="document_upload" class="center m-r-sm">Attach</a>

                        <input type="hidden" name="document" id="document" value=""/>
                    </div>  
EOS;
            else
				echo $harvest_form; */
				
			echo $task_documents[DOC_TYPE_HARVEST];
        ?> 	

		</div>
	</div>
</div>