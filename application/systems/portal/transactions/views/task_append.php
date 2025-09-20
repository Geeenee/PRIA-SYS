<div class="form-basic p-lg p-t-md white">	
	<div class="row m-b-lg">
        <div class="col s12">
	    	<div class="input-field">
                <h6><b>Reference : <?php echo $trans_ref; ?></b></h6>
	      	</div>
	    </div>
    </div>

<!--     <input type="hidden" name="ac" id="ac"   value="<?php //echo $ag_code; ?>" />
    <input type="hidden" name="tid" id="tid" value="<?php //echo $ref_id; ?>" /> -->

    <input type="hidden" name="pwi" id="pwi"   value="<?php echo $pria_workflow_id; ?>" />

    <div class="row">
	    <div class="col s12">
	    	<div class="input-field">
                <select id="task" name="task" class="selectize" data-parsley-required="true">
                    <option value=""></option>
                <?php 
                    foreach($options as $o)
                    {
                        echo <<<EOS
                            <option value="{$o['value']}">{$o['workflow_name']}</option>
EOS;
                    }
                ?>    
                </select>

	      		<label for="task" class="active required">Task</label>
	      	</div>
	    </div>
	</div>
</div>