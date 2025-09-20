
<?php 
    $rfp_no            = $payment_details['rfp_no'];
    $rfp_date          = (ISSET($payment_details['rfp_date'])) ? std_datepicker_format($payment_details['rfp_date']) : '';
    $finance_in_charge = ($view) ? $payment_details['fullname'] : $payment_details['finance_in_charge'];
?>


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

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Request for Payment (RFP) No.</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$rfp_no</div>
EOS
            : 
			<<<EOS
				<input type="text" name="rfp_no" id="rfp_no" data-parsley-required="true" value="$rfp_no" />
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Request for Payment (RFP) Date.</label>
		</div>

		<div class="col l3 m8 s12">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$rfp_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="rfp_date" id="rfp_date" data-parsley-required="true" value="$rfp_date" />
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
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Finance-In-Charge</label>
		</div>

		<div class="col l3 m8 s12">
        <?php if ($view) {
            echo '<div class="div-task-values">'. $finance_in_charge .'</div>';  
        } else { ?>
            <select id="finance_in_charge" class="selectize" name="finance_in_charge" placeholder="Select Finance In-Charge" data-parsley-required="true">
                <option value=""></option>
                <?php foreach($in_charge as $key => $val){ 
                    $is_selected = ($val['user_id'] == $finance_in_charge) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $val['user_id']; ?>" <?php echo $is_selected?>><?php echo $val['fullname']; ?></option>
                <?php } ?>
            </select>
        <?php } ?>
		</div>
	
	</div>
</div>