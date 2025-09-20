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
		<div class="col l3 m4 s12   label-col p-r-md">
			<label class="<?php echo $class_label ?>">File for submission</label>
		</div>

		<div class="col l9 m8 s12 ">
        <?php
            if($view)
            {
                $f = [];

                foreach($file_list as $c)
                {
                    if( ! EMPTY($c['sys_file_name']))
                        $f[] = $c['document_type_name'];
                }

                $documents = implode(', ', $f);

                echo <<<EOS
                    <div class="div-task-values">
                        $documents
                    </div>
EOS;
            }
            else
            {
                echo <<<EOS
                    <select id="completion_files" class="selectize" name="completion_files[]" placeholder="Select file" data-parsley-required="true" multiple>
                        <option value=""></option>
EOS;

                foreach($file_list as $c)
                {
                    $selected = ! EMPTY($c['sys_file_name']) ? 'selected' : '';
                    echo <<<EOS
                        <option value="{$c['document_type_code']}" $selected>{$c['document_type_name']}</option>
EOS;
                }

                echo <<<EOS
                    </select>
EOS;
            }
		?>
		</div>
	</div>
</div>

<?php
    //print_var_export($file_list, $task_documents);
    foreach($file_list as $fl)
    {
        $id     = strtolower($fl['document_type_code']);
        $class  = ( ! EMPTY($fl['sys_file_name'])) ? '' : 'hide';

        echo <<<EOS
        <div class="input-field m-n $class" id="{$id}_container">
            <div class="row m-b-n p-n">
                <div class="col l3 m4 s12   label-col p-r-md">
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