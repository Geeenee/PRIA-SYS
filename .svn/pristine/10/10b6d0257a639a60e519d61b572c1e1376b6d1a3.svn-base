<?php
	$actual_clean_up_date	= ( ISSET($clean_up_details['actual_clean_up_date']) ) ? std_datepicker_format($clean_up_details['actual_clean_up_date']) : '';
	$harvested_head         = ( ISSET($clean_up_details['harvested_heads_num']) ) ? $clean_up_details['harvested_heads_num'] : '';
	$harvested_kilos        = ( ISSET($clean_up_details['harvested_kilos_num']) ) ? $clean_up_details['harvested_kilos_num'] : '';
	$feeds_delivered 		= ( ISSET($clean_up_details['delivered_feeds_num']) ) ? $clean_up_details['delivered_feeds_num'] : '';
	$feeds_used     		= ( ISSET($clean_up_details['feeds_used_num']) ) ? $clean_up_details['feeds_used_num'] : '';

	//$medvac_dr_numbers		= ISSET($doc_medvac_numbers['dr_nums']) ? $doc_medvac_numbers['dr_nums'] : '' ;
	//$doc_gr_numbers			= ISSET($doc_gr_numbers['gr_nums']) ? $doc_gr_numbers['gr_nums'] : '';

	$regex 					= PARSLEY_AMOUNT_REGEX;
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Actual Clean-Up Date</label>
		</div>

		<div class="col l3 m8 s12  ">
		
        <?php
            echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$actual_clean_up_date</div>
EOS
            : 
			<<<EOS
				<input type="text" class="datepicker_start" name="actual_clean_up_date" id="actual_clean_up_date" placeholder="Enter Actual Clean Up Date" data-parsley-required="true" value="$actual_clean_up_date" data-max-date="0"/>
EOS;
		?>

		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">Harvested Head</label>
		</div>

		<div class="col l3 m8 s12  ">

		<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$harvested_head</div>
EOS
            : 
			<<<EOS
				<input type="text" name="harvested_head" id="harvested_head" placeholder="Enter Harvested Head" data-parsley-required="true" data-parsley-type="digits" value="$harvested_head"/>
EOS;
        ?>

		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">Harvested Kilos</label>
		</div>

		<div class="col l3 m8 s12  ">

		<?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$harvested_kilos</div>
EOS
            : 
			<<<EOS
				<input type="text" name="harvested_kilos" id="harvested_kilos" placeholder="Enter Harvested Kilos" data-parsley-required="true"  data-parsley-pattern="$regex" value="$harvested_kilos"/>
EOS;
        ?>

		</div>
	</div>
</div>


<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label class="<?php echo $class_label ?>">Feeds Delivered (in bags)</label>
		</div>

		<div class="col l3 m8 s12  ">

        <?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$feeds_delivered</div>
EOS
            : 
			<<<EOS
				<input type="text" name="feeds_delivered" id="feeds_delivered" placeholder="Enter Feeds Delivered" data-parsley-required="true"  data-parsley-type="digits" value="$feeds_delivered"/>
EOS;
        ?>
		
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label class="<?php echo $class_label ?>">Feeds Used (in bags)</label>
		</div>

		<div class="col l3 m8 s12  ">

        <?php
			echo ($view)
            ? 
            <<<EOS
                <div class="div-task-values">$feeds_used</div>
EOS
            : 
			<<<EOS
				<input type="text" name="feeds_used" id="feeds_used" placeholder="Enter Feeds Used" data-parsley-required="true"  data-parsley-type="digits" value="$feeds_used"/>
EOS;
        ?>
		
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>MedVac DR Number/s</label>
		</div>

		<div class="col l3 m8 s12  ">
			<div class="div-task-values red-text">
				<?php 
					$drs = [];
					foreach($doc_medvac_numbers as $val)
					{
						$path  = base_url().PORTAL_TRANSACTIONS.'/'.$val['controller'].'?t='.base64_url_encode($val['pria_task_id']);

						$drs[] = <<<EOS
							<a href="$path" target="_blank" class="underline">{$val['ref_num']}</a>
EOS;
					}		

					echo implode(',', $drs);
				?>
			</div>
		</div>
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label>DOC GR Number/s</label>
		</div>

		<div class="col l3 m8 s12  ">
            <div class="div-task-values red-text">
			<?php 
					$grs = [];
					foreach($doc_gr_numbers as $val)
					{
						$path  = base_url().PORTAL_TRANSACTIONS.'/'.$val['controller'].'?t='.base64_url_encode($val['pria_task_id']);

						$grs[] = <<<EOS
							<a href="$path" target="_blank" class="underline">{$val['ref_num']}</a>
EOS;
					}	

					echo implode(',', $grs);
			?>
			</div>
		</div>
	</div>
</div>