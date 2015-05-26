/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

var mapRange = function(from, to, s) {
  return to[0] + (s - from[0]) * (to[1] - to[0]) / (from[1] - from[0]);
};


function createTimeline() {


	var navbarContainer = d3.select( $(".banner")[0]);

	var makeTimeline = navbarContainer.append("svg")
																	.attr("width", "100%")
																	.attr("height","15px")
																	.attr("display", "block")
																	.attr("class", "timelineContainer")
																	;

																	adjustMainMargintop();


	// récupérer le temps actuel
	var timeinMSNow = new Date().getTime();
	var maxtimeinMS;

	$(".projetContainer").find(".postContainer").last().each(function() {

		$this = $(this);

		timeinISO = new Date( $this.find("time").attr("datetime") );
		maxtimeinMS = timeinISO.getTime();

	});

	// pour chaque post de la page
	$(".projetContainer").find(".postContainer").each(function(index) {

		$this = $(this);

		timeinISO = new Date( $this.find("time").attr("datetime") );
		timeinMS = timeinISO.getTime();
		//console.log(" TIME : " + timeinMS);

		// faire un pourcentage un placement sur X
		var timeRangeFrom0to100 = mapRange([timeinMSNow, maxtimeinMS], [0, 100], timeinMS);

		var dataStatus = $this.find(".publish-private-post").attr("data-status");

		fillColor = dataStatus === "publish" ? "#45C1B4" : "#F2682C";

		makeTimeline.append("circle")
								.attr("data-status", dataStatus)
								.attr("r", 0)
								.attr("fill", fillColor)
								.attr("stroke", "transparent")
								.attr("cx", timeRangeFrom0to100 + "%")
								.attr("cy", "6.5")
								.transition()
								.delay(function() {
								    return index * 110; // Or whatever you want the delay to be.
								})
								.duration(300)
								.ease("in-out")
								.attr("r", 3.5)

								;


	});

	ppostVisible = -1;
	$(window).on('scroll', function () {

		postVisible = whichPostIndexIsVisible(window.pageYOffset);
		if( postVisible !== ppostVisible ) {

			$('.postContainer').eq(	ppostVisible).removeClass("is-active");
			makeTimeline.selectAll("circle")
									.filter(function (d, i) { return i === ppostVisible;})
									.transition()
									.duration(300)
									.attr("r", 3.5);

			$('.postContainer').eq(	postVisible).addClass("is-active");
			makeTimeline.selectAll("circle")
									.filter(function (d, i) { return i === postVisible;})
									.transition()
									.duration(300)
									.attr("r", 6.5);

			ppostVisible = postVisible;
		}

	});


}


function whichPostIndexIsVisible(modwscrollTop) {
	var dist =0;
	var pDist=10000000000;
	var articleActif;
	//optimisation : stocker le numéro d'article plutôt que l'article : http://jsperf.com/jquery-each-this-vs-eq-index
	var numArticleActif;

	var $articles = $('.postContainer');
	$articles.each( function(index){
		dist = Math.abs(modwscrollTop - this.offsetTop);
		if(dist<pDist) {
			pDist = dist;
			numArticleActif = index;
		}
		dist = Math.abs(modwscrollTop - (this.offsetTop + this.offsetHeight));
		if(dist<pDist) {
			pDist = dist;
			numArticleActif = index;
		}
	});
	//articleActif = $articles.eq(numArticleActif);
	return numArticleActif;
}


function replacePostWithIframe( $thisPost, pageLink ) {

	$thisPost.addClass("is-edited");
	$thisPost.find(".entry-header, .entry-content").remove();

	console.log("pageLink = " + pageLink);

	$thisPost.find(".entry-title-and-content").empty().append('<iframe class="edit-frame" src="' + pageLink + '#edit=true" style="border:0px;width:100%;height:100%;"></iframe>');

	/*********************** ajouter un bouton "Save" a cote de .button-right .edit-post ***********************/
	// trigger un click sur "Mettre a jour" avant tout
	var $save_button = $thisPost.find(".save-modifications");

	$save_button.removeClass("is-disabled");

	$save_button.on( "click", function() {

		sendActionToAnalytics("Fin édition d'un post");

		var $thisPost = $(this).parents(".post");
		$thisPost.find(".edit-frame").contents().find(".fee-save").click();

	});

			/*
	url = pageLink;
	width = $thisPost.width();
	height = 400;

  var leftPosition, topPosition;
  //Allow for borders.
  leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
  //Allow for title and status bars.
  topPosition = (window.screen.height / 2) - ((height / 2) + 50);
  //Open the window.
  window.open(url, "Window2", "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
*/



}

function replaceiFrameWithPost( $thisPost, pageLink ) {

	$thisPost.find(".save-modifications").addClass("is-disabled");

	$.get( pageLink, function( data ) {
		$thisPost.removeClass("is-edited");
		$data = $(data);

		var $thisContent = $data.find('.post .entry-title-and-content').html();

		console.log( "$thisContent");
		console.log(  $thisContent );

		$thisPost.find(".entry-title-and-content").fadeOut(400).empty().append($thisContent).fadeIn(400);

		console.log( "found " + $thisPost.find(".entry-content:not(.is-sketch):contains(void setup)").length + " canvas");
		$thisPost.find(".entry-content:not(.is-sketch):contains(void setup)").each( function() {

			$this = $(this);
			$this.addClass("is-sketch");

			// récupérer le sketch, le transformer en canvas
			textToCanvas( $this );


		});

	});

}

function textToCanvas( $this ) {

	$thisPost = $this.parents(".post");
	thisPostID = $thisPost.attr("data-id");

	// poulet basquaise aux pâtes
	sketch = $this.text();

	var mapObj = {
	   "«":"\"",
	   "»":"\"",
	};
	var re = new RegExp(Object.keys(mapObj).join("|"),"gi");
	sketch = sketch.replace(re, function(matched){
		console.log("matched : "  + mapObj[matched]);
	  return mapObj[matched];
	});

	sketch = sketch
		.replace("void setup() {", "void setup() { noLoop();")
		.replace("void setup(){", "void setup(){ noLoop();")
		.replace("void setup () {", "void setup(){ noLoop();")
		.replace("void setup (){", "void setup(){ noLoop();");

	// supprimer le println
	var reg = /(println(.*?);)/gi;
	sketch = sketch.replace(reg,"");

	//.replace(/<br>/g, '').replace(/<p>/g, '').replace(/<\/p>/g, '')

	// console.log( sketch );

	//sketch = "void setup() { size(200, 200); } void draw() { background(155); }";

	var processingScript = $("<script type='application/processing'>" + sketch + "</script>");
	var processingCanvas = $("<canvas id=" + thisPostID + "></canvas>");

	$this.wrapInner("<pre class='thisCode brush:pde; gutter: false; '></pre>");

	$thisCode = $this.find(".thisCode");

	// Code affiché
	$thisCode.html( $thisCode.text().replace("<","&lt;").replace(">","&gt;").replace(/«/g, "\"").replace(/»/g, "\"") );

	SyntaxHighlighter.all();

/*
<script src="sh/shCore.js"></script>
<link rel=stylesheet href="sh/shCore.css">
<script src="sh/shBrushProcessing.js"></script>
<link rel=stylesheet href="sh/shProcessing2Theme.css">
<link rel=stylesheet href="sh/customStyleTricodeurInitiation.css">
<script>SyntaxHighlighter.all();</script>
*/

	$thisCode.before( processingScript );
	$thisCode.before( processingCanvas );

  var thisScript = processingScript[0];
  var thisCanvas = processingCanvas[0];
  var canvas;
  if (thisScript.type === "application/processing") {
    console.log("Trying to P5");
		try {
    	new Processing(thisCanvas, thisScript.text);
		}
		catch (e) {
			console.log("GODDAMMIT");
			$(thisCanvas).before("<div clas='errorMessage sketchNotValid'><small>Le sketch n'a pas pu être chargé car le script contient des erreurs. Corrigez-le en cliquant sur le crayon.</small></div>");
			$(thisCanvas).remove();
		}
    console.log("Pfewww passed");
  }

	//Processing.getInstanceById(canvas).noLoop();
	$this.find("canvas#" + thisPostID).hover(function() {
		var thisID = this.id;
		console.log(thisID);
		Processing.getInstanceById(thisID).loop();
	}, function() {
		var thisID = this.id;
		Processing.getInstanceById(thisID).noLoop();
	});
}

function animateLogo() {

	$(".navbar-brand svg").find("rect,circle,polyline,line,path").velocity({
			scale: 0
		}, {
    	duration: 0,
		});

	$(".navbar-brand svg").velocity({ opacity: 1} );
	$(".navbar-brand svg").find("rect,circle,polyline,line,path").each(function(i) {
		$(this).delay(i*20).velocity({
			scale: 1
		}, {
    	duration: 1200,
    	easing: "spring"
		});
	});

}

function adjustMainMargintop() {
	$("body:not(.iframe) .main").css("margin-top", $(".navbar-default").height() );

}

function makeLinksBlank() {

	$(".entry-content a").each( function() {
		var attr = $(this).attr('target');
		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof attr !== typeof undefined && attr !== false) {
		} else {
			$(this).attr("target","_blank");
		}
	});
}

function urlParam(name, url) {
    if (!url) {
    	url = window.location.href;
    }
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) {
        return undefined;
    }
    return results[1] || undefined;
}

function newPost() {

	console.log("NEW POST");

	$("body").addClass("is-overlaid");

	// de fee-adminbar.js
	wp.ajax.post( 'fee_new', {
		post_type: 'post',
		nonce: fee.nonce
	} ).done( function( url ) {

		var iframeAvecLien = '<iframe class="edit-frame" src="' + url + '?fee=visible&type=newpost" style="border:0px;width:100%;height:100%;"></iframe>';
		fillPopOver( iframeAvecLien, $(".button.add-post"), 900, 600);
		$(".popover").addClass("is-loading");

		// lui attribuer le bon projet
		var thisActionUrl = window.location.href;
		var thisID = urlParam('p', url);
		console.log( "thisID : " + thisID);

		var projName = $(".taxProj").data("term");




	  var data = {
        'action': 'add_taxonomy_to_post',
				'post_id': thisID,
				'term': projet
    };
    $.post(ajaxurl, data, function(response) {
      //alert('Server response from the AJAX URL ' + response);
		  var databis = {
		      'action': 'change_post_visibility',
					'post_id': thisID,
					'post_status': 'private'
		  };
		  console.log("new call thisID " + thisID + " databis : " + databis);
		  $.post(ajaxurl, databis, function(response) {
		    console.log('Server response from the AJAX URL ' + response);
				$(".popover").removeClass("is-loading");
		  });
    });





	});
}


function newProject(projName) {

	$("body").addClass("is-overlaid");

	console.log( "newProject : " + projName);

	// de fee-adminbar.js
	wp.ajax.post( 'fee_new', {
		post_type: 'post',
		nonce: fee.nonce
	} ).done( function( url ) {

		var iframeAvecLien = '<iframe class="edit-frame" src="' + url + '?fee=visible&type=newproject" style="border:0px;width:100%;height:100%;"></iframe>';
		fillPopOver( iframeAvecLien, $("#nouveauProjet"), 900, 600);
		$(".popover").addClass("is-loading");

		// lui attribuer le bon projet
		var thisActionUrl = window.location.href;
		var thisID = urlParam('p', url);
		console.log( "thisID : " + thisID);

    var data = {
        'action': 'add_taxonomy_to_post',
				'post_id': thisID,
				'term': projName
    };
    $.post(ajaxurl, data, function(response) {
//        alert('Server response from the AJAX URL ' + response);
			$(".popover").removeClass("is-loading");
    });


	});

}


function loginField() {

	$("body").addClass("is-overlaid");

	var loginWindow = $(".login").clone(true);

	fillPopOver( loginWindow, $(".button.login-field"), 300, 380 );

}

function fillPopOver( content, thisbutton, finalWidth, finalHeight ) {
	var $popover = $(".popover .popoverContainer");
	$popover.html(content);

	$popover.append('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div>');

	$popover.addClass("is-visible");
	$popover.find(".login").css("visibility", "visible");

	var button = thisbutton;
	var maxQuickWidth = 900;

	var topSelected = button.offset().top - $(window).scrollTop(),
	leftSelected = button.offset().left,
	widthSelected = button.width(),
	heightSelected = button.height(),
	windowWidth = $(window).width(),
	windowHeight = $(window).height(),
	finalLeft = (windowWidth - finalWidth)/2,
	finalTop = (windowHeight - finalHeight)/2,
	quickViewWidth = ( windowWidth * 0.8 < maxQuickWidth ) ? windowWidth * 0.8 : maxQuickWidth ,
	quickViewLeft = (windowWidth - quickViewWidth)/2;

	$('.popover').css({
	    "top": topSelected,
	    "left": leftSelected,
	    "width": widthSelected,
	    "height": heightSelected
	}).velocity({
		//animate the quick view: animate its width and center it in the viewport
		//during this animation, only the slider button is visible
	    'top': finalTop+ 'px',
	    'left': finalLeft+'px',
	    'width': finalWidth+'px',
	    'height': finalHeight+'px'
	}, 1000, [ 400, 0 ], function(){
		//animate the quick view: animate its width to the final value
/*
		$('.popover').addClass('animate-width').velocity({
			'left': quickViewLeft+'px',
	    	'width': quickViewWidth+'px',
		}, 300, 'ease' ,function(){
			//show quick view content
//					$('.cd-quick-view').addClass('add-content');
		});
*/
	}).addClass('is-visible');

	$("body").on('click', function(event){
		if( $(event.target).is('.close-panel') || $(event.target).is('body.is-overlaid')) {

			closePopover();
		}
	});
	$(document).keyup(function(event){
  	if(event.which === '27'){
			closePopover();
		}
	});
}

function closePopover() {
	$("body").removeClass("is-overlaid");
	$(".popover").removeClass("is-visible").empty();

}

fixedNav = {

	init: function() {



	},

	update: function() {





	}
};


postViewRoutine = {
	init: function() {

		// fonctions propres à chaque post (a appliquer si infinite scroll), à ne pas appliquer si iframe

		makeLinksBlank();

		if( !$("body").hasClass("iframe") ) {

			// transformer en gallerie d'image
			$("img").parents("a:not(.thumbnail)").each(function() {
				var $that = $(this);
				var $imgSrc = $that.attr("href");

				if( $imgSrc.match(/\.(jpg|png|gif|jpeg)/i) ) {
					$that.wrap("<div class='singleThumbnail gallery'><figure></figure></div>");
					var $figure = $that.closest("figure");

					$that.on("click", function(e) {

					sendActionToAnalytics("Ouverture d'une image en grand");

						if( !!$figure.attr( "data-fullimagesize") ) {
							console.log("has data attr");
						} else {
							console.log("no data attr");

							$that.addClass("is-loading");

							e.preventDefault();

	    				var image = new Image();
							image.src = $imgSrc;
							image.onload = function() {
								$that.removeClass("is-loading");
								console.log("images Loaded : image.naturalWidth = " + image.naturalWidth );
								$figure.attr( "data-fullimagesize", image.naturalWidth + "x" + image.naturalHeight );
								$that.trigger("click");
							};

							return false;

						}

					});
				}

			});

			// lancer lphoto swipe pour la gallerie d'image
			initPhotoSwipeFromDOMForGalleries('.entry-content .gallery');


			$(".entry-title-and-content").each(function() {
				if( $(this).find(".entry-title").text() === "Brouillon auto" && $(this).find(".entry-content").text().trim() === "" ) {
					$(this).parents(".postContainer").remove();
				}
			});

			$(".publish-private-post").click(function(e) {

				// récupérer l'id du post
				$this = $(this);
				$thisPost = $this.siblings(".post");
				thisID = $thisPost.attr("data-id");
				thisActionUrl = window.location.href;
				currentStatus = $thisPost.attr("data-status");

				newStatus = "";

				if( currentStatus === "publish" ) {
					newStatus = "private";
					sendActionToAnalytics("Passer un post en privé");
				} else {
					newStatus = "publish";
					sendActionToAnalytics("Passer un post en en public");
				}

				//backup
		/*
				  $('<form action="comments.php" method="POST">' +
				    '<input type="hidden" name="aid" value="' + imgnum + '">' +
				    '</form>'
		*/

		/*
				thisForm = $('<form id="update_post_visibility_bis" name="update_post_visibility" method="post" action="' + thisActionUrl + '">' +
		        '<input value="' + newStatus + '" name="visibility" />' +
		        '<input name="post_id" value="' + thisID + '" />' +
		        '<input type="hidden" name="action" value="update_post_visibility" />' +
		    '</form>');

		    console.log("thisForm : ");
		    console.log( thisForm );

				thisForm
					.submit(function() {
					});
		*/

				console.log("Submitted ajax post request");
				console.log("TO : " + thisActionUrl);
				console.log("post_id : " + thisID);
				console.log("action : " + "update_post_visibility");
				console.log("visibility : " + newStatus);


				$thisPost.parents(".postContainer").addClass("is-loading");

		    var data = {
		        'action': 'change_post_visibility',
						'post_id': thisID,
						'post_status': newStatus
		    };
		    $.post(ajaxurl, data, function(response) {
					var str = JSON.parse(response);

					$thisPost.parents(".postContainer").removeClass("is-loading");
					$thisPost.attr("data-status", str);
					$thisPost.siblings(".publish-private-post").attr("data-status", str);
					console.log( "response : " + str);
		    });


				e.preventDefault();

			});

			///////////////////////////////////////////////// click sur éditer /////////////////////////////////////

			$(".post .button-right .edit-post").click( function(e) {


				e.preventDefault();

				// ouvrir dans un nouvel onglet

				console.log("edit-post click");

				var $thisPost = $(this).parents(".post");
				var pageLink = $thisPost.attr("data-singleurl");

				if( $thisPost.hasClass("is-edited") ) {
					// si déja édité, alors revenir au mode normal en replacant le contenu updaté dans la page
					sendActionToAnalytics("Fin d'édition d'un post");
					replaceiFrameWithPost( $thisPost, pageLink );
				} else {

					sendActionToAnalytics("Édition d'un post");
					replacePostWithIframe( $thisPost, pageLink );

				}

				return false;

			});

			///////////////////////////////////////////////// click sur supprimer /////////////////////////////////////

			$(".post .button-right .remove-post").click( function(e) {

				sendActionToAnalytics("Suppression d'un post");

				e.preventDefault();

				var $thisPost = $(this).parents(".post");
				thisID = $thisPost.attr("data-id");

				thisActionUrl = window.location.href;

				console.log("is-removing");
				console.log("thisID " + thisID );

				$thisPost.addClass("is-removing");

		    var data = {
		        'action': 'remove_post',
						'post_id': thisID
		    };
		    $.post(ajaxurl, data, function(response) {
						console.log("SuccessAjaxPost!");
						console.log("Removed post!");
						console.log(response);

						$thisPost.parents(".postContainer").slideUp("normal", function() { $(this).remove(); } );

		    });

				return false;

			});


			console.log( '$(".post .entry-content:contains(void setup())").length = ' + $(".post .entry-content:contains(void setup())").length );

			// si y a besoin de pjs
			if( $(".post .entry-content:contains(void setup)").length > 0 ) {

			  var scriptSrc = '//cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.8/processing.min.js';

				var script = document.createElement('script');
				script.src = scriptSrc;

				script.onload = function() {

					$(".post .entry-content:not(.is-sketch):contains(void setup)").each( function(i) {

						$this = $(this);
						$this.addClass("is-sketch");

						console.log("textToCanvas pour le " + i);

						// récupérer le sketch, le transformer en canvas
						textToCanvas($this);

					});

				};

				var head = document.getElementsByTagName('head')[0];
				head.appendChild(script);


				// append SyntaxHighlighter


			}

		} // fin 'si pas iframe'

	// fin postViewRoutine.init();
	},

};


var initPhotoSwipeFromDOMForGalleries = function(gallerySelector) {

    console.log("INIT photoswipe forgalleries");

    // parse slide data (url, title, size ...) from DOM elements
    // (children of gallerySelector)
    var parseThumbnailElements = function(el) {
        var thumbElements = el.childNodes,
            numNodes = thumbElements.length,
            items = [],
            figureEl,
            linkEl,
            size,
            item;

		    console.log("numNodes : " + numNodes);


        for(var i = 0; i < numNodes; i++) {

            figureEl = thumbElements[i]; // <figure> element

            // include only element nodes
            if(figureEl.nodeType !== 1) {
                continue;
            }

            linkEl = figureEl.children[0]; // <a> element

            console.log("figureEl : " + figureEl );
            console.log("figureEl.getAttribute('data-fullimagesize') : " + figureEl.getAttribute('data-fullimagesize') );

            size = figureEl.getAttribute('data-fullimagesize').split('x');

            // create slide object
            item = {
                src: linkEl.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };



            if(figureEl.children.length > 1) {
                // <figcaption> content
                item.title = figureEl.children[1].innerHTML;
            }

            if(linkEl.children.length > 0) {
                // <img> thumbnail element, retrieving thumbnail url
                item.msrc = linkEl.children[0].getAttribute('src');
            }

            item.el = figureEl; // save link to element for getThumbBoundsFn
            items.push(item);
        }

        return items;
    };

    // find nearest parent element
    var closest = function closest(el, fn) {
        return el && ( fn(el) ? el : closest(el.parentNode, fn) );
    };

    // triggers when user clicks on thumbnail
    var onThumbnailsClick = function(e) {

	    	console.log( "--onThumbnailsClick");

        e = e || window.event;
        if( e.preventDefault ) {
	        e.preventDefault();
	      } else {
		      e.returnValue = false;
		    }

        var eTarget = e.target || e.srcElement;

        // find root element of slide
        var clickedListItem = closest(eTarget, function(el) {
            return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
        });

        if(!clickedListItem) {
            return;
        }

        // find index of clicked item by looping through all child nodes
        // alternatively, you may define index via data- attribute
        var clickedGallery = clickedListItem.parentNode,
            childNodes = clickedListItem.parentNode.childNodes,
            numChildNodes = childNodes.length,
            nodeIndex = 0,
            index;

        for (var i = 0; i < numChildNodes; i++) {
            if(childNodes[i].nodeType !== 1) {
                continue;
            }

            if(childNodes[i] === clickedListItem) {
                index = nodeIndex;
                break;
            }
            nodeIndex++;
        }



        if(index >= 0) {
            // open PhotoSwipe if valid index found
            openPhotoSwipe( index, clickedGallery );
        }
        return false;
    };

    // parse picture index and gallery index from URL (#&pid=1&gid=2)
    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
        params = {};

        if(hash.length < 5) {
            return params;
        }

        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if(!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');
            if(pair.length < 2) {
                continue;
            }
            params[pair[0]] = pair[1];
        }

        if(params.gid) {
            params.gid = parseInt(params.gid, 10);
        }

        if(!params.hasOwnProperty('pid')) {
            return params;
        }
        params.pid = parseInt(params.pid, 10);
        return params;
    };

    var openPhotoSwipe = function(index, galleryElement, disableAnimation) {
        var pswpElement = document.querySelectorAll('.pswp')[0],
            gallery,
            options,
            items;

        items = parseThumbnailElements(galleryElement);

        // define options (if needed)
        options = {
            index: index,

            // define gallery index (for URL)
            galleryUID: galleryElement.getAttribute('data-pswp-uid'),

            getThumbBoundsFn: function(index) {
                // See Options -> getThumbBoundsFn section of documentation for more info
                var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                    pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                    rect = thumbnail.getBoundingClientRect();

                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
            }

        };

        if(disableAnimation) {
            options.showAnimationDuration = 0;
        }

        // Pass data to PhotoSwipe and initialize it
        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    };

    // loop through all gallery elements and bind events
    var galleryElements = document.querySelectorAll( gallerySelector );

    for(var i = 0, l = galleryElements.length; i < l; i++) {
        galleryElements[i].setAttribute('data-pswp-uid', i+1);
        galleryElements[i].onclick = onThumbnailsClick;
    }

    // Parse URL and open gallery if it contains #&pid=3&gid=1
    var hashData = photoswipeParseHash();
    if(hashData.pid > 0 && hashData.gid > 0) {
        //openPhotoSwipe( hashData.pid - 1 ,  galleryElements[ hashData.gid - 1 ], true );
    }
};


window.instanceName = $(".navbar-brand").attr("href").substring( $(".navbar-brand").attr("href").indexOf("opendoc.org/") + 12);
instanceName = instanceName.substring( 0, instanceName.indexOf("\/") );

window.projet = $(".taxProj").data("term");

function sendActionToAnalytics(thisAction ) {
	if( typeof gaTracker !== 'undefined' ) {
		console.log("Sent analytics action : instance = " + instanceName + "username = " + username + " projet : "  + projet + " action : " + thisAction);
		//gaTracker('send', 'event', 'button', 'click', {'instance': instanceName, 'projet': projet, 'action': thisAction});
/*
		gaTracker('send', 'event',
		  'instanceName': instanceName,          // Required.
		  'user': username,          // Required.
		  'project': projet,      // Required.
		  'thisAction': thisAction
		});
*/
		gaTracker('send', 'event', instanceName + "|" + projet + "|" + username, 'click', thisAction);
		console.log( instanceName + " " + projet + " " + username);
	}

}

// Use this variable to set up the common and page specific functions. If you
// rename this variable, you will also need to rename the namespace below.
var Roots = {

  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages

      // désactive les console.log si pas un superadmin
			if( !$("body").hasClass("superadmin") ) {
		    logger.disableLogger();
			}

			postViewRoutine.init();

			animateLogo();
			adjustMainMargintop();

			///////////////////////////////////////////////// ajouter un post /////////////////////////////////////////////////

			$(".add-post").click(function() {

				sendActionToAnalytics( "Nouveau post" );

				// générer la bonne url, remplir le pop-over avec ce contenu quand il sera dispo
				var newPostURL = newPost();

			});

			///////////////////////////////////////////////// passer en mode édition /////////////////////////////////////////////////
			$(".switch-edition").click(function() {

				if( $("body").hasClass("is-edition") ) {
					sendActionToAnalytics("Annuler la vue édition");
				} else {
					sendActionToAnalytics("Passer en vue édition");
				}

				$("body").toggleClass("is-edition");
			});

			///////////////////////////////////////////////// rafraichir postie /////////////////////////////////////////////////
			$(".refresh-postie").click(function() {

				sendActionToAnalytics("Rafraichissement de Postie");

				$this = $(this);

				if( $this.find(".results").length > 0 ) {
					location.reload(true);
					return false;
				}

				$this.addClass("is-loading");

				thisActionUrl = window.location.href;
				thisActionUrl += "?postie=get-mail";
				console.log("Submitted ajax post request");
				console.log("TO : " + thisActionUrl);



	       $.ajax({
	          type: "POST",
	          url: thisActionUrl,
	          data: {},
	          success: function(data)
	          {
							console.log("SuccessAjax !");
							console.log("Refreshed Postie !");
							console.log( data );

							var projectTerm = $("article.taxProj").attr("data-term");

							var countNewContent = 0;
							var countNewContentForProject = 0;

							while ( data.search("##") !== -1 ) {
								project = data.substring( data.indexOf("##")+2 );
								projectName = project.substring( 0, project.indexOf("##") );

								data = project.substring( project.indexOf("##")+2 );

								if( projectName.toLowerCase() === projectTerm.toLowerCase() ) {
									countNewContentForProject++;
									countNewContent++;
								} else {
									countNewContent++;
								}

								console.log ( "project gotten = " + projectName);
							}



							$this.removeClass("is-loading");

							// récupérer le nombre de mails parsés
							if ( countNewContent > 0 ) {

								$this.empty();

								if( countNewContentForProject > 0 ) {
									$this.append("<div class='results'>" + countNewContentForProject + " nouveaux message(s) pour le projet <em>" + projectTerm + "</em>. <a href=''>Rafraichissez la page.</a></div>");
								} else {

									$this.append("<div class='results'>" + countNewContent + " nouveaux message(s) pour d'autres projets.</a></div>");
									setTimeout( function() {
										$this.empty().text("Rafraîchir");
									}, 2000);

								}

							}

	          }
	      });

			});

			// click sur déconnexion
			$(".deconnexion-field").click(function() {
				sendActionToAnalytics("Déconnexion");

				var decoURL = $("#wp-logout").attr("href");
				window.location.href = decoURL;
			});

			// click sur inscription
			$(".login-field").click(function() {
				sendActionToAnalytics("Inscription");
				loginField();
			});

    }

  },
  // Home page
  home: {
    init: function() {



    }
  },

  page_template_template_page_accueil: {
	  init: function() {

			$('#wideView img').removeAttr('style');

			var $container = $('#wideView #colonnesContainer');
			$container.packery({
			  itemSelector: '.colonneswrappers',
			  gutter: 10
			});
			setTimeout(function() {
				$container.packery({
				  itemSelector: '.colonneswrappers',
				  gutter: 10
				});

			}, 500);

			///////////////////////////////////////////////// ajouter un projet /////////////////////////////////////////////////

			$(".add-project").click(function() {

				// ouvrir un champ formulaire
				$("#nouveauProjet").addClass("is-visible");

				$("body").addClass("is-overlaid");

				var newProjectInputField = $("#nouveauProjet");
				fillPopOver( newProjectInputField, $(this), 300, 380 );

				$("#nouveauProjet button").click( function(e) {





					e.preventDefault();

					sendActionToAnalytics("Nouveau projet ");



					var projName = $("#projectName").val();

					// ajouter en ajax un nouveau terme
			    var data = {
			        'action': 'add_tax_term',
							'tax_term': projName
			    };
			    $.post(ajaxurl, data, function(response) {
						// recharger la page

						console.log( "Reload page ");
						$this = $(this);
						window.top.location.reload(true);

					});




/*

					if( projName.length > 0 ) {
						sendActionToAnalytics( "Nouveau projet" );
						var newPostURL = newProject(projName);
					}
*/
				});
			});


		}
	},

  new_post: {
	  init: function() {
			/*********************** ajouter un bouton "Save" a cote de .button-right .edit-post ***********************/
			// trigger un click sur "Mettre a jour" avant tout
			var $save_button = $(".save-modifications");

			$save_button.removeClass("is-disabled");

			$save_button.on( "click", function() {

				sendActionToAnalytics("Enregistrer nouveau post");
				console.log("clicked save");
				$("body").find(".fee-publish").click();

			});

			$(".edit-post").click(function(e) {

				sendActionToAnalytics("Fin édition nouveau post");
				console.log( "Reload page ");
				$this = $(this);
				window.top.location.reload(true);

				e.preventDefault();

			});


			$(".remove-post").click( function(e) {

				sendActionToAnalytics("Supprimer un post");

				e.preventDefault();

				var $thisPost = $(this).parents(".post");
				thisID = $thisPost.attr("data-id");

		    var data = {
		        'action': 'remove_post',
						'post_id': thisID
		    };
		    $.post(ajaxurl, data, function(response) {
						console.log( "Reload page ");

						$this = $(this);
						window.top.location.reload(true);

		    });

				return false;

			});

		}
	},

  new_project: {
	  init: function() {
			/*********************** ajouter un bouton "Save" a cote de .button-right .edit-post ***********************/
			// trigger un click sur "Mettre a jour" avant tout
			var $save_button = $(".save-modifications");

			$save_button.removeClass("is-disabled");

			$save_button.on( "click", function() {

				sendActionToAnalytics("Enregistrer nouveau projet");
				console.log("clicked save");
				$("body").find(".fee-publish").click();

			});

			$(".edit-post").click(function(e) {

				sendActionToAnalytics("Fin édition nouveau post");
				console.log( "Reload page ");
				$this = $(this);
				window.top.location.reload(true);

				e.preventDefault();

			});


			$(".remove-post").click( function(e) {

				sendActionToAnalytics("Supprimer un post");
				e.preventDefault();

				var $thisPost = $(this).parents(".post");
				thisID = $thisPost.attr("data-id");

		    var data = {
		        'action': 'remove_post',
						'post_id': thisID
		    };
		    $.post(ajaxurl, data, function(response) {
						console.log( "Reload page ");

						$this = $(this);
						window.top.location.reload(true);

		    });


				return false;

			});

		}
	},

  // About us page, note the change from about-us to about_us.
  about_us: {
    init: function() {
      // JavaScript to be fired on the about us page
    }
  },

  tax_projets: {
	 	init: function() {

			if( $("body").hasClass("logged-in") ) {
				$("body").addClass("is-edition");
			}

			createTimeline();




		}
	},

};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
