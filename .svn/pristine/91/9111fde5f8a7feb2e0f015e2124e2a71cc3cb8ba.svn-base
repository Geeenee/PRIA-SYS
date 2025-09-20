<?php 

	$store_name = ISSET($site_det['official_store_name']) ? $site_det['official_store_name'] : '';
	$lessor 	= ISSET($vendor_det['vendor_name']) ? $vendor_det['vendor_name'] : '';
	
	$previous_contract_period 	= $contract_det['original_start_date'].' to '.$contract_det['expiration_date'];
	$new_contract_period 		= $contract_det['recommended_date_from'].' to '.$contract_det['recommended_date_to'];;
	
	$previous_payment_term 		= ISSET($contract_det['payment_term_code']) ? $contract_det['payment_term_code'] : '';
	$new_payment_term 			= ISSET($contract_det['recommended_payment_term_code']) ? $contract_det['recommended_payment_term_code'] : '';
?>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Store Name</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $store_name; ?></div>
        </div>

    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Lessor</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $lessor; ?></div>
        </div>

    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Current Contract Period</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $previous_contract_period; ?></div>
        </div>
</div>
<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Recommended Contract Period</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $new_contract_period; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Current Payment Terms</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $previous_payment_term; ?></div>
        </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Recommended Payment Terms</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $new_payment_term; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Site Renewal File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php 
			echo $task_documents[DOC_TYPE_RENEWAL];
        ?>   

		</div>
	</div>
</div>


<div class="input-field m-n b-t p-t-sm">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Recommendations</label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php
            $sn_recom = ( ISSET($contract_details['cn_recommendation']) && ! EMPTY($contract_details['cn_recommendation'])) ? (($view === true || $w_edit_recom === false)? nl2br($contract_details['cn_recommendation']): $contract_details['cn_recommendation']) : ''; 

            echo ( $view === true || $w_edit_recom === false)
            ? 
            <<<EOS
                <div class="div-task-values">$sn_recom</div>
EOS
            : 
			<<<EOS
            <textarea id="recommendation" name="recommendation" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$sn_recom</textarea>    
EOS;
		?>
           
        </div>
	</div>
</div>
