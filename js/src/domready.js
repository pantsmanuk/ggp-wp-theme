// Mootools Domready event, triggers when DOM is accessible, before full page load
window.addEvent('domready', function() {
// Navigation for the carousel
	var carcookie;
	carouselFx = new Fx.Tween($('c_container'), {duration: '1000', link: 'cancel'});
	carouselEls = $('c_container').getChildren('li');
	var c_nav = $('c_nav').getChildren('img');
	c_nav.each(function(index) {
		index.addEvents({
		    'click': function(){
		    	status = 0;
		    	var navid = $$('#c_nav img').indexOf(index);
		        carouselFx.start('left', -carouselEls[navid].offsetLeft).chain(function() {
			        swap_info('show', navid);
					swapStatus.delay(3000);
			    });
		        $$('#c_nav img').removeClass('sel');
		        index.addClass('sel');
		        swap_info('hide', navid);
		    }
	    });
	});
	
// Next and previous navigation for the carousel
	$('prev').addEvents({
	    'click': function(){
	    	status = 0;
	    	c_goto('prev', carouselEls, carouselFx);
	   	}
	});
	$('next').addEvents({
	    'click': function(){
	    	status = 0;
	    	c_goto('next', carouselEls, carouselFx);
	   	}
	});
	
// Duplicate the last carousel element and inject before the first and vice versa
	var firstclone = carouselEls[carouselEls.length-1].clone().inject(carouselEls[0], 'before');
	$('c_container').setStyle('left', '-950px');
	var secondclone = carouselEls[0].clone().inject(carouselEls[carouselEls.length-1], 'after');
	
// Subnav animations
	var snav_current = -1;
	if ($$('ul.expander .active').length > 0) {
		snav_current = $$('ul.subnav ul.expander').indexOf($$('ul.expander .active')[0].getParent());
	}
	var myAccordion = new Fx.Accordion('a.trigger', 'ul.expander', {
		fixedHeight: false,
		display: snav_current,
		alwaysHide: true,
		onActive: function(toggler, element){
			toggler.getParent().addClass('active');
		},
		onComplete: function(toggler, element){
			if (this.options.returnHeightToAuto) {
				$$('li.active ul.expander').setStyle('height', 'auto');
			}
		},
		onBackground: function(toggler, element){
			toggler.getParent().removeClass('active');
		}
	});

// Search show and hide
	$('search').addEvents({
	    'click': function(){
	    	$('searchfield').toggle();
	   	}
	});

// Get twitter feed - get_tweets.php doesn't work any more!!!
//	var twitters = new Request({
//		method: 'get', 
//		url: '/wp-content/themes/ggp/get_tweets.php', 
//		evalScripts: true,
//		onRequest: function() {
//			$('feed_1').setStyle('background', 'transparent url(/wp-content/themes/ggp/imgs/loading.gif) no-repeat 0px 45px');
//		}
//	});
//	twitters.send();
	
// Get downloads section folder tree and inject
	if ($('downloads-foldertree')) {
		$$('#downloads-foldertree li a').addEvents({
		    'click': function(){
				getDownloads(this.getProperty('rel'));
				$$('#downloads-foldertree li').removeClass('active');
				this.getParent().addClass('active');
				this.addClass('nochild');
		    }
	    });
	}
	
// Initiate Shadowbox and slideshow
	Shadowbox.init();
	slideShow();
	//
	if (!Shadowbox.flash.hasFlashPlayerVersion("9.0.0")) {
		$$('a[rel$="player=swf;"]').removeProperty('rel');
		$$('a[rel$="player=flv;"]').removeProperty('rel');
	}
});
