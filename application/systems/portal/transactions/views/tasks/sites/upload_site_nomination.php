<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Business Center</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $site_details['name'] ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Suggested Store Name</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $site_details['suggested_store_name'] ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="<?php echo $class_label ?>">Site Nomination Form File</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php 
            echo $task_documents[DOC_TYPE_SITE_FORM];
        ?>    
        </div>
    </div>
</div>
