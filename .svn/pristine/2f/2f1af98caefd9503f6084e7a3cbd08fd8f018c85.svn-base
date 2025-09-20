<div class="page-title">
  <div class="table-display">
    <div class="table-cell valign-middle s2"><h5>Sites</h5></div>
  		<div class="table-cell valign-middle right-align s4">
     <?php if ($permission_add){ ?>
  			<div class="inline p-l-xs">
  				<button type="button" name="add_site" class="btn waves-effect waves-light red lighten-2"
              data-target="modal_add_site"
              onclick="modal_add_site_init('<?php print $security; ?>', 'Add New Site')">
               <i class="material-icons">library_add</i>Add New
          </button>
  			</div>
      <?php }?>
  		</div>
  </div>
</div>

<div class="m-md">
  <div class="pre-datatable"></div>
  <div>
    <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="tbl_sites">
      <thead class="text-shadow-dark">
        <tr>
          <th width="15%" class="p-l-md">Site Code</th>
          <th width="15%" class="p-l-md">Site Type</th>
          <th width="35%" class="p-l-md">Site Name</th>
          <th width="15%" class="p-l-md">Cost Center Code</th>
          <th width="10%" class="p-l-md">Created Date</th>
          <th width="10%" class="center-align col-actions p-r-md">actions</th>
        </tr>
        <tr class="table-filters">
          <td width="15%"><input type="text" name="A-site_code" class="form-filter" placeholder="Site Code" /></td>
          <td width="15%"><input type="text" name="B-site_type_name" class="form-filter" placeholder="Site Type" /></td>
          <td width="35%"><input type="text" name="A-official_store_name" class="form-filter" placeholder="Site Name" /></td>
          <td width="15%"><input type="text" name="A-cost_center_code" class="form-filter" placeholder="Cost Center Code" /></td>
          <td width="10%">
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
          <td width="10%" class="center-align table-actions">
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
