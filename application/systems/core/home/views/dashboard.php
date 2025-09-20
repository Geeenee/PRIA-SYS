<div class="p-md">
	<?php
		$previous = '';
		$previous_col = 0;
		
		foreach($area_layout as $row):
			$start 			= $row['area_code'];
			$start_col		= $row['area_column_id'];
			$col_size 		= $row['size'];
			$col_desc 		= $row['column_description'];
			$widget_id		= $row['widget_id'];
			$widget_name	= $row['widget_name'];
		
			if(!EMPTY($previous) && $previous !== $start)
				echo '</div>';
				
			if($previous !== $start)
				echo '<div class="row region-row wireframe">';
			
				if(!EMPTY($previous_col) && $previous_col !== $start_col)
				{
					if(!EMPTY($widget_id))
						echo '</ul>';
					
					echo '</div>';
				}
				
				if($previous_col !== $start_col)
				{
					echo '<div class="col l'.$col_size.' m12 s12 center-align region-col" data-title="'.$col_desc.'">';
					
					if(!EMPTY($widget_id))
						echo '<ul class="region-col-widgets">';
				}
				
				if(!EMPTY($widget_id))
					echo '<li>'.$widget_name.'</li>';
			
				$previous_col 	= $start_col;
			
			$previous = $start;
		endforeach;
	?>
</div>