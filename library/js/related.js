//Get canonical URL from page
var request_data = {
	permalink: outbrainurl,
	widgetId: 'JS_1'
};

var outbrain_callback = function(json) {

	var firstTime = ( $j('#related-ul').length ? false : true);
	var dataSuccess = false;

	var resultstring = ( firstTime ? '<ul id="related-ul" class="multi-column large-block-grid-3 medium-block-grid-2">' : '' );

	$j.each(json.doc,function(index,value) {
		var nofollow = ( !value.same_source ? ' rel="nofollow"' : '' );
		resultstring += '<li class="relatedli">';
			resultstring += '<article class="post type-post status-publish format-standard hentry related">';
        		resultstring += '<div class="entry-body">';
            		resultstring += '<header class="entry-header">';
            			if (value.thumbnail != null) {
	            			resultstring += '<div class="entry-thumbnail">';
								resultstring += '<div class="mainimgholder"></div>';
								resultstring += '<a href="' + value.url + '"' + nofollow + ' title="' + value.content + '">';
									resultstring += '<div class="mainimg" style="background-image:url(\'' + value.thumbnail.url + '\');"></div>';
								resultstring += '</a>';
	    					resultstring += '</div>';
	    				}
						resultstring += '<div class="entry-category">';
							resultstring += '<div class="entry-meta">';
								resultstring += '<a href="' + value.url +'" title="' + value.source_name + '"' + (!value.same_source ? ' class="sponsored"' : '') + '>' + value.source_name + '</a>';
								resultstring += '</div>';
							resultstring += '</div>';
							resultstring += '<h2 class="entry-title">';
								resultstring += '<a href="' + value.url + '" title="' + value.content + '"' + nofollow + '>' + value.content + '</a>';
							resultstring += '</h2>';
						resultstring += '</header>';
    				resultstring += '</div>';
    			resultstring += '<div class="clear"></div>';
			resultstring += '</article>';
		resultstring += '</li>';
		dataSuccess = ( value.content.length ? true : false );
	});
	
	resultstring += ( firstTime ? '</ul>' : '' );

	if (resultstring.length && dataSuccess) {
		if ( firstTime ) {
			$j('#relatedwrap').css('display', 'block');
			$j('#related-content').html(resultstring);
		} else {
			$j('#related-ul').append(resultstring);
		}
		$j('div.relatednext').bind('inview', function(event, visible) {
			if (visible) {
				OBR.extern.callRecs(request_data, outbrain_callback);
			}
		});
	}
}

OBR.extern.callRecs(request_data, outbrain_callback);