<?php
	
	$dr_id 		= ISSET($dr_info['dr_gr_id']) ? $dr_info['dr_gr_id'] : '';
	$dr_num 	= ISSET($dr_info['dr_num']) ? $dr_info['dr_num'] : '';
	$dr_remarks = ISSET($dr_info['dr_remarks']) ? $dr_info['dr_remarks'] : '';
	
?>
<div class="form-basic p-lg white">
	<input type="hidden" name="dr_id" value="<?php echo $dr_id; ?>" placeholder="">
	<h4>Delivery Receipt Cancellation</h4>
	<br><br>
	<div class="row m-b-lg">
		<div class="col s12">
	    	<div class="input-field">
	      		<label for="soa_date" class="active ">Selected DR</label>
	      		<div class=""><b><?php echo $dr_num ?></b></div>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s12">
	    	<div class="input-field">
	    		<label for="soa_date" class="active">Cancellation Remarks</label>
	      		<div class=""><b><?php echo $dr_remarks ?></b></div>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s12">
	    	<div class="input-field">
	    		<textarea class="materialize-textarea" required="required" name="comments"></textarea>
				<label for="comments" class="active required">Comments</label>
	      	</div>
	    </div>
	</div>
</div>