<?php
	$fhr_document_num		= ( ISSET($fhr_details['fhr_document_num']) ) ? $fhr_details['fhr_document_num'] : '';
	$date_submitted  		= ( ISSET($fhr_details['fhr_submit_date']) ) ? std_datepicker_format($fhr_details['fhr_submit_date']) : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">FHR Document No.</label>
		</div>

		<div class="col  l3 m8 s12 ">
            <div class="div-task-values"><?php echo $fhr_document_num ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Date Submitted</label>
		</div>

		<div class="col  l3 m8 s12 ">
            <div class="div-task-values"><?php echo $date_submitted ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Flock History Report File</label>
		</div>
		<div class="col l9 m8 s12 ">
		<?php echo $task_documents[DOC_TYPE_FHR]; ?>
		</div>
	</div>
</div>