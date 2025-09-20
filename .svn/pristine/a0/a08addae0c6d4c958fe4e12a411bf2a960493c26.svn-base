<?php
	$fhr_document_num       	= ( ISSET($fhr_details['fhr_document_num']) ) ? $fhr_details['fhr_document_num'] : '';
	$fhr_submit_date        	= ( ISSET($fhr_details['fhr_submit_date']) ) ? std_datepicker_format($fhr_details['fhr_submit_date']) : '';

	$audit_pre_placement    	= ( ISSET($fhr_details['pre_placement']) ) ? ($fhr_details['pre_placement']) : '';
	$audit_brooding_audit   	= ( ISSET($fhr_details['brooding_audit']) ) ? ($fhr_details['brooding_audit']) : '';
	$audit_biosecurity_audit  	= ( ISSET($fhr_details['biosecurity_audit']) ) ? ($fhr_details['biosecurity_audit']) : '';

	$flock_history_file 	= 'Sample.pdf';
	$file_ext 				= 'PDF';
	$created_by 			= (ISSET($user_info['fname']) AND $user_info['lname']) ? $user_info['fname'].' '.$user_info['lname'] : 'Juan Dela Cruz';
	$file_size 				= '851.13 kb';
?>
<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">FHR Document No.</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
            <div class="div-task-values"><?php echo $fhr_document_num ?></div>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
			<label class="<?php echo $class_label ?>">Date Submitted</label>
		</div>

		<div class="table-cell l3 m6 s6 valign-middle">
            <div class="div-task-values"><?php echo $fhr_submit_date ?></div>
		</div>
		<div class="table-cell l6 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Flock History Report File</label>
		</div>

		<div class="table-cell l9 m6 s6 valign-middle blue lighten-5">
			<?php if(EMPTY($flock_history_file)): ?>
				<div class="div-task-values m-sm p-n">
	            	<div class="row m-b-n p-b-n">
	            		<p class="red-text">No File Uploaded</p>
	            	</div>
				</div>
				<?php else: ?>
	            <div class="div-task-values m-sm p-n">
	            	<div class="row m-b-n p-b-n">
	            		
	            		<div class="col l1 red p-md">
							<span class="white-text"><b><?php echo strtoupper($file_ext) ?></b></span>
	            		</div>
	            		<div class="col l7 p-t-xs">
		            		<b><?php echo $flock_history_file ?></b>
		            		<span class="help-text font-sm">uploaded <?php echo $created_date; ?> by <?php echo $created_by.' '.$file_size ?></span>
	            		</div>
	            		<div class="col l2 b m-r-sm m-t-xs blue-grey-text">
	            			<i class="material-icons p-t-xs p-l-xs m-b-xs">search</i>
	            			<b>View</b>
	            		</div>
	            		<div class="col l1 b m-t-xs blue-grey-text">
	            			<i class="material-icons p-t-xs p-l-xs m-b-xs">cloud_download</i>
	            		</div>
	            	</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="input-field">
	<div class="row ">
		<div class="col l3 m5 s5 right-align valign-middle p-r-md">
			<label class="<?php echo $class_label ?>">Audit Scores</label>
			<div class="help-text font-sm m-t-xs"><i>(indicate N/A if not applicable)</i></div>
		</div>

		<div class="col l2 m3 s3 valign-middle">
			<input type="text" class="" readonly name="pre_placement" placeholder="Pre-placement" value="<?php echo $audit_pre_placement; ?>"/>
			<div class="help-text font-sm m-t-xs">Pre-placement</div>
		</div>
		
		<div class="col l2 m3 s3 valign-middle">
			<input type="text" class="" readonly name="brooding_audit" placeholder="Brooding Audit" value="<?php echo $audit_brooding_audit ?>"/>
			<div class="help-text font-sm m-t-xs">Brooding Audit</div>
		</div>

		<div class="col l2 m3 s3 valign-middle">
			<input type="text" class="" readonly name="biosecurity_audit" id="biosecurity_audit" placeholder="Biosecurity Audit" value="<?php echo $date_submitted; ?>"/>
			<div class="help-text font-sm m-t-xs">Biosecurity Audit</div>
		</div>
	</div>
</div>