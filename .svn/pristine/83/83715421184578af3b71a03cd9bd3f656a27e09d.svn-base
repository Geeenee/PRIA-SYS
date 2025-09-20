<?php
	
	$id  = ( ISSET($task['id']) && ! EMPTY($task['id']) ) ? $task['id'] : 'nav_container';
	//Did not reuse the variable $enc_task_id so that the data displayed/inputted in the html will be different but still points to the same task_id.
	$etd = encrypt_id($task['pria_task_id']);
	//Remember disabled state is used to display the minus sign
	$available_txt = 'assigned';

	switch($task['task_status_id'])
	{
		case TASK_STATUS_ONGOING:
			$class_status  	 	= 'white-text task-ongoing';
			$class_checkbox 	= 'tag-task-done';
			$checked 	     	= '';
			$disabled 	    	= '';
		break;
		
		case TASK_STATUS_APPROVED:
			$class_status 		= 'white-text task-approved';
			$class_checkbox  	= 'disabled cursor-default';
			$checked 	  		= 'checked';
			$disabled 	    	= '';
		break;

		case TASK_STATUS_DONE:
			$class_status 		= 'white-text task-completed';
			$class_checkbox  	= 'disabled cursor-default';
			$checked 	  		= 'checked';
			$disabled 	    	= '';
		break;

		case TASK_STATUS_DISAPPROVED:
		case TASK_STATUS_SKIPPED:
			$class_status 		= 'white-text task-disapproved';
			$class_checkbox  	= 'disabled cursor-default';
			$checked 	  		= '';
			$disabled 	  		= ($task['returned_flag'] == ENUM_YES) ? '' :  'disabled';
		break;

		case TASK_STATUS_RETURNED:
			$class_status		= 'white-text task-returned';
			$class_checkbox  	= 'disabled cursor-default';
			$checked 	  		= '';
			$disabled 	  		= ($task['returned_flag'] == ENUM_YES) ? '' :  'disabled';
		break;
		
		//PENDING
		default:
			$class_status  	 	= 'white-text task-pending';
			$class_checkbox 	= 'tag-task-done disabled cursor-default';
			$checked 	     	= '';
			$disabled 	    	= '';
			//$available_txt 		= 'available';
	}
?>

<div id="<?php echo $id ?>" class="nav-container p-lg">
	
	<div class="panel-wrapper centered shadowed">
		<div class="form-layout-1 horizontal">

			<div class="nav-content">

				<div class="m-b-md nav-content-title"><?php echo $task['stage_name'] ?></div>
				<div class="row b-t-n m-n p-n p-t-sm">
					<div class="col l1 m1 s2 list-task left-align checkbox-div">
						<input type="checkbox" class="labelauty rounded <?php echo $class_checkbox ?>" name="dr_chk[]" value="<?php echo $etd; ?>"  <?php echo $checked; ?> <?php  echo $disabled; ?>/>
						<input type="hidden" id="tid" class="tid" name="tid" value="<?php echo $etd; ?>">
					</div>
					<div class="col l11 m11 s10 p-l-sm">
						<div class="font-lg">
							<?php echo $task['task_name'] ?> 
							<span class="badge rounded hide-on-small-only <?php echo $class_status; ?>" id="task-status"><?php echo $task['task_status'] ?></span>
							<div class="help-text  m-b-sm font-sm m-t-xs">
								<?php //echo $available_txt; ?> 
								assigned to: <span class="font-bold"><?php echo $task['task_role']; ?></span><?php if( ! EMPTY($task['actor'])) { ?><br/>modified by: <span class="font-bold"><?php echo $task['actor']; ?></span><?php } ?><br/>modified date: <span class="font-bold"><?php echo $task['modified_date']; ?></span>
							</div>
							<span class="badge rounded hide-on-med-and-up m-n <?php echo $class_status; ?>" id="task-status"><?php echo $task['task_status'] ?></span>
						</div>
					</div>
					<!--div class="col l3 m3 s2 right-align">
						
						<?php //if($task['returned_flag'] == YES_FLAG AND $task['task_status_id'] != TASK_STATUS_RETURNED): ?>
							<!-- <span class="badge rounded white-text red accent-1 ?>" id="task-status">Returned</span> -->
						<?php //endif; ?>
					<!--/div-->
				</div>
	
				<div class="row b-t-n m-b-n p-n m-t-md">
					<div class="col l1 m1 s1 hide-on-med-and-down"></div>
					<div class="col l11 m12 s12">
						<form id="form-task" class="form-basic" autocomplete="off">
							<input type="hidden" name="etd" value="<?php echo $enc_task_id; ?>" />

							<div class="panel-wrapper white p-xs p-r-lg p-l-md p-t-md">
								<?php echo $task['content']; ?>

								<div class="input-field task-action-btns">
									<div class="table-display">
										<div class="table-cell l3 m4 s4 right-align valign-middle p-r-md hide-on-small-only"></div>
 
										<div class="table-cell l9 m8 s8 valign-middle">
											<?php echo $task_btn_html; ?>
										</div>
									</div>
								</div>	
							</div>
						</form>
					</div>		
				</div>

			</div>
			
			<?php echo $task_attachments ?>

			<?php echo $task_comments ?>
			
		</div>
	</div>
</div>