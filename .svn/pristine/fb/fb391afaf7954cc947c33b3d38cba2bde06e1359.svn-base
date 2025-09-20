<?php 

if(!EMPTY($pr_details)){
	$pr_num 				= ISSET($pr_details['pr_num']) ? $pr_details['pr_num'] : '';
	$boq_num				= ISSET($pr_details['boq_code']) ? $pr_details['boq_code'] : '';
	$project_ref			= ISSET($pr_details['project_ref']) ? $pr_details['project_ref'] : '';
	$store					= ISSET($pr_details['official_store_name']) ? $pr_details['official_store_name'] : '';
}

?>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>PR Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $pr_num . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>BOQ Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $boq_num . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Project Reference Number</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $project_ref . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>Store</label>
		</div>

		<div class="col l3 m8 s12 valign-middle">
		<?php
        	echo '<div class="div-task-values">' . $store . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n m-b-lg m-t-lg">
	<div class="row m-b-n p-n">
		<div class="col l12 m12 s12 p-r-md">			
			<label>Asset Code and Internal Order</label>
		</div>
	</div>
	<div class="row m-b-n p-n">
        
        <div class="col l12 m12 s12  "  style="height:30%;width:100%;overflow:auto !important;">
            <table class="table table-default table-layout-auto stripped" id="tbl-category">
                <thead class="text-shadow-dark red darken-3">
                    <tr>
                        <th width="20%">Category</th>
                        <th width="35%">Asset Code</th>
                        <th width="35%">Internal Order</th>
                    </tr>
                </thead>

                <tbody id="tbody-category">
                    <?php echo $boq_asset_codes_view; ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="input-field m-n m-b-lg">
	<div class="row m-b-n p-n">
		<div class="col l12 m12 s12 p-r-md">			
			<label>Confirmed Contractor/s</label>
		</div>
	</div>
	<div class="row m-b-n p-n">
        <div class="col l12 m12 s12 p-r-md" style="height:30%;width:100%;overflow:auto !important;">
            <table class="table table-default table-layout-auto stripped" id="tbl-contractor" >
                <thead class="text-shadow-dark red darken-3">
                    <tr>
                        <th width="45%">Category</th>
                        <th width="45%">Vendor</th>
                        <?php
                         // echo ( ! $view) ? '<th width="10%">Action</th>' : '';
                        ?>
                    </tr>
                </thead>

                <tbody id="tbody-contractor">
                    <?php echo $contractor_view; ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="view">RFA Form</label>
		</div>
		<div class="col l9 m8 s12 ">

		<?php 
			echo $rfa_form;
        ?>   

		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="<?php echo $class_label ?>">PR Document File</label>
		</div>
		<div class="col l9 m8 s12 ">

		<?php 
			echo $task_documents[DOC_TYPE_PR];
        ?>   

		</div>
	</div>
</div>