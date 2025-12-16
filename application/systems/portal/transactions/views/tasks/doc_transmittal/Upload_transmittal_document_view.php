<?php
	#Row 1
	$transmittal_date 			 	= ( ISSET($dt_details['transmittal_date']) ) ? std_datepicker_format($dt_details['transmittal_date']) : '-';
	$courier_tracking_number    	= ( ISSET($dt_details['courier_tracking_number']) ) ? $dt_details['courier_tracking_number'] : '-';

	#Row 2
	$document_batch_number  		= ( ISSET($dt_details['document_tracer_batch_number']) ) ? $dt_details['document_tracer_batch_number'] : '-';
	$transmittal_document_sender = ( ISSET($dt_details['transmittal_document_sender']) ) ? $dt_details['transmittal_document_sender'] : '-';

	#Row 3
	$org_name 						= ( ISSET($org_details['name']) ) ? $org_details['name'] : '-';
	$release_date 					= ( ISSET($dt_details['release_date']) ) ? std_datepicker_format($dt_details['release_date']) : '';

	#
	$edit_task = isset($edit_task) ? $edit_task : FALSE;
	$is_returned = isset($is_returned) ? $is_returned : FALSE;
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Date of Transmittal</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
			if ($edit_task) {
				echo '<input type="text" class="datepicker" name="transmittal_date" id="transmittal_date" placeholder="Enter Date of Transmittal" value="'.$transmittal_date.'" data-max-date="0" />';
			} else {
				echo '<div class="div-task-values">'.$transmittal_date.'</div>';
			}
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Courier/Tracking Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
			if ($edit_task) {
				echo '<input type="text" name="courier_tracking_number" id="courier_tracking_number" placeholder="Enter Courier/Tracking Number" value="'.$courier_tracking_number.'" />';
			} else {
				echo '<div class="div-task-values">' . $courier_tracking_number . '</div>';
			}
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
			if ($edit_task) {
				echo '<input type="text" name="document_tracer_batch_number" id="document_tracer_batch_number" placeholder="Enter Document Batch Number" value="'.$document_batch_number.'" />';
			} else {
				echo '<div class="div-task-values">' . $document_batch_number . '</div>';
			}
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Transmittal Documents Sender</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
			if ($edit_task) {
				echo '<input type="text" name="transmittal_document_sender" id="transmittal_document_sender" placeholder="Enter Transmittal Documents Sender" value="'.$transmittal_document_sender.'" />';
			} else {
				echo '<div class="div-task-values">' . $transmittal_document_sender . '</div>';
			}
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">

	<div class="row m-b-n p-n">
		
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Business Center Name</label>
		</div>

			<div class="col l3 m8 s12 ">
				<?php
				if ($edit_task) {
					$selected_org_code = (isset($org_details['org_code'])) ? $org_details['org_code'] : '';
					$select_html = '<select id="business_center" class="selectize" name="business_center" data-parsley-required="true">';
					foreach ($organizations as $o) {
						$val = htmlspecialchars($o['org_code'], ENT_QUOTES);
						$text = htmlspecialchars($o['name'], ENT_QUOTES);
						$sel = ($o['org_code'] == $selected_org_code) ? ' selected' : '';
						$select_html .= '<option value="' . $val . '"' . $sel . '>' . $text . '</option>';
					}
					$select_html .= '</select>';
					echo $select_html;
				} else {
					echo '<div class="div-task-values">' . $org_name . '</div>';
				}
				?>
			</div>

		<?php
		#This field is only visible when task is returned
		if($is_returned){
			if ($edit_task) {	
			echo
				'<div class="col l3 m4 s12 p-r-md label-col">			
					<label>Date Release to Accounts Payable</label>
				</div>

				<div class="col l3 m8 s12 valign-middle">
					<input type="text" class="datepicker" name="release_date" id="release_date" placeholder="Enter Date Release" value="'.$release_date.'"/>
				</div>';
			} else {
			echo 
				'<div class="col l3 m4 s12 p-r-md label-col">			
					<label>Date Release to Accounts Payable</label>
				</div>

				<div class="col l3 m8 s12 valign-middle">
					<div class="div-task-values">'.$release_date.'</div>
				</div>';
			}
		}
		?>
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
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Remarks</label>
		</div>

        <div class="col l9 m8 s12 ">
		<?php
			$remarks = ( ISSET($dt_details['remarks']) && ! EMPTY($dt_details['remarks'])) ? (($view === true || $w_edit_recom === false)? nl2br($dt_details['remarks']): $dt_details['remarks']) : '';

			if ($view === false) {
				echo '<textarea id="remarks" name="remarks" class="materialize-textarea m-t-sm" style="min-height:100px; overflow: auto;">' . $remarks . '</textarea>';
			} else {
				echo '<div class="materialize-textarea m-t-sm" style="min-height:100px; overflow: auto; border: 1px solid #ccc; border-radius: 2px; padding: 8px;">' . $remarks . '</div>';
			}
		?>
        </div>
	</div>
</div>