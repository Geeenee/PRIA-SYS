<?php

	$dr_reference   = ( ISSET($doc_gr_details['dr_reference']) ) ? $doc_gr_details['dr_reference'] : '';
	$doc_gr_no    	= ( ISSET($doc_gr_details['gr_num']) ) ? $doc_gr_details['gr_num'] : '';
	$doc_gr_date  	= ( ISSET($doc_gr_details['gr_date']) ) ? std_datepicker_format($doc_gr_details['gr_date']) : '';

//	$doc_dr_no   	= ( ISSET($doc_dr_details['dr_num']) ) ? $doc_dr_details['dr_num'] : '';
	$gross_placement = ( ISSET($doc_gr_details['gross_placement']) ) ? $doc_gr_details['gross_placement'] : '';
	$net_placement   = ( ISSET($doc_gr_details['net_placement']) ) ? $doc_gr_details['net_placement'] : '';

?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label for="doc_dr_no" class="<?php echo $class_label ?>">DOC DR Number</label>
		</div>

		<div class="col l9 m8 s12  ">
        	<div class="div-task-values"><?php echo $doc_dr_no ?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">
			<label class="<?php echo $class_label ?>">DOC GR Number</label>
		</div>

		<div class="col l9 m8 s12  ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$doc_gr_no</div>
EOS
            : 
			<<<EOS
				<input type="text" name="doc_gr_no" id="doc_gr_no" placeholder="Enter DOC GR Number" data-parsley-required="true" value="$doc_gr_no"/>
EOS;
		?>
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">
			<label class="<?php echo $class_label ?>">DOC GR Date</label>
		</div>

		<div class="col l9 m8 s12  ">
		<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$doc_gr_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="doc_gr_date" id="doc_gr_date" placeholder="Enter GR Date" data-parsley-required="true" value="$doc_gr_date" data-max-date="0"/>
EOS;
        ?>
		</div>
	</div>
</div>

<!-- Change request 12.21.22 Starts Here -->
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Gross Placement (in heads)</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$gross_placement</div>
EOS
            : 
			<<<EOS
				<input type="text" name="gross_placement" id="gross_placement" placeholder="Enter Gross Placement" data-parsley-required="true" data-parsley-type="digits" value="$gross_placement"/>
EOS;
		?>
		</div>
	
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Net Placement (in heads)</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$net_placement</div>
EOS
            : 
			<<<EOS
				<input type="text" name="net_placement" id="net_placement" placeholder="Enter Net Placement" data-parsley-required="true" data-parsley-type="digits" value="$net_placement"/>
EOS;
		?>
		</div>
	
	</div>
</div>
<!-- Change request 12.21.22 Ends Here -->