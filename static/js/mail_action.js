var MailAction = (function($, document, window)
{ 
	var init = function(){
		document.addEventListener('click', function(ev){
			let targetElem 		= ev.target,
				  condi 	 	= false,
				  isValid 	 	= false,
				  validateForm 	= true,
				  btnIds 		= ['btn-approve-task', 'btn-return-task', 'btn-disapprove-task'],
				  return_req	= false;

			if(btnIds.includes(targetElem.id))
			{
				switch(targetElem.id)
				{
					case 'btn-approve-task':
						str_val 	 = 'APPROVED';
						condi 		 = 'tag_approve';
						isValid 	 = true;
						validateForm = false;
					break;

					case 'btn-disapprove-task':
						str_val 	 = 'DISAPPROVED';
						condi 		 = 'tag_disapprove';
						isValid 	 = false;
						validateForm = true; 
					break;

					case 'btn-return-task':
						str_val 	 = 'RETURNED';
						condi 		 = 'tag_return';
						isValid 	 = false;
						validateForm = true;
						return_req	 = true;
					break;
				}

				if($('#return_task_id').length > 0)
				{
					$('#return_task_id').attr('data-parsley-required', return_req);
				}

				if(validateForm)
				{
					$form 		= $('#mail_action_form');
					$parlsey 	= $form.parsley();
					isValid     = $parlsey.validate();
				}


				if(condi !== false  && isValid == true){
					$('#confirm_modal').confirmModal({
						onOkBut : function(event) {

							if(isValid)
								_tagStatusOk(condi);
							
						},
						onLoad 	: function(event) {
							var p = `This action will tag the task as <b>${str_val}</b> and cannot be undone.`;
							
							$('.confirmModal_content h4').html(`<span class="font-lg font-normal">Are you sure?</span>`);
							$('.confirmModal_body ').find('form').attr('id', 'confirm-task-form');
							$('.confirmModal_content p').html(p);
						}	
					});
				}	
					
			}

		});	
	};


	var _tagStatusOk = function(condi){
		
		const options = {
			blockUI    : true,
			body 	   : $('#mail_action_form').serialize(),
			path       : $base_url + 'mail_action/' + condi,
			successFunc: function(response){	
				notification_msg(response.flag, response.msg);
				
				if(response.flag == $.CONSTANTS.SUCCESS)
					setTimeout( args => { start_loading(); location.reload(); } , 3000);
				
			},
		};

		General.Fetch(options); 
	};


	return {
		init 
	};

}(jQuery, document, window));