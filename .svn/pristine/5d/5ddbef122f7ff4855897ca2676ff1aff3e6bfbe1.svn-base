<?php 
    foreach($comments as $comment)
    { 
        $id       = $comment['pria_task_comment_id'];

        //$img_path = PATH_USER_UPLOADS.$comment['photo'];
        $img_path = PATH_USER_UPLOADS;
        
        $options  = ['class' => 'responsive-img circle ', 'style' => 'width:55px; height:55px;', 'name' => $comment['commented_by']];

        $image    = create_img_tag($comment['photo'], $img_path, $options, TRUE);

        $time     = get_date_format($comment['created_date'], 1);

        $tci      = encrypt_id($id);

        $actions  = '';
        //<a href="#"><i class="material-icons">reply</i>Reply</a>

        if($comment['created_by'] == $this->session->user_id && $task_status == TASK_STATUS_ONGOING)
        {
            $actions =<<<EOS
            <a href="javascript:;" class="edit-comment"><i class="material-icons">edit</i>Edit</a> 
            <a href="javascript:;" class="delete-comment"><i class="material-icons">delete</i>Delete</a>
EOS;
        }


        echo <<<EOS
            <div class="b-solid p-t-n m-b-n comment" data-tci="$tci" id="comment-$id">
				<div class="comment-section">					
					<div class="comment-avatar">$image</div>
					
					<div class="comment-contents">
						<div class="comment-content-top">
							<div class="comment-author">{$comment['commented_by']}</div>
							<div class="comment-timestamp">$time</div>
						</div>
						<div class="task-comment-content">{$comment['pria_task_comment']}</div>
					</div>
					<div class="task-comment-actions">$actions</div>
				</div>
            </div>
EOS;
    } 
?>
 
                    
