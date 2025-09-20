<div class="table-display ">
  <div class="p-md table-cell valign-top b-r" style="border-color:#e2e7e7!important; width:30%;">
    <div class="p-t-sm p-b-xs">
      <label class="label m-b-n-xs text-uppercase">Module</label>
	  <p><?php echo $audit_trail['module_name'] ?></p>
    </div>
    <div class="p-t-sm p-b-xs">
      <label class="label m-b-n-xs text-uppercase">Activity</label>
	  <p class="break-all"><?php echo $audit_trail['activity'] ?></p>
    </div>
    <div class="p-t-sm p-b-xs">
      <label class="label m-b-n-xs text-uppercase">Activity Date</label>
	  <p><?php echo $audit_trail['activity_date'] ?></p>
    </div>
    <div class="p-t-sm p-b-xs">
      <label class="label m-b-n-xs text-uppercase">User</label>
	  <p><?php echo $audit_trail['name'] ?></p>
	  <div class="font-xs text-muted">I.P. Address: <?php echo $audit_trail['ip_address'] ?></div>
    </div>
  </div>
  <div class="p-n table-cell valign-top" style=" width:70%;">
	<table class="table table-default">
	<thead>
	<tr>
	  <th width="30%">Fields</th>
	  <th width="35%">Previous Value</th>
	  <th width="35%">Current Value</th>
	</tr>
	</thead>
	</table>
    <div>
      <div class="scroll-pane" style="height:380px">
		<table class="table table-default font-sm" cellpadding="0" cellspacing="0" width="100%">
	    <tbody>
	      <?php foreach($audit_trail_detail as $row): ?>
		  <tr>
			<td width="30%"><?php echo $row['field'] ?></td>
			<td width="35%"><?php echo $row['prev_detail'] ?></td>
			<td width="35%"><?php echo $row['curr_detail'] ?></td>
		  </tr>
		  <?php endforeach; ?>
	    </tbody>
	    </table>
	  </div>
    </div>
  </div>
</div>