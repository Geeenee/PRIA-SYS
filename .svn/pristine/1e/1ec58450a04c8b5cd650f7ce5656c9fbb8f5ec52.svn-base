<?php
$file_name			= ISSET($file_name) ? $file_name : '';
$label_name			= ISSET($label_name) ? $label_name : '';

$file_extensions	= (COUNT($allowed_extensions) > 0)? implode(", ", array_map("strtoupper", array_filter($allowed_extensions))): "";
?>

<div class="form-layout-1">
	<div class="row m-n">

		<input type="hidden" name="tmp_file" id="tmp_file_id">
		<input type="hidden" name="batch_value" value="<?php echo $file_name; ?>">
		
		<div class="col s12">
			<div class="input-field p-md m-md red lighten-3 white-text none" id="error_div">
			</div>
		</div>

		<div class="col s12">

			<div class="input-field p-l-md p-r-md">
				<h6>To import <b><?php echo $label_name; ?></b>, upload file(s):</h6>
				<br>
				<div class="red-text font-sm"><?php echo $format; ?></div>
				<br>
				<div class="font-sm">Note: <span class="red-text">__ is 2 underscores. Bracket not included.</span></div>
				<br>
				<h6 class="font-sm"><?php echo $example ?></h6>
				<br>
				<h6 class="font-sm"><?php echo (ISSET($multiple_pr))? $multiple_pr: "" ?></h6>
				<br>
			</div>

			<div class="input-field p-l-md p-r-md">
				<p class="font-bold">Select <?php echo $file_extensions; ?> file(s):</p>
				<div id="attachments_section" class="field-multi-attachment">
					<input type="hidden" id="attachments" name="multiple_file_name[]" value="" class="form_dynamic_upload"/>
					<input type="hidden" id="attachments_orig_filename" name="file_orig[]" value="" class="form_dynamic_upload_origfilename"/>
					<a href="#" id="attachments_upload" class="tooltipped m-r-sm m-b-md" data-position="bottom" data-delay="50" data-tooltip="Upload">Select Files</a>
				</div>
			</div>
		</div>
	</div>
</div>