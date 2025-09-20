var Task = (function($, document, window)
{
	var $tasks			= "tasks/";
	var $module_task	= "task";
	var liIndex 		= 0;
/* 	var statusComplete  = 'COMPLETED',
		statusReturn 	= 'RETURNED',
		statusOngoing 	= 'ONGOING',
		statusSubmitted	= 'SUBMITTED',
		statusApproved 	= 'APPROVED',
		statusSkipped 	= 'SKIPPED'; */

	var statusComplete  	= 'COMPLETE',
		statusReturn 		= 'RETURN',
		statusOngoing 		= 'ONGOING',
		statusSubmitted		= 'SUBMIT',
		statusApproved 		= 'APPROVE',
		statusSkipped 		= 'SKIPPED';
		statusDisapproved 	= 'DISAPPROVE';

	let _taskConfig 		= {};



	var toggleFilter = function(id){
		$( "#filter_task" ).click(function() {
			$("#" + id).toggleClass("none");

			if ( $("#" + id).is( ".none" ) ) {
				var display = 0;
				$("#filter_task").removeClass("active");
			} else {
				var display = 1;
				$("#filter_task").addClass("active");
			}

			var data = "filter_display=" + display;

			$.post($base_url + $tasks + $module_task + "/set_filter_session/", data, function()
			{

			});
		});
	}

	var tagStatus = function(reload){

		document.addEventListener('click', function(ev){
			const elem 			= ev.target;
			const parentElem 	= elem.closest('li.toggle-task');
			var condi 			= false;
			var det_id 			= $('#tid').val();
			var str_val 		= false;
			var isValid 		= false;
			var validateForm 	= true;

			if(elem.classList.contains('disabled'))
			{
				ev.preventDefault();
				return;
			}

			if(elem.classList.contains('tag-task-done') && elem.checked == true)
			{
				ev.preventDefault();
				 return;
				 det_id 		= elem.value;
				 condi 			= 'tag_complete';
				 str_val 		= statusComplete;
				 validateForm	= false;
				 isValid 		= true;
			}
			else if(elem.id == 'btn-return-task' && elem.classList.contains('btn'))
			{
				 condi 		= 'tag_return';
				 str_val 	= statusReturn;
			}
			else if(elem.id == 'btn-approve-task' && elem.classList.contains('btn'))
			{
				 condi 		= 'tag_approve';
				 str_val 	= statusApproved;
			}
			else if(elem.id == 'btn-submit-task' && elem.classList.contains('btn'))
			{
				 condi 		= 'tag_complete';
				 str_val 	= statusSubmitted;
			}
			else if(elem.id == 'btn-get-task' && elem.classList.contains('btn'))
			{
				 condi 			= 'tag_get';
				 str_val 		= statusOngoing;
				 validateForm	= false;
				 isValid 		= true;
			}
			else if(elem.classList.contains('task-skip'))
			{
				 det_id 		= elem.parentElement.querySelector('input[type="checkbox"]').value;
				 condi 			= 'tag_skip';
				 str_val 		= statusSkipped;
				 validateForm	= false;
				 isValid 		= true;
			}
			else
			{
				return;
			}

			if(validateForm)
			{
				$form 		= $('#form-task');
				$parlsey 	= $form.parsley();
				isValid     = $parlsey.validate();
			}

			if(condi !== false  && isValid == true){
				$('#confirm_modal').confirmModal({
					topOffset : 0,
					onOkBut : function(event) {
						var parameters 	= [reload, elem, str_val, det_id, condi];

							_tagStatusOk(...parameters);
					},
					onCancelBut : () => elem.checked = false,
					onLoad 		: function() {

						var p = `This action will tag the task as <b>${str_val}</b> and cannot be undone.`;
							additional = '';


						switch(str_val)
						{
							case statusReturn:
									additional = `
										<div class="row p-n m-b-sm">
											<div class="col s12 p-n">
												<div class="input-field">
													<h6>Return to: </h6>
													<select id="task_return" class="selectize white browser-default task_select m-t-sm" name="task_return" placeholder="Select task" data-parsley-required="true"></select>
													<label for="task_return"></label>
												</div>

												<div class="input-field  p-n">
													<h6>Remarks</h6>
													<textarea id="task_remark" name="task_remark" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required"></textarea>
													<label for="task_remark"></label>
												</div>
											</div>
										</div>
									`;

									$.post($base_url + 'transactions/task/get_return_values', {etd: det_id}, function(result) {
										for(let i = 0; i < result.return_val.length; i++){
											   $('.confirmModal_content select.task_select')
												.append($("<option></option>")
												.attr("value",result.return_val[i].ret_pria_task_id)
												.text(result.return_val[i].task_name));
										}
									}, 'json');
							break;

							case statusOngoing:
								p = `This action will tag this task as <b>${str_val}</b>. Are you sure you want to get this task?`;
							break;

						/* 	case statusApproved:
									additional = `
										<div class="row p-n m-b-sm">
											<div class="col s12 p-n">
												<div class="input-field p-n">
													<h6>Remarks</h6>
													<textarea id="task_remark" name="task_remark" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required"></textarea>
													<label for="task_remark"></label>
												</div>
											</div>
										</div>
									`;
							break; */

							default:
									if( ! reload){
										taskName = elem.closest('li').querySelector('a.task-name').textContent;

										p = `This action will tag the task <b>${taskName.trim()}</b> as <b>${str_val}</b> and cannot be undone.`;
									}

									$('.confirmModal_content h6').html('');

									additional = '';

						}

						$('.confirmModal_content h4').html(`<span class="font-lg font-normal">Are you sure?</span>`);
						$('.confirmModal_body ').find('form').attr('id', 'confirm-task-form');
						$('.confirmModal_content p').html(p);
						$('.confirmModal_content div.additonal').html(additional);
					},
					onClose : function() {}
				});
			}
		});
	}

	var _tagStatusOk = function(reload, elem, str_val, det_id, condi){
		const parentElem 	= elem.closest('li.toggle-task');
		const options 		= {
			blockUI    : true,
			body 	   : $('#confirm-task-form').serialize() + '&etd=' + det_id,
			path       : $base_url + 'transactions/task/' + condi,
			successFunc: function(response){
				notification_msg(response.flag, response.msg);

				if(response.flag == $.CONSTANTS.SUCCESS)
				{
					if(reload)
					{
						setTimeout( args => { start_loading(); location.reload(); } , 3000);
					}
					else
					{
						const parent 		= elem.closest('.table-display');
						const completedSpan = parent.querySelector('.completed-by');
						const aTag 			= completedSpan.parentElement.querySelector('a');
						const divStatus 	= parent.querySelector('div.table-cell.status');

						completedSpan.innerText = response.completed_by;
						divStatus.innerText 	= str_val;

						aTag.classList.add('text-line-through');

						switch(str_val)
						{
							case statusSkipped:
								start_loading();

								const body = elem.closest('.collapsible-body');

								if(response.html)
								{
									body.innerHTML = response.html;
									labelauty_init();
								}

								end_loading();

								/*const pElem = elem.parentElement,
									  lBlty = pElem.querySelector('span.labelauty-unchecked-image');

								lBlty.classList.add('skip-icon-chst');

								lBlty.parentElement.classList.add('skip-label-chst');

								pElem.querySelector('input[type="checkbox"]').classList.add('disabled', 'cursor-default');

								elem.remove();

								divStatus.classList.remove('pending');
								divStatus.classList.add('skipped');*/
							break;

							case statusComplete:
								if(document.querySelector('.task-skip'))
									document.querySelector('.task-skip').remove();
							break;

							default:
								elem.classList.add('disabled', 'cursor-default');
						}

						if(response.cleared_task.length > 0)
						{
							for (var i of response.cleared_task ) {
								const li 	 	= parentElem.querySelector(`.task-${i.num}`),
									checkbox 	= li.querySelector('.labelauty');
									divcell  	= li.querySelector('div.table-cell:nth-child(2)');
									frstdivcell = li.querySelector('div.table-cell:nth-child(1)');

									if(i.is_role)
									{
										frstdivcell.insertAdjacentHTML('afterbegin', i.skip);

										checkbox.classList.remove('disabled', 'cursor-default');
										checkbox.disabled = false;
									}

									divcell.innerHTML = i.anchor;
							}
						}
					}
				}
				else
				{
					elem.checked = false;
				}
			},
		};

		General.Fetch(options);
	};

	var init = function(){
		$('.tooltipped').tooltip({delay: 50});

		$('.dropdown-trigger').dropdown( {belowOrigin:true, constrainWidth:false} );

		document.querySelector('.list-toggle').addEventListener('click', function(ev){
			const targetElem = ev.target;

			if(targetElem.closest('div.collapsible-header'))
			{
				ev.stopPropagation();

				if(targetElem.classList.contains('toggle'))
					_toggleTransaction(targetElem);

				if(targetElem.parentElement.classList.contains('more-task-actions') == true)
					_triggerTaskActions(targetElem.parentElement);

				/* if(targetElem.classList.contains('task-append'))
					_triggerTaskAppend(targetElem); */
				/*if(targetElem.parentElement.classList.contains('edit_trans') == true ||
					targetElem.parentElement.classList.contains('delete_trans') == true)
				{

				}
				else
				{
				}*/
			}
			else if(targetElem.closest('div.collapsible-body'))
			{
				if(targetElem.classList.contains('a-subtask'))
					_triggerTaskAppend(targetElem);
			}
		}, true);

		tagStatus(false);
	};

	var _triggerTaskActions = function(elem){
		$('.dropdown-trigger').dropdown('close');

		$(elem).dropdown('open');
	};

	var _triggerTaskAppend = function(elem){
	/* 	const   li 							= elem.closest('li.toggle-task'),
				transaction_ref_num 		= li.querySelector('#transaction_ref_num').innerText;
				button             	  		= document.createElement("button");
				button.id             		= 'task-append';
				button.dataset.target 		= elem.dataset.target;
				//button.dataset.modal_post 	= JSON.stringify({ag:li.dataset.ag, id:li.dataset.id, ref_num:transaction_ref_num});
				button.dataset.modal_post 	= JSON.stringify({pwi: li.dataset.pwi, ref_num:transaction_ref_num});

				button.dataset.onclick		= window[elem.dataset.target + '_init']('', button, 'Add Task');
				button.style.display  		= 'none';

				document.body.appendChild(button);

				button.click();

				liIndex = $(li).index() + 1;
				//document.querySelector('ul.list-toggle').querySelector(`li:nth-child(${index})`).innerText = 'WOOOOO';

				document.body.removeChild(button); */



				const body = elem.closest('.collapsible-body');


				$('#confirm_modal').confirmModal({
					topOffset 	: 0,
					onOkBut 	: () => {
						start_loading();

						const data   = {
							awid   		: elem.dataset.awi,
							wid    		: elem.dataset.wid,
							wsid   		: elem.dataset.wsid,
							pwid   		: elem.dataset.pwid,
							tid   		: elem.dataset.tid,
						   filter_form  : $('#filter_form').serializeObject()
						};

						const options = {
							body        : $.param(data),
							path        : $base_url + 'transactions/task/append_stage/',
							successFunc : function(response){
								notification_msg(response.flag, response.msg);

								if(response.html)
									body.innerHTML = response.html;

								labelauty_init();

								end_loading();
							}
						};

						General.Fetch(options);
					},
					onLoad 		: () => {
						$('.confirmModal_content p').html(`This action will add a <b>${elem.dataset.wname}</b> and cannot be undone.`);

						$('.confirmModal_content h4').html(`<span class="font-bold">Add another ${elem.dataset.wname}?</span>`);
					}
				});
	};

	var _toggleTransaction = function(elem){

		const li 	= elem.closest('li'),
			 index 	= $(li).index(),
			 body   = li.querySelector('.collapsible-body');

		$('.collapsible').collapsible('open', index);

		if(li.classList.contains('active'))
		{
			body.innerHTML = `
				<div class="row p-b-n">
					<div class="col s4 offset-s4 center">
						<div class="progress">
							<div class="indeterminate"></div>
						</div>
					</div>
				</div>
			`;

			/* const id 	  = $(li).data('id');
			const ag 	  = $(li).data('ag'); */
			const pwi 	    = $(li).data('pwi');
			const mid 	    = $(li).data('mid');
			const filter    = {'filter_form' : $('#filter_form').serializeObject()};
			const param_obj	= jQuery.extend({pwi: pwi, mid:mid}, filter);

			const options = {
				//body       : $.param({id: id, ag: ag}),
				body       : $.param(param_obj),
				path       : $base_url + 'transactions/task/display_task_list',
				successFunc: function(response){
					if(response.success){
						body.innerHTML = response.html;

						$('.tooltipped').tooltip({delay: 50});

						labelauty_init();
					}
				}
			};

			General.Fetch(options);
		}
		else
		{
			body.innerHTML = ``;
		}
	};

	var initPage = function(obj, config){
		_taskConfig = config;
	console.log('kevin');
		create_avatar($('.letter-avatar'), {width:45,height:45,fontSize:30});

		const divActions   = document.querySelector('.task-action-btns'),
			  form 		   = document.getElementById('form-task'),
			  $form 	   = $(form),
			  $parsley 	   = $form.parsley(),
			  path		   = $base_url + 'transactions/';

	console.log('config: ', config);
		if(config.disableForm)
		{
			const elems = form.closest('div.col').querySelectorAll('input, textarea');

			for (let elem of elems) {
				if(elem.type == 'hidden') continue;

				elem.disabled = true;
			}

			const selects = form.closest('div.col').querySelectorAll('select.selectize');

			for (let sel of selects)
				sel.selectize.disable();

			//const files   = document.getElementById('form-task').querySelectorAll('input[type="file"]');
			const files   = document.getElementById('form-task').querySelectorAll('a.file-task-attach');

			for (let fl of files)
				fl.closest('div.input-field').addEventListener('click', (ev) =>  ev.preventDefault()  );
		}


		document.querySelector('input[name="dr_chk[]"').addEventListener('click', function(ev){
			ev.preventDefault();
		});


		divActions.addEventListener('click', function(ev){
			ev.preventDefault();

			const elem 					= ev.target,
				  targetId 				= elem.id;
			let	  wConfirm  			= false,
				  controller   			= 'task';
				  validateForm			= true;
				  _taskConfig.formData 	= $form.serialize();
				  _taskConfig.targetId  = targetId;

			switch(targetId)
			{
				case 'btn-get-task':
					validateForm				= false;
					controller 					= 'task/tag_get';
					_taskConfig.buttonLoader 	= true;
				break;

				case 'btn-save-task':
					_taskConfig.buttonLoader 	= true;
					controller  				= obj.toLowerCase() + '/process';
					_taskConfig.formData	   += '&task_status=' + $.CONSTANTS.TASK_STATUS_ONGOING;
				break;

				case 'btn-submit-task':
					wConfirm					= true;
					controller  				= obj.toLowerCase() + '/process';
					_taskConfig.statusText 		= statusSubmitted;
					_taskConfig.formData	   += '&task_status=' + $.CONSTANTS.TASK_STATUS_DONE;
				break;

				case 'btn-approve-task':
					validateForm				= false;
					wConfirm					= true;
					controller 					= 'task/tag_approve';
					_taskConfig.statusText 		= statusApproved;
				break;

				case 'btn-disapprove-task':
					validateForm				= false;
					wConfirm					= true;
					controller 					= 'task/tag_disapprove';
					_taskConfig.statusText 		= statusDisapproved;
				break;

				case 'btn-return-task':
					validateForm				= false;
					wConfirm					= true;
					controller 					= 'task/tag_return';
					_taskConfig.statusText 		= statusReturn
				break;

				default:
					return;
			}

			const isFormValid 			= (validateForm) ? $parsley.validate() : true;
				  _taskConfig.url  		= path + controller;



			if(isFormValid)
			{
				if(_taskConfig.buttonLoader)
				{
					_taskConfig.buttonId = targetId;

					button_loader(targetId, 1);
				}

				if(wConfirm)
				{
					const taskConfirm 	  = $('#task_confirm_modal');

					taskConfirm.confirmModal({
						topOffset 	: 0,
					/* 	onOkBut 	: _processAction, */
						onLoad 		: _loadConfirm,
					});


				}
				else
				{
					_processAction();
				}
			}
		});

		document.addEventListener('keydown', function disableEnter(e){
			prevent = ['btn-return-task', 'btn-disapprove-task', 'btn-approve-task', 'btn-get-task', 'btn-submit-task', 'btn-save-task'];

			if(e.key == 'Enter' && prevent.includes(e.target.id))
				e.preventDefault();
	 	});

	 	$(".auto_save_dr").off("change").on("change", function() {
	 		const check_flag = ($(this).prop('checked') == true)? "Y": "N";
			const revert_prop = ($(this).prop('checked') == true)? false: true;

	 		start_loading();

	 		$.post($base_url + $tasks + $module_task + "/update_last_dr_flag/", {dr_gr_id: $('#dr_gr_id').val() || "0", last_dr_flag : check_flag, core_task_id: $('#core_task_id').val() || "0", dependent_task: $('#dependent_task').val() || "0"}, function(result) {
	 			end_loading();
				notification_msg(result.status, result.msg);

				if(result.status == "error")
				{
					$(".auto_save_dr").prop('checked', revert_prop);
				}
			}, 'json');
	 	});
	};

	var _processAction = function(){
		var concatForm = ['btn-return-task', 'btn-disapprove-task', 'btn-approve-task'];

		if(concatForm.includes(_taskConfig.targetId))
			 _taskConfig.formData += '&' + $('.confirmModal').find('#task_form_confirm_modal').serialize();

		var options = {
			blockUI	   : true,
			body       : _taskConfig.formData,
			path       : _taskConfig.url,
			successFunc: function(response){

				if(_taskConfig.buttonLoader)
					button_loader(_taskConfig.buttonId, 0);

				if(response.flag == $.CONSTANTS.SUCCESS)
				{
					start_loading();

					end_loading();

					notification_msg(response.flag, response.msg);

					setTimeout( () => start_loading() , 1000);

					location.reload();

					/* if(_taskConfig.hasUpload == true && (response.doc_ref) )
					{
						Documents.set(response.doc_ref);

						for(var a of autoSubObj)
							window[a].startUpload();
					}
					else
					{
						end_loading();

						notification_msg(response.flag, response.msg);

						setTimeout( () => start_loading() , 1000);

						location.reload();
					}	 */
				}
				else
				{
					end_loading();

					notification_msg(response.flag, response.msg);
				}
			},
			completeFunc: function(){

			}
		};

		General.Fetch(options);
	};

	var _loadConfirm   = function(){

		var p 		   = `You are about to <b>${_taskConfig.statusText}</b> this task. Are you sure you want to proceed?`;

		switch(_taskConfig.targetId)
		{
			case 'btn-approve-task':
			case 'btn-disapprove-task':

				required 	= (_taskConfig.targetId == 'btn-approve-task') ? 'false' : 'true';
				additional 	= `
					<div class="row p-n m-b-sm">
						<div class="col s12 p-n">
							<div class="input-field  p-n">
								<h6>Remarks/Comment</h6>
								<textarea id="task_remark" name="task_remark" class="materialize-textarea m-t-sm" data-parsley-error-message="This field is required. Please indicate your reasons for returning this task." data-parsley-required="${required}"></textarea>
								<label for="task_remark"></label>
							</div>
						</div>
					</div>
				`;
			break;

			case 'btn-return-task':
				additional = `
					<div class="row p-n m-b-sm">
						<div class="col s12 p-n">
							<div class="input-field">
								<h6 class="required">Return to: </h6>
								<select id="task_return" class="selectize white browser-default task_select m-t-sm" name="task_return" placeholder="Select task" data-parsley-required="true"></select>
								<label for="task_return"></label>
							</div>

							<div class="input-field  p-n">
								<h6 class="required">Remarks/Comment</h6>
								<textarea id="task_remark" name="task_remark" class="materialize-textarea m-t-sm" data-parsley-error-message="This field is required. Please indicate your reasons for returning this task." data-parsley-required="true"></textarea>
								<label for="task_remark"></label>
							</div>
						</div>
					</div>
				`;

				$.post($base_url + 'transactions/task/get_return_values', _taskConfig.formData, function(result) {
					for(let i = 0; i < result.return_val.length; i++){

						let value = result.return_val[i].ret_pria_task_id,
							name  = result.return_val[i].actor_name + ' ( '+ result.return_val[i].task_name + ' )';


						$('.confirmModal_content select.task_select')
							.append($("<option></option>")
							.attr("value", value)
							.text(name));
					}
				}, 'json');
			break;

			default:
				additional = '';
		}

		$('.confirmModal_content h4').html(`<span class="font-lg font-normal">Are you sure?</span>`);
		$('.confirmModal_body ').find('form').removeAttr('novalidate');
		$('.confirmModal_content p').html(p);
		$('.confirmModal_content div.additonal').html(additional);

		$(document).off('click').on('click', '#btn-task-ok', function(){

			const form = $('.confirmModal').find('#task_form_confirm_modal');

			if(form.parsley().validate()){
				$('#task_confirm_modal').confirmModal('hide');

				_processAction();
			}
		});
	};

	var initDropdown = function(){
		$('.dropdown-trigger').dropdown( {belowOrigin:true, constrainWidth:false} );

		console.log('inside InitDropdown');
	};

	var successCallback = function()
    {
        //hide system file name
        $('.ajax-file-upload-filename').hide();
    };

    var removeAttachment = function()
	{
		 deleteObj = new handleData({ module: 'common', controller : 'task_attachment', method : 'delete_attachment'  });
	}

	return {
		init,
		initPage,
		toggleFilter,
		//appendTask,
		tagStatus,
		initDropdown,
		successCallback,
		removeAttachment
	};
}(jQuery, document, window));