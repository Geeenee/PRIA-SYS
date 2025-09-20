<?php
	$soa_number = ( ISSET($soa_details['soa_num']) ) ? $soa_details['soa_num'] : '';
	$soa_date = ( ISSET($soa_details['soa_date']) ) ? std_datepicker_format($soa_details['soa_date']) : '';
	$vendor = ( ISSET($soa_details['vendor_name']) ) ? $soa_details['vendor_name'] : '';
	$soa_file_name  	= ( ISSET($soa_details['file_name']) ) ? ($soa_details['file_name']) : '';
?>
<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label>SOA Number</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $soa_number . '</div>';
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label>SOA Date</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $soa_date . '</div>';
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">			
			<label>Vendor</label>
		</div>

		<div class="table-cell l5 m6 s6 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $vendor . '</div>';
		?>
		</div>
		<div class="table-cell l4 m1 s1 valign-middle"></div>
	</div>
</div>

<div class="input-field">
	<div class="table-display">
		<div class="table-cell l3 m5 s5 right-align valign-middle p-r-md">
			<label class="required<?php echo $class_label ?>">SOA File</label>
		</div>
		<div class="table-cell l4 m6 s6 valign-middle">
			<div class="file-field input-field">

		<?php
			if(empty($soa_file_name)){
				echo '<i class="material-icons">attach_file</i>
		      	<span><a href="#">Attach file</a><input type="file" /></span>
		      		<input class="file-path validate" type="text" name="soa_report_file" value="' . $soa_file_name . '">';
			}else{
				echo "<p>" . $soa_file_name . "</p>";
			}
        ?>
		    </div>
		</div>

		
		<div class="table-cell l6 m1 s1 valign-middle"></div>
	</div>
</div>