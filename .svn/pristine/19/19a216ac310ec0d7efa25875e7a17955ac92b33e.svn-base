<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>PO Number</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['po_num'] . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>PO Date</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . std_datepicker_format($po_details['po_date']) . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Vendor</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $vendor_info['vendor_name'] . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>Amount</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . number_format($po_details['po_amount'],2) . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Released Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">

			<?php
            if($view):
			?>
            	<div class="div-task-values"><?php echo std_datepicker_format($po_details['po_released_date']) ?></div>
            <?php
        	else:
            ?> 
            	<input type="text" class="datepicker" name="po_released_date" id="po_released_date" placeholder="Enter Actual Released Date" value="<?php echo std_datepicker_format($po_details['po_released_date']) ?>" data-max-date="0" />
            <?php 
	        endif;
	        ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">
			<label class="<?php echo $class_label ?>">PO Document File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php 
			echo $task_documents[DOC_TYPE_PO];
        ?>   

		</div>
	</div>
</div>