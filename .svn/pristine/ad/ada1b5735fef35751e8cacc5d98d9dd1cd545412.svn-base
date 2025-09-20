<div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="<?php echo $class_label ?>">Profit/Cost Center</label>
        </div>

        <div class="col l3 m8 s12 ">
        <?php
            $profit_cost_code = ( ! EMPTY($site_details['cost_center_code'])) ? $site_details['cost_center_code'] : '';;
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$profit_cost_code</div>
EOS
            : 
			<<<EOS
                <input type="text" name="profit_cost_code" id="profit_cost_code" placeholder="Enter Profit/Cost Code" data-parsley-required="true" value="$profit_cost_code"/>
EOS;
		?>
        </div>
    </div>
</div>


<div class="input-field m-n">
    <div class="row m-b-n p-n">
        <div class="col l3 m4 s12 label-col p-r-md">
            <label class="<?php echo $class_label ?>">Site Code</label>
        </div>

        <div class="col l3 m8 s12 ">
        <?php
            $site_code = ( ! EMPTY($site_details['site_code'])) ? $site_details['site_code'] : '';;
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$site_code</div>
EOS
            : 
			<<<EOS
                <input type="text" name="site_code" id="site_code" placeholder="Enter Site Code" data-parsley-required="true" value="$site_code"/>
EOS;
		?>
        </div>
    </div>
</div>