<div class="form-layout-1 p-md">
	<div class="file-field input-field">
					    
		<input type="hidden" name="prim_id" value="<?php echo $prim_id; ?>" placeholder="">
		<input type="hidden" name="doc_type" value="<?php echo $doc_type; ?>" placeholder="">

		<!-- <a href="#" id="attach_file_upload" class="center m-r-sm">Please select a file</a>
			<input type="hidden" name="version_sysfile[]" id="attach_file" value="" required/>
			<input type="hidden" name="attachments_orig_filename[]" id="attach_file" value="" required/>

			<label class="p-t-n active"></label>

	    <div class="file-path-wrapper p-l-n">
	        <input class="file-path validate" type="hidden" readonly placeholder="Upload file" name="attach_file" value="">
	    </div> -->

		<div class="input-field p-l-md p-r-md">
				
				<div id="attachments_section" class="field-multi-attachment">
					<input type="hidden" id="attachments" name="multiple_file_name[]" value="" class="form_dynamic_upload"/>
					<input type="hidden" id="attachments_orig_filename" name="file_orig[]" value="" class="form_dynamic_upload_origfilename"/>
					<a href="#" id="attachments_upload" class="tooltipped m-r-sm m-b-md" data-position="bottom" data-delay="50" data-tooltip="Upload">Select Files</a>
				</div>
			</div>
	</div>
</div>