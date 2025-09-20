<?php if($display_scroll) { ?>
	<div class="row">
		<div class="col s4 offset-s4 center">
			<div class="page-load-status" style="display:none;">		
				<div class="progress infinite-scroll-request">
					<div class="indeterminate"></div>
				</div>   

				<p class="infinite-scroll-last">
					<?php echo ( ISSET($scroll['last']) ) ? $scroll['last'] : ''; ?>
				</p>

				<p class="infinite-scroll-error">
					<?php echo ( ISSET($scroll['error']) ) ? $scroll['error'] : 'An error has occured. Please reload the page.'; ?>
				</p>
			</div>	
		</div>
	</div>

	<div class="row">
		<div class="col s12 center">
			<button type="button" class="btn" id="view-more">
				<i class="material-icons">search</i>View More
			</button>
		</div>
	</div>
<?php } ?>


<div class="fixed-action-btn">
	<a class="btn-floating hide" id="btn-scroll-up">
		<i class=" material-icons">keyboard_arrow_up</i>
	</a>  
</div>


<?php if($display_scroll) { ?>
	<script>
		var infScroll = new InfiniteScroll('<?php echo $container ?>', {
			path           : '<?php echo $path ?>{{#}}/?<?php echo ISSET($get_params) ? $get_params : ''; ?>',
			append         : '<?php echo $append ?>',
			scrollThreshold: false,
			button         : '#view-more',
			status         : '.page-load-status',
			history		   : false,
			checkLastPage  : '<?php echo $last_page ?>'
		});
		
		infScroll.on( 'append', function( response, path, items ) {
			$('.tooltipped').tooltip({delay: 50});

			create_avatar($('.letter-avatar'), {width:50,height:50,fontSize:30});
		});
	</script>
<?php } ?>

	<script>
		$(function(){	
			$(window).scroll(function(){
				if($(window).scrollTop() > 200)
					$('#btn-scroll-up').removeClass('hide');
				else
					$('#btn-scroll-up').addClass('hide');
			});

			$('#btn-scroll-up').on('click', function(e){
				e.preventDefault();

				$('html, body').animate({scrollTop: 0}, '300');
			});
		});
	</script>

