<?php 
	//value to be canceled
	$dr_nums = array_column($delivery_rec, 'dr_num');
	$dr_grs  = array_column($delivery_rec, 'dr_gr_id');
	$drs = implode(", ",$dr_nums);
	$dr_grs = implode(", ",$dr_grs);
?>

<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="btn_action" value="save">
<input type="hidden" name="dr_rec" value="<?php echo $dr_grs; ?>" >
<div class="form-basic p-lg white">

	<div class="row m-b-lg">
	   
	    <div class="col s12">
	    	<div class="input-field">
	    		<h6>
	    			<b>
	    			<?php if(!EMPTY($drs)): ?>
	    			<?php echo $drs; ?>
	    			<?php else: ?>
	    			<?php echo "No DR Selected" ?>
	    			<?php endif; ?>
	    			</b>
	    		</h6>
	      		<label for="dr_num" class="active">Selected DR Number</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-lg">
	    <div class="col s12">
	    	<div class="input-field">
	    		<textarea name="remarks" id="remarks" required="required" class="materialize-textarea"></textarea>
				<label for="remarks" class="active required">Cancellation Remarks</label>
	      	</div>
	    </div>
	</div>
</div>