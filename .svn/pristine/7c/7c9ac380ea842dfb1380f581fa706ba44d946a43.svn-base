<?php 

	$store_name        = ISSET($site_det['official_store_name']) ? $site_det['official_store_name'] : '';
	$lessor 	       = ISSET($vendor_det['vendor_name']) ? $vendor_det['vendor_name'] : '';
	
	$contract_period   = $contract_det['recommended_date_from'].' to '.$contract_det['recommended_date_to'];;
	$payment_term      = ISSET($contract_det['recommended_payment_term_code']) ? $contract_det['recommended_payment_term_code'] : '';
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
            <label class="view">Contract Period</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $contract_period; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Payment Terms</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $payment_term; ?></div>
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
			<label class="">ROH Recommendations </label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php
            $sn_recom = ( ISSET($contract_details['cn_recommendation']) && ! EMPTY($contract_details['cn_recommendation'])) ? nl2br($contract_details['cn_recommendation']) : ''; 

            echo 
            <<<EOS
                <div class="div-task-values">$sn_recom</div>
EOS;
		?>
           
        </div>
	</div>
</div>


<div class="input-field m-n b-t p-t-sm">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Remarks on Recommendation</label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php
            $sn_recom_bh = ( ISSET($contract_details['cn_recommendation_bh']) && ! EMPTY($contract_details['cn_recommendation_bh'])) ? (($view === true || $w_edit_recom === false)? nl2br($contract_details['cn_recommendation_bh']): $contract_details['cn_recommendation_bh']) : ''; 

            echo ( $view === true || $w_edit_recom === false)
            ? 
            <<<EOS
                <div class="div-task-values">$sn_recom_bh</div>
EOS
            : 
			<<<EOS
            <textarea id="recommendation_bh" name="recommendation_bh" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required" style="min-height:100px; overflow: auto;">$sn_recom_bh</textarea>    
EOS;
		?>
           
        </div>
	</div>
</div>