<div class="input-field">
	<div class="table-display">
        <div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
            <label class="view">Site Nomination</label>
        </div>

        <div class="table-cell l5 m6 s6 valign-middle">
            <div class="div-task-values"><?php echo $boq_details['boq_code'].' - '.$boq_details['official_store_name']; ?></div>
        </div>

        <div class="table-cell l4 m1 s1 valign-middle"></div>
    </div>
</div>

<div class="input-field">
	<div class="table-display">
        <div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="table-cell l5 m6 s6 valign-middle">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>

        <div class="table-cell l4 m1 s1 valign-middle"></div>
    </div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Recommended Amount</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
		
        <?php
            $amount = ( ! EMPTY($boq_details['recommended_amount'])) ? number_format($boq_details['recommended_amount'], DECIMAL_PLACES) : '';
            echo 
            <<<EOS
                <div class="div-task-values">$amount</div>
EOS;
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>


<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php echo $class_label ?>">Final Amount</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
		
        <?php
            $amount = ( ! EMPTY($boq_details['final_amount'])) ? number_format($boq_details['final_amount'], DECIMAL_PLACES) : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$amount</div>
EOS
            : 
			<<<EOS
                <input type="text" name="amount" id="amount" placeholder="Enter Amount" class="number" data-parsley-required="true" value="$amount"/>
EOS;
		?>

		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
        <div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
            <label class="<?php echo $class_label ?>">BOQ File</label>
        </div>

        <div class="table-cell l9 m7 s7 valign-middle">
            <?php echo $task_documents[DOC_TYPE_BOQ]; ?>    
        </div>
    </div>
</div>
