<?php
	
	$po_num			= ( ISSET($po_details['po_num']) ) ? ($po_details['po_num']) : '';
	$support_center	= '';

	$dr_num		= ( ISSET($delivery_receipt_details['dr_num']) ) ? ($delivery_receipt_details['dr_num']) : '';
	$dr_date    = ( ISSET($delivery_receipt_details['dr_date']) ) ? std_datepicker_format($delivery_receipt_details['dr_date']) : '';
	$dr_amount 	= ( ISSET($delivery_receipt_details['dr_amount']) ) ? $delivery_receipt_details['dr_amount'] : '';

	$org_code 	= ( ISSET($delivery_receipt_details['org_code']) ) ? $delivery_receipt_details['org_code'] : '';
	$site_id 	= ( ISSET($delivery_receipt_details['site_id']) ) ? $delivery_receipt_details['site_id'] : '';
	$dr_recipient_id 	= ( ISSET($delivery_receipt_details['dr_recipient_id']) ) ? $delivery_receipt_details['dr_recipient_id'] : '';

	$business_center = $org_code;
	$last_po_delivery = ($delivery_receipt_details['last_dr_flag'] == ENUM_YES) ? 'Yes' : 'No';

	$location_req	= (ISSET($location_required) AND !EMPTY($location_required))? "required": "";
	$location_pars	= (ISSET($location_required) AND !EMPTY($location_required))? "true": "false";
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>"><b>PO Number</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $po_num ?></div>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class=""><b>Delivered to</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"></div>
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">DR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">	
			<div class="div-task-values"><?php echo $dr_num ?></div>
		</div>
	
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Business/ Support Center</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">

			<?php foreach ($support_centers as $support_cent): ?>
				<?php IF($support_cent['org_code'] == $org_code ){ ?> 
			<?php 	
					echo "<div class='div-task-values'>".$support_cent['name']."</div>";
				} 
			?>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">DR Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<div class="div-task-values"><?php echo $dr_date ?></div>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $location_required ?>">Location</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<div class="div-task-values"><?php echo (ISSET($site_info['official_store_name']) AND !EMPTY($site_info['official_store_name']))? $site_info['official_store_name']: "N/A" ?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<!-- <div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Total Amount</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<div class="div-task-values"><?php echo $dr_amount ?></div>
		</div> -->

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">PIC for GR</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<?php foreach ($recipient_info as $recipient_info): ?>
				<?php $fullname = (ISSET($recipient_info['fname']) AND $recipient_info['lname']) ? $recipient_info['fname'].' '.$recipient_info['lname'] : ''; ?>
			<?php 	if($recipient_info['user_id'] == $recipient_id){
					echo "<div class='div-task-values'>".$fullname."</div>";
				} 
			?>
			<?php endforeach; ?>
			
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 valign-middle label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Last PO Delivery</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
			<div class="div-task-values"><?php echo $last_po_delivery ?></div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 valign-middle label-col p-r-md">
			<label class="<?php echo $class_label ?>">DR File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php 
			echo $task_documents[DOC_TYPE_DOC_DR];
        ?>   

		</div>
	</div>
</div>