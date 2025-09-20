<div class="view-content p-md m-b-lg">
	<div class="flat-tbl">
		<div class="panel p-b-md table-scroll">
			<div style="height:30%;width:100%;overflow:auto;">
				<table id="tbl_contracts" cellpadding="0" cellspacing="0" class="table table-default table-layout-auto highlight">
					<thead>
						<tr>
							<th width="10%">Reference No.</th>
							<th width="14%">Business Center</th>
							<th width="12%">Store Name</th>
							<th width="14%">Lessor</th>
							<th width="10%">Payment Terms</th>
							<th width="10%">Expiration Date</th>
							<th width="10%">Ref Contract</th>
							<th width="10%">Status</th>
							<th width="10%">Action</th>
						</tr>

						<tr class="table-filters">
							<td width="10%"><input type="text" name="ref_no" class="form-filter" placeholder="Search reference no." value="<?php echo $keyword; ?>"/></td>
							<td width="14%"><input type="text" name="business_center_name" class="form-filter" placeholder="Search business center"/></td>
							<td width="12%"><input type="text" name="store_name" class="form-filter" placeholder="Search store name"/></td>
							<td width="14%"><input type="text" name="lessor" class="form-filter" placeholder="Search lessor"/></td>
							<td width="10%"><input type="text" name="payment_terms" class="form-filter" placeholder="Search payment terms" /></td>
							<td width="10%"><input type="text" name="exp_date" class="form-filter datepicker" placeholder="Search expiration date" /></td>
							<td width="10%"><input type="text" name="ref_contract" class="form-filter" placeholder="Search ref contract"/></td>
							<td width="10%">
								<select class="form-filter selectize" name="F-contract_status_id" placeholder="">
					                <option value="">Search contract status</option>
									<?php if(COUNT($contract_statuses) > 0)
										{
											foreach ($contract_statuses AS $key => $contract_status) 
											{
									?>
												<option value="<?php echo $contract_status['contract_status_id']; ?>"><?php echo $contract_status['contract_status_name']; ?></option>
									<?php 
											}
										}
									?>
								</select>
							</td>
							<td width="10%" class="table-actions">
								<a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Search" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
								<a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
							</td>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>