<div class="view-content p-md">
	<div class="flat-tbl">
		<table id="tbl_apvs" cellpadding="0" cellspacing="0" class="table table-default table-layout-auto highlight">
			<thead>
				<tr>
					<th width="8%">AG Code</th>
					<th width="8%">Vendor</th>
					<th width="10%">Reference No.</th>
					<th width="10%">APV Number</th>
					<th width="8%">APV Date</th>
					<th width="10%">APV Amount</th>
					<th width="10%">CV Number</th>
					<th width="10%">CV Amount</th>
					<th width="10%">Particulars</th>
					<th width="8%">Status</th>
					<th width="8%">Action</th>
				</tr>

				<tr class="table-filters">
					<td width="8%"><input type="text" name="account_group_code" class="form-filter" placeholder="Search AG code"/></td>
					<td width="8%"><input type="text" name="vendor_name" class="form-filter" placeholder="Search vendor name"/></td>
					<td width="10%"><input type="text" name="reference_num" class="form-filter" placeholder="Search reference no."/></td>
					<td width="10%"><input type="text" name="apv_num" class="form-filter" placeholder="Search APV number" /></td>
					<td width="8%"><input type="text" name="apv_date" class="form-filter datepicker" placeholder="Search APV date" /></td>
					<td width="10%"><input type="text" name="apv_amount" class="form-filter" placeholder="Search APV amount" /></td>
					<td width="10%"><input type="text" name="cv_num" class="form-filter" placeholder="Search CV number" /></td>
					<td width="10%"><input type="text" name="cv_amount" class="form-filter" placeholder="Search CV amount"/></td>
					<td width="10%"><input type="text" name="particulars" class="form-filter" placeholder="Search particulars"/></td>
					<td width="8%">
						<select class="form-filter material-select" name="apv_status_name" placeholder="Search status">
			                <option value=""></option>
			            <?php 
			            if(COUNT($param_apv_status) > 0):
			            	foreach ($param_apv_status AS $key => $apv_status): ?>
			            	<option value="<?php echo $apv_status['apv_status_id']; ?>"><?php echo $apv_status['apv_status_name']; ?></option>
			            <?php endforeach;
			        	endif; ?>
						</select>
					</td>
					<td width="8%" class="table-actions">
						<a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Search" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
						<a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
					</td>
				</tr>
			</thead>
		</table>
	</div>
</div>