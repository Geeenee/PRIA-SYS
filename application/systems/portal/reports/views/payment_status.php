<div class="page-title">
  <div class="table-display">
    <div class="table-cell valign-middle s2"><h5>Payment Status</h5></div>
      <div class="table-cell valign-middle right-align s4">
     
      </div>
  </div>
</div>

<div class="m-sm">
  <div class="row">
    <form id="payment_status_form">
      <div class="col s12 m12 l12">
        <div class="card grey lighten-5 m-sm p-md">
          <div class="form-layout-1">
            <div class="row m-n p-n">
              <div class="col s12 m12 l3">
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

              <div class="col s12 m12 l2">
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

              <div class="col s12 m12 l3">
                <div class="input-field">
                  <label>Reference A:</label>
                  <select id="reference_a" name="reference_a" class="selectize select-reference_a" placeholder="Select Reference A" data-parsley-required="true">
                    <option value="<?php echo SELECT_ALL; ?>">All</option>
                    <?php //foreach($reference_titles as $key => $reference_a): ?>
                    <!-- <option value="<?php //echo $key; ?>"><?php //echo $reference_a; ?></option> -->
                    <?php //endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col s12 m12 l3">
                <div class="input-field">
                  <label>Reference B:</label>
                  <select id="reference_b" name="reference_b" class="selectize select-reference_b" placeholder="Select Reference B" data-parsley-required="true">
                    <option value="<?php echo SELECT_ALL; ?>">All</option>
                    <?php //foreach($reference_b as $key => $ref_b): ?>
                    <!-- <option value="<?php //echo $key; ?>"><?php //echo $ref_b; ?></option> -->
                    <?php //endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col s12 m12 l1">
                <div class="input-field">
                    <label>&nbsp;</label>
                    <a href="javascript:;" id="search" class="btn-floating" ><i class="red material-icons">search</i></a>
                    <a href="javascript:;" id="refresh_btn" class="btn-floating" ><i class="red material-icons">refresh</i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

<div class="row">
  <div class="col s6 m6 l12">
    <div class="card grey lighten-5 m-md p-sm">
      <div style="height:30%;width:100%;overflow:auto;">
        <div class="pre-datatable"></div>
        <div>
          <table cellpadding="0" cellspacing="0" class="table table-default table-layout-auto" id="tbl_payment_report">
            <thead>
              <tr>
                  <th width="15%" id="org_name" class="white-text">Business Center</th>
                  <th width="10%" id="account_group_name" class="white-text">Account Group</th>
                  <th width="15%" id="store_name" class="white-text">Store</th>
                  <th width="15%" id="ref_a_no" class="white-text">Reference A No.</th>
                  <th width="15%" id="ref_a_date" class="white-text">Reference A Date</th>
                  <th width="15%" id="ref_b_no" class="white-text">Reference B No.</th>
                  <th width="15%" id="ref_b_date" class="white-text">Reference B Date</th>
                  <th width="0%" id="dummy" class="white-text none">&nbsp;</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>