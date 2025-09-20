<?php
	//$display_filter = ($this->session->show_task_filter == 1) ? "" : $display;  

	$display_filter = $display;  
?>

<div id="<?php echo $id ?>" class="sub-navigation <?php echo $position ?> <?php echo $theme ?> <?php echo $display_filter ?>" style="width: <?php echo $width ?>px" data-width="<?php echo $width ?>">
	<?php 
		if($sidebar_toggle): 
			$arrow = ($position == SB_LEFT) ? "&llarr;" : "&rrarr;";
	?>
		<a href="javascript:;" class="nav-toggle collapse"><?php echo $arrow ?></a>
	<?php endif; ?>
	
	<?php if($sidebar_close): ?>
		<a href="javascript:;" class="nav-close">&#10006;</a>
	<?php endif; ?>
	<div class="nav-content"><?php echo $content ?></div>
</div>