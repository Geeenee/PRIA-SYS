<?php
	//Row 1
	$transmittal_date 			 	= ( ISSET($dt_details['transmittal_date']) ) ? std_datepicker_format($dt_details['transmittal_date']) : '-';
	$number_of_documents    		= ( ISSET($dt_details['number_of_documents']) ) ? number_format($dt_details['number_of_documents']) : '-';

	//Row 2
	$document_batch_number  		= ( ISSET($dt_details['document_tracer_batch_number']) ) ? $dt_details['document_tracer_batch_number'] : '-';
	$transmittal_document_requestor = ( ISSET($dt_details['transmittal_document_requestor']) ) ? $dt_details['transmittal_document_requestor'] : '-';

	//Row 3
	$org_name 						= ( ISSET($org_details['name']) ) ? $org_details['name'] : '-';
	$release_date 					= ( ISSET($dt_details['release_date']) ) ? std_datepicker_format($dt_details['release_date']) : '-';


	// $submission_date 	= ( ISSET($soa_details['submission_date']) ) ? date('m/d/Y', strtotime($soa_details['submission_date'])) : 'N/A';

	// $week_no 			= ( ISSET($soa_details['week_num']) ) ? $soa_details['week_num'] : '-';


	// $soa_document_recipient = ( ISSET($soa_details['doc_recipient']) ) ? $soa_details['doc_recipient'] : '-';

	// $date_from 			= ( ISSET($soa_details['date_from']) ) ? date('m/d/Y', strtotime($soa_details['date_from'])) : '-';
	// $date_to 			= ( ISSET($soa_details['date_to']) ) ? date('m/d/Y', strtotime($soa_details['date_to'])) : '-';

	// $recipient 			= (ISSET($recipient_info['fname']) AND $recipient_info['lname']) ? $recipient_info['fname'].' '.$recipient_info['lname'] : '-';
	
	// $po_num 			= (ISSET($po_ref_info) AND is_array($po_ref_info) AND count($po_ref_info) > 0) ? implode(', ', array_column($po_ref_info, 'po_num')) : '-';

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
			<label>Number of Documents</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $number_of_documents . '</div>';
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
			<label>Transmittal Documents Requestor</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $transmittal_document_requestor . '</div>';
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
			<label for="release_date">Date Release to Accounts Payable</label>
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
				<input type="text" class="datepicker" name="release_date" id="release_date" placeholder="Enter Release Date" value="$release_date" data-max-date="0"/>
EOS;
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
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="">Remarks</label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php
			$remarks = ( ISSET($dt_details['remarks']) && ! EMPTY($dt_details['remarks'])) ? (($view === true || $w_edit_recom === false)? nl2br($dt_details['remarks']): $dt_details['remarks']) : ''; 

            echo 
            <<<EOS
            <div class="materialize-textarea m-t-sm" style="min-height:100px; overflow: auto; border: 1px solid #ccc; border-radius: 2px; padding: 8px;">$remarks</div>
EOS
		?>
           
        </div>
	</div>
</div>