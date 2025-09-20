<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Business Center</label>
        </div>
        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['name']; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">BOQ Number</label>
        </div>
        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['boq_code']; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Site Name</label>
        </div>
        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['official_store_name']; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Store Lay-Out Plan</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_LAYOUT_PLAN]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Supporting Pictures</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_SUPP_PICS]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="<?php echo $class_label ?>">Store Mock-Up Design</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_MOCKUP_DESIGN]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php //echo $class_label ?>">Specifications/Remarks</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $layout_specifications = ( ISSET($boq_details['layout_specifications']) && ! EMPTY($boq_details['layout_specifications'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($boq_details['layout_specifications']): $boq_details['layout_specifications']) : ''; 

            echo ( 1==1 OR $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$layout_specifications</div>
EOS
            : 
            <<<EOS
            <textarea id="layout_specifications" name="layout_specifications" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required" style="min-height:100px; overflow: auto;">$layout_specifications</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>