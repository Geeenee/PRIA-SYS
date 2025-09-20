<input type="hidden" name="security" value="<?php echo $security ?>">
<input type="hidden" name="btn_action" value="save">
<div class="form-basic p-lg white">	
	<div class="row m-b-lg">

		<?php if(empty($info['bc_code'])): ?>
		
	    <div class="col s4">
	    	<div class="input-field">
	    		<input type="text" maxlength="45" name="bc_code" class="bc_code" <?php echo !empty($info['bc_code']) ? 'readonly="readonly"' : ''; ?> placeholder="Enter business center code" value="<?php echo isset($info['bc_code']) ? $info['bc_code'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="bc_code" class="active required">Business Center Code</label>
	      	</div>
	    </div>

	    <?php else: ?>
	    <div class="col s4 p-b-lg">
	    	<div class="input-field">
	    		<h6><b><?php echo $info['bc_code']; ?></b></h6>
	      		<label for="bc_code" class="active">Business Center Code</label>
	      	</div>
	    </div>

	    <input type="hidden" maxlength="45" name="bc_code" class="bc_code" value="<?php echo isset($info['bc_code']) ? $info['bc_code'] : ''; ?>" />

		<?php endif; ?>
	   
	    <div class="col <?php echo !empty($info['bc_code']) ? 's12' : 's8' ?>">
	    	<div class="input-field">
	    		<input type="text" maxlength="100" name="bc_name" class="bc_name" placeholder="Enter business center name" value="<?php echo isset($info['bc_name']) ? $info['bc_name'] : ''; ?>"  data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" />
	      		<label for="bc_name" class="active required">Business Center Name</label>
	      	</div>
	    </div>
	</div>
</div>