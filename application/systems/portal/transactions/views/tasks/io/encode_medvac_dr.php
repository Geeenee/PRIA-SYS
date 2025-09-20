<?php
	$dr_gr_id         = ( ISSET($medvac_details['dr_gr_id']) ) ? $medvac_details['dr_gr_id'] : '';
	$dr_no            = ( ISSET($medvac_details['dr_num']) ) ? $medvac_details['dr_num'] : '';
	$dr_date          = ( ISSET($medvac_details['dr_date']) ) ? std_datepicker_format($medvac_details['dr_date']) : '';
	$transaction_date = ( ISSET($medvac_details['transaction_date']) ) ? std_datepicker_format($medvac_details['transaction_date']) : '';
	$last_dr_flag     = ( ISSET($medvac_details['last_dr_flag']) ) ? $medvac_details['last_dr_flag'] : '';
	//Variables below are for last_dr_flag
	$checked 		  = ($last_dr_flag == ENUM_YES) ? 'checked ': ''; 
	$last_dr 	  	  = ($last_dr_flag == ENUM_YES) ? 'Yes': 'No'; 
	$enum_yes 		  = ENUM_YES;
?>
<input type="hidden" id="dr_gr_id" name="dr_gr_id" value="<?php echo $dr_gr_id; ?>" />
<input type="hidden" id="core_task_id" name="core_task_id" value="<?php echo $core_task_id; ?>" />
<input type="hidden" id="dependent_task" name="dependent_task" value="<?php echo $dependent_task; ?>" />
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
<!-- Change request 12.21.22 Starts Here -->
<!-- <label class="<?php echo $class_label ?>">DR Transaction No. (SAP)</label> -->
			<label class="<?php echo $class_label ?>">DR Transaction No.</label>
<!-- Change request 12.21.22 Ends Here -->
		</div>

		<div class="col l3 m8 s12 ">
		
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$dr_no</div>
EOS
            : 
			<<<EOS
				<input type="text" name="dr_transaction_no" id="dr_transaction_no" placeholder="Enter DR Transaction Number" data-parsley-required="true" value="$dr_no"/>
EOS;
		?>

		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
<!-- Change request 12.21.22 Starts Here -->
<!-- <label class="<?php echo $class_label ?>">DR Date</label> -->
			<label class="<?php echo $class_label ?>">DR Document Date</label>
<!-- Change request 12.21.22 Ends Here -->
		</div>

		<div class="col l3 m8 s12  ">

		<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$dr_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="medvac_dr_date" id="medvac_dr_date" placeholder="MM/DD/YYYY" data-parsley-required="true" value="$dr_date" data-max-date="0"/>
EOS;
        ?>

		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
<!-- Change request 12.21.22 Starts Here -->
<!-- <label class="<?php echo $class_label ?>">Transaction Date (per IO)</label> -->
			<label class="<?php echo $class_label ?>">Transaction Date</label>
<!-- Change request 12.21.22 Ends Here -->
		</div>

		<div class="col l3 m8 s12  ">

        <?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$transaction_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="transaction_date" id="transaction_date" placeholder="MM/DD/YYYY" data-parsley-required="true"  value="$transaction_date"  data-max-date="0" />
EOS;
        ?>
		
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class=" row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>Last MedVac Delivery?</label>
		</div>

		<div class="col l9 m8 s12  ">

        <?php
			echo ($view AND !$open_last_dr)
            ? 
            <<<EOS
                <div class="div-task-values">$last_dr</div>
EOS
            : 
			"
				<div class='input-field m-n'>
					<input type='checkbox' class='filled-in".(($open_last_dr) ? ' auto_save_dr': '')."' name='medvac_deliveries' id='medvac_deliveries' value='$enum_yes' $checked/>
					<label for='medvac_deliveries'>Checking this box will signify completion of all MedVac deliveries</label>
				</div>
			"
        ?>

		</div>
	</div>
</div>