<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Business Center</label>
        </div>

        <div class="col l9 m8 s12">
            <div class="div-task-values"><?php echo $site_details['name'] ?></div>
        </div>

        
    </div>
</div>


<?php if($task_status_id != TASK_STATUS_DONE){ ?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Suggested Store Name</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $site_details['suggested_store_name'] ?></div>
        </div>

        
    </div>
</div>
<?php } ?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label>Site Nomination Form File</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php echo $task_documents[DOC_TYPE_SITE_FORM]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Official Store Name</label>
		</div>

		<div class="col l9 m8 s12 ">
		
        <?php
            $store_name = ( ! EMPTY($site_details['official_store_name'])) ? $site_details['official_store_name'] : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$store_name</div>
EOS
            : 
			<<<EOS
                <input type="text" name="official_store_name" id="official_store_name" placeholder="Enter Official Store Name" data-parsley-required="true" value="$store_name"/>
EOS;
		?>

		</div>
		
	</div>
</div>
