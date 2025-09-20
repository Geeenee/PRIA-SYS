<?php
$reco_addtl_label   = ($boq_details['additional_flag'])? "Additional": "Recommended";
$reco_addtl_req     = ($boq_details['additional_flag'])? "": $class_label;
$reco_addtl_req_fld = ($boq_details['additional_flag'])? "false": "true";
$as_built_label     = ($boq_details['additional_flag'])? "As Built Plan": "BOQ";
?>
<input type="hidden" id="additional_flag" name="additional_flag" value="<?php echo $boq_details['additional_flag']; ?>" />
<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Site Nomination</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['site_nomination']; ?></div>
        </div>

        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>

        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col p-r-md">			
			<label class="<?php echo $class_label ?>"><?php echo $reco_addtl_label; ?> Amount for Civil Works</label>
		</div>

		<div class="col l3 m8 s12 ">
		
        <?php
            $regex 	= PARSLEY_AMOUNT_REGEX;
            $reco_amount_civil_works = ( ! EMPTY($boq_details['reco_amount_civil_works'])) ? number_format($boq_details['reco_amount_civil_works'], DECIMAL_PLACES) : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$reco_amount_civil_works</div>
EOS
            : 
			<<<EOS
                <input type="text" name="reco_amount_civil_works" id="reco_amount_civil_works" placeholder="Enter Amount" class="number" 	data-parsley-pattern="$regex" data-parsley-required="true" value="$reco_amount_civil_works"/>
EOS;
		?>

		</div>
		
	</div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">            
            <label class="<?php echo $reco_addtl_req; ?>"><?php echo $reco_addtl_label; ?> Amount for Signage</label>
        </div>

        <div class="col l3 m8 s12 ">
        
        <?php
            $regex  = PARSLEY_AMOUNT_REGEX;
            $reco_amount_signage = ( ! EMPTY($boq_details['reco_amount_signage'])) ? number_format($boq_details['reco_amount_signage'], DECIMAL_PLACES) : '';
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$reco_amount_signage</div>
EOS
            : 
            <<<EOS
                <input type="text" name="reco_amount_signage" id="reco_amount_signage" placeholder="Enter Amount" class="number" data-parsley-pattern="$regex" data-parsley-required="$reco_addtl_req_fld" value="$reco_amount_signage"/>
EOS;
        ?>

        </div>
        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="<?php echo $class_label ?>"><?php echo $as_built_label; ?> File</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_BOQ]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label class="<?php echo $class_label ?>">RFA Form File</label>
        </div>

        <div class="col l9 m8 s12 ">
            <?php echo $task_documents[DOC_TYPE_RFA]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>">Recommendations</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $boq_recom = ( ISSET($boq_details['boq_recommendation']) && ! EMPTY($boq_details['boq_recommendation'])) ? (($view == TRUE)? nl2br($boq_details['boq_recommendation']): $boq_details['boq_recommendation']) : ''; 

            echo ($view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$boq_recom</div>
EOS
            : 
            <<<EOS
            <textarea id="recommendation" name="recommendation" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$boq_recom</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>

<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>">Next Approver</label>
        </div>

        <div class="col l4 m4 s12 ">
            <?php 
                if($view){ 
                        foreach ($next_approvers as $key => $next_approver):                                
                            echo ($key == $boq_details['boq_reco_approver']) ? '<div class="font-md">'.$next_approver.'</div>' : ''; 
                        endforeach; 
                }else{ 
            ?>

            <select name="boq_reco_approver" id="boq_reco_approver" class="selectize" data-parsley-required="false">
                <option value=""></option>

                <?php foreach ($next_approvers as $key => $next_approver): ?>
                    <option <?php echo ($key == $boq_details['boq_reco_approver']) ? 'selected' : ''; ?> value="<?php echo $key ?>"><?php echo $next_approver ?></option>  
                <?php endforeach; ?>
            </select>

            <?php } ?>
        </div>
    </div>
</div>
