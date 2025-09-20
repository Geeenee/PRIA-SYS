<div class="page-title">
	<div class="table-display">
		<div class="table-cell valign-middle s12"><h5>Audit Trail</h5></div>
		<div class="table-cell valign-middle right-align s12">
			
		</div>
	</div>
	<div class="row m-b-n right-align col s12">
		<div class="col s5"><label class="filter-label">Filter by System</label></div>
		<div class="input-field filter-field col s3 m-t-n">
			<select name="filter_audit_log" id="filter_audit_log" class="material-select" placeholder="Select System">
				<option value="0">All</option>
				<?php foreach($systems as $system): ?>
				<option value="<?php echo $system['system_code'] ?>"><?php echo $system['system_name'] ?></option>
				<?php endforeach; ?>
		   </select>
		</div>
		<div class="col s2 p-r-n">
			<button class="btn waves-effect waves-light" type="button" id="refresh_btn"><i class="material-icons">refresh</i>Refresh</button>	
		</div>
		<div class="col s2 m-l-n p-r-lg">
			<button type="button" class="btn waves-effect waves-light grey darken-1 popmodal-dropdown"
				data-ondocumentclick-close='false' data-ondocumentclick-close-prevent='e' data-placement='leftCenter' data-showclose-but='false' data-popmodal-bind='#download_div'
			>
				<i class="material-icons">file_download</i> Download
			</button>
		</div>
	</div>
</div>
<div class="m-md">
	<div class="pre-datatable"></div>
	<div>
		<table cellpadding="0" cellspacing="0" class="table table-default table-layout-fixed" id="audit_log_table">
			<thead>
				<tr>
					<th width="20%">User</th>
					<th width="12%">Module</th>
					<th width="30%">Activity</th>
					<th width="15%">Date</th>
					<th width="15%">I.P</th>
					<th width="8%" class="text-center">Actions</th>
				</tr>
				<tr class="table-filters">
		          <td width="20%" ><input name="fullname" class="form-filter"/></td>
		          <td width="12%" ><input name="C-module_name" class="form-filter"/></td>
		          <td width="30%" ><input name="A-activity" class="number form-filter"/></td>
		          <td width="15%" >
		          		<input name="activity_date" class="number form-filter"/>
		          </td>
		           <td width="15%" >
		           	<input name="A-ip_address" class="number form-filter"/>
		           </td>
		          <td width="8%" class="table-actions">
		            <a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Submit" data-position="top" data-delay="50"><i class="material-icons">search</i></a>
		            <a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50"><i class="material-icons">find_replace</i></a>
		          </td>
		        </tr>
			</thead>
		</table>
	</div>
</div>

<div class="none">
	<div id="download_div">
  		<form id="download_form" class="form-basic">
			<p class="p-n m-b-md font-bold font-sm">Extra Filter for downloading Audit Trail.</p>
			<div class="row">
				<p class="p-n m-b-md font-bold font-sm">Audit Date</p>
				<div class="col s6">
					<div class="input-field">
						
						<input type="text" data-parsley-date="true" class="date datepicker_start" value="" name="date_from" placeholder="mm/dd/yyyy" id="date_from_audit_log"  />
						<label for="date_from_audit_log" class="active">From</label>
					</div>
					
				</div>
				<div class="col s6">
					<div class="input-field">
						
						<input type="text" data-parsley-date="true" class="date datepicker_end" value="" name="date_to" placeholder="mm/dd/yyyy" id="date_to_audit_log"  />
						<label for="date_to_audit_log" class="active">To</label>
					</div>
					
				</div>
			</div>
			<div class="popModal_footer">
				<button type="button" data-report="<?php echo base64_url_encode( REPORT_TYPE_EXCEL ) ?>" class="btn btn_download green darken-3 waves-effect waves-light" data-popmodal-but="ok" id="excel_btn"><i class="material-icons">file_download</i>Excel</button>
				<button type="button" class="btn-flat btn-small" data-popmodal-but="cancel">Cancel</button>
			</div>
	  </form>
	</div>
</div>