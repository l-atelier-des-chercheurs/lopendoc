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

function createTimeline() {

	// pour chaque post de la page
	$(".projetContainer").find(".postContainer").each(function() {

		$this = $(this);

		timeinISO = new Date( $this.find("time").attr("datetime") );
		timeinMS = timeinISO.getTime();
		//console.log(" TIME : " + timeinMS);




	});




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

     $.ajax({
        type: "POST",
        url: thisActionUrl,
        data: {
             post_id: thisID,
             action: "set_taxonomy"
        },
        success: function(data)
        {
			     $.ajax({
			        type: "POST",
			        url: thisActionUrl,
			        data: {
			             post_id: thisID,
			             action: "update_post_visibility",
			             visibility: "private",
			        },
			        success: function(data)
			        {
								$(".popover").removeClass("is-loading");
			        }
			    });

        }
    });

	} );
}

function loginField() {

	$("body").addClass("is-overlaid");

	var loginWindow = $(".login").clone(true);

	fillPopOver( loginWindow, $(".button.login-field"), 300, 300 );

	$(".popover").addClass("is-loading");

	$(".popover").removeClass("is-loading");

}

function fillPopOver( content, thisbutton, finalWidth, finalHeight ) {
	var $popover = $(".popover");
	$popover.html( content);

	$popover.append('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div>');

	$popover.addClass("is-visible");

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

			// lancer la gallerie d'image
			initPhotoSwipeFromDOM('.entry-content .gallery');

			$(".entry-title").each(function() {
				if( $(this).text() === "Brouillon auto" ) {
					$(this).parents(".post").addClass("hidden");
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
				} else {
					newStatus = "publish";
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

		     $.ajax({
		        type: "POST",
		        url: thisActionUrl,
		        data: {
		             post_id: thisID,
		             action: "update_post_visibility",
		             visibility: newStatus,
		        },
		        success: function(data)
		        {
							console.log("SuccessAjax !");
							console.log("NewStatus !");
							console.log( newStatus );

							$thisPost.parents(".postContainer").removeClass("is-loading");
							$thisPost.attr("data-status", newStatus);
							$thisPost.siblings(".publish-private-post").attr("data-status", newStatus);

		        }
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
					replaceiFrameWithPost( $thisPost, pageLink );
				} else {

					// sinon
					replacePostWithIframe( $thisPost, pageLink );


				}

				return false;

			});

			///////////////////////////////////////////////// click sur supprimer /////////////////////////////////////

			$(".post .button-right .remove-post").click( function(e) {

				e.preventDefault();

				var $thisPost = $(this).parents(".post");
				thisID = $thisPost.attr("data-id");

				thisActionUrl = window.location.href;

				console.log("thisID " + thisID );

				$thisPost.addClass("is-removing");

				$.ajax({
				  type: "POST",
				  url: thisActionUrl,
				  data: {
				       post_id: thisID,
				       action: "remove_post"
				  },
				  success: function(data)
				  {
						console.log("SuccessAjaxPost!");
						console.log("Removed post!");
						console.log(data);

						$thisPost.parents(".postContainer").slideUp("normal", function() { $(this).remove(); } );

				  }
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


var switchEditionMode = {
	init: function() {

	},

	setSwitch: function() {

		$("body").toggleClass("is-edition");

	},

};

var initPhotoSwipeFromDOM = function(gallerySelector) {

    console.log("INIT photoswipe");

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
        openPhotoSwipe( hashData.pid - 1 ,  galleryElements[ hashData.gid - 1 ], true );
    }
};



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

				// générer la bonne url, remplir le pop-over avec ce contenu quand il sera dispo
				var newPostURL = newPost();

				// proposer de rafraichir la page pour valider

			});

			///////////////////////////////////////////////// passer en mode édition /////////////////////////////////////////////////
			$(".switch-edition").click(function() {
				switchEditionMode.setSwitch();
			});

			///////////////////////////////////////////////// rafraichir postie /////////////////////////////////////////////////
			$(".refresh-postie").click(function() {

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
				var decoURL = $("#wp-logout").attr("href");
				window.location.href = decoURL;
			});

			// click sur inscription
			$(".login-field").click(function() {

				loginField();


			});

    }

  },
  // Home page
  home: {
    init: function() {



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
