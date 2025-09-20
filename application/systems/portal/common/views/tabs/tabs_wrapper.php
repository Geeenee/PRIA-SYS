
 <div class="tabs-wrapper <?php echo $class ?>">
	<div>
		
		<ul class="tabs">
			<?php foreach($tabs as $details): ?>
				<li class="tab"><a href="#tab_<?php echo $details['tab'] ?>" onclick="load_index_post('tab_<?php echo $details['tab'] ?>', '<?php echo $details['controller'] ?>', '<?php echo $details['module'] ?>', null, 'Filter.filter_tab_reset();')" data-post='' data-form='<?php echo $details['post_form']; ?>'><?php echo $details['title'] ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php foreach($tabs as $details): ?>
  <div id="tab_<?php echo $details['tab'] ?>" class="tab-content col s12"></div>
<?php endforeach; ?> 


<script>
	$(function(){
		var hash = window.location.hash;
	
		$('.tabs').find('li a[href="'+hash+'"]').addClass('active');
	});
</script>

