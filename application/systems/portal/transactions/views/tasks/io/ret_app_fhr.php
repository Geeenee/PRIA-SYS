<?php
	$version_id 				= ( ISSET($fhr_details['io_id'])) ? base64_url_encode($fhr_details['io_id']) : '' ;
	$fhr_document_num       	= ( ISSET($fhr_details['fhr_document_num']) ) ? $fhr_details['fhr_document_num'] : '';
	$date_submitted        		= ( ISSET($fhr_details['fhr_submit_date']) ) ? std_datepicker_format($fhr_details['fhr_submit_date']) : '';

	$audit_pre_placement    	= ( ISSET($fhr_details['pre_placement']) ) ? ($fhr_details['pre_placement']) : '';
	$audit_brooding_audit   	= ( ISSET($fhr_details['brooding_audit']) ) ? ($fhr_details['brooding_audit']) : '';
	$audit_biosecurity_audit  	= ( ISSET($fhr_details['biosecurity_audit']) ) ? ($fhr_details['biosecurity_audit']) : '';


// Change request 12.21.22 Starts Here

	$actual_clean_up_date    = ( ISSET($io['actual_clean_up_date']) ) ? std_datepicker_format($io['actual_clean_up_date']) : '';
	$harvested_head          = ( ISSET($io['harvested_heads_num']) ) ? $io['harvested_heads_num'] : '';
	$harvested_kilos         = ( ISSET($io['harvested_kilos_num']) ) ? $io['harvested_kilos_num'] : '';
	$feeds_delivered         = ( ISSET($io['delivered_feeds_num']) ) ? $io['delivered_feeds_num'] : '';
	$feeds_used              = ( ISSET($io['feeds_used_num']) ) ? $io['feeds_used_num'] : '';

	$feeds_retrieval         = ( ISSET($fhr_details['feeds_retrieval']) ) ? $fhr_details['feeds_retrieval'] : '';

	$regex                   = PARSLEY_AMOUNT_REGEX;

	$fmis_transacted_flag       = $fhr_details['fmis_transacted_flag'];
	$fmis_transacted_flag_label = ($fmis_transacted_flag == ENUM_YES) ? 'Yes' : 'No';
	$checked                    = ($fmis_transacted_flag == ENUM_YES) ? 'checked ': '';
	$enum_yes                   = ENUM_YES;

	$gross_placement = ( ISSET($doc_gr_details['gross_placement']) ) ? $doc_gr_details['gross_placement'] : '';
	$net_placement   = ( ISSET($doc_gr_details['net_placement']) ) ? $doc_gr_details['net_placement'] : '';

// Change request 12.21.22 Ends Here

?>

<!-- Change request 12.21.22 Starts Here -->
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Actual Clean-Up Date</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values"><?=$actual_clean_up_date?></div>
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Harvested Head</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values"><?=$harvested_head?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Harvested Kilos</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values"><?=$harvested_kilos?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Feeds Delivered (in bags)</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values"><?=$feeds_delivered?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Feeds Used (in bags)</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values"><?=$feeds_used?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="">Gross Placement (in heads)</label>
		</div>

		<div class="col l3 m8 s12 ">
                <div class="div-task-values"><?=$gross_placement?></div>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="">Net Placement (in heads)</label>
		</div>

		<div class="col l3 m8 s12 ">
			<div class="div-task-values"><?=$net_placement?></div>
		</div>
	
	</div>
</div>
<!-- Change request 12.21.22 Ends Here -->


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php //echo $class_label ?>">FHR Document No.</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $fhr_document_num ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
<!-- Change request 12.21.22 Starts Here -->
<!-- <label class="">Date Submitted</label> -->
			<label class="">Date Received from CG</label>
<!-- Change request 12.21.22 Ends Here -->
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $date_submitted ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php //echo $class_label ?>">Flock History Report File</label>
		</div>
		<div class="col l9 m8 s12 ">
		<?php echo $task_documents[DOC_TYPE_FHR]; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-b-n">
		<div class="col l3 m4 s12 label-col  p-r-md p-t-md">
			<label class="<?php //echo $class_label ?>">Audit Scores</label>
		
		</div>

		<div class="col l2 m3 s3 ">
			<?php $audit_pre_placement = ($audit_pre_placement != NULL) ? $audit_pre_placement : 'N/a'; ?>
			<div class="div-task-values"><?php echo $audit_pre_placement ?></div>
			<div class="help-text font-sm m-t-xs">Pre-placement</div>
		</div>

		<div class="col l2 m3 s3 ">
			<?php $audit_brooding_audit = ($audit_brooding_audit != NULL) ? $audit_brooding_audit : 'N/a'; ?>
			<div class="div-task-values"><?php echo $audit_brooding_audit ?></div>
			<div class="help-text font-sm m-t-xs">Brooding Audit</div>
		</div>

		<div class="col l2 m3 s3 ">
			<?php $audit_biosecurity_audit = ($audit_biosecurity_audit != NULL) ? $audit_biosecurity_audit : 'N/a'; ?>
			<div class="div-task-values"><?php echo $audit_biosecurity_audit ?></div>
			<div class="help-text font-sm m-t-xs">Biosecurity Audit</div>
		</div>
	</div>
</div>


<!-- Change request 12.21.22 Starts Here -->
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="">Harvest Summary</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_HARVEST_SUM']; ?>
		</div>
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="">Feeds Summary</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_FEEDS_SUM']; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="">Audit Report</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_AUDIT_REPORT']; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="">Feeds Retrieval (in bags)</label>
		</div>

		<div class="col l3 m8 s12 ">
			<div class="div-task-values"><?=$feeds_retrieval?></div>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Transacted in FMIS?</label>
		</div>
		<div class="col l9 m8 s12  ">
			<div class="div-task-values"><?=$fmis_transacted_flag_label?></div>
		</div>
	</div>
</div>

<!-- Change request 12.21.22 Ends Here -->