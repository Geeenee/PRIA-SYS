<form id="filter_form" name="filter_form">
<div class="p-md m-t-lg">
	<div class="form-layout-1">
		<div class="row">
			<div class="col s12">
				<h5>Filter</h5>
				
				<div class="input-field">
					<label>Keyword</label>
					<input type="text" class="filter-input" name="filter-keyword" id="filter-keyword" value="<?php echo $keyword; ?>" placeholder="Search" />
				</div>
				
				<div class="input-field">
					<label>Assigned To / Uploaded By</label>
					<select name="filter-assign-to[]" id="filter-assign-to" class="selectize" multiple>
						<option value="">Select Users</option>
						<?php if(ISSET($core_users) AND COUNT($core_users) > 0):
							foreach($core_users AS $key => $user): ?>
							<option value="<?php echo $user['user_id']; ?>"><?php echo $user['full_name']; ?></option>
						<?php endforeach;
						endif; ?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col s12">
				<div class="input-field">
					<label>Status</label>
					<select name="filter-status[]" id="filter-status" class="selectize" multiple>
						<option value="">Select Status</option>
						<option value="0">Pending</option>
						<option value="1">Ongoing</option>
						<option value="2">Completed/Approved</option>
						<option value="3">Returned</option>
					</select>
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="col s6 p-l-n">
				<div class="input-field">
					<button type="button" class="btn-flat p-n" id="reset_btn">Clear Filter</button>
				</div>
			</div>
			<div class="col s6 p-r-n">
				<div class="input-field right-align">
					<button type="button" class="btn green" id="filter_btn">Filter</button>
				</div>
			</div>
		</div>

		<?php echo $extra_filters; ?>
	</div>
</div>
</form>