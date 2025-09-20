<?php
$roh_recommendation_label   = "Recommendations";
$bh_recommendation_label    = $rh_recommendation_label = "Remarks on Recommendation";
$with_bh_reco = $with_rh_reco = FALSE;

switch($core_workflow_task_id)
{
    case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION:
        $rh_recommendation_label    = "RH Remarks on Recommendation";
    case CORE_TASK_SITES_RECOM_SITE_PRES:
        $with_rh_reco               = TRUE;
        $bh_recommendation_label    = "BCH Remarks on Recommendation";
    case CORE_TASK_SITES_RECOM_SITE_REGIONAL:
        $with_bh_reco               = TRUE;
        $roh_recommendation_label   = "ROH Recommendations";
    break;
}
?>
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
        <div class="col l3 m4 s12  label-col p-r-md">
            <label class="view">Official Store Name</label>
        </div>

        <div class="col l9 m8 s12 ">
            <div class="div-task-values"><?php echo $site_details['official_store_name'] ?></div>
        </div>

        
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col  p-r-md">
            <label>Site Nomination Form File</label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php echo $task_documents[DOC_TYPE_SITE_FORM]; ?>    
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">iView Map File</label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php 
            echo $task_documents[DOC_TYPE_IVIEW_MAP];
        ?>    
        </div>
	</div>
</div>

<div class="input-field m-n b-t p-t-sm">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>"><?php echo $roh_recommendation_label; ?></label>
		</div>

        <div class="col l9 m8 s12 ">
        <?php
            $sn_recom = ( ISSET($site_details['sn_recommendation']) && ! EMPTY($site_details['sn_recommendation'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($site_details['sn_recommendation']): $site_details['sn_recommendation']) : ''; 

            echo ( $w_edit_recom == FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$sn_recom</div>
EOS
            : 
			<<<EOS
            <textarea id="recommendation" name="recommendation" class="materialize-textarea m-t-sm" data-parsley-required="false" style="min-height:100px; overflow: auto;">$sn_recom</textarea>    
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
            $sn_recom_bh = ( ISSET($site_details['sn_recommendation_bh']) && ! EMPTY($site_details['sn_recommendation_bh'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($site_details['sn_recommendation_bh']): $site_details['sn_recommendation_bh']) : ''; 

            echo ( $w_edit_recom_bh == FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$sn_recom_bh</div>
EOS
            : 
            <<<EOS
            <textarea id="recommendation_bh" name="recommendation_bh" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required" style="min-height:100px; overflow: auto;">$sn_recom_bh</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($with_rh_reco): ?>
<div class="input-field m-n b-t p-t-sm">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12  label-col p-r-md">           
            <label class="<?php echo $class_label ?>"><?php echo $rh_recommendation_label; ?></label>
        </div>

        <div class="col l9 m8 s12 ">
        <?php
            $sn_recom_rh = ( ISSET($site_details['sn_recommendation_rh']) && ! EMPTY($site_details['sn_recommendation_rh'])) ? (( $w_edit_recom == FALSE || $view == TRUE)? nl2br($site_details['sn_recommendation_rh']): $site_details['sn_recommendation_rh']) : ''; 

            echo ( $w_edit_recom_rh == FALSE || $view == TRUE)
            ? 
            <<<EOS
                <div class="div-task-values">$sn_recom_rh</div>
EOS
            : 
            <<<EOS
            <textarea id="recommendation_rh" name="recommendation_rh" class="materialize-textarea m-t-sm" data-parsley-required="true" required="required" style="min-height:100px; overflow: auto;">$sn_recom_rh</textarea>    
EOS;
        ?>
           
        </div>
    </div>
</div>
<?php endif; ?>