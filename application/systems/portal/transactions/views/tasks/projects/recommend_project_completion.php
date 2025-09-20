<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12   label-col p-r-md">
            <label class="view">Site</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['boq_code'].' - '.$boq_details['official_store_name']; ?></div>
        </div>
    </div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">
			<label>Additional PR/PO and Payment Required?</label>
		</div>

		<div class="col l9 m8 s12 valign-middle">
			<div class="input-field m-n">
			<?php
			if($view || in_array($core_task_id, [CORE_TASK_PROJ_COMPLETION_APPROVED, CORE_TASK_PROJ_COMPLETION_APPROVED_APPEND])){

            ?>
                <div class="div-task-values"><?php echo ($additional_flag == INITIAL_YES) ? 'Yes ': 'No'; ?></div>
            <?php
            }else{
            ?>
				<input type="checkbox" class="filled-in" name="additional_flag" id="additional_flag" value="<?php echo ENUM_YES ?>" <?php echo ($additional_flag == INITIAL_YES) ? 'checked ': ''; ?>/>
				<label for="additional_flag"></label>
			<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php

    foreach($file_list as $fl)
    {
        $id     = strtolower($fl['document_type_code']);
        $class  = ( ! EMPTY($fl['sys_file_name'])) ? '' : 'hide';

        echo <<<EOS
        <div class="input-field $class" id="{$id}_container">
            <div class="row m-b-n p-n">
                <div class="col l3 m4 s12  label-col p-r-md">
                    <label class="$class_label">{$fl['document_type_name']}</label>
                </div>

                <div class="col l9 m8 s12 ">
                    {$task_documents[$fl['document_type_code']]}
                </div>
            </div>
        </div>
EOS;
    }

?>