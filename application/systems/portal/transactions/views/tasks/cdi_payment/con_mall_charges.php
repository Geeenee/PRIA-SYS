
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
			<label class="view">General Contractor</label>
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
            <label class="view">Remarks for Verification</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $payment_details['remarks_for_verification'] ?></div>
        </div>
    </div>
</div>


<?php 
    $is_checked = ($payment_details['ok_to_process_payment']) ? 'checked' : '';
    $confirmation_remarks = ( ISSET($payment_details['confirmation_remarks']) && ! EMPTY($payment_details['confirmation_remarks'])) ?
        (($view == TRUE)?  nl2br($payment_details['confirmation_remarks']): $payment_details['confirmation_remarks']) : '';
?>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="<?php echo $class_label ?>">Supporting Documents</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php 
            echo $task_documents[DOC_TYPE_SUPP_DOC];
        ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="<?php echo $class_label ?>">Ok to Process Payment?</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php if($view) :?>
                <?php echo ($payment_details['ok_to_process_payment'])? 'Yes' : 'No'?> 
            <?php else: ?>
	    	<div class="input-field">
                <input type="checkbox" class="labelauty disabled-icon" name="ok_to_process_payment" id="ok_to_process_payment" value="<?php echo ACTIVE ?>" data-labelauty="No|Yes" <?=$is_checked?> />
	      	</div>
            <?php endif; ?>
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
                <div class="div-task-values">$confirmation_remarks</div>
EOS
            : 
            <<<EOS
            <textarea id="confirmation_remarks" name="confirmation_remarks" class="materialize-textarea m-t-sm" data-parsley-required="true" style="min-height:100px; overflow: auto;">$confirmation_remarks</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>

