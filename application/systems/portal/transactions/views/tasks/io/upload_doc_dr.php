<?php
	$dr_gr_id         		= ( ISSET($doc_dr_details['dr_gr_id']) ) ? $doc_dr_details['dr_gr_id'] : '';
	$doc_dr_number     		= ( ISSET($doc_dr_details['dr_num']) ) ? $doc_dr_details['dr_num'] : '';
	$doc_dr_date      		= ( ISSET($doc_dr_details['dr_date']) ) ? std_datepicker_format($doc_dr_details['dr_date']) : '';
	$actual_placement_date  = ( ISSET($doc_dr_details['actual_placement_date']) ) ? std_datepicker_format($doc_dr_details['actual_placement_date']) : '';
	$last_dr_flag     		= ( ISSET($doc_dr_details['last_dr_flag']) ) ? $doc_dr_details['last_dr_flag'] : '';

	IF(ISSET($doc_dr_file_details)){
		$version_id 			= (ISSET($doc_dr_file_details['dr_gr_id'])) ? base64_url_encode($doc_dr_file_details['dr_gr_id']) : '' ;
		$doc_dr_file  			= ( ISSET($doc_dr_file_details['file_name']) ) ? ($doc_dr_file_details['file_name']) : '';
		$sys_file_name  		= ( ISSET($doc_dr_file_details['sys_file_name']) ) ? ($doc_dr_file_details['sys_file_name']) : '';
		$created_date  			= ( ISSET($doc_dr_file_details['modified_date']) ) ? std_db_datetime_format($doc_dr_file_details['modified_date']) : std_db_datetime_format($doc_dr_file_details['created_date']);
		$file_ext 				= explode('.', $doc_dr_file_details['file_name']);

		$path = PATH_UPLOADED_FILES.$sys_file_name;
		$path = str_replace(array('\\','/'), array(DS,DS), $path);

	    if( file_exists( $path ) )
	        {
	            $file_size         = file_size_convert( filesize( $path ) );
	            $file_size_num     = filesize( $path );
	    }
	}
	
	$created_by 			= (ISSET($user_info['fname']) AND $user_info['lname']) ? $user_info['fname'].' '.$user_info['lname'] : '';
	//file size
	
?>
<input type="hidden" id="dr_gr_id" name="dr_gr_id" value="<?php echo $dr_gr_id; ?>" />
<input type="hidden" id="core_task_id" name="core_task_id" value="<?php echo $core_task_id; ?>" />
<input type="hidden" id="dependent_task" name="dependent_task" value="<?php echo $dependent_task; ?>" />
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col  l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">DOC DR Number</label>
		</div>

		<div class="col l3 m8 s12  ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$doc_dr_number</div>
EOS
            : 
			<<<EOS
				<input type="text" name="doc_dr_number" id="doc_dr_number" placeholder="Enter DOC DR Number" data-parsley-required="true" value="$doc_dr_number" />
EOS;
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col  l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">DOC DR Date</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$doc_dr_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker_start" name="doc_dr_date" id="doc_dr_date" placeholder="MM/DD/YYYY" data-parsley-required="true" data-max-date="0" value="$doc_dr_date"  />
EOS;
		?>
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col  l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">Actual Placement Date</label>
		</div>

		<div class="col l3 m8 s12  ">
		<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$actual_placement_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker_end" name="actual_placement_date" id="actual_placement_date" placeholder="Enter Actual Placement Date" data-parsley-required="true" data-max-date="0" value="$actual_placement_date"  />
EOS;
        ?>
		</div>
	</div>
</div>


<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col  l3 m4 s12  label-col p-r-md">
			<label class="<?php echo $class_label ?>">DOC DR File</label>
		</div>
		<div class="col l9 m8 s12  ">

		<?php 
			echo $task_documents[DOC_TYPE_DOC_DR];
        ?>    

		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Last DOC Delivery?</label>
		</div>

		<div class="col l9 m8 s12  ">
			
			<?php
			if($view AND !$open_last_dr){
            ?>
				<div>
                <div class="div-task-values"><?php echo ($last_dr_flag == ENUM_YES) ? 'Yes ': 'No'; ?></div>
            <?php
            }else{
            ?>
				<div class="input-field m-n">
				<input type="checkbox" class="filled-in<?php echo ($open_last_dr) ? ' auto_save_dr ': ''; ?>" name="doc_dr_deliveries" id="doc_dr_deliveries" value="<?php echo ENUM_YES ?>" <?php echo ($last_dr_flag == ENUM_YES) ? 'checked ': ''; ?>/>

				<label for="doc_dr_deliveries">Checking this box will signify completion of all DOC deliveries</label>
			<?php } ?>
			</div>
		</div>
	</div>
</div>