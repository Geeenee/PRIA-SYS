<div class="page-title">
  <div class="table-display">
    <div class="table-cell valign-middle s2"><h5>Vendors</h5></div>
  		<div class="table-cell valign-middle right-align s4">
     <?php if ($permission_add): ?>
  			<div class="inline p-l-xs">
  				<button type="button" name="add_vendor" class="btn waves-effect waves-light red lighten-2"
              data-target="modal_add_vendor"
              onclick="modal_add_vendor_init('<?php print $security; ?>', 'Add New Vendor')">
        <i class="material-icons">library_add</i>Add New
     </button>
  			</div>
     <?php endif; ?>
  		</div>
  </div>
</div>

<div class="m-md">
  <div class="pre-datatable"></div>
  <div>
    <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="tbl_vendors">
      <thead class="text-shadow-dark">
        <tr>
          <th width="15%" class="p-l-md">Vendor Code</th>
          <th width="25%" class="p-l-md">Vendor Name</th>
          <th width="20%" class="p-l-md">Account Group/s</th>
          <th width="20%" class="p-l-md">Business Center/s</th>
          <th width="12%" class="p-l-md">Created Date</th>
          <!-- <th width="15%"class="p-l-md">Status</th> -->
          <th width="8%" class="center-align col-actions p-r-md">actions</th>
        </tr>
        <tr class="table-filters">
          <td width="15%"><input type="text" name="vendor_code" class="form-filter" placeholder="Filter Vendor Code" /></td>
          <td width="25%"><input type="text" name="vendor_name" class="form-filter" placeholder="Filter Vendor Name" /></td>
          <td width="20%"><input type="text" name="account_group" class="form-filter" placeholder="Filter Account Groups/s" /></td>
          <td width="20%"><input type="text" name="business_center" class="form-filter" placeholder="Filter Business Center/s" /></td>
          <td width="12%">
            <div class="row m-b-xs">
              <div class="col">
                <input name="created_date_start" class="form-filter datepicker_start col" placeholder="Select start date">
              </div>
            </div>
            <div class="row m-b-n">
              <div class="col">
                <input name="created_date_end" class="form-filter datepicker_end col" placeholder="Select end date">
              </div>
            </div>
          </td>
          <!-- <td width="15%" class="valign-middle p-t-xs">
            <select name="status" class="form-filter selectize" placeholder="Select status">
              <option></option>
              <option value="Yes">Active</option>
              <option value="No">Inactive</option>
            </select>
          </td> -->
          <td width="8%" class="center-align table-actions">
            <a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Submit" data-position="top" data-delay="50">
            <i class="material-icons">search</i></a>
            <a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50">
            <i class="material-icons">find_replace</i></a>
          </td>
        </tr>
      </thead>
    </table>
  </div>
</div>
