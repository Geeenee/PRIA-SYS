<?php

	
	$date_submitted  		= ( ISSET($live_sales_details['live_sales_submit_date']) ) ? std_datepicker_format($live_sales_details['live_sales_submit_date']) : '';
	
?>
<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Date Submitted by Sales</label>
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
				<input type="text" class="datepicker" name="date_submitted" id="date_submitted" placeholder="MM/DD/YYYY" data-parsley-required="true" value="$date_submitted" data-max-date="0" />
EOS;
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
			<label class="<?php echo $class_label ?>">Live Sale File</label>
		</div>
		<div class="table-cell l9 m7 s6 valign-middle">
			<?php echo $task_documents[DOC_TYPE_LIVESALES]; ?>
		</div>
	</div>
</div>