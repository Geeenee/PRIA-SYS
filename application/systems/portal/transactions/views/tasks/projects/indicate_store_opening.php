<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12    label-col p-r-md">
            <label class="view">Site</label>
        </div>

        <div class="col l9 m8 s12  ">
            <div class="div-task-values"><?php echo $boq_details['boq_code'].' - '.$boq_details['official_store_name']; ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12    label-col p-r-md">
            <label class="view">Turnover date</label>
        </div>

        <div class="col l9 m8 s12  ">
            <div class="div-task-values"><?php echo std_date_format($proj_details['turnover_date']); ?></div>
        </div>
    </div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Opening Date</label>
		</div>

		<div class="col l3 m8 s12  ">
        <?php
            $opening_date = (EMPTY($proj_details['opening_date'])) ? '' : std_datepicker_format($proj_details['opening_date']);
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$opening_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker_start" name="opening_date" id="opening_date" placeholder="Enter Opening Date" data-parsley-required="true" value="$opening_date"/>
EOS;
		?>
		</div>
		
	</div>
</div>
