<?php

	$po_num			= ( ISSET($po_details['po_num']) ) ? ($po_details['po_num']) : '';
	
	$dr_num		= ( ISSET($delivery_receipt_details['dr_num']) ) ? ($delivery_receipt_details['dr_num']) : '';
	$dr_date    = ( ISSET($delivery_receipt_details['dr_date']) ) ? std_datepicker_format($delivery_receipt_details['dr_date']) : '';
	$dr_amount 	= ( ISSET($delivery_receipt_details['dr_amount']) ) ? $delivery_receipt_details['dr_amount'] : '';

	$org_code 	= ( ISSET($delivery_receipt_details['org_code']) ) ? $delivery_receipt_details['org_code'] : $po_org_code;
	$site_id 	= ( ISSET($delivery_receipt_details['site_id']) ) ? $delivery_receipt_details['site_id'] : '';
	$dr_recipient_id	= ( ISSET($delivery_receipt_details['dr_recipient_id']) ) ? $delivery_receipt_details['dr_recipient_id'] : '';
	
	$last_po_delivery	= (ISSET($delivery_receipt_details) AND $delivery_receipt_details['last_dr_flag'] == ENUM_YES) ? 'Yes' : 'No';

	$last_dr_flag		= ( ISSET($delivery_receipt_details['last_dr_flag']) ) ? $delivery_receipt_details['last_dr_flag'] : '';

	$location_req		= (ISSET($location_required) AND !EMPTY($location_required))? "required": "";
	$location_pars		= (ISSET($location_required) AND !EMPTY($location_required))? "true": "false";
?>
<div class="input-field m-n">
	<input type="hidden" name="action_dr_num" value="<?php echo $dr_num; ?>" placeholder="">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php //echo $class_label ?>"><b>PO Number</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $po_num ?></div>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class=""><b>Delivered to:</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"></div>
		</div>
	</div>
</div>
<!-- <div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php //echo $class_label ?>"><b>Remaining Balance</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"><?php //echo number_format($po_details['remaining_amount'], DECIMAL_PLACES, ".", ","); ?></div>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class=""><b>Delivered to:</b></label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values"></div>
		</div>
	</div>
</div> -->
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>">DR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">	
			<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$dr_num</div>
EOS
            : 
			<<<EOS
				<input type="text" name="dr_num" id="dr_num" placeholder="Enter DR Number" data-parsley-required="true" value="$dr_num"/>
EOS;
        ?>
    	</div>

    	<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Business/ Support Center</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">

				<?php if($view){ ?>
					<?php foreach ($support_centers as $sup_center): ?>
					<?php 	echo ($org_code == $sup_center['org_code']) ? "<div class='div-task-values'>".$sup_center['name']."</div>" : ''; ?>
					<?php endforeach; ?>
				
				<?php }else{ ?> 
					<select name="support_center" id="support_center" class="selectize" required="" data-parsley-required="true" placeholder="Select Business/ Support Center">
						<option value=""></option>
					<?php foreach ($support_centers as $sup_center): ?>
						
							<option  <?php echo ($org_code == $sup_center['org_code'] OR count($support_centers) == 1) ? 'selected' : ''; ?>    value="<?php echo $sup_center['org_code']; ?>"><?php echo $sup_center['name']; ?></option>
					<?php endforeach; ?>
					</select>
				<?php } ?>
		</div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">DR Date</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$dr_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker" name="dr_date" id="dr_date" placeholder="Enter DR Date" data-parsley-required="true" value="$dr_date" data-max-date="0"/>
EOS;
        ?>
		</div>

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $location_req ?>">Location</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
			<?php if($view){ ?>
				<?php foreach ($locations as $location): ?>
				<?php 	echo ($site_id == $location['site_id']) ? '<div class="font-md">'.$location['official_store_name'].'</div>' : ''; ?>
				<?php endforeach; ?>
				<?php echo (EMPTY($site_id))? "N/A": ""; ?>
			
			<?php }else{ ?>

			<select name="location" id="location" class="selectize" data-parsley-required="<?php echo $location_pars; ?>" <?php echo $location_req; ?>>
				<option value=""></option>
				<?php foreach ($locations as $location): ?>
					<option <?php echo ($site_id == $location['site_id']) ? 'selected' : ''; ?> value="<?php echo $location['site_id']; ?>"><?php echo $location['official_store_name'] ?></option>	
				<?php endforeach; ?>
			</select>

			<?php } ?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<!-- <div class="col l3 m4 s12 label-col p-r-md"> -->			
			<!-- <label class="<?php echo $class_label ?>">Total Amount</label> -->
		<!-- </div> -->

		<!-- <div class="col l3 m8 s12 valign-middle"> -->
			<?php
/* 			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$dr_amount</div>
EOS
            : 
			<<<EOS
				<input type="text" name="dr_amount" id="dr_amount" placeholder="Enter Total Amount" data-parsley-required="true" value="$dr_amount"/>
EOS; */
        ?>
		<!-- </div> -->

		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">PIC for GR</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">

			<?php 
				if($view){ 
						foreach ($dr_document_recipients as $dr_document_recipient):
								//$fullname = (ISSET($dr_document_recipient['fname']) AND $dr_document_recipient['lname']) ? $dr_document_recipient['fname'].' '.$dr_document_recipient['lname'] : ''; 
								
								echo ($dr_recipient_id == $dr_document_recipient['user_id']) ? '<div class="font-md">'.$dr_document_recipient['fullname'].'</div>' : ''; 
						endforeach; 
				}else{ 
			?>

			<select name="dr_document_recipient" id="dr_document_recipient" class="selectize" data-parsley-required="true" required="" placeholder="Select PIC for GR">
				<option value=""></option>

				<?php foreach ($dr_document_recipients as $dr_document_recipient): ?>

					<?php //$fullname = (ISSET($dr_document_recipient['fname']) AND $dr_document_recipient['lname']) ? $dr_document_recipient['fname'].' '.$dr_document_recipient['lname'] : ''; ?>

					<option <?php echo ($dr_recipient_id == $dr_document_recipient['user_id'] OR COUNT($dr_document_recipients) == 1) ? 'selected' : ''; ?> value="<?php echo $dr_document_recipient['user_id'] ?>"><?php echo $dr_document_recipient['fullname'] ?></option>	
				<?php endforeach; ?>
			</select>

			<?php } ?>
		</div>
	</div>
</div>


<!-- <div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m5 s5 right-align valign-middle p-r-md">			
			<label class="<?php //echo $class_label ?>">Last PO Delivery</label>
		</div> -->

		<?php
// 			echo ($view)
//             ? 
//             <<<EOS
//                 <div class="div-task-values">$last_po_delivery</div>
// EOS
//             : 
// 			<<<EOS
// 				<div class="input-field m-n">
// 					<input type="checkbox" class="filled-in" name="po_deliveries" id="po_deliveries" value="$enum_yes" $checked/>
// 					<label for="po_deliveries">Checking this box will signify completion of all PO deliveries</label>
// 				</div>
// EOS;
        ?>
	<!-- </div>
</div> -->

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">Last Delivery?</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
			<div class="input-field m-n">
			<?php
			if($view){
            ?>
                <div class="div-task-values"><?php echo ($last_dr_flag == ENUM_YES) ? 'Yes ': 'No'; ?></div>
            <?php
            }else{
            ?>
				<input type="checkbox" class="filled-in" name="doc_dr_deliveries" id="doc_dr_deliveries" value="<?php echo ENUM_YES ?>" <?php echo ($last_dr_flag == ENUM_YES) ? 'checked ': ''; ?>/>

				<label for="doc_dr_deliveries">Checking this box will signify completion of all deliveries</label>
			<?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label class="<?php echo $class_label ?>">DR File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php 
			echo $task_documents[DOC_TYPE_DOC_DR];
        ?>   

		</div>
	</div>
</div>