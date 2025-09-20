<?php
$reference_num			= (ISSET($task_details['reference_num']) AND !EMPTY($task_details['reference_num']))? $task_details['reference_num']: "N/A";
$task_name				= (ISSET($task_details['task_name']) AND !EMPTY($task_details['task_name']))? $task_details['task_name']: "N/A";
$ag_name				= (ISSET($task_details['ag_name']) AND !EMPTY($task_details['ag_name']))? $task_details['ag_name']: "N/A";
$business_center_name	= (ISSET($task_details['business_center_name']) AND !EMPTY($task_details['business_center_name']))? $task_details['business_center_name']: "N/A";
$vendor_name			= (ISSET($task_details['vendor_name']) AND !EMPTY($task_details['vendor_name']))? $task_details['vendor_name']: "N/A";

$task_actions_val		= array_column($task_actions, 'pria_task_action_id');

$with_return = (in_array(TASK_STATUS_RETURNED, $task_actions_val))? TRUE: FALSE;
?>
	
<input type="hidden" id="etd"  name="etd" value="<?php echo $etd; ?>" />
<input type="hidden" id="euid" name="euid" value="<?php echo $euid; ?>" />

<div class="row">
	<div class="col s12 form-basic">
		<div class="row m-b-lg">
			<div class="col s12">
				<span class="font-md"><strong><?php echo $task_name; ?></strong></span>
			</div>
			<div class="col s12">
				<span class="font-md"><strong>Reference Number</strong>: <?php echo $reference_num; ?></span>
			</div>
			<div class="col s12">
				<span class="font-md"><strong>Account Group</strong>: <?php echo $ag_name; ?></span>
			</div>
			<div class="col s12">
				<span class="font-md"><strong>Business Center</strong>: <?php echo $business_center_name; ?></span>
			</div>
			<div class="col s12">
				<span class="font-md"><strong>Vendor</strong>: <?php echo $vendor_name; ?></span>
			</div>
		</div>
		<div class="row">
			<div class="col s12">
				<div class="input-field m-t-n">
					<label for="task_remarks" class="active font-md m-t-xs">Remarks <span class="font-sm font-normal">(Required if Returned)</span></label>
					<textarea id="task_remarks" name="task_remarks" data-parsley-required="true" placeholder="Enter remarks" style="min-height: 150px; overflow-y: auto; resize: none;"></textarea>
				</div>
			</div>
		</div>
		<div class="row p-t-sm">
			<?php if($with_return): ?>
			<div class="col s12">
				<div class="input-field m-t-n">
					<label for="return_task_id" class="active font-md m-t-xs">Return To: <span class="font-sm font-normal">(Required to select if Returned)</span></label>
					<select id="return_task_id" name="return_task_id" class="selectize" data-parsley-required="true">
						<option value="">Select Return To</option>
						<?php if(is_array($return_tasks) AND count($return_tasks) > 0):
							foreach($return_tasks AS $key => $r_task): ?>
							<option value="<?php echo $r_task['ret_pria_task_id']; ?>"><?php echo $r_task['actor_name'] . " (".$r_task['task_name'].")"; ?></option>
						<?php endforeach;
						endif; ?>
					</select>
				</div>
			</div>
			<?php endif; ?>
			<div class="col s12">
				<div class="input-field">
				<?php
					$html = '';
					foreach($task_actions AS $key => $task_action)
					{
						switch($task_action['pria_task_action_id'])
						{
							case TASK_STATUS_APPROVED :
								$id 	= 'btn-approve-task';
								$class  = 'purple lighten-1';
							break;

							case TASK_STATUS_RETURNED :
								$id 	= 'btn-return-task';
								$class  = 'red lighten-1';
							break;

							case TASK_STATUS_DISAPPROVED :
								$id 	= 'btn-disapprove-task';
								$class  = 'blue-grey lighten-1';
							break;
						}

						$html .=<<<EOS
							<button type="button" id="$id" class="btn $class" >{$task_action['btn_label']}</button>
EOS;
					}

					echo $html;
?>		
				</div>	
			</div>		
		</div>
	</div>
</div>
	