<?php
	//$version_id 				= ( ISSET($fhr_details['io_id'])) ? base64_url_encode($fhr_details['io_id']) : '' ;
	$fhr_document_num       	= ( ISSET($fhr_details['fhr_document_num']) ) ? $fhr_details['fhr_document_num'] : '';
	$date_submitted        		= ( ISSET($fhr_details['fhr_submit_date']) ) ? std_datepicker_format($fhr_details['fhr_submit_date']) : '';

	$audit_pre_placement    	= ( ISSET($fhr_details['pre_placement']) ) ? ($fhr_details['pre_placement']) : '';
	$audit_brooding_audit   	= ( ISSET($fhr_details['brooding_audit']) ) ? ($fhr_details['brooding_audit']) : '';
	$audit_biosecurity_audit  	= ( ISSET($fhr_details['biosecurity_audit']) ) ? ($fhr_details['biosecurity_audit']) : '';

	$regex 					= PARSLEY_AMOUNT_REGEX;
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">			
			<label>FHR Document No.</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo <<<EOS
                <div class="div-task-values">$fhr_document_num</div>
EOS;
		?>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label>Date Submitted</label>
		</div>

		<div class="col l3 m8 s12 ">
		<?php
            echo <<<EOS
			<div class="div-task-values">$date_submitted</div>
EOS;
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">
			<label class="<?php echo $class_label ?>">Flock History Report File</label>
		</div>
		<div class="col l9 m8 s12 ">
		<?php echo $task_documents[DOC_TYPE_FHR]; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row ">
		<div class="col l3 m4 s12 p-r-md">
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