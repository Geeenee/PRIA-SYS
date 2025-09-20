<div class="p-lg p-t-sm">
	<div class="row">
		<div class="col l8 m12 s12 m-b-md">
			<div class="panel p-b-md table-scroll">
				<div class="panel-title p-md b-b b-light-gray font-bold">Tasks</div>
				<div class="scroll-pane scroll-dark" style="height:306px">
					<?php if(ISSET($user_tasks) AND COUNT($user_tasks) > 0): ?>
						<ul class="list-task-2">
						<?php foreach($user_tasks AS $key => $user_task):
							$task_date_strtotime	= strtotime($user_task['task_date']);
							$encoded_id				= base64_url_encode($user_task['pria_task_id']);
							?>
							<li class="no-padding">							
								<!--input type="checkbox" class="labelauty rounded" checked name="dr_chk[]" value="1" /-->
								<a href="<?php echo base_url() . PORTAL_TRANSACTIONS . '/' . $user_task['controller'] . '/?t=' . $encoded_id; ?>">
									<div class="table-display stripped">
										<div class="table-cell l1 m1 s1 valign-middle">
											<i class="material-icons">open_in_new</i>
										</div>
										<div class="table-cell l5 m5 s11 content">
											<div class="grey-text text-darken-1 font-bold font-sm"><?php echo $user_task['reference_num']; ?></div>
											<div class="black-text font-bold"><?php echo $user_task['task_name']; ?></div>
											<div class="help-text m-b-n">
												Vendor : <?php echo $user_task['vendor_name'] ?>
												<br>
												Business Center :  <?php echo $user_task['org_name']; ?>
												<span class="hide-on-med-and-up"> &bull; <?php echo (EMPTY($task_date_strtotime))? "Not yet started": ((date('Y-m-d') == date('Y-m-d', $task_date_strtotime))? ("Today " . date('h:i A',$task_date_strtotime)): (date('M. d, Y h:i A', $task_date_strtotime))); ?></span>
												</div>

											<div class="status icon m-t-xs hide-on-med-and-up <?php echo strtolower($user_task['action_name']); ?>"><i class="material-icons">lens</i> <?php echo $user_task['action_name']; ?></div>
										</div>
										<div class="table-cell l3 m3 s1 center-align valign-middle detail hide-on-small-only">
											<div class="status icon <?php echo strtolower($user_task['action_name']); ?>"><i class="material-icons">lens</i> <?php echo $user_task['action_name']; ?></div>
										</div>
										<div class="table-cell l3 m3 s1 timestamp valign-middle hide-on-small-only">
											<i class="material-icons">access_time</i> <?php echo (EMPTY($task_date_strtotime))? "Not yet started": ((date('Y-m-d') == date('Y-m-d', $task_date_strtotime))? ("Today " . date('h:i A',$task_date_strtotime)): (date('M. d, Y h:i A', $task_date_strtotime))); ?>
										</div>
									</div>
								</a>
							</li>
						<?php  endforeach; ?>
						</ul>
					<?php else: ?>
						<div class="p-md p-l-md font-md">No active tasks.</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col l4 m12 s12">
			<div class="panel p-b-md table-scroll">
				<?php
					/* GET USER AVATAR */
					/*$root_path		= get_root_path();
					$avatar_path 	= $root_path . PATH_USER_UPLOADS . $this->session->photo;
					$avatar_path 	= str_replace(array('\\','/'), array(DS,DS), $avatar_path);

					$avatar_photo 	= $this->session->photo;

					if( !is_dir( $avatar_path ) AND file_exists( $avatar_path ) )
					{	
						$avatar_src = base_url() . PATH_USER_UPLOADS . $this->session->photo;

						if( !EMPTY( $change_upload_path ) )
						{	
							$avatar_src = output_image($this->session->photo, PATH_USER_UPLOADS);
						}

						$avatar_src = @getimagesize($avatar_path) ? $avatar_src : base_url() . PATH_IMAGES . "avatar.jpg";	
					}
					else
					{
						$avatar_photo = '';
					}*/
				?>
				<!--div class="card-user">
					<?php 
						//if( !EMPTY( $avatar_photo ) ) :	
					?>	
					<img src="<?php //echo $avatar_src ?>" class="card-avatar"  alt="avatar">
					<?php 
						//else :
					?>
					<img src="" class="profile_avatar card-avatar" data-name="<?php //echo $this->session->name ?>" alt="avatar">
					<?php 
						//endif;
					?>
					<h5><?php //echo $this->session->name ?></h5>
				</div-->
				<div class="panel-title p-md b-b b-light-gray font-bold">Reminders</div>
				<div id="reminders" class="scroll-pane scroll-dark" style="height:306px;">
					<?php $ctr = 0; ?>
					<?php if(ISSET($user_reminders) AND COUNT($user_reminders) > 0): ?>
						<ul class="list-tl-notification m-n">
						<?php foreach($user_reminders AS $key => $user_reminder): ?>
						<?php
						if (EMPTY($user_reminder['read_date']) ) {
						//if (in_array($user_reminder['ag_code'],$per_reminders) AND EMPTY($user_reminder['read_date']) ) {
						?>
							<li>
								<!-- <a href="javascript:; <?php /*echo $user_reminder['notification_html']; */?>"> -->
									<div class="tl-icon" onclick="remove_reminder('<?php echo $user_reminder['notification_id']; ?>');"><i class="material-icons tooltipped" data-position="top" data-tooltip="Done"><?php /*echo $user_reminder['notification_icon'];*/ ?></i>
									<input type="hidden" name="rem_id" value="<?php echo $user_reminder['notification_id']; ?>">
									</div>
									<div class="tl-contents">
										<div class="tl-title"><?php echo $user_reminder['notification']; ?></div>
										<div class="tl-subtitle"><?php echo findTimeAgo($user_reminder['notification_date']); ?></div>
									</div>
								<!-- </a> -->
							</li>
						<?php 
						$ctr++;
						}
						?>
						<?php  endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if($ctr==0): ?>
						<div class="p-md font-md">Empty reminder.</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col s12">
			<div id="payment_panel" class="panel p-b-md">
				<div class="row panel-title p-md p-t-sm p-b-sm m-b-n b-b b-light-gray font-bold">
					<div class="col l9 m12 s12 m-t-sm p-t-xs">Payments</div>
					<div class="col l3 m12 s12 pull-right p-l-n form-basic">
						<div class="row m-n">
							<div class="col s12">
								<?php if(ISSET($account_groups) AND COUNT($account_groups) > 0): ?>
								<div class="input-field p-n m-n">
									<label for="payment_ag" class="active hide-on-large-only">Payment Account Group</label>
									<select class="selectize" id="payment_ag" placeholder="Select Payment Account Group">
										<option value="">Select Payment Account Group</option>
										<?php foreach($account_groups AS $key => $account_group): ?>
											<option value="<?php echo $account_group['account_group_code']; ?>"><?php echo $account_group['account_group_name']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="row m-n p-n">
					<div class="col s12 m-n p-sm">
						<div id="payment_empty" class="p-t-sm">
							<div class="p-l-lg font-md">Select payment account group</div>
						</div>
						<div id="payment_content" class="none m-n p-n">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>