<input type="hidden" name="tab_module" value="<?php echo $tab_module ?>">
<input type="hidden" name="btn_action" value="save">
<div class="form-basic p-lg white">

	<div class="row m-b-md">
		<div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="account_group" name="account_group" class="selectize" placeholder="Select Account Group" data-parsley-required="true" data-parsley-trigger-after-failure="change">
					<option value=""></option>
					<?php foreach($account_groups as $key => $account_group){
					?>
						<option value="<?php echo $account_group['account_group_code']; ?>"><?php echo $account_group['account_group_name']; ?></option>
					<?php } ?>
				</select>
	      		<label for="account_group" class="active required">Account Group</label>
	      	</div>
	    </div>
	   
	    <div class="col s12 m12 l6 p-t-sm">
	    	<div class="input-field">
	    		<select id="purchasing_group" name="purchasing_group" class="selectize" placeholder="Select Purchasing Group" data-parsley-required="true" data-parsley-trigger-after-failure="change">
						<option value=""></option>
					<?php foreach($purchasing_groups as $key => $purchasing_group){
					?>
						<option value="<?php echo $purchasing_group['purchasing_group_code']; ?>" data-ag-code="<?php echo $purchasing_group['account_group_code']; ?>"><?php echo $purchasing_group['purchasing_group_name']; ?></option>
					<?php } ?>
				</select>
	      		<label for="purchasing_group" class="active required">Purchasing Group</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s6 m6 l6 m-t-xs">
	    	<div class="input-field">
				<input type="text" id="pr_num" name="pr_num" data-parsley-required="true">
	      		<label for="pr_num" class="active required">PR Number</label>
	      	</div>
	    </div>
	   
	    <div class="col s6 m6 l6">
	    	<div class="input-field">
	    		<select id="item" name="item" class="selectize" placeholder="Select Item Type" data-parsley-required="true" data-parsley-trigger-after-failure="change">
					<option value=""></option>
					<?php foreach($items as $key => $item){ ?>
						<option value="<?php echo $item['pr_item_code']; ?>"><?php echo $item['pr_item_code']; ?></option>
					<?php } ?>
				</select>
	      		<label for="item" class="active required">Item</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<div class="col s12 m12 l6 p-t-xs">
	    	<div class="input-field">
	    		<select id="cost_center" name="cost_center[]" multiple class="selectize" placeholder="Select Cost Center" data-parsley-required="true" data-parsley-trigger-after-failure="change">
						<option value="<?php echo NULL ?>"></option>
					<?php //foreach($sites as $key => $site){ ?>
						<!--option value="<?php //echo $site['cost_center_code']; ?>"><?php //echo '['.$site['cost_center_code'].']-'.$site['official_store_name']; ?></option-->
					<?php //} ?>
				</select>
	      		<label for="cost_center" class="active required">Cost Center</label>
	      	</div>
	    </div>
	   
	    <div class="col s12 m12 l6 p-t-xs">
	    	<div class="input-field">
	    		<select id="gl_account" name="gl_account" class="selectize" placeholder="Select GL Accounts"  data-parsley-trigger-after-failure="change">
						<option value="<?php echo NULL ?>"></option>
					<?php foreach($gl_accounts as $key => $gl_account){ ?>
						<option value="<?php echo $gl_account['gl_account_code']; ?>"><?php echo  '['.$gl_account['gl_account_code'].']-'.$gl_account['gl_account_name']; ?></option>
					<?php } ?>
				</select>
	      		<label for="gl_account" class="active">GL Account</label>
	      	</div>
	    </div>
	</div>

	<div class="row m-b-md">
		<!-- 
		<div class="col s12">
	    	<div class="input-field">
	    		<select id="requested_by" class="selectize" name="requested_by" placeholder="Select Requested By">
						<option value=""></option>
					<?php //foreach($requested_by as $key => $requested){

						//$fullname = (ISSET($requested['fname']) AND $requested['lname']) ? $requested['fname'].' '.$requested['lname'] : '';
					?>
						<option value="<?php //echo $requested['user_id'] ?>"><?php //echo $fullname ?></option>
					<?php //} ?>
				</select>
	      		<label for="requested_by" class="active required">Requested by</label>
	      	</div>
	    </div> -->

	    <div class="col s12 m12 l12">
	    	<div class="input-field">
				<input type="text" name="requested_by" value="" id="requested_by" data-parsley-required="true">
	      		<label for="requested_by" class="active required">Requested by</label>
	      	</div>
	    </div>
	</div>
</div>