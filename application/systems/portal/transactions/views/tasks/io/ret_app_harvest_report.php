<?php
	$date_submitted  		= ( ISSET($harvest_details['harvest_rep_submit_date']) ) ? std_datepicker_format($harvest_details['harvest_rep_submit_date']) : '';

?>
<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Date Submitted by DP</label>
		</div>

		<div class="table-cell l3 m6 s6 valign-middle">
            <div class="div-task-values"><?php echo $date_submitted ?></div>
		</div>
		<div class="table-cell l6 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
			<label >Harvest Report File</label>
		</div>

		<div class="table-cell l9 m6 s6 valign-middle blue lighten-5">
			<?php echo $task_documents[DOC_TYPE_HARVEST]; ?>
		</div>
	</div>
</div>