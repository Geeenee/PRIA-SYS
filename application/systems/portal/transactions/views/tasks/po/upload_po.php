<?php
	if($account_group_code == AG_CONTRACTORS)
	{
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>BOQ #</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['boq_code'] .'</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>Site Name</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['site_name'] .'</div>';
		?>
		</div>
	</div>
</div>

<?php } ?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>PR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['pr_num'] . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>PO Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['po_num'] . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>PO Type</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $po_details['po_type_name'] . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>PO Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . std_datepicker_format($po_details['po_date']) . '</div>';
		?>
            <input type="hidden" class="datepicker" name="po_date" id="po_date" placeholder="Enter PO Date" value="<?php echo std_datepicker_format($po_details['po_date']) ?>" data-max-date="0" />
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
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
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>Amount</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . number_format($po_details['po_amount'],2) . '</div>';
		?>
		</div>
	</div>
</div>

<?php
	if($account_group_code == AG_CONTRACTORS)
	{
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">
			<label class="<?php echo ($require_receiving_num)? $class_label: ''; ?>">Receiving Report #</label>
		</div>
		<div class="col l3 m8 s12 valign-middle">
			<?php
            if($view):
			?>
            	<div class="div-task-values"><?php echo $po_details['receiving_report_num'] ?></div>
            <?php
        	else:
            ?>
            	<input type="text" name="receiving_report_num" id="receiving_report_num" placeholder="Enter Receiving Report #" value="<?php echo $po_details['receiving_report_num'] ?>"  />
            <?php
	        endif;
	        ?>
		</div>
	</div>
</div>
<?php
	}
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">
			<label class="<?php echo ($require_release_date)? $class_label: ''; ?>">Released Date</label>
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
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="<?php echo $class_label ?>">PO Document File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php
			echo $task_documents[DOC_TYPE_PO];
        ?>

		</div>
	</div>
</div>