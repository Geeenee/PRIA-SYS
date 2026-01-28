<?php
	#Row 1
	$transmittal_date 			 	= ( ISSET($dt_details['transmittal_date']) ) ? std_datepicker_format($dt_details['transmittal_date']) : '-';
	$document_transmittal_date 		= ( ISSET($dt_details['document_transmittal_date']) ) ? std_datepicker_format($dt_details['document_transmittal_date']) : '-';

	#Row 2
	$document_batch_number  		= ( ISSET($dt_details['document_tracer_batch_number']) ) ? $dt_details['document_tracer_batch_number'] : '-';
	$vendor_name 					= ( ISSET($vendor_details['vendor_name']) ) ? $vendor_details['vendor_name'] : '-';

	#Row 3
	$org_name 						= ( ISSET($org_details['name']) ) ? $org_details['name'] : '-';
	$date_from 						= ( ISSET($dt_details['date_from']) ) ? std_datepicker_format($dt_details['date_from']) : NULL;
	$date_to 						= ( ISSET($dt_details['date_to']) ) ? std_datepicker_format($dt_details['date_to']) : NULL;
	$period_covered					= ( !EMPTY($date_from) AND !EMPTY($date_to) ) ? $date_from . ' to ' . $date_to : 'N/A';
	#Row 4
	$courier_tracking_number    	= ( ISSET($dt_details['courier_tracking_number']) ) ? $dt_details['courier_tracking_number'] : '-';
	$transmittal_document_sender 	= ( ISSET($dt_details['transmittal_document_sender']) ) ? $dt_details['transmittal_document_sender'] : '-';

	#Row 5
	$release_date 					= ( ISSET($dt_details['release_date']) ) ? std_datepicker_format($dt_details['release_date']) : NULL;
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Date of Transmittal</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $transmittal_date . '</div>';
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Document Transmittal Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
			echo '<div class="div-task-values">' . $document_transmittal_date . '</div>';
		?>
		</div>

	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Document Batch Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $document_batch_number . '</div>';
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Vendor Name</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
			echo '<div class="div-task-values">' . $vendor_name . '</div>';
		?>
		</div>

	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Business Center Name</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $org_name . '</div>';
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Period Covered</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
			echo '<div class="div-task-values">' . $date_from . ' to ' . $date_to . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
	
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Courier/Tracking Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $courier_tracking_number . '</div>';
		?>
		</div>
				<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Transmittal Documents Sender</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $transmittal_document_sender . '</div>';
		?>
		</div>

	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="<?php echo $class_label ?>">Transmittal Document File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php
			echo $task_documents[DOC_TYPE_TRANSMITTAL];
        ?>   

		</div>
	</div>
</div>

<div class="input-field m-n b-t p-t-sm">

	<div class="row m-b-n p-n">

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Date Release to Accounts Payable</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$release_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="release_date" id="release_date" placeholder="Enter Release Date" data-parsley-required="true" value="$release_date"/>
EOS;
		?>
		</div>
	</div>
</div>

<div class="input-field m-n b-t p-t-sm">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Remarks</label>
		</div>

        <div class="col l9 m8 s12 ">
		<?php
			$remarks = ( ISSET($dt_details['remarks']) && ! EMPTY($dt_details['remarks'])) ? nl2br($dt_details['remarks']) : '';

			echo '<div class="materialize-textarea m-t-sm" style="min-height:100px; overflow: auto; border: 1px solid #ccc; border-radius: 2px; padding: 8px;">' . $remarks . '</div>';
		?>
        </div>
	</div>
</div>