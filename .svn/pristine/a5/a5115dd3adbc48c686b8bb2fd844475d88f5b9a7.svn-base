<?php
	$po_number 			= ( ISSET($po_details['po_num'])) ? ($po_details['po_num']) : '';
	$po_date  			= ( ISSET($po_details['po_date']) ) ? std_datepicker_format($po_details['po_date']) : '';
	$vendor 			= ( ISSET($vendor_details['vendor_name']) ) ? $vendor_details['vendor_name'] : '';
	$po_amount  		= ( ISSET($po_details['po_amount']) ) ? (number_format($po_details['po_amount'],2)) : '0';
	$submission_date 	= ( ISSET($po_details['submission_date']) ) ? $po_details['submission_date'] : '';
	
	$recipient 			= (ISSET($recipient_info['fname']) AND $recipient_info['lname']) ? $recipient_info['fname'].' '.$recipient_info['lname'] : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>PO Number</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_number . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>PO Date</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $po_date . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Vendor</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $vendor . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Amount</label>
		</div>

		<div class="col l9 m s12 valign-middle">
        <?php
        	// $po_amount = ISSET($po_amount) ? number_format($po_amount,2) : 0;
        	echo '<div class="div-task-values">' .$po_amount . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">PO Document File</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
			<?php 
				echo $task_documents[DOC_TYPE_PO];
	        ?>
		</div>
	</div>
</div>