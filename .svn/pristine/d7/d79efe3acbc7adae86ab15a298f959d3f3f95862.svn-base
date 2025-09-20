<div class="view-content p-md m-b-lg">
	<div class="flat-tbl">
		<div style="height:30%;width:100%;overflow:auto;">
			<form id="dr_form_check">
				<input data-target="modal_cancel_dr" class="btn" id="trigger_btn" type="hidden"   name="trigger_btn" placeholder="" onclick="modal_cancel_dr_init()" >
				<input data-target="modal_add_soa" class="btn" id="trigger_soa_btn" type="hidden" name="trigger_soa_btn" placeholder="" onclick="modal_add_soa_init()" >


				<table id="tbl_drs" cellpadding="0" cellspacing="0" class="table table-default table-layout-auto highlight">
					<thead>
						<tr>
							<th width="4%">
								<input class='check_all_dr' type='checkbox' id="check_all_dr" onclick="Dr.selectAll('dr')"/><label for="check_all_dr"></label>
							</th>
							<th width="8%">Date</th>
							<th width="10%">Business Center</th>
							<th width="10%">Reference No.</th>
							<th width="10%">Vendor</th>
							<th width="10%">Cost Center</th>
							<th width="8%">SOA Number</th>
							<th width="10%">SOA Status</th>
							<th width="10%">Remarks</th>
							<th width="10%">Status</th>
							<th width="10%">Action</th>
						</tr>

						<tr class="table-filters">
							<td width="4%"><input type="checkbox" name="batch_flag" value="Y" /></td>
							<td width="8%"><input type="text" name="date" class="form-filter datepicker" placeholder="Search date"/></td>
							<td width="10%"><input type="text" name="business_center" class="form-filter" placeholder="Search business center"/></td>
							<td width="10%"><input type="text" name="ref_no" class="form-filter" placeholder="Search reference no." value="<?php echo $keyword; ?>"/></td>
							<td width="10%"><input type="text" name="vendor" class="form-filter" placeholder="Search vendor" /></td>
							<td width="10%"><input type="text" name="official_store_name" class="form-filter" placeholder="Search cost center"/></td>
							<td width="8%"><input type="text" name="soa_num" class="form-filter" placeholder="Search SOA number"/></td>
							<td width="10%"><input type="text" name="soa_status" class="form-filter" placeholder="Search SOA status"/></td>
							<td width="10%"><input type="text" name="dr_remarks" class="form-filter" placeholder="Search remarks"/></td>
							<!-- <td width="10%"><input type="text" name="status" class="form-filter" placeholder="Search Status"/></td> -->
							<td width="10%">
								<select class="form-filter plain" name="dr_status_name" placeholder="Search status">
					                <option value=""></option>
					            <?php if(COUNT($delivery_statuses) > 0):
					            	foreach ($delivery_statuses AS $key => $delivery_status): ?>
					            	<option value="<?php echo $delivery_status['dr_status_code']; ?>"><?php echo $delivery_status['dr_status_name']; ?></option>
					            <?php endforeach;
					        	endif; ?>
								</select>
							</td>
							<td width="10%" class="table-actions">
								<a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Search" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
								<a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
							</td>
						</tr>
					</thead>
				</table>
			</form>
		</div>
	</div>
</div>