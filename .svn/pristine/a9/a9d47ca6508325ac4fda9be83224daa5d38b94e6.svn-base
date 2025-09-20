<?php 

if(!EMPTY($pr_details)){
	$pr_num 				= ISSET($pr_details['pr_num']) ? $pr_details['pr_num'] : '';
	$pr_item_type 			= ISSET($pr_details['pr_item_type']) ? $pr_details['pr_item_type'] : '';
	$gl_account_code 		= ISSET($pr_details['gl_account_code']) ? $pr_details['gl_account_code'] : '';
	$gl_account_name 		= ISSET($pr_details['gl_account_name']) ? $pr_details['gl_account_name'] : '';
	$purchasing_group_code 	= ISSET($pr_details['purchasing_group_code']) ? $pr_details['purchasing_group_code'] : '';
	$cc_codes			 	= ISSET($pr_cc_details['cost_centers']) ? $pr_details['cost_centers'] : '';
	$cc_names			 	= ISSET($pr_cc_details['cost_center_names']) ? $pr_cc_details['cost_center_names'] : '';
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

		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>PR Group</label>
		</div>

		<div class="col l3 m8 s12 ">
		<?php
        	echo '<div class="div-task-values">' . $purchasing_group_code . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">			
			<label>GL Account</label>
		</div>

		<div class="col l3 m8 s12 ">
		<?php
        	echo '<div class="div-task-values">' . $gl_account_code . ' - '. $gl_account_name .'</div>';
		?>
		</div>

		<div class="col l3 m4 s8 p-r-md label-col">			
			<label>Item</label>
		</div>

		<div class="col l3 m8 s12 ">
		<?php
			echo '<div class="div-task-values">' . $pr_item_type . '</div>';
		?>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 p-r-md label-col">
			<label class="<?php echo $class_label ?>">Cost Centers</label>
		</div>
		<div class="col l9 m8 s12 ">

		<?php 
			echo $cc_names;
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