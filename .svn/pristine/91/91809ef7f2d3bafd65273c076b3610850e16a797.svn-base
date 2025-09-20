<?php 

	$store_name        = ISSET($site_det['official_store_name']) ? $site_det['official_store_name'] : '';
	$lessor 	       = ISSET($vendor_det['vendor_name']) ? $vendor_det['vendor_name'] : '';
	
	//$contract_period   = $contract_det['recommended_date_from'].' to '.$contract_det['recommended_date_to'];
    // if($contract_det['contract_status_code'] != CONTRACT_NEW){
    $recommended_date_from      = (ISSET($renewal_contract_details['date_from']) AND $renewal_contract_details['saved_flag'] == MAINTAINER_YES) ? $renewal_contract_details['date_from'] : $renewal_contract_details['recommended_date_from'];
    
    $recommended_date_to        = (ISSET($renewal_contract_details['date_to']) AND $renewal_contract_details['saved_flag'] == MAINTAINER_YES) ? $renewal_contract_details['date_to'] : $renewal_contract_details['recommended_date_to'];

    $recommended_payment_term_code          = (ISSET($renewal_contract_details['payment_term_code']) AND $renewal_contract_details['saved_flag'] == MAINTAINER_YES) ? $renewal_contract_details['payment_term_code'] : $renewal_contract_details['recommended_payment_term_code'];
    // }else{
    //     $recommended_date_from      = ISSET($renewal_contract_details['original_start_date']) ? $renewal_contract_details['original_start_date'] : '';
    //     $recommended_date_to        = ISSET($renewal_contract_details['expiration_date']) ? $renewal_contract_details['expiration_date'] : '';
    
    //     $recommended_payment_term_code          = ISSET($renewal_contract_details['payment_term_code']) ? $renewal_contract_details['payment_term_code'] : ''; 
    // }
?>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Store Name</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $store_name; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Lessor</label>
        </div>

        <div class="col l9 m8 s12 valign-middle">
            <div class="div-task-values"><?php echo $lessor; ?></div>
        </div>

    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Contract Period From</label>
        </div>

        <div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values p-n">

                <?php
                if($view):
                ?>
                    <div class="div-task-values"><?php echo $recommended_date_from ?></div>
                <?php
                else:
                ?> 
                    <input type="text" name="contract_period_from" class="datepicker_start" value="<?php echo $recommended_date_from; ?>" placeholder=""> 
                <?php 
                endif;
                ?>
       
            </div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Contract Period To</label>
        </div>

        <div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values p-n">

                <?php
                if($view):
                ?>
                    <div class="div-task-values"><?php echo $recommended_date_to ?></div>
                <?php
                else:
                ?> 
                    <input type="text" name="contract_period_to" class="datepicker_end" value="<?php echo $recommended_date_to; ?>" placeholder="">
                <?php 
                endif;
                ?>

            </div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Payment Terms</label>
        </div>

        <div class="col l3 m8 s12 valign-middle">
            <div class="div-task-values p-n">


                <?php
                if($view):
                ?>
                    <div class="div-task-values"><?php echo $recommended_payment_term_code ?></div>
                <?php
                else:
                ?> 
                    <select id="payment_terms" class="selectize" name="payment_terms" placeholder="Select Store Name" data-parsley-required="true">
                        <option value=""></option>
                        <?php foreach($payment_terms as $payment_term): ?>
                        <option <?php echo ($payment_term['payment_term_code'] == $recommended_payment_term_code) ? 'selected' : '' ; ?> value="<?php echo $payment_term['payment_term_code'] ?>"><?php echo $payment_term['payment_term_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php 
                endif;
                ?>

            </div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">
			<label class="<?php echo $class_label ?>">Signed Contract File</label>
		</div>
		<div class="col l9 m8 s12 valign-middle">

		<?php 
			echo $task_documents[DOC_TYPE_SIGNED_RENEWAL];
        ?>   

		</div>
	</div>
</div>