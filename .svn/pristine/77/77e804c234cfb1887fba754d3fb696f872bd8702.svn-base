jQuery(document).ready(function(){
	//cache DOM elements
	var accountInfo = $('.account');
		notification = $('.notif');
		apps = $('.apps');
		quickadd = $('.quick-add');

	//click on item and show submenu
	$('.cd-top-nav .has-children > a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this),
			selectedItemNav = $(this).closest('nav');
		
		if( mq == 'mobile' || mq == 'tablet' || selectedItemNav.hasClass('cd-nav-compact') ) {
			event.preventDefault();
			if( selectedItem.parent('li').hasClass('selected')) {
				selectedItem.parent('li').removeClass('selected');
			} else {
				accountInfo.removeClass('selected');
				notification.removeClass('selected');
				apps.removeClass('selected');
				quickadd.removeClass('selected');
				selectedItem.parent('li').addClass('selected');
			}
		}
	});

	//click on item and show submenu
	$('.cd-top-mod-nav .has-children > a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this),
			id = selectedItem.parent('li').attr("id");

		$('.cd-top-mod-nav .has-children').not("#"+id).removeClass('active');
		$('.cd-top-mod-nav > .has-children').not("#"+id).children('ul').removeClass('block');

		$('.cd-top-mod-nav > .has-children').not("#"+id).children('ul').find('.normal-sub-child').removeClass('block');
		selectedItem.parent('li').toggleClass('active');


		if(!$("#"+id).hasClass('active'))
		{
			$("#"+id + " > ul > li > ul.normal-sub-child ").removeClass('block');
			$("#"+id + " > ul > li ").removeClass('activeSub');
		}

	});

	//click on account and show submenu - desktop version only
	accountInfo.children('a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this);
		if( mq == 'desktop') {
			event.preventDefault();
			accountInfo.toggleClass('selected');
			notification.removeClass('selected');
			quickadd.removeClass('selected');
			apps.removeClass('selected');
			accountInfo.find('.tooltipped').tooltip('remove');
			accountInfo.find('.tooltipped').tooltip({delay: 50});
		}
	});
	
	notification.children('a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this);
		if( mq == 'desktop') {
			event.preventDefault();
			notification.toggleClass('selected');
			accountInfo.removeClass('selected');
			quickadd.removeClass('selected');
			apps.removeClass('selected');
			notification.find('.tooltipped').tooltip('remove');
			notification.find('.tooltipped').tooltip({delay: 50});
		}
	});
	
	apps.children('a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this);
		if( mq == 'desktop') {
			event.preventDefault();
			apps.toggleClass('selected');
			notification.removeClass('selected');
			accountInfo.removeClass('selected');
			quickadd.removeClass('selected');
			apps.find('.tooltipped').tooltip('remove');
			apps.find('.tooltipped').tooltip({delay: 50});
		}
	});
	
	quickadd.children('a').on('click', function(event){
		var mq = checkMQ(),
			selectedItem = $(this);
		if( mq == 'desktop') {
			event.preventDefault();
			quickadd.toggleClass('selected');
			notification.removeClass('selected');
			accountInfo.removeClass('selected');
			apps.removeClass('selected');
			quickadd.find('.tooltipped').tooltip('remove');
			quickadd.find('.tooltipped').tooltip({delay: 50});
		}
	});

	function checkMQ() {
		//check if mobile or desktop device
		return window.getComputedStyle(document.querySelector('.cd-main-content'), '::before').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
	}

	$(document).on('click', function(event){
		if( !$(event.target).is('.has-children a') ) {
			accountInfo.removeClass('selected');
			notification.removeClass('selected');
			apps.removeClass('selected');
			quickadd.removeClass('selected');
		}
	});

	$(".normal-sub > .normal-sub-has-children").click(function() {
        //no more overlapping menus
        //hides other children menus when a list item with children menus is clicked
        var thisMenu 	= $(this).children("ul");
        var id 			= $(this).attr("id");
        
        if ($(window).width() < 943) {
            thisMenu.toggleClass('block');
            thisMenu.parent().parent().toggleClass('block');
            $('.normal-sub-has-children').not("#"+id).removeClass('activeSub');
            $(this).toggleClass('activeSub');
            $('.normal-sub-has-children').not("#"+id).children('ul').removeClass('block');
        }
    });
});