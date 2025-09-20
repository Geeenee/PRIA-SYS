<div class="sub-tab-wrapper">
	<ul class="sub-tabs <?php echo $class ?>">
		<?php 
			foreach($tabs as $tab => $row):
				$content = ($class == 'icon') ? '<i class="material-icons">'.$row['icon'].'</i>': $row['title']; 
		?><!-- 
		--><li><a class="links tooltipped" data-tooltip="<?php echo $row['tooltip'] ?>" id="<?php echo $tab ?>" href="javascript:;" onclick="load_content('sub-content', '<?php echo $module . '/'. $tab ?>', '<?php echo $tab ?>', '<?php echo $tab ?>')"><?php echo $content ?></a></li><!--
		--><?php endforeach; ?>
	</ul>

	<div class="sub-content"></div>
</div>
