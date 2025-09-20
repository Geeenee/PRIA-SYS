<div class="page-title">
  <div class="table-display">
    <div class="table-cell valign-middle s2"><h5>Aging Report</h5></div>
      <div class="table-cell valign-middle right-align s4">
     
      </div>
  </div>
</div>

<div class="m-sm">
  <div class="row">
    <div class="col s12 m12 l3">
      <div class="card red lighten-1 m-sm">
        <div class="card-content p-b-n">
          <div class="row">
            <div class="col s8 p-n font-md white-text"><b>Total APVs Delayed</b></div>
            <div class="col s4 right-align"><p class="flow-text white-text font-md"><b><?php echo $counter; ?></b></p></div>
          </div>
          <div class="card-action p-sm p-l-n">
            <a href="#" class="white-text font-md"><!-- 30 days from APV date --></a>
          </div>
        </div>
      </div>
    </div>
    <div class="col s12 m12 l9">
      <form id="aging_report_form">
        <div class="card grey lighten-5 m-sm p-md">
          <div class="form-layout-1">
            <div class="row m-n p-n">
              <div class="col s12 m6 l2">
                <div class="input-field">
                  <label>Account Group:</label>
                  <select id="account_group" name="account_group" class="selectize select-account_group" placeholder="Select Account Group" data-parsley-required="true">
                    <option value="<?php echo SELECT_ALL; ?>">All</option>
                    <?php foreach ($account_groups as $account_group): ?>
                    <option value="<?php echo $account_group['account_group_code']; ?>"><?php echo $account_group['account_group_name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col s12 m6 l3">
                <div class="input-field">
                  <label>Business Center:</label>
                  <select id="business_center" name="business_center" class="selectize select-business_center" placeholder="Select Business Center" data-parsley-required="true">
                    <option value="<?php echo SELECT_ALL; ?>">All</option>
                    <?php foreach($organizations as $organization): ?>
                    <option value="<?php echo $organization['value']; ?>"><?php echo $organization['text']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col s12 m6 l2">
                <div class="input-field">
                  <label>Date From:</label>
                  <input type="text" class="datepicker_start" data-date-format='yy-mm-dd' id="date_from" name="date_from" value="" placeholder="From">
                </div>
              </div>

              <div class="col s12 m6 l2">
                <div class="input-field">
                  <label>Date To:</label>
                  <input type="text" class="datepicker_end" data-date-format='yy-mm-dd' id="date_to" name="date_to" value="" placeholder="To">
                </div>
              </div>

              <div class="col s12 m6 l2">
                <div class="input-field">
                  <label>Status:</label>
                  <select id="apv_status" name="apv_status" class="selectize select-apv_status" placeholder="Select Status" data-parsley-required="true">
                    <option value="<?php echo SELECT_ALL; ?>">All</option>
                    <?php foreach($apv_statuses as $apv_status):?>
                    <option value="<?php echo $apv_status['apv_status_id']; ?>"><?php echo $apv_status['apv_status_name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col s12 m6 l1">
                <div class="input-field">
                    <label>&nbsp;</label>
                    <a href="javascript:;" id="search" class="btn-floating" ><i class="red material-icons">search</i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card grey lighten-5 m-md p-sm">
    <div style="height:30%;width:100%;overflow:auto;">
      <div class="pre-datatable"></div>
      <div>
        <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="tbl_aging_report">
          <thead class="text-shadow-dark">
            <tr>
              <th width="9%" class="p-l-md">AG</th>
              <th width="8%" class="p-l-md">BC</th>
              <th width="5%" class="p-l-md">Vendor</th>
              <th width="9%" class="p-l-md">Ref Number</th>
              <th width="8%" class="p-l-md">Reference Date</th>
              <th width="9%" class="p-l-md">APV</th>
              <th width="8%" class="p-l-md">APV Date</th>
              <th width="9%" class="p-l-md">CV</th>
              <th width="8%" class="p-l-md">Due Date</th>
              <th width="8%" class="p-l-md">Days Delayed</th>
              <th width="16%" class="p-l-md">Status</th>
              <th width="8%" class="p-l-md">Timeliness</th>
              <td width="8%" class="center-align table-actions">
                <a href="javascript:;" class="tooltipped filter-submit" data-tooltip="Submit" data-position="top" data-delay="50">
                <i class="material-icons white-text">search</i></a>
                <a href="javascript:;" class="tooltipped filter-cancel" data-tooltip="Reset" data-position="top" data-delay="50">
                <i class="material-icons white-text">find_replace</i></a>
              </td>
            </tr>
            <tr class="table-filters">
              <td width="9%"><input type="text" name="account_group_name" class="form-filter" placeholder="AG" /></td>
              <td width="8%"><input type="text" name="org_name" class="form-filter" placeholder="BC" /></td>
              <td width="5%"><input type="text" name="vendor_name" class="form-filter" placeholder="Vendor" /></td>
              <td width="9%"><input type="text" name="reference_num" class="form-filter" placeholder="Ref Number" /></td>
              <td width="8%"><input type="text" name="reference_date" class="form-filter" placeholder="Reference Date" /></td>
              <td width="9%"><input type="text" name="apv_num" class="form-filter" placeholder="APV" /></td>
              <td width="8%"><input type="text" name="apv_date" class="form-filter" placeholder="APV Date" /></td>
              <td width="9%"><input type="text" name="cv_num" class="form-filter" placeholder="CV" /></td>
              <td width="8%"><input type="text" name="due_date" class="form-filter" placeholder="Due Date" /></td>
              <td width="8%"><input type="text" name="days_delayed_convert" class="form-filter" placeholder="Days Delayed" /></td>
              <td width="16%"><input type="text" name="apv_status_name" class="form-filter" placeholder="Status" /></td>
              <td width="8%"><input type="text" name="timeliness" class="form-filter" placeholder="Timeliness" /></td>
              <td width="8%"></td>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>