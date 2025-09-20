<?php

	$soa_id 			= ( ISSET($soa_transmittals['soa_id'])) ? ($soa_transmittals['soa_id']) : '';
	$transmittal_date  	= ( ISSET($soa_transmittals['transmittal_date']) ) ? std_datepicker_format($soa_transmittals['transmittal_date']) : '';
	$courier 			= ( ISSET($soa_transmittals['courier_waybill_num']) ) ? $soa_transmittals['courier_waybill_num'] : '';
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m5 s5 label-col p-r-md">			
			<label>Transmittal Date</label>
		</div>

		<div class="col l3 m4 s12 valign-middle">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$transmittal_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="transmittal_date" id="transmittal_date" placeholder="Enter Date of Transmittal" data-parsley-required="true" value="$transmittal_date" data-max-date="0"/>
EOS;
		?>
		</div>
		<div class="col l3 m8 s12 valign-middle"></div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label>Courier Way Bill No.</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$courier</div>
EOS
            : 
			<<<EOS
				<input type="text" name="courier" id="courier" placeholder="Enter Courier" data-parsley-required="true" value="$courier"/>
EOS;
		?>
		</div>
	</div>
</div>