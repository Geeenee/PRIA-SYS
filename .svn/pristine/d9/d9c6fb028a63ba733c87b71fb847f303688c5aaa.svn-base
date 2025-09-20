<?php

	$dr_num		= ( ISSET($delivery_receipt_details['dr_num']) ) ? ($delivery_receipt_details['dr_num']) : '';
	$dr_date    = ( ISSET($delivery_receipt_details['dr_date']) ) ? std_datepicker_format($delivery_receipt_details['dr_date']) : '';
	$dr_amount 	= ( ISSET($delivery_receipt_details['dr_amount']) ) ? $delivery_receipt_details['dr_amount'] : '';

	$gr_num		= ( ISSET($delivery_receipt_details['gr_num']) ) ? $delivery_receipt_details['gr_num'] : '';
	$gr_date	= ( ISSET($delivery_receipt_details['gr_date']) ) ? std_datepicker_format($delivery_receipt_details['gr_date']) : '';
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">DR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $dr_num ?></div>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">DR Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $dr_date ?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<!-- <div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Total Amount</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo ISSET($dr_amount) ? number_format($dr_amount,2) : '0.00'; ?></div>
		</div> -->

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">GR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$gr_num</div>
EOS
            : 
			<<<EOS
				<input type="text" name="gr_num" id="gr_num" placeholder="Enter GR Number" data-parsley-required="true" value="$gr_num"/>
EOS;
        ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col valign-middle p-r-md">
			<label class="<?php echo $class_label ?>">GR Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$gr_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="gr_date" id="gr_date" placeholder="Enter GR Date" data-parsley-required="true" value="$gr_date" data-max-date="0" />
EOS;
        ?>
			
		</div>
	</div>
</div>