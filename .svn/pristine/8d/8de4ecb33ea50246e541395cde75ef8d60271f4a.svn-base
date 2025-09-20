<?php ISSET($file_name) ? $file_name : ''; ?>
<?php ISSET($label_name) ? $label_name : ''; ?>

<div class="form-layout-1">
	<div class="row m-n">

		<input type="hidden" name="tmp_file" id="tmp_file_id" value="<?php echo $file_name; ?>">
		
		<div class="col s12">
			<div class="input-field p-md m-md red lighten-3 white-text none" id="error_div">
			</div>
		</div>

		<div class="col s12">
			<div class="input-field p-md">
				<h6 class="font-md">To see the required excel format, download this <a href="<?php echo base_url().PATH_QA_TEMPLATE.$file_name; ?>"><?php echo $label_name; ?> template</a></h6>
			</div>
		</div>

  		<div class="col s12">
  			<div class="input-field p-l-md p-r-md">
				<h6 class="font-md">To import <b><?php echo $label_name; ?></b>, upload an excel in .xls or .xlsx file:</h6>
			</div>

			<div class="input-field p-l-md p-r-md">
				<a href="#" id="attachments_upload" class="center m-r-sm">Select File</a>
				<input type="hidden" name="multiple_file_name" id="attachments" value="" />
				<label class="p-t-n active"></label>
			</div>
		</div>
	</div>
</div>