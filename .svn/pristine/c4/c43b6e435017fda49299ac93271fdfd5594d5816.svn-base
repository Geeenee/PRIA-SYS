<?php
	$toggle_display = ($this->session->show_task_filter == 1) ? " active " : "";  
?>

<div class="p-md">
	<div class="none">
		<a class="waves-effect waves-light btn" onClick="expand_all();"><i class="material-icons left">fullscreen</i>Expand All</a>
		<a class="waves-effect waves-light btn" onClick="collapse_all();"><i class="material-icons left">fullscreen_exit</i>Collapse All</a>
	</div>

	<div class="right-align more-transaction-actions">
		<?php echo ISSET($more_actions) ? $more_actions : ''; ?>
	</div>

	<ul class="collapsible list-toggle"  data-collapsible="expandable">
		<?php echo $list; ?>
	</ul>
</div>




<!-- <div class="p-md">
	<div class="none">
		<a class="waves-effect waves-light btn" onClick="expand_all();"><i class="material-icons left">fullscreen</i>Expand All</a>
		<a class="waves-effect waves-light btn" onClick="collapse_all();"><i class="material-icons left">fullscreen_exit</i>Collapse All</a>
	</div>
	<div class="right-align">
		<button type="button" id="filter_task" class="btn-toggle <?php echo $toggle_display ?>"><i class="material-icons">tune</i> Filter</button>
	</div>
	<ul class="collapsible list-toggle"  data-collapsible="expandable">
		<li>
			<div class="collapsible-header active">
				<div class="row m-b-n">
					<div class="col l3 m3 s3 toggle font-bold">IO 0001 1901 02</div>
					<div class="col l5 m5 s5 font-bold">XYZ Corporation</div>
					<div class="col s4 m4 s4 mute font-sm  font-bold right-align">Business Center: Business Center A</div>
				</div>
			</div>
			<div class="collapsible-body">
				<ul class="list-nested m-l-md m-r-md">
					<li class="title p-b-md p-t-md"><h5>MedVac Delivery</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded" name="dr_chk[]" value="1" checked />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s8 valign-middle">
										<a class="text-line-through" href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_MEDVAC_DR ?>/<?php echo PORTAL_ENCODE ?>">Encode MedVac Delivery and Receipt</a>
										<span class="completed-by">Jan 21, 2019 by Sylvia Ramos</span>
									</div>
									<div class="table-cell s2 valign-middle status">Completed</div>
								</div>
							</li>
							<li>
								<div class="actions">
									<a href="javascript:;" class="a-subtask"><i class="material-icons valign-middle font-md">add</i> Add MedVac DR</a>
								</div>
							</li>
						</ul>
					</li>
					<li class="title p-b-md p-t-md"><h5>Doc Delivery and Receipt</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded" name="dr_chk[]" value="1" />
									
									<div class="assignment">CG Sup</div></div>
									<div class="table-cell s8 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_DOC_DR ?>/<?php echo PORTAL_UPLOAD ?>">Upload DOC Delivery Receipt</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s8 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_DOC_GR ?>/<?php echo PORTAL_ENCODE ?>">Encode DOC Goods Receipt</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="actions">
									<a href="javascript:;" class="a-subtask"><i class="material-icons valign-middle font-md">add</i> Add DOC Delivery Receipt</a>
								</div>
							</li>
						</ul>
					</li>
					<li class="title p-b-md p-t-md"><h5>Cleanup Report</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_CLEANUP_REPORT ?>/<?php echo PORTAL_ENCODE ?>">Encode Cleanup Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">CG Liq Finance</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_CLEANUP_REPORT ?>/<?php echo PORTAL_REVIEW_APPROVE ?>">Return / Approve Cleanup Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
						</ul>
					</li>

					<li class="title p-b-md p-t-md"><h5>Harvest Report</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_HARVEST_REPORT ?>/<?php echo PORTAL_UPLOAD ?>">Upload Harvest Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">CG Liq</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_HARVEST_REPORT ?>/<?php echo PORTAL_REVIEW ?>">Return / Approve Harvest Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
						</ul>
					</li>

					<li class="title p-b-md p-t-md"><h5>Lives Sales Report</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_LIVES_SALES_REPORT ?>/<?php echo PORTAL_UPLOAD ?>">Lives Sales Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
						</ul>
					</li>
					
					<li class="title p-b-md p-t-md"><h5>Flock History Record</h5>
						<ul class="list-task">
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">Vendor</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_FHR ?>/<?php echo PORTAL_ENCODE ?>">Encode Flock History Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_FHR ?>/<?php echo PORTAL_REVIEW ?>">Return Flock History Report or Send for Audit</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">BC Admin</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_FHR ?>/<?php echo PORTAL_REVIEW ?>">Encode Audit Scores and Return / Recommend Approval</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
							<li>
								<div class="table-display m-b-n">
									<div class="table-cell s2 valign-middle">
									<input type="checkbox" class="labelauty rounded red-text" name="dr_chk[]" value="1" disabled />
									
									<div class="assignment">CG Liq Finance</div></div>
									<div class="table-cell s7 valign-middle"><a href="<?php echo base_url() ?><?php echo PORTAL_TASK ?>/task/view_page/<?php echo FORM_INTERNAL_ORDERS ?>/<?php echo TYPE_FHR ?>/<?php echo PORTAL_REVIEW ?>">Return / Approve Flock History Report</a></div>
									<div class="table-cell s2 valign-middle status pending">Pending</div>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</li>
	</ul>
</div> -->