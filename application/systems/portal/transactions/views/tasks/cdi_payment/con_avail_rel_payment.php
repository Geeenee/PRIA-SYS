
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


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="view">APV No.</label>
		</div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['apv_no'] ?></div>
        </div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="view">APV Document Date</label>
		</div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo std_datepicker_format($payment_details['apv_doc_date'] ) ?></div>
        </div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="view">Transmittal Date to BGC</label>
		</div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo std_datepicker_format($payment_details['transmittal_date_to_bgc'] ) ?></div>
        </div>
	
	</div>
</div>

    <?php 
        $check_date                   = (ISSET($payment_details['check_date'])) ? std_datepicker_format($payment_details['check_date']) : '';
        $date_received_fr_bgc         = (ISSET($payment_details['date_received_fr_bgc'])) ? std_datepicker_format($payment_details['date_received_fr_bgc']) : '';
        $date_received_fr_vendor      = (ISSET($payment_details['date_received_fr_vendor'])) ? std_datepicker_format($payment_details['date_received_fr_vendor']) : '';
        $avail_rel_of_payment_remarks = ( ISSET($payment_details['avail_rel_of_payment_remarks']) && ! EMPTY($payment_details['avail_rel_of_payment_remarks'])) ?
            (($view == TRUE)?  nl2br($payment_details['avail_rel_of_payment_remarks']): $payment_details['avail_rel_of_payment_remarks']) : '';
    ?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Check Date</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$check_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="check_date" id="check_date" data-parsley-required="true" value="$check_date" />
EOS;
		?>
		</div>
	
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Date Received from BGC</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$date_received_fr_bgc</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="date_received_fr_bgc" id="date_received_fr_bgc" data-parsley-required="true" value="$date_received_fr_bgc" />
EOS;
		?>
		</div>
	
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Date Received from Vendor</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$date_received_fr_vendor</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="date_received_fr_vendor" id="date_received_fr_vendor" data-parsley-required="true" value="$date_received_fr_vendor" />
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>">Remarks</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php

            echo ( $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$avail_rel_of_payment_remarks</div>
EOS
            : 
            <<<EOS
            <textarea id="avail_rel_of_payment_remarks" name="avail_rel_of_payment_remarks" class="materialize-textarea m-t-sm" data-parsley-required="true" style="min-height:100px; overflow: auto;">$avail_rel_of_payment_remarks</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>

