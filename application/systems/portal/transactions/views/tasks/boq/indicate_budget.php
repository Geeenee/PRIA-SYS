<?php
$reco_addtl_label   = ($boq_details['additional_flag'])? "Additional": "Recommended";
$as_built_label     = ($boq_details['additional_flag'])? "As Built Plan": "BOQ";
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Site Nomination</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['site_nomination']; ?></div>
        </div>

        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>

        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">           
            <label class="view"><?php echo $reco_addtl_label; ?> Amount for Civil Works</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $reco_amount_civil_works = ( ! EMPTY($boq_details['reco_amount_civil_works'])) ? number_format($boq_details['reco_amount_civil_works'], DECIMAL_PLACES) : '';
            echo 
            <<<EOS
                <div class="div-task-values">$reco_amount_civil_works</div>
EOS;
        ?>
        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="view"><?php echo $reco_addtl_label; ?> Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $reco_amount_signage = ( ! EMPTY($boq_details['reco_amount_signage'])) ? number_format($boq_details['reco_amount_signage'], DECIMAL_PLACES) : '';
            echo
            <<<EOS
                <div class="div-task-values">$reco_amount_signage</div>
EOS;
        ?>
        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="<?php echo $class_label ?>">Budget Amount for Civil Works</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $regex  = PARSLEY_AMOUNT_REGEX;
            $budget_amount_civil_works = ( ! EMPTY($boq_details['budget_amount_civil_works'])) ? $boq_details['budget_amount_civil_works'] : '';
            $budget_amount_civil_works_disp = ( ! EMPTY($boq_details['budget_amount_civil_works'])) ? $boq_details['budget_amount_civil_works'] : 'N/A';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$budget_amount_civil_works_disp</div>
EOS
            : 
            <<<EOS
                <input type="text" name="budget_amount_civil_works" id="budget_amount_civil_works" placeholder="Enter Amount" data-parsley-required="true" value="$budget_amount_civil_works"/>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="<?php echo $class_label ?>">Budget Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $regex  = PARSLEY_AMOUNT_REGEX;
            $budget_amount_signage = ( ! EMPTY($boq_details['budget_amount_signage'])) ? $boq_details['budget_amount_signage'] : '';
            $budget_amount_signage_disp = ( ! EMPTY($boq_details['budget_amount_signage'])) ? $boq_details['budget_amount_signage'] : 'N/A';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$budget_amount_signage_disp</div>
EOS
            : 
            <<<EOS
                <input type="text" name="budget_amount_signage" id="budget_amount_signage" placeholder="Enter Amount" data-parsley-required="true" value="$budget_amount_signage"/>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
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
            <label class="view">Project Engineer Recommendations</label>
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
            <label class="view">BCH Remarks on Recommendation</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_recom_bh = ( ISSET($boq_details['boq_recommendation_bh']) && ! EMPTY($boq_details['boq_recommendation_bh'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($boq_details['boq_recommendation_bh']): $boq_details['boq_recommendation_bh']) : ''; 

            echo
            <<<EOS
                <div class="div-task-values">$boq_recom_bh</div>
EOS;
        ?>
           
        </div>
    </div>
</div>


<div class="input-field m-n none">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="<?php echo $class_label ?>">Budgeted ?</label>
        </div>

        <div class="col l9 m8 s12 ">
                <?php
                     $initial_yes   = INITIAL_YES;
                     $checked       = 'checked'; // $initial_yes == $boq_details['budgeted_flag'] ? 'checked' : ''; 
                     $checked_text  = $initial_yes == $boq_details['budgeted_flag'] ? 'Yes' : 'No'; 
                     echo ($view)
                     ? 
                     <<<EOS
                         <div class="div-task-values">$checked_text</div>
EOS
                     : 
                     <<<EOS
                         <div class="input-field m-n">  
                         <input type="checkbox" class="labelauty" name="budgeted" id="budgeted" value="$initial_yes" data-labelauty="No|Yes" $checked/>
                         </div> 
EOS;
                ?>
        </div>
    </div>
</div>

