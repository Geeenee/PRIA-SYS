<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
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
            <label class="view">Vendor</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['awarded_contractor_name'] ?></div>
        </div>
    </div>
</div>

<?php
    $this->view('amounts_field', [
        'civil_amount' => $boq_details['final_amount_civil_works'],
        'signage_amount' => $boq_details['budget_amount_signage'],
    ]);
?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12   label-col p-r-md">
            <label class="<?php echo $class_label ?>">BOQ File</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_BOQ_PROGRESS]; ?>
        </div>
    </div>
</div>
