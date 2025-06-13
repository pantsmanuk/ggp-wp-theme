var slideShowInterval;
var carouselFx;
var carouselEls;
var status = 1;
var swapStatus = function() {
	status = 1;
};

// Swap the link in the carousel on change
function swap_info(how, which) {
	$$('#c_links a').addClass('hidden');
	if (how == 'show') {
		$$('#c_links a')[which].removeClass('hidden');
	}
}

// Sends the carousel to a specific element. Takes an integer as value.
function c_goto(which) {
	status = 0;
	var current = $$('#c_nav img').indexOf($$('#c_nav img.sel')[0]);
	var previous = current-1;
	var next = current+1;
	if (which == 'prev') {
		if (previous >= 0) {
		    carouselFx.start('left', -carouselEls[previous].offsetLeft).chain(function() {
		        $$('#c_nav img').removeClass('sel');
		        $$('#c_nav img')[previous].addClass('sel');
		        swap_info('show', previous);
		        swapStatus.delay(3000);
		    });
		} else {
		    carouselFx.start('left', -carouselEls[carouselEls.length-1].offsetLeft).chain(function() {
		        $$('#c_nav img').removeClass('sel');
		        $$('#c_nav img')[$$('#c_nav img').length-1].addClass('sel');
		        swap_info('show', $$('#c_nav img').length-1);
		        swapStatus.delay(3000);
		    });
		}
	} else {
		if (next < carouselEls.length) {
		    carouselFx.start('left', -carouselEls[next].offsetLeft).chain(function() {
		        $$('#c_nav img').removeClass('sel');
		        $$('#c_nav img')[next].addClass('sel');
		        swap_info('show', next);
		        swapStatus.delay(3000);
		    });
		} else {
		    carouselFx.start('left', -carouselEls[0].offsetLeft).chain(function() {
		        $$('#c_nav img').removeClass('sel');
		        $$('#c_nav img')[0].addClass('sel');
		        swap_info('show', 0);
		        swapStatus.delay(3000);
		    });
		}
	}
	swap_info('hide', current);
}

// Initiate automated carousel change every 7000 ms
function slideShow() {
	var periodicalSwap = function() {
		if (status == 1) {
			c_goto('next');
		}
	};
	slideShowInterval = periodicalSwap.periodical(7000);
}

// Retrieve downloads folder tree by ajax
function getDownloads(folder) {
	if ($('downloads-foldertree')) {
		var getFolders = new Request({
			method: 'get', 
			url: 'wp-content/themes/ggp/get_folders.php?folder='+folder, 
			evalScripts: true
		});
		getFolders.send();
	}
}

// Share to facebook button
function fbs_click() {
	var u=location.href;
	var t=document.title;
	window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;
}