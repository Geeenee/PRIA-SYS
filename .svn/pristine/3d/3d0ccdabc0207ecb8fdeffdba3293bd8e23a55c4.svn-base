var TaskComments = (function($, document, window) 
{
	var $tasks			 = "tasks/";
	var $module_task	 = "task";
	var liIndex 		 = 0;
	var divTaskComments  = document.getElementById('task-comments-container');
	var elemTaskComment  = document.getElementById('task-comment');
	var saveCommentBtn 	 = document.getElementById('btn-save-comment');
	var cancelCommentBtn = document.getElementById('btn-cancel-comment');
	var currTci 		 = '';
	var moduleCode;
	
	var init = function(mCode){
		
		if(document.querySelector('#task-comment-form'))
		{
			const ckeditorConfig = {
				coreStyles_italic: { element: 'i', overrides: 'em' },
				coreStyles_bold  : { element: 'b', overrides: 'strong' },
				toolbarGroups    : [
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
					{ name: 'links', groups: [ 'links' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks' ] },
				],
				removeButtons : 'Underline,Subscript,Superscript,Scayt,Cut,Undo,Redo,Table,SpecialChar,HorizontalRule,Maximize,Source,Styles,Format,About,Paste,PasteText,PasteFromWord,Copy,EasyImageUpload,Anchor,Strike,RemoveFormat,Blockquote'
			};

		


			CKEDITOR.replace('task-comment', ckeditorConfig);
			
			moduleCode = mCode;

			create_avatar($('.letter-avatar'), {width:45,height:45,fontSize:30});

			const	$comment 	   = $('#task-comment'),
					$parsley	   = $comment.parsley();
				
					saveCommentBtn.addEventListener('click', function(ev){
						
						if($parsley.validate())
						{
							update_editor();

							button_loader('btn-save-comment', 1);

							const data 	  = {tid: $('#tid').val(), comment: $comment.val(), tci : currTci, module : moduleCode};
							const options = {
								body       : $.param(data),
								path       : $base_url + 'transactions/task_comment/save_comment',
								successFunc: function(response){
									notification_msg(response.flag, response.msg);

									button_loader('btn-save-comment', 0);
										
									if(response.flag == $.CONSTANTS.SUCCESS)
									{
										html = document.createRange().createContextualFragment(response.html);	

										if( ! currTci)//Insert
										{
											divTaskComments.prepend(html);
										}	
										else
										{
											replace = document.querySelector(`[data-tci="${currTci}"]`);

											divTaskComments.replaceChild(html, replace);
										}

										CKEDITOR.instances['task-comment'].setData('');

										saveCommentBtn.innerText = 'POST COMMENT';

										create_avatar($('.letter-avatar'), {width:45,height:45,fontSize:30});
										
										cancelCommentBtn.classList.add('hide');
									}	

									currTci = '';
								}
							};	
							
							General.Fetch(options);
						}
					});

			cancelCommentBtn.addEventListener('click', function(ev){
				CKEDITOR.instances['task-comment'].setData('');
				
				saveCommentBtn.innerText = 'POST COMMENT';

				cancelCommentBtn.classList.add('hide');

				currTci = '';
			});

			divTaskComments.addEventListener('click', function(ev){
				
				const targetElem  	= ev.target,
					targetClass 	= targetElem.classList,
					elemComment		= targetElem.closest('.comment');
					tci	  	  		= elemComment.dataset.tci;
					
			

				if(targetElem.closest('.task-comment-actions'))
				{	
					ev.preventDefault();

					if(targetClass.contains('edit-comment'))
						_triggerEditTask(tci, elemComment);

					if(targetClass.contains('delete-comment'))
						_triggerDeleteTask(tci, elemComment);
				}
			

			});	
		}
	};

	var _triggerEditTask   = function(tci, elemComment){
		const elemCommentContent       = elemComment.querySelector('.task-comment-content');
 			  elemCommentSectionOffset = $('#task-comments-section').offset().top - 150, 
			  comment 				   = elemCommentContent.innerHTML;

			  saveCommentBtn.innerText = 'Save Comment';

			  cancelCommentBtn.classList.remove('hide');

			  CKEDITOR.instances['task-comment'].setData(comment, {
				  callback: function(){
					CKEDITOR.instances['task-comment'].focus();

					var range = CKEDITOR.instances['task-comment'].createRange();
								range.moveToElementEditEnd( range.root );
								CKEDITOR.instances['task-comment'].getSelection().selectRanges( [ range ] );
				  }
			  });
			  
			  currTci 				   = elemComment.closest('.comment').dataset.tci;


			  $( 'html, body' ).scrollTop( elemCommentSectionOffset );
	};

	var _triggerDeleteTask = function(tci, elemComment)
	{
		$('#confirm_modal').confirmModal({
			topOffset : 0,
			onOkBut : function() {
				const options = {
					blockUI    : true,
					body 	   : $.param({tci : tci, module : moduleCode}),
					path       : $base_url + 'transactions/task_comment/delete_comment',
					successFunc: function(response){
						notification_msg(response.flag, response.msg);
						
						if(response.flag == $.CONSTANTS.SUCCESS)
							elemComment.remove();
					},
				};

				General.Fetch(options);
			},
			onLoad 		: function() {
				$('.confirmModal_content h4').html(`
					<span class="font-lg font-normal">Are you sure?</span>
				`);

			
				$('.confirmModal_content p').html(
					'This action will delete the comment and cannot be undone.'
				);
			},
			onClose : function() {}
		});
	};

	return {
		init
	};
}(jQuery, document, window));