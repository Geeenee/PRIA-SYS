<?php
$reco_addtl_label   = ($boq_details['additional_flag'])? "Additional": "Recommended";
$as_built_label     = ($boq_details['additional_flag'])? "As Built Plan": "BOQ";
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Site Nomination</label>
        </div>

        <div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['site_nomination']; ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>

       
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">           
            <label class="view">Approved Amount for Civil Works</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $final_amount_civil_works = ( ! EMPTY($boq_details['final_amount_civil_works'])) ? number_format($boq_details['final_amount_civil_works'], DECIMAL_PLACES) : '';
            echo 
            <<<EOS
                <div class="div-task-values">$final_amount_civil_works</div>
EOS;
        ?>
        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="view">Approved Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $final_amount_signage = ( ! EMPTY($boq_details['final_amount_signage'])) ? number_format($boq_details['final_amount_signage'], DECIMAL_PLACES) : '';
            echo
            <<<EOS
                <div class="div-task-values">$final_amount_signage</div>
EOS;
        ?>
        </div>
        
    </div>
</div>
<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="view">Budget Amount for Civil Works</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $budget_amount_civil_works = ( ! EMPTY($boq_details['budget_amount_civil_works'])) ? $boq_details['budget_amount_civil_works'] : 'N/A';
            echo
            <<<EOS
                <div class="div-task-values">$budget_amount_civil_works</div>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="view">Budget Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $budget_amount_signage = ( ! EMPTY($boq_details['budget_amount_signage'])) ? $boq_details['budget_amount_signage'] : 'N/A';
            echo  
            <<<EOS
                <div class="div-task-values">$budget_amount_signage</div>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view"><?php echo $as_built_label; ?> File</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_BOQ]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">RFA Form File</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_RFA]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="view">Recommendations</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_recom = ( ISSET($boq_details['boq_recommendation']) && ! EMPTY($boq_details['boq_recommendation'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($boq_details['boq_recommendation']): $boq_details['boq_recommendation']) : ''; 

            echo
            <<<EOS
                <div class="div-task-values">$boq_recom</div>
EOS;
        ?>
           
        </div>
    </div>
</div>
<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="view">Justification for Approval</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_justification = ( ISSET($boq_details['boq_justification']) && ! EMPTY($boq_details['boq_justification'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($boq_details['boq_justification']): $boq_details['boq_justification']) : ''; 

            echo
            <<<EOS
                <div class="div-task-values">$boq_justification</div>  
EOS;
        ?>
           
        </div>
    </div>
</div>
