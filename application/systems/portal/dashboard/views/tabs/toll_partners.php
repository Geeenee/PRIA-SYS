<div class="flat-tbl">
	<table id="tbl_dashboard_toll_partners" cellpadding="0" cellspacing="0" class="table table-default table-layout-auto scroll dataTable highlight" style="width: 100% !important;">
		<thead>
			<tr>
				<th>Created Date</th>
				<th>Business Center</th>
				<th>Vendor Name</th>
				<th>SOA Number</th>
				<th>SOA Period Covered</th>
				<th>SOA Upload Date</th>
				<th>SOA Acknowledgement Date</th>
				<th>APV Number</th>
				<th>APV Date</th>
				<th>APV Amount</th>
				<th>CV Number</th>
				<th>CV Amount</th>
				<th>Particulars</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
			<tr class="table-filters">
				<td><input type="text" name="created_date" class="form-filter" /></td>
				<td><input type="text" name="business_center_name" class="form-filter" placeholder="Search business center"/></td>
				<td><input type="text" name="vendor_name" class="form-filter" placeholder="Search vendor name"/></td>
				<td><input type="text" name="soa_number" class="form-filter" placeholder="Search SOA number"/></td>
				<td><input type="text" name="soa_period" class="form-filter" placeholder="Search SOA week period"/></td>
				<td><input type="text" name="soa_date" class="form-filter" placeholder="Search SOA upload date"/></td>
				<td><input type="text" name="acknowledgement_date" class="form-filter" placeholder="Search SOA acknowledgement date"/></td>
				<td><input type="text" name="apv_number" class="form-filter" placeholder="Search APV number"/></td>
				<td><input type="text" name="apv_date" class="form-filter" placeholder="Search APV date"/></td>
				<td><input type="text" name="apv_amount" class="form-filter" placeholder="Search APV amount"/></td>
				<td><input type="text" name="check_number" class="form-filter" placeholder="Search check number"/></td>
				<td><input type="text" name="check_amount" class="form-filter" placeholder="Search check amount"/></td>
				<td><input type="text" name="apv_particular" class="form-filter" placeholder="Search particulars"/></td>
				<td>
					<select name="apv_statuses_id" placeholder="Search status" class="form-filter plain">
							<option value=""></option>
							<option value="NULL">For Payment Processing</option>
						<?php foreach($statuses as $status) { ?>
							<option value="<?php echo $status['apv_status_id']; ?>"><?php echo $status['apv_status_name']; ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="table-actions">
					<a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Search" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
					<a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
				</td>
			</tr>
		</thead>
	</table>
</div>