<?php
$reco_addtl_label   = ($boq_details['additional_flag'])? "Additional": "Recommended";
$as_built_label     = ($boq_details['additional_flag'])? "As Built Plan": "BOQ";
$addtl_req          = ($boq_details['additional_flag'])? "": $class_label;
$addtl_req_fld      = ($boq_details['additional_flag'])? "false": "true";

$roh_recommendation_label   = "Recommendations";
$bh_recommendation_label = $rh_recommendation_label = "Remarks on Recommendation";
$with_bh_reco = $with_rh_reco = FALSE;

switch($core_task_id)
{
    case CORE_TASK_BOQ_PRES_APPROVED:
        $rh_recommendation_label    = "MCS Remarks on Recommendation";
    case CORE_TASK_BOQ_MCS_APPROVED:
        $with_rh_reco               = TRUE;
        $bh_recommendation_label    = "BCH Remarks on Recommendation";
    case CORE_TASK_BOQ_MCS_ENGINEERING:
        $with_bh_reco               = TRUE;
        $roh_recommendation_label   = "Project Engineer Recommendations";
    break;
}

?>
<input type="hidden" id="additional_flag" name="additional_flag" value="<?php echo $boq_details['additional_flag']; ?>" />
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

<?php if( ! EMPTY($boq_details['final_amount']) || $core_task_id == CORE_TASK_BOQ_MCS_APPROVED) { ?>
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
            <label class="<?php echo $class_label ?>">Final <?php echo $reco_addtl_label; ?> Amount for Civil Works</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $regex  = PARSLEY_AMOUNT_REGEX;
            $final_amount_civil_works = ( ! EMPTY($boq_details['final_amount_civil_works'])) ? number_format($boq_details['final_amount_civil_works'], DECIMAL_PLACES) : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$final_amount_civil_works</div>
EOS
            : 
            <<<EOS
                <input type="text" name="final_amount_civil_works" id="final_amount_civil_works" placeholder="Enter Amount" class="number"    data-parsley-pattern="$regex" data-parsley-required="true" value="$final_amount_civil_works"/>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="<?php echo $addtl_req ?>">Final <?php echo $reco_addtl_label; ?> Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $regex  = PARSLEY_AMOUNT_REGEX;
            $final_amount_signage = ( ! EMPTY($boq_details['final_amount_signage'])) ? number_format($boq_details['final_amount_signage'], DECIMAL_PLACES) : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$final_amount_signage</div>
EOS
            : 
            <<<EOS
                <input type="text" name="final_amount_signage" id="final_amount_signage" placeholder="Enter Amount" class="number" data-parsley-pattern="$regex" data-parsley-required="$addtl_req_fld" value="$final_amount_signage"/>
EOS;
        ?>

        </div>
        
    </div>
</div>
<?php } ?>

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
            <label class="view"><?php echo $roh_recommendation_label; ?></label>
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

<?php if($with_bh_reco): ?>
<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>"><?php echo $bh_recommendation_label; ?></label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_recom_bh = ( ISSET($boq_details['boq_recommendation_bh']) && ! EMPTY($boq_details['boq_recommendation_bh'])) ? (( $w_edit_recom_bh == FALSE || $view == TRUE)? nl2br($boq_details['boq_recommendation_bh']): $boq_details['boq_recommendation_bh']) : ''; 

            echo ( $w_edit_recom_bh == FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$boq_recom_bh</div>
EOS
            : 
            <<<EOS
            <textarea id="recommendation_bh" name="recommendation_bh" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$boq_recom_bh</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>
<?php endif; ?>


<?php if($with_rh_reco) { ?>
<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>">Justification for Approval</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_justification = ( ISSET($boq_details['boq_justification']) && ! EMPTY($boq_details['boq_justification'])) ? (( $w_edit_recom_rh == FALSE || $view == TRUE)? nl2br($boq_details['boq_justification']): $boq_details['boq_justification']) : ''; 

            echo ( $w_edit_recom_rh === FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$boq_justification</div>
EOS
            : 
            <<<EOS
            <textarea id="justification" name="justification" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$boq_justification</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>
<!--div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php //echo $class_label ?>"><?php //echo $rh_recommendation_label; ?></label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            /*$boq_recom_rh = ( ISSET($boq_details['boq_recommendation_rh']) && ! EMPTY($boq_details['boq_recommendation_rh'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($boq_details['boq_recommendation_rh']): $boq_details['boq_recommendation_rh']) : ''; 

            echo ( $w_edit_recom_rh == FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$boq_recom_rh</div>
EOS
            : 
            <<<EOS
            <textarea id="recommendation_rh" name="recommendation_rh" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$boq_recom_rh</textarea>    
EOS;*/
        ?>
           
        </div>
    </div>
</div-->
<?php } ?>