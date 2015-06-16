jQuery(document).ready(function($) {
	function setCookie(cname, cvalue, exdays, path, domain) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString() +
	    ((path == null) ? "; path=/" : "; path=" + path);
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	}

	function bringTheBar(text,prev,next) {
		text = typeof text !== 'undefined' ? text : 'Swipe to navigate!';
		prev = prev ? '<span style="display:block;position:fixed;left:2%;bottom:7px;font-size:22px;font-weight:bold;">&lt;</span>' : '';
		next = next ? '<span style="display:block;position:fixed;right:2%;bottom:7px;font-size:22px;font-weight:bold;">&gt;</span>' : '';
		var fixedDiv = document.createElement('div');
		fixedDiv.id = 'messageBar';
		fixedDiv.style.position = 'fixed';
		fixedDiv.style.bottom = '0';
		fixedDiv.style.left = '0';
		fixedDiv.style.height = '36px;';
		fixedDiv.style.width = '100%';
		fixedDiv.style.background = 'linear-gradient(to bottom, rgba(17,17,17,0) 0%,rgba(17,17,17,0.45) 25%,rgba(17,17,17,0.9) 100%)';
		fixedDiv.style.fontWeight = 'bold';
		fixedDiv.style.color = '#FFFFFF';
		fixedDiv.style.fontSize = '18px';
		fixedDiv.style.fontFamily = '\'Open Sans\',\'PT Sans\',Arial,Helvetica,sans-serif';
		fixedDiv.style.paddingBottom = '3px';
		fixedDiv.style.display = 'none';
		fixedDiv.style.textAlign = 'center';
		fixedDiv.innerHTML = '<span style="display:block;position:fixed;left:2%;bottom:0;font-size:22px;font-weight:bold;">&lt;</span>' + text + '<span style="display:block;position:fixed;right:2%;bottom:0;font-size:22px;font-weight:bold;">&gt;</span>';
		document.body.appendChild(fixedDiv);
		$('#messageBar').fadeIn('fast');
		window.setTimeout(function(){
			$('#messageBar').fadeOut('slow',function(){
				$(this).remove();
			});
		},1500);
	}
	if ( $('.gesture').hasClass( 'iphone' ) ) {
		
		var next = $('.nav-next a').attr('href');
	    var prev = $('.nav-previous a').attr('href');
	    nextYes = typeof next !== 'undefined' && next.indexOf('reverb') ? true : false;
	    prevYes = typeof prev !== 'undefined' && prev.indexOf('reverb') ? true : false;

		if (!getCookie('seenTheBar')) {
			bringTheBar('Swipe to navigate!',prevYes,nextYes);
			setCookie('seenTheBar',true,60);
		}

		$(document).on('swipeleft swiperight', function(event) {
			if ( event.type == 'swipeleft' ) {
				if ( next.indexOf('reverb') ) {
			        window.location = next;
			    } else {
			    	bringTheBar('Nothing newer posted.',true,false);
			    }
			} 
			if ( event.type == 'swiperight' ) {
		        if ( prev.indexOf('reverb') ) {
		        	window.location = prev;
		        } else {
		        	bringTheBar('Nothing older posted.',false,true);
		        }
			}
		});
	}
});