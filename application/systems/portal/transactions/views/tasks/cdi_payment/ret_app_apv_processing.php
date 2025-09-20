
<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Business Center</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['name'] ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Store name</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['official_store_name'] ?></div>
        </div>

       
    </div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">BOM Approval Reference</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php $bom_url = base_url().PORTAL_TRANSACTIONS.'/'. FOLDER_STORE_RENOVATION. '/?keyword='.$payment_details['bom_num']. '#' . TAB_BOM_APPROVAL?>
            <a href="<?php echo $bom_url ?>" target="_blank" rel="noreferer"> <?php echo $payment_details['bom_num'] ?> </a>
        </div>

       
    </div>
</div>

<?php 
    $general_contractor = (ISSET($payment_details['general_contractor'])) ? std_datepicker_format($payment_details['general_contractor']) : '';
    $remarks_for_verification = ( ISSET($con_plan_details['remarks_for_verification']) && ! EMPTY($con_plan_details['remarks_for_verification'])) ?
        (($view == TRUE)?  nl2br($con_plan_details['remarks_for_verification']): $con_plan_details['remarks_for_verification']) : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">General Contractor</label>
		</div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['general_contractor'] ?></div>
        </div>

	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">SOA Document Date</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo std_datepicker_format($payment_details['soa_document_date']) ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">SOA Submission Date</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo std_datepicker_format($payment_details['soa_submission_date']) ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">SOA No</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['soa_num'] ?></div>
        </div>

       
    </div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">SOA File</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php 
            echo $task_documents[DOC_TYPE_CDI_SOA_DOC];
        ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Ok to Process Payment?</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo ($payment_details['ok_to_process_payment']) ? 'YES' : 'NO'?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="view">Confirmation Remarks</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['confirmation_remarks'] ?></div>
        </div>
    </div>
</div>

<?php 
    $apv_no                  = (ISSET($payment_details['apv_no'])) ? $payment_details['apv_no'] : '';
    $apv_doc_date            = (ISSET($payment_details['apv_doc_date'])) ? std_datepicker_format($payment_details['apv_doc_date']) : '';
    $transmittal_date_to_bgc = (ISSET($payment_details['transmittal_date_to_bgc'])) ? std_datepicker_format($payment_details['transmittal_date_to_bgc']) : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">APV No.</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$apv_no</div>
EOS
            : 
			<<<EOS
				<input type="text" name="apv_no" id="apv_no" data-parsley-required="true" value="$apv_no" />
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">APV Document Date</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$apv_doc_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="apv_doc_date" id="apv_doc_date" data-parsley-required="true" value="$apv_doc_date" />
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Transmittal Date to BGC</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$transmittal_date_to_bgc</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="transmittal_date_to_bgc" id="transmittal_date_to_bgc" data-parsley-required="true" value="$transmittal_date_to_bgc" />
EOS;
		?>
		</div>
	
	</div>
</div>