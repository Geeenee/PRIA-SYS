<div class="page-title">
	<div class="table-display">
		<div class="table-cell valign-middle s6"><h5>Organizations</h5></div>
		<div class="table-cell valign-middle right-align s6">
			<button class="btn waves-effect waves-light" type="button" id="refresh_btn"><i class="material-icons">refresh</i>Refresh</button>
			<div class="inline p-l-xs">
				<?php 
					if( $add_per ) :
				?>
				<button data-target="modal_organizations" class="btn waves-effect waves-light green lighten-2 modal_organizations_trigger" name="add_organization" onclick="modal_organizations_init()" type="button"><i class="material-icons">library_add</i>Create New</button>
				<?php 
					endif;
				?>
			</div>
		</div>
	</div>
</div>
<div class="pre-datatable"></div>
<div class="m-md">
  <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="organizations_table">
  <thead>
	<tr>
	  <th width="25%">Organization</th>
	  <th width="25%">Parent Organization</th>
	  <th width="20%">Website</th>
	  <th width="20%">Email</th>
	  <th width="10%" class="text-center">Actions</th>
	</tr>
	<tr class="table-filters">
          <td width="25%" ><input name="A-name" class="form-filter"/></td>
          <td width="25%" ><input name="parent_name" class="form-filter"/></td>
          <td width="20%" ><input name="A-website" class="number form-filter"/></td>
          <td width="20%" >
          		<input name="A-email" class="number form-filter"/>
          </td>
          <td width="10%" class="table-actions">
            <a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Submit" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
            <a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
          </td>
        </tr>
  </thead>
  <tbody>
  </tbody>
  </table>
</div>