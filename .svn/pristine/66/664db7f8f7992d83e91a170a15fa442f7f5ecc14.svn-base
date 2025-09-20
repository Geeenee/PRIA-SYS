<input class="" type="hidden" name="table_name"  value="<?php echo ISSET($table_name) ? $table_name : ''; ?>">
<input class="" type="hidden" name="batch_flag"  value="<?php echo ISSET($batch_flag) ? $batch_flag : ''; ?>">

<div class="form-layout-1">
	<div class="row m-n p-md">
		<table class="striped">
			<thead>
				<tr>
					<th class="p-sm valign-top class_to_hide">
						<input class='check_all_quick_add' type='checkbox' id="check_all_quick_add" onclick="Quick_add.selectAll('quick_add')" checked /><label for="check_all_quick_add"></label>
					</th>
					<?php foreach ($table_header as $th_val): ?>
					<th class="p-sm valign-top"><?php  echo $th_val; ?> </th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
			<?php
			$ctr = 0;
			foreach ($records as $key => $record): 
				$disabled	= (ISSET($record['error_msg']) AND !EMPTY($record['error_msg']))? "disabled": "";
				$checked	= (ISSET($record['error_msg']) AND !EMPTY($record['error_msg']))? "": ((ISSET($record['temp_reference_id']) AND !EMPTY($record['temp_reference_id']))? "": "checked");
			?>
				<tr class="p-sm">
					<td class="p-sm class_to_hide">
						<div class="input-field">
							<input class="ind_checkbox_quick_add" type="checkbox" name="import_checkbox[]" id="<?php echo 'ind_checkbox_quick_add'.$record[$primary_field]; ?>" value="<?php echo $record[$primary_field]; ?>" <?php echo $disabled . ' ' . $checked; ?> >
							<label for="<?php echo 'ind_checkbox_quick_add'.$record[$primary_field]; ?>"></label>
						</div>
					</td>

				<?php
				foreach($record as $key2 => $val):
					if($key2 != $primary_field AND $key2 != 'temp_reference_id' ):
						if($key2 == 'error_msg' AND ISSET($record['temp_reference_id']) AND !EMPTY($record['temp_reference_id'])):
							$duplicate_text	= "Duplicate entry. File contains same record. Note: " . (($table_name != Portal_Model::PORTAL_TABLE_TEMP_APVS)? "Only the first selected record will be imported if multiple duplicate records are selected.": "Record will be updated with the last selected record if multiple duplicate records are selected.");
							$val			= ((!EMPTY($val))? "<br/>": "") . $val;
						else:
							$duplicate_text	= "";
						endif;
				?>
					<td class="p-sm valign-top" <?php echo ($key2 == "error_msg")? "style='color: red;'": ""; ?>><?php echo $duplicate_text; ?><?php echo $val; ?></td>
					<?php endif;
				endforeach; ?>
				</tr>
			<?php $ctr++;
			endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<div class="col s12">
	<div class="input-field p-md m-md red lighten-3 white-text none" id="table_body"></div>
</div>

<style>
table {
    table-layout: fixed;
}
table > thead > tr > th, table > tbody > tr > td {
    word-wrap: break-word !important;
}
</style>