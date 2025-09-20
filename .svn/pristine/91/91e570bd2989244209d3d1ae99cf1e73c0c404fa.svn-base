<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 right-align  label-col p-r-md">
            <label class="view">Site</label>
        </div>

        <div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $boq_details['boq_code'].' - '.$boq_details['official_store_name']; ?></div>
        </div>

        
    </div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12 right-align  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Turnover Date</label>
		</div>

		<div class="col l3 m8 s12 ">
        <?php
            $turnover_date = (EMPTY($proj_details['turnover_date'])) ? '' : std_datepicker_format($proj_details['turnover_date']);
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$turnover_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker_start" name="turnover_date" id="turnover_date" placeholder="Enter Turnover Date" data-parsley-required="true" value="$turnover_date"/>
EOS;
		?>
		</div>
		
	</div>
</div>
