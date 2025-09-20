<?php

	$soa_number 		= ( ISSET($soa_details['soa_num']) ) ? $soa_details['soa_num'] : '-';
	$soa_date 			= ( ISSET($soa_details['soa_date']) ) ? std_datepicker_format($soa_details['soa_date']) : '-';
	$vendor 			= ( ISSET($vendor_details['vendor_name']) ) ? $vendor_details['vendor_name'] : '-';

	$submission_date 	= ( ISSET($soa_details['submission_date']) ) ? date('m/d/Y', strtotime($soa_details['submission_date'])) : 'N/A';

	$week_no 			= ( ISSET($soa_details['week_num']) ) ? $soa_details['week_num'] : '-';

	$soa_amount 		= ( ISSET($soa_details['soa_amount']) ) ? number_format($soa_details['soa_amount'],2) : '-';

	$soa_document_recipient = ( ISSET($soa_details['doc_recipient']) ) ? $soa_details['doc_recipient'] : '-';

	$date_from 			= ( ISSET($soa_details['date_from']) ) ? date('m/d/Y', strtotime($soa_details['date_from'])) : '-';
	$date_to 			= ( ISSET($soa_details['date_to']) ) ? date('m/d/Y', strtotime($soa_details['date_to'])) : '-';

	$recipient 			= (ISSET($recipient_info['fname']) AND $recipient_info['lname']) ? $recipient_info['fname'].' '.$recipient_info['lname'] : '-';
	
	$po_num 			= (ISSET($po_ref_info) AND is_array($po_ref_info) AND count($po_ref_info) > 0) ? implode(', ', array_column($po_ref_info, 'po_num')) : '-';

?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>SOA Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $soa_number . '</div>';
		?>
		</div>

		<?php if($show_po): ?>
			<div class="col l3 m4 s12 p-r-md label-col">			
				<label>PO Reference</label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
	        <?php
	        	echo '<div class="div-task-values">' . $po_num . '</div>';
			?>
			</div>
		<?php else: ?>
			<div class="col l3 m4 s12 p-r-md label-col">			
				<label></label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
				<div class="div-task-values"></div>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>SOA Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $soa_date . '</div>';
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Date Submitted</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $submission_date . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Vendor</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $vendor . '</div>';
		?>
		</div>

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>SOA Amount</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
        <?php
        	echo '<div class="div-task-values">' . $soa_amount . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<?php if($show_week_period): ?>
			<div class="col l3 m4 s12 p-r-md label-col">			
				<label>Period Covered</label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
	        <?php
	        	echo '<div class="div-task-values">' . $date_from . ' - ' . $date_to . '</div>';
			?>
			</div>
		<?php else: ?>

			<div class="col l3 m4 s12 p-r-md label-col">			
				<label></label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
	        <?php
	        	echo '<div class="div-task-values"></div>';
			?>
			</div>
		<?php endif; ?>

		<?php if($show_receipt): ?>
			<div class="col l3 m4 s12 p-r-md label-col">			
				<label>Finance In-charge <!--SOA Document Recipient --> </label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
	        <?php
	        	echo '<div class="div-task-values">' . $recipient . '</div>';
			?>
			</div>
		<?php else: ?>

			<div class="col l3 m4 s12 p-r-md label-col">			
				<label></label>
			</div>

			<div class="col l3 m8 s12 valign-middle">
	        <?php
	        	echo '<div class="div-task-values"></div>';
			?>
			</div>
		<?php endif; ?>
	</div>
</div> 


<?php if($show_document_receipt): ?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label>SOA Document Recipient</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php
        	echo '<div class="div-task-values">' . $soa_document_recipient . '</div>';
		?> 
		</div>
	</div>
</div>
<?php endif; ?>

<?php 
if($show_fowarders == TRUE) 
{
?>	
	<div class="input-field m-n">
		<div class="row m-b-n p-n">
			<div class="col l3 m4 s12 p-r-md label-col">
				<label>DR Number(s)</label>
			</div>
			<div class="col l9 m8 s12 valign-middle">

			<?php
				echo '<div class="div-task-values">' . $forwarders_dr . '</div>';
			?> 
			</div>
		</div>
	</div>
<?php
}
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="<?php echo $class_label ?>">SOA File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php
			echo $task_documents[DOC_TYPE_SOA];
        ?>   

		</div>
	</div>
</div>