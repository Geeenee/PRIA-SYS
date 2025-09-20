<?php
	$actual_clean_up_date	= ( ISSET($clean_up_details['actual_clean_up_date']) ) ? std_datepicker_format($clean_up_details['actual_clean_up_date']) : '';
	$harvested_head         = ( ISSET($clean_up_details['harvested_heads_num']) ) ? $clean_up_details['harvested_heads_num'] : '';
	$feeds_delivered 		= ( ISSET($clean_up_details['delivered_feeds_num']) ) ? $clean_up_details['delivered_feeds_num'] : '';
	$feeds_used     		= ( ISSET($clean_up_details['feeds_used_num']) ) ? $clean_up_details['feeds_used_num'] : '';
	$harvested_kilos        = ( ISSET($clean_up_details['harvested_kilos_num']) ) ? $clean_up_details['harvested_kilos_num'] : '';
?>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label >Clean-Up Date</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $actual_clean_up_date ?></div>
		</div>
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label >Harvested Heads</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $harvested_head ?></div>
		</div>
		
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label >Harvested Kilos</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $harvested_kilos ?></div>
		</div>
		
	</div>
</div>
<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">
			<label >Feeds Delivered (in bags)</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $feeds_delivered ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col  p-r-md">			
			<label >Feeds Used (in bags)</label>
		</div>

		<div class="col l3 m8 s12 ">
            <div class="div-task-values"><?php echo $feeds_used ?></div>
		</div>
		
	</div>
</div>

<div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>MedVac DR Number/s</label>
		</div>

		<div class="col l3 m8 s12 ">
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
		<div class="col l3 m4 s12  label-col p-r-md">			
			<label>DOC GR Number/s</label>
		</div>

		<div class="col l3 m8 s12 ">
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