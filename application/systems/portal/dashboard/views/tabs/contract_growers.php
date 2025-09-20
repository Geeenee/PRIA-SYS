<div class="flat-tbl">
	<table id="tbl_dashboard_cg" cellpadding="0" cellspacing="0" class="table table-default table-layout-auto scroll dataTable no-footer highlight">
		<thead>
			<tr>
				<th>Created Date</th>
				<th>Cycle #</th>
				<th>Business Center</th>
				<th>Vendor Name</th>
				<th>Internal Order</th>
				<th>Clean-Up Date</th>
				<!-- <th>Harvest Report Approval Date</th>
				<th>Live Sales Report Upload Date</th> -->
				<th>FHR Upload Date</th>
				<th>FHR Submission Date</th>
				<th>FHR Approval Date</th>
				<th>FHR Number</th>
				<th>APV Number</th>
				<th>APV Date</th>
				<th>APV Amount</th>
				<th>CV Number</th>
				<th>CV Amount</th>
				<th>Particulars</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			<tr class="table-filters">
				<td><input type="text" name="created_date" class="form-filter" /></td>
				<td><input type="text" name="cycle" class="form-filter" placeholder="Search cycle #"/></td>
				<td><input type="text" name="business_center_name" class="form-filter" placeholder="Search business center"/></td>
				<td><input type="text" name="vendor_name" class="form-filter" placeholder="Search vendor name"/></td>
				<td><input type="text" name="reference_number" class="form-filter" placeholder="Search internal order"/></td>
				<td><input type="text" name="clean_up_date" class="form-filter" placeholder="Search clean-up date"/></td>
				<!-- <td><input type="text" name="harvest_approval_date" class="form-filter" placeholder="Search harvest report approval date"/></td>
				<td><input type="text" name="live_sales_upload_date" class="form-filter" placeholder="Search live sales report upload date"/></td> -->
				<td><input type="text" name="fhr_upload_date" class="form-filter" placeholder="Search FHR upload date"/></td>
				<td><input type="text" name="fhr_submission_date" class="form-filter" placeholder="Search FHR submission date"/></td>
				<td><input type="text" name="fhr_approval_date" class="form-filter" placeholder="Search FHR approval date"/></td>
				<td><input type="text" name="fhr_number" class="form-filter" placeholder="Search FHR number"/></td>
				<td><input type="text" name="apv_number" class="form-filter" placeholder="Search APV number"/></td>
				<td><input type="text" name="apv_date" class="form-filter" placeholder="Search APV date"/></td>
				<td><input type="text" name="apv_amount" class="form-filter" placeholder="Search APV amount"/></td>
				<td><input type="text" name="check_number" class="form-filter" placeholder="Search cv number"/></td>
				<td><input type="text" name="check_amount" class="form-filter" placeholder="Search cv amount"/></td>
				<td><input type="text" name="apv_particular" class="form-filter" placeholder="Search particulars"/></td>
				<!-- <td><input type="text" name="apv_status" class="form-filter" placeholder="Search status"/></td> -->
				<td>
					<select name="apv_statuses_id" placeholder="Search status" class="form-filter plain">
							<option value="">Search status</option>
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