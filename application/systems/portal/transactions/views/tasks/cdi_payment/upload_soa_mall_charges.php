
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
    $general_contractor = (ISSET($payment_details['general_contractor'])) ? $payment_details['general_contractor'] : '';
    $remarks_for_verification = ( ISSET($payment_details['remarks_for_verification']) && ! EMPTY($payment_details['remarks_for_verification'])) ?
        (($view == TRUE)?  nl2br($payment_details['remarks_for_verification']): $payment_details['remarks_for_verification']) : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">General Contractor</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$general_contractor</div>
EOS
            : 
			<<<EOS
				<input type="text" name="general_contractor" id="general_contractor" data-parsley-required="true" value="$general_contractor" />
EOS;
		?>
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
            <label class="<?php echo $class_label ?>">SOA File</label>
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
            <label class="<?php echo $class_label ?>">Remarks</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php

            echo ( $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$remarks_for_verification</div>
EOS
            : 
            <<<EOS
            <textarea id="remarks_for_verification" name="remarks_for_verification" class="materialize-textarea m-t-sm" data-parsley-required="true" style="min-height:100px; overflow: auto;">$remarks_for_verification</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>

