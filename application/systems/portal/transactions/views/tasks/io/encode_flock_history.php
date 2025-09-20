<?php
	$version_id 			= ( ISSET($flock_history_details['io_id'])) ? base64_url_encode($flock_history_details['io_id']) : '' ;
	$fhr_document_num		= ( ISSET($flock_history_details['fhr_document_num']) ) ? $flock_history_details['fhr_document_num'] : '';
	$date_submitted  		= ( ISSET($flock_history_details['fhr_submit_date']) ) ? std_datepicker_format($flock_history_details['fhr_submit_date']) : '';


// Change request 12.21.22 Starts Here

	$actual_clean_up_date    = ( ISSET($io['actual_clean_up_date']) ) ? std_datepicker_format($io['actual_clean_up_date']) : '';
	$harvested_head          = ( ISSET($io['harvested_heads_num']) ) ? $io['harvested_heads_num'] : '';
	$harvested_kilos         = ( ISSET($io['harvested_kilos_num']) ) ? $io['harvested_kilos_num'] : '';
	$feeds_delivered         = ( ISSET($io['delivered_feeds_num']) ) ? $io['delivered_feeds_num'] : '';
	$feeds_used              = ( ISSET($io['feeds_used_num']) ) ? $io['feeds_used_num'] : '';

	$feeds_retrieval         = ( ISSET($flock_history_details['feeds_retrieval']) ) ? $flock_history_details['feeds_retrieval'] : '';
	$audit_pre_placement     = ( ISSET($flock_history_details['pre_placement']) ) ? ($flock_history_details['pre_placement']) : '';
	$audit_brooding_audit    = ( ISSET($flock_history_details['brooding_audit']) ) ? ($flock_history_details['brooding_audit']) : '';
	$audit_biosecurity_audit = ( ISSET($flock_history_details['biosecurity_audit']) ) ? ($flock_history_details['biosecurity_audit']) : '';

	$regex                   = PARSLEY_AMOUNT_REGEX;

	$fmis_transacted_flag       = $flock_history_details['fmis_transacted_flag'];
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
			<label class="<?php echo $class_label ?>">Gross Placement (in heads)</label>
		</div>

		<div class="col l3 m8 s12 ">
                <div class="div-task-values"><?=$gross_placement?></div>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Net Placement (in heads)</label>
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
			<label class="<?php echo $class_label ?>">FHR Document No.</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$fhr_document_num</div>
EOS
            : 
			<<<EOS
				<input type="text" class="" name="fhr_document_num" id="fhr_document_num" placeholder="Enter FHR Document No." data-parsley-required="true" value="$fhr_document_num"/>
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">			
<!-- Change request 12.21.22 Starts Here -->
<!-- <label class="<?php echo $class_label ?>">Date Submitted</label> -->
			<label class="<?php echo $class_label ?>">Date Received from CG</label>
<!-- Change request 12.21.22 Ends Here -->
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$date_submitted</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="date_submitted" id="date_submitted" placeholder="MM/DD/YYYY" data-parsley-required="true" data-max-date="0" value="$date_submitted"/>
EOS;
		?>
		</div>
	
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Flock History Report Files</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents[DOC_TYPE_FHR]; ?>
		</div>
	</div>
</div>



<!-- Change request 12.21.22 Starts Here -->

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Harvest Summary</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_HARVEST_SUM']; ?>
		</div>
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Feeds Summary</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_FEEDS_SUM']; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Audit Report</label>
		</div>
		<div class="col l9 m8 s12 ">
			<?php echo $task_documents['DOC_AUDIT_REPORT']; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Feeds Retrieval (in bags)</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$feeds_retrieval</div>
EOS
            : 
			<<<EOS
				<input type="text" class="" name="feeds_retrieval" id="feeds_retrieval" placeholder="Enter Feeds Retrieval" value="$feeds_retrieval"/>
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Transacted in FMIS?</label>
		</div>

		<div class="col l9 m8 s12  ">

        <?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$fmis_transacted_flag_label</div>
EOS
            : 
			"
				<div class='input-field m-n'>
					<input type='checkbox' class='filled-in' name='fmis_transacted_flag' id='fmis_transacted_flag' value='$enum_yes' $checked/>

					<label for='fmis_transacted_flag'>Checking this box will signify that the feeds retrieval is transacted in FMIS</label>
				</div>
			"
        ?>

		</div>
	</div>
</div>


<div class="input-field m-n">
	<div class="row ">
		<!-- <div class="col l3 m4 s12 p-r-md"> -->
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">Audit Scores</label>
			<div class="help-text font-sm  <?php  echo ($view) ? 'hide' : ''; ?>"><i>(leave blank if not applicable) </i></div>
		</div>

		<div class="col l2 m3 s3 m-t-xs ">
        <?php
        	$audit_pre_placement = ($audit_pre_placement != NULL) ? $audit_pre_placement :(($view) ? 'N/a' : '');   
            echo ($view)
            ? 
            <<<EOS
				<div class="div-task-values">$audit_pre_placement%</div>
				<div class="help-text font-sm m-t-xs">Pre-placement</div>
EOS
            : 
			<<<EOS
				<div class="input-group">
					<input type="text" class="" name="pre_placement" placeholder="Pre-placement" 	data-parsley-pattern="$regex"  value="$audit_pre_placement"/>
					<div class="input-group-addon">%</div>
				</div>

				<div class="help-text font-sm m-t-xs">Pre-placement</div>
EOS;
		?>
		</div>
		<div class="col l2 m3 s3 m-t-xs">
        <?php
        	$audit_brooding_audit = ($audit_brooding_audit != NULL) ? $audit_brooding_audit : (($view) ? 'N/a' : '');   
            echo ($view)
            ? 
            <<<EOS
				<div class="div-task-values">$audit_brooding_audit%</div>
				<div class="help-text font-sm m-t-xs">Brooding Audit</div>
EOS
            : 
			<<<EOS
				<div class="input-group">
					<input type="text" class="" name="brooding_audit" placeholder="Brooding Audit" 	data-parsley-pattern="$regex"  value="$audit_brooding_audit"/>
					<div class="input-group-addon">%</div>
				</div>

				<div class="help-text font-sm m-t-xs">Brooding Audit</div>
EOS;
		?>
		</div>
		<div class="col l2 m3 s3 m-t-xs">
        <?php
        	$audit_biosecurity_audit = ($audit_biosecurity_audit != NULL) ? $audit_biosecurity_audit : (($view) ? 'N/a' : '');   
            echo ($view)
            ? 
            <<<EOS
				<div class="div-task-values">$audit_biosecurity_audit%</div>
				<div class="help-text font-sm m-t-xs">Biosecurity Audit</div>
EOS
            : 
			<<<EOS
				<div class="input-group">
					<input type="text" class="" name="biosecurity_audit" id="biosecurity_audit" placeholder="Biosecurity Audit" data-parsley-pattern="$regex"  value="$audit_biosecurity_audit"/>
					<div class="input-group-addon">%</div>
				</div>

				<div class="help-text font-sm m-t-xs">Biosecurity Audit</div>
EOS;
		?>
		</div>
	</div>
</div>

<!-- Change request 12.21.22 Ends Here -->