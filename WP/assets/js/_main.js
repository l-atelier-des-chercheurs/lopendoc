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

var startDetectEnregistrementNewPost = function() {
	setTimeout(function () {
			if( $("#fee-notice-area .updated").length > 0) {
			} else {
				startDetectEnregistrementNewPost();
			}
	}, 1000);
};



// voir http://stackoverflow.com/questions/12433604/how-can-i-find-matching-values-in-two-arrays
Array.prototype.diff = function(arr2) {
	var ret = [];
	this.sort();
	arr2.sort();
	for(var i = 0; i < this.length; i += 1) {
		if(arr2.indexOf( this[i] ) > -1){
			ret.push( this[i] );
		}
	}
	return ret;
};

jQuery.fn.the_filters = function(){
	var self = this;

	this.init = function(){
		if($(this.selector).length){

			console.log("the_filters start");

			var elements = ($(".filter-elements>:not(.descriptionContainer) .category-list [data-categorie]").toArray());

			categories = [];
			for(var i=0;typeof(elements[i])!=='undefined';) {
				if( $.inArray(elements[i].outerHTML, categories) === -1) {
					categories.push( elements[i].outerHTML);
				}
				i++;
			}
			$(this).find(".contenu").append( categories.join(" ") );
			if( categories.length === 0) {
				$(this).remove();
			}


			$(this).find(".category-term").bind("tap", function() {

				$(this).toggleClass("is-active");
				$(".category-term").not($(this)).removeClass("is-active");
				self.showIntervenants();
				self.updateIsotope();
			});

			// ouverture du menu filtre au click
			$(".open-filters").bind("tap", function() {
				if( $(".main").attr("data-filters") !== "show") {
					$(".main").attr("data-filters", "show");
				} else {
					$(".main").attr("data-filters", "");
				}
			});

			// tap sur le tag d'un projet
			$(".colonnes .category-term").bind("tap", function() {
				$(".category-filters .category-term[data-categorie=" + $(this).attr("data-categorie")	+ "]").trigger("tap");
			});
			// tap sur le tag d'un projet
			$(".postContainer .category-term").bind("tap", function() {
				$(".category-filters .category-term[data-categorie=" + $(this).attr("data-categorie")	+ "]").trigger("tap");
			});
		}
	};


	this.showIntervenants = function() {
				// 1. récupérer tous ".filtre" et pusher dans un array

					var activetags = [];
					$(".category-filters .category-term.is-active").each(function() {
						activetags.push( $(this).attr("data-categorie"));
					});

					console.log( "activetags : " + activetags.toString());

					// aucun tag actif : désactiver tout

					// s'il y a des colonnes wrappers
					if( $(".filter-elements .colonneswrappers").length > 0) {
						if( activetags.length === 0) {
							$(".filter-elements .colonneswrappers").removeClass("is-shown");
							$(".filter-elements .colonneswrappers").find(".category-list span").removeClass("is-active");
						} else {

							// 2. comparer les data-motscles de chaque intervenant avec cet array. Si un des termes correspond, lui ajouter is-active s'il ne l'a pas
							// sinon, lui retirer

							$(".colonneswrappers").each( function() {
								motsClesFiche = $(this).find("[data-allcategories]").attr("data-allcategories");

								if( motsClesFiche !== undefined) {
									motsClesFicheArray = motsClesFiche.split(" ");
									// si ça fit

									//console.log( "motsClesFicheArray : " + motsClesFicheArray[0]);
									//console.log( "activetags.diff(motsClesFicheArray) : " + activetags.diff(motsClesFicheArray));

									if( activetags.diff(motsClesFicheArray).length > 0) {
										$(this).addClass("is-shown");
										// mettre en surbrillance son tag
										$(this).find(".category-list span").each(function() {
											motsClesTag = $(this).attr("data-categorie");


											console.log( "Activation du mot-clé dans la vignette --- ");
											console.log( "motsClesTag = " + motsClesTag);


											if( $.inArray( motsClesTag, activetags) > -1) {
												$(this).addClass("is-active");
											} else {
												$(this).removeClass("is-active");
											}

										});

									} else {
										$(this).removeClass("is-shown");
									}
								} else {
									$(this).removeClass("is-shown");
								}
							});
						}
					}

					// s'il y a des posts
					if( $(".filter-elements .postContainer").length > 0) {

						if( activetags.length === 0) {
							$(".filter-elements .postContainer").removeClass("is-filtered");
							$(".filter-elements .postContainer").slideDown();
							$(".filter-elements .postContainer").find(".category-list span").removeClass("is-active");
						} else {

							$(".postContainer").each( function() {
								motsClesFiche = $(this).find("[data-allcategories]").attr("data-allcategories");

								if( motsClesFiche !== undefined) {
									motsClesFicheArray = motsClesFiche.split(" ");
									// si ça fit

									if( activetags.diff(motsClesFicheArray).length > 0) {
										$(this).removeClass("is-filtered");
										$(this).slideDown();

										// mettre en surbrillance son tag
										$(this).find(".category-list span").each(function() {
											motsClesTag = $(this).attr("data-categorie");

											console.log( "motsClesTag = " + motsClesTag);

											if( $.inArray( motsClesTag, activetags) > -1) {
												$(this).addClass("is-active");
											} else {
												$(this).removeClass("is-active");
											}

										});

									} else {
										$(this).addClass("is-filtered");
										$(this).slideUp();
									}
								} else {
									$(this).addClass("is-filtered");
									$(this).slideUp();
								}
							});

						}

					}


	};
	this.updateIsotope = function() {

		if( $(".colonneswrappers.is-shown").length > 0) {
			$('#colonnesContainer .colonnesContainerInside').isotope({
				filter: '.is-shown'
			});
		} else {
			$('#colonnesContainer .colonnesContainerInside').isotope({
				filter: ''
			});
		}
	};

	this.init();

};


jQuery.fn.sort_items = function(){
	var self = this;

	this.init = function(){
		if($(this.selector).length){

			$(this).find("[data-type=edited]").addClass("is-active");
			this.changeSort( "edited");

			$(this).find(".sort-term").bind("tap", function() {
				$(this).addClass("is-active");
				$(".sort-term").not($(this)).removeClass("is-active");
				var sortType = $(this).attr("data-type");
				self.changeSort( sortType);
			});
		}
	};

	this.changeSort = function ( sortType) {

	console.log( "sorttype L " + sortType);
		if( sortType === "edited") {
			$('#colonnesContainer .colonnesContainerInside').isotope({
			  sortBy : ['edited', 'titreproj', 'created']
			});
		} else
		if( sortType === "ab") {
			$('#colonnesContainer .colonnesContainerInside').isotope({
			  sortBy : ['titreproj', 'edited', 'created']
			});
		} else
		if( sortType === "created") {
			$('#colonnesContainer .colonnesContainerInside').isotope({
			  sortBy : ['created', 'edited', 'titreproj']
			});
		}
	};

	this.init();
};


jQuery.fn.fixedUI = function(){
	var self = this;

	this.init = function(){
		if($(this.selector).length){
			self.fixGallery();

			console.log("fixedUI start");
			//fix lateral filter and gallery on scrolling
			$(window).on('scroll', function(){
				jshint = (!window.requestAnimationFrame) ? self.fixGallery() : window.requestAnimationFrame(self.fixGallery);
			});

		}

	};

	this.fixGallery = function() {
		var offsetTop = $('.main').offset().top;
		var	scrollTop = $(window).scrollTop();
		console.log("fixGallery");
		jshint = ( scrollTop >= offsetTop ) ? $('.main').addClass('is-fixed') : $('.main').removeClass('is-fixed');
	};

	this.init();
};

function jumpTo(index){
	$(window).scrollTop($('.postContainer:eq('+index+')').offset().top - 100, 300);
}

d3.selection.prototype.first = function() {
  return d3.select(this[0][0]);
};
d3.selection.prototype.last = function() {
  var last = this.size() - 1;
  return d3.select(this[0][last]);
};


jQuery.fn.reverse = [].reverse;



function createTimeline() {

	var options = {
		radius : {
			min: 6,
			max: 10
		},
	};


	// create object
	var navbarContainer = d3.select( $("#colonnesOptionsTopBar")[0]);
	var makeTimeline = navbarContainer
		.append("svg")
		.on({
			mouseover : function(d, i){
				// $(this).find('circle').each(function(){
				// 	$(this).attr('r', 4.5);
				// });
			},
			mouseleave : function(d, i){
				// $(this).find('circle').each(function(){
				// 	$(this).attr('r', 2.5);
				// });
			}
		})
		.attr("width", "95%")
		.attr("height","20px")
		.attr("class", "timelineContainer")
		.attr("style", "left: 0; overflow:visible; bottom:-14px; position: absolute; margin-left: 2.5%");



	// min/max time
	var now = new Date();
	var time = {
		min : now.getTime(),
		max : 0,
	};

	if($("#projetContainer").find(".postContainer").length > 1){
		$("#projetContainer").find(".postContainer").last().each(function(){
			timeinISO = new Date($(this).find("time").attr("datetime"));
			time.max = timeinISO.getTime();
		});

		$("#projetContainer").find(".postContainer").first().each(function(){
			timeinISO = new Date($(this).find("time").attr("datetime"));
			time.min = timeinISO.getTime();
		});
	}



	// timeline stroke
	makeTimeline.append("rect")
		.attr("class", "repere")
		.attr("x", 0)
		.attr("y", 6)
		.attr("width", "0%")
		.attr("fill", "#f2682c")
		.attr("height", 4);



	// spawn circles
	var circles_position_y;
	$("#projetContainer").find(".postContainer").each(function(index){
		t = $(this);

		// get time range
		var timeinISO = new Date(t.find("time").attr("datetime"));
		var timeRangeFrom0to100 = parseInt( mapRange([time.min, time.max], [0, 100], timeinISO.getTime()) );

		// color
		var dataStatus = t.find(".publish-private-post").attr("data-status");
		var fillColor = (dataStatus === "publish") ? "#45C1B4" : "#F2682C";


		var last_circle_cx = parseInt( $(".timeline-circle").last().attr('cx') ) || -1;

		// if(last_circle_cx !== timeRangeFrom0to100){
			circles_position_y = 8;

			makeTimeline.append("circle")
				.on({
					click : function(d){
						jumpTo(index);
					},
					mouseover : function(d){
						d3.select(this)
							.transition()
							.duration(300)
							.attr("r", options.radius.max);
					},
					mouseleave : function(d){
						d3.select(this)
							.transition()
							.duration(300)
							.attr("r", options.radius.min);
					},
				})
				.attr('data-title', t.find('.entry-title').text())
				.attr('data-content', "crée le : " + t.find('time.createdDate .contenu').text())
				.attr('data-toggle-tooltip-color', (dataStatus === "publish") ? '#293275' : '#fbb41d')
				.attr('class', 'timeline-circle')
				.attr("data-status", dataStatus)
				.attr("r", 0)
				.attr("fill", fillColor)
				.attr("style", "cursor: pointer")
				.attr("stroke", "transparent")
				.attr("cx", timeRangeFrom0to100 + "%")
				.attr("cy", "8")
				.transition()
				.delay(function(){ return index * 150; })
				.duration(300)
				.ease("in-out")
				.attr("r", options.radius.min);
		// }else{
			// circles_position_y += 10;

			// makeTimeline.append("circle")
			// 	.on({
			// 		click : function(d){
			// 			jumpTo(index);
			// 		},
			// 		mouseover : function(d){
			// 			d3.select(this)
			// 				.transition()
			// 				.duration(300)
			// 				.attr("r", options.radius.max);
			// 		},
			// 		mouseleave : function(d){
			// 			d3.select(this)
			// 				.transition()
			// 				.duration(300)
			// 				.attr("r", options.radius.min);
			// 		},
			// 	})
			// 	.attr('class', 'timeline-circle')
			// 	.attr('data-title', t.find('.entry-title').text())
			// 	.attr('data-content', "crée le : " + t.find('time.createdDate .contenu').text())
			// 	.attr("data-status", dataStatus)
			// 	.attr("r", 0)
			// 	.attr("fill", fillColor)
			// 	.attr("style", "cursor: pointer")
			// 	.attr("stroke", "transparent")
			// 	.attr("cx", timeRangeFrom0to100 + "%")
			// 	.attr("cy", circles_position_y)
			// 	.transition()
			// 	.delay(function(){ return index * 150; })
			// 	.duration(300)
			// 	.ease("in-out")
			// 	.attr("r", options.radius.min);
		// }
	});



	// create popovers
	$('.timelineContainer').find("circle").each(function(){
		$(this).tooltip({
			container: 'body',
			// template: '<div class="marie-popin" role="tooltip"><div class="arrow"></div><h3 class="marie-popin-title">'+$(this).attr('data-title')+'</h3><div class="marie-popin-content">'+ $(this).attr('data-content')+'</div></div>',
			trigger: 'hover',
			placement: 'top'
		});
	});



	// onscroll function
	ppostVisible = -1;
	$(window).on('scroll', function () {
		postVisible = whichPostIndexIsVisible(window.pageYOffset);

		if(postVisible !== ppostVisible){
			$('.postContainer').eq(ppostVisible).removeClass("is-active");
			makeTimeline.selectAll("circle")
				.filter(function (d, i) { return i === ppostVisible;})
				.transition()
				.duration(300)
				.attr("r", options.radius.min);

			$('.postContainer').eq(postVisible).addClass("is-active");

			var posXofNewCircle = 0;
			var colorofNewCircle;

			makeTimeline.selectAll("circle")
				.filter(function (d, i) {
					if( i === postVisible ){
						posXofNewCircle = d3.select(this).attr("cx");
						colorofNewCircle = d3.select(this).attr("fill");
						return true;
					}
				})
				.transition()
				.duration(300)
				.attr("r", options.radius.max);

			if(posXofNewCircle.length > 0){
				makeTimeline.select(".repere")
					.transition()
					.duration(300)
					// .attr("fill", colorofNewCircle)
					.attr("x", 0)
					.attr("width", posXofNewCircle);
			}

			ppostVisible = postVisible;
		}

	});


}

function createCustomFavicon() {
	var canvas = document.createElement('canvas'),
		ctx,
		img = document.createElement('img'),
		link = document.getElementById('favicon');

	if (canvas.getContext) {
	  canvas.height = canvas.width = 32; // set the size
	  ctx = canvas.getContext('2d');

		ctx.beginPath();
		ctx.arc(16, 16, 16, 0, Math.PI*2, true);
		ctx.closePath();
	ctx.fillStyle = couleurSecondaire;
	ctx.fill();

		ctx.globalAlpha=0.8; // Half opacity
		ctx.beginPath();
		ctx.arc(16, 16, 8, 0, Math.PI*2, true);
		ctx.closePath();

	ctx.fillStyle = couleurPrimaire;
	ctx.fill();

	link.href = canvas.toDataURL('image/png');
	}
}

function langIsFrench() {
	return $("html").attr("lang") === "fr-FR";
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
	var $blocOuMettreliframe = $thisPost.find(".entry-stuff");

	// si on edit une description
	if( $thisPost.parent().is(".descriptionContainer")) {
		$blocOuMettreliframe.empty().append('<div class="embed-responsive embed-responsive-16by9"><iframe class="edit-frame" src="' + pageLink + '#edit=true&type=description&editor=" style="border:0px;width:100%;height:100%;"></iframe></div>');
	} else {
		$blocOuMettreliframe.empty().append('<div class="embed-responsive embed-responsive-4by3"><iframe class="edit-frame" src="' + pageLink + '#edit=true" style="border:0px;width:100%;height:100%;"></iframe></div>');
	}

	/*********************** ajouter un bouton "Save" a cote de .button-right .edit-post ***********************/
	// trigger un click sur "Mettre a jour" avant tout
	var $save_button = $thisPost.find(".save-modifications");
	$save_button.removeClass("is-disabled");
	$save_button.bind( "tap", function() {
		sendActionToAnalytics("Fin édition d'un post");
		var $thisPost = $(this).closest(".post");

		$thisPost.find(".edit-frame").contents().find(".fee-save").click();
	});


}

function updatePostContent( $thisPost, pageLink ) {

	$thisPost.find(".save-modifications").addClass("is-disabled");

	$.get( pageLink, function( data ) {
		$thisPost.removeClass("is-edited");
		$data = $(data);

		var $thisContent = $data.find('.post').html();
		var $blocOuMettrelepost = $thisPost;
		$blocOuMettrelepost.empty().append($thisContent).post_view_routine();

	});

}


function animateLogo() {

	$(".navbar-brand svg").find("rect,circle,polyline,line,path").velocity({
			scale: 0.8
		}, {
		duration: 0,
		});

	$(".navbar-brand svg").velocity({ opacity: 1} );
	$(".navbar-brand svg").find("rect,circle,polyline,line,path").each(function(i) {
		$(this).delay(i*40).velocity({
			scale: 1
		}, {
		duration: 800,
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

	// créer un post
	var data = {
	  'action': 'create_private_post_with_tax',
			'security': ajaxnonce,
			'term': nomProjet,
			'userid'	: userid,
  };
  $.post(ajaxurl, data, function(the_permalink) {
		console.log('Server response from the AJAX URL ' + the_permalink);

		$.get( the_permalink, function( data ) {
			$data = $(data);

			var $thisPost = $data.find('.postContainer');
			console.log( "$thisContent");
	//		console.log(  $thisContent );

			$("#projetContainer").prepend( $thisPost);
			$("body").removeClass("is-overlaid");

			$thisPost.find(".post").post_view_routine();

			$thisPost.find(".button-right .edit-post").trigger("click");

			$('html, body').animate({
				scrollTop: $thisPost.offset().top - $(".banner").height() * 1.5
			}, 600);

		});

  });


	// puis quand on récupère on url, l'ouvrir

	// de fee-adminbar.js
/*
	wp.ajax.post( 'fee_new', {
		post_type: 'post',
		nonce: fee.nonce
	} ).done( function( url ) {

		var iframeAvecLien = '<div class="embed-responsive embed-responsive-4by3"><iframe class="edit-frame" src="' + url + '?fee=visible&type=newpost" style="border:0px;width:100%;height:600px;"></iframe></div>';
		fillPopOver( iframeAvecLien, $(".button.add-post"), 900, 600);
		$(".popover").addClass("is-loading");

	});
*/
}

function updateProjectAuthors( newauthors) {


	console.log('edit_projet_authors : ');
	console.log('nomProjet : ' + nomProjet);
	console.log('newauthors : ' + newauthors);
  var data = {
	  'action': 'edit_projet_authors',
			'projet': nomProjet,
			'security': ajaxnonce,
			'newauthors': newauthors
  };
  $.post(ajaxurl, data, function(response) {
	console.log('Server response from the AJAX URL : ' + response);
		$(".popover").removeClass("is-loading");
		window.top.location.reload(true);
  });

}











function fillPopOver( content, thisbutton, finalWidth, finalHeight ) {



	var $popover = $(".popover");

	if( $popover.find(".popoverContainer").length === 0) {
		$(".popover").html( "<div class='popoverContainer'></div>");
	}

	$popoverContainer = $(".popover .popoverContainer");

	$popoverContainer.html(content);

// 	$popoverContainer.append('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div>');

	$popover.addClass("is-visible");
	$popoverContainer.find(".login").css("visibility", "visible");

	if( $popover.find("input").length > 0) {
		$popover.find("input").eq(0).focus();
	}

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
	}, 1000, [ 200, 0 ], function(){
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
	});

	$("body").on('click', function(event){
		if( $(event.target).is('.close-panel') || $(event.target).is('body.is-overlaid')) {
			closePopover();
		}
	});
	$(document).keyup(function(event){
		console.log( "event.which :  " + event.which);
	if(event.keyCode === 27){
			closePopover();
		}
	if (event.keyCode === 13) {
			$popover.find("button").trigger("click");
	}
	});
}

function closePopover() {
	$("body").removeClass("is-overlaid");
	$(".popover").removeClass("is-visible").empty();
}

jQuery.fn.post_view_routine = function(){

	var self = this;
	this.init = function(){
		// fonctions propres à chaque post (a appliquer si infinite scroll), à ne pas appliquer si iframe
		//makeLinksBlank();
		if( !$("body").hasClass("iframe") ) {
			this.makeImgIntoGallery();
			this.publishPrivateTap();
			this.editPostTap();
			this.removePostTap();
			this.activateTooltips();
			this.loadProcessingSketch();
			this.activate_comments();
		}
	};

	this.makeImgIntoGallery = function(){

		// transformer en gallerie d'image
		this.find("img").parents("a:not(.thumbnail)").each(function() {
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
	};

	this.publishPrivateTap = function(){

		console.log("publish private set");
			this.siblings(".publish-private-post").click( function(e) {
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

				console.log("Submitted ajax post request");
				console.log("TO : " + thisActionUrl);
				console.log("post_id : " + thisID);
				console.log("action : " + "update_post_visibility");
				console.log("visibility : " + newStatus);

				$thisPost.parents(".postContainer").addClass("is-loading");

			var data = {
				'action': 'change_post_visibility',
						'security': ajaxnonce,
						'post_id': thisID,
						'post_status': newStatus
			};

			$.post(ajaxurl, data, function(response) {
					var str = JSON.parse(response);
					$thisPost.parents(".postContainer").removeClass("is-loading").attr("data-status", str);
					$thisPost.attr("data-status", str);
					$thisPost.siblings(".publish-private-post").attr("data-status", str);
					console.log( "response : " + str);
			});


				e.preventDefault();

			});
	};

	this.editPostTap = function(){

		///////////////////////////////////////////////// click sur éditer /////////////////////////////////////
		this.find(".button-right .edit-post").bind("tap", function(e) {

			e.preventDefault();
			// ouvrir dans un nouvel onglet
			console.log("edit-post click");

			var $thisPost = $(this).closest(".post");
			var pageLink = $thisPost.attr("data-singleurl");

			if( $thisPost.hasClass("is-edited") ) {

				// si déja édité, alors revenir au mode normal en replacant le contenu updaté dans la page
				sendActionToAnalytics("Fin d'édition d'un post");
				updatePostContent( $thisPost, pageLink );

			} else {
				sendActionToAnalytics("Édition d'un post");
				replacePostWithIframe( $thisPost, pageLink );
			}

			return false;

		});

	};

	this.removePostTap = function(){

		///////////////////////////////////////////////// click sur supprimer /////////////////////////////////////
		this.find(".button-right .remove-post").bind("tap", function(e) {

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
					'security': ajaxnonce,
					'post_id': thisID,
		};
		$.post(ajaxurl, data, function(response) {
					console.log("SuccessAjaxPost!");
					console.log("Removed post!");
					console.log(response);

					$thisPost.parents(".postContainer").slideUp("normal", function() { $(this).remove(); } );
		});

			return false;

		});

	};

	this.loadProcessingSketch = function(){

		console.log( '$(".post .entry-content:contains(void setup())").length = ' + this.find(".entry-content:contains(void setup)").length );
		// si y a besoin de pjs
		if( this.find(".entry-content:contains(void setup)").length > 0 && $.find("#pjs").length === 0) {

			console.log( " PLop");

		  var scriptSrc = '//cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.8/processing.min.js';
			var script = document.createElement('script');
			script.src = scriptSrc;
			script.id = 'pjs';

			var $thisPost = $(this);

			script.onload = function() {
				self.convertThisPostToCanvas( $thisPost);
			};

			var head = document.getElementsByTagName('head')[0];
			head.appendChild(script);
		}	else if( this.find(".entry-content:contains(void setup)").length > 0 && $.find("#pjs").length > 0) {
			$.find("#pjs").onload = function() {
				self.convertThisPostToCanvas( $thisPost);
			};
		}
	};

	this.convertThisPostToCanvas = function( $thisPost) {
		$thisPost.addClass("is-sketch");
		this.textToCanvas( $thisPost);
	};

	this.textToCanvas = function( $thisPost) {

		thisPostID = $thisPost.attr("data-id");

		$thisPostContent = $thisPost.find('.entry-content');

		// poulet basquaise aux pâtes
		sketch = $thisPostContent.text();

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

		$thisPostContent.wrapInner("<pre class='thisCode brush:pde; gutter: false; '></pre>");
		$thisCode = $thisPostContent.find(".thisCode");

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
		$thisPost.find("canvas#" + thisPostID).hover(function() {
			var thisID = this.id;
			console.log(thisID);
			Processing.getInstanceById(thisID).loop();
		}, function() {
			var thisID = this.id;
			Processing.getInstanceById(thisID).noLoop();
		});
	};


	this.activateTooltips = function() {
		this.find('[data-toggle="tooltip"]').tooltip();
	};

	this.activate_comments = function() {
		this.find(".entry-meta .comments-link a").bind("tap", function(e) {
			e.preventDefault();
			$thisPost = $(this).parents(".post");
			self.display_comments($thisPost);
	    $('html, body').animate({
	        scrollTop: $thisPost.find(".entry-footer").offset().top - $(".banner").height() * 1.5
	    }, 600);
			return false;
		});
	};

	this.display_comments = function($thisPost) {

		$thisPost.find(".entry-footer").addClass("is-loading");
		var pageLink = $thisPost.attr("data-singleurl") + "?comments=show";


		$.get( pageLink, function( data ) {
			$data = $(data);
			var $thisContent = $data.find('.entry-footer').html();
			$thisFooter = $thisPost.find(".entry-footer");

			$thisFooter.removeClass("is-loading").empty().html($thisContent);

			// fermer les commentaires
			$(".close-comments").bind("tap", function() {
				$(this).closest(".comments").fadeOut(400, function() {
					$(this).closest(".entry-footer").empty();
				});
			});

			// ajouter lien vers spam
			$(".send-to-spam").bind("tap", function() {

				$thisComment = $(this).closest(".comment-body").addClass("is-removing");
				thisID = $thisPost.attr("data-id");

				thisCommentID = $(this).attr("data-commentID");

			var data = {
				'action': 'spam_comment',
						'security': ajaxnonce,
						'comment_id': thisCommentID,
						'post_id': thisID
			};

			$.post(ajaxurl, data, function(response) {
					self.display_comments( $thisPost);
			});

			});

			$(".comment-reply-login").bind("tap", function() {
				$("body").addClass("is-overlaid");
				var loginWindow = $(".login").clone(true);
				fillPopOver( loginWindow, $(this), 300, 460 );
			});

			$commentform = $thisFooter.find(".comment-form");

			$commentform.prepend('<div class="comment-status" ></div>');
			var $statusdiv = $thisFooter.find('.comment-status');

		$commentform.submit(function(){

				var formdata = $commentform.serialize();
				$statusdiv.html('<p class="ajax-placeholder">En cours…</p>');
		var formurl = $commentform.attr('action');

		  $.ajax({
			type: 'post',
			url: formurl,
			data: formdata,
			error: function(XMLHttpRequest, textStatus, errorThrown){
			  $statusdiv.html('<p class="ajax-error" >Certains champs sont manquants. Veuillez les ajouter.</p>');
			},
			success: function(data, textStatus){
			  if(data === "success" || textStatus === "success") {
				$statusdiv.html('<p class="ajax-success" >Merci pour votre commentaire !</p>');
			  } else {
				$statusdiv.html('<p class="ajax-error" >Erreur de publication du commentaire...</p>');
			  }
						$commentform.find('textarea[name=comment]').val('');
						self.display_comments( $thisPost);
			}
		  });
				return false;

		});

			});
	};

	this.init();
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
			if( !$("body").hasClass("is-superadmin") || username !== "arnaudjuracek") {
			// logger.disableLogger();
			} else {
			}

			if( username === "louis") {
				$(".content-info").append("<button style='color: #ccc;'>showgrid</button>").on("click", function() {
					$(".thisGrid").toggle();
				});

			}

			$(".post").each(function() { $(this).post_view_routine(); });
			//initPhotoSwipeFromDOMForGalleries('.entry-content .gallery');

			createCustomFavicon();
			$(".main").fixedUI();

			if( $(window).width() > 1024) {
				animateLogo();
			}


			if( $(".category-filters .contenu").length > 0) {
				$(".category-filters").the_filters();
			} else {
				$(".category-filters").remove();
			}


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

			///////////////////////////////////////////// éditer les auteurs d'un projet ////////////////////////////////////////////
			$(".edit-authors").click( function(e) {

				// ouvrir un champ formulaire
				$("body").addClass("is-overlaid");

				var editAuthorsCheckboxField = $(".editProjetAuteurs").clone(true);
				fillPopOver( editAuthorsCheckboxField, $(this), 300, 600 );

				$(".popover .submit-updateAuthors").click( function(e) {

					if( $(this).hasClass( "is--disabled")) { return false; }

					$(this).text( $(this).attr("data-submitted"));
					$(this).addClass( "is--disabled");

					var $authorsList = $(this).closest(".editProjetAuteurs");
					$(".popover").addClass("is-loading");

					sendActionToAnalytics( "Édition des auteurs d'un projet" );

					nomsAuteurs = [];
					$authorsList.find("input:checked").each( function() {
						nomsAuteurs.push( $(this).val());
					});
					listAllAuthors = nomsAuteurs.toString();
					console.log("authors : " + listAllAuthors);
					updateProjectAuthors( listAllAuthors);
				});
			});

			///////////////////////////////////////////////// rafraichir postie /////////////////////////////////////////////////
			$(".refresh-postie").click(function() {

				sendActionToAnalytics("Rafraichissement de Postie");

				$this = $(this);

				if( $this.find(".results").length > 0 ) {
					location.reload(true);
					return false;
				}

				if( $this.hasClass("is-loading")) {
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

							if( projectName.toLowerCase().replace(/ /g, '-') === projectTerm.toLowerCase().replace(/ /g, '-') ) {
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
								if( langIsFrench() ) {
									$this.append("<div class='results'>" + countNewContentForProject + " nouveaux message(s) pour le projet <em>" + projectTerm + "</em>. <a href=''>Rafraichissez la page.</a></div>");
								} else {
									$this.append("<div class='results'>" + countNewContentForProject + " new message(s) for the project <em>" + projectTerm + "</em>. <a href=''>Refresh the page.</a></div>");

								}
							} else {

								if( langIsFrench() ) {
									$this.append("<div class='results'>" + countNewContent + " nouveaux message(s) pour d'autres projets.</a></div>");
								} else {
									$this.append("<div class='results'>" + countNewContent + " new message(s) for other projects.</a></div>");
								}

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
/*
				var decoURL = $("#wp-logout").attr("href");
				window.location.href = decoURL;
*/
			var data = {
				'action': 'logout_user',
						'security': ajaxnonce,
						'userid'	: userid
			};
			$.post(ajaxurl, data, function(response) {
					$("body").addClass("is-overlaid");
					window.top.location.reload(true);
				});

			});

			// click sur inscription
			$(".login-field").bind("tap",function() {
				sendActionToAnalytics("Inscription");
				$("body").addClass("is-overlaid");
				var loginWindow = $(".login").clone(true);
				console.log( "Start popover with content : " + loginWindow);

				fillPopOver( loginWindow, $(this), 300, 460 );
			});

			$('[data-toggle="tooltip"]').tooltip();

	}

  },
  // Home page
  home: {
		init: function() {



		}
  },

  page_template_template_page_accueil: {
	  init: function() {

			$('.colonnes img').removeAttr('style');

			setTimeout(function() {
/*

				tinysort($("#colonnesContainer .colonneswrappers"), {
					attr: 'data-lastpostdate',
					order: 'desc',
				});
*/

			  $("body").addClass("is-loaded");


				var pckry = $('#colonnesContainer .colonnesContainerInside').isotope({
				  layoutMode: 'packery',
				  itemSelector: '.colonneswrappers',
				  percentPosition: true,
				  sortAscending: true,

				  getSortData: {
					edited: '[data-timesincelastpostdate] parseInt',
					created: '[data-timecreated] parseInt',
						titreproj: '[data-name]'
				  },
				  sortBy : ['edited', 'titreproj', 'created']
				});

				if( $(".sort-list").length > 0) {
					$(".sort-list .contenu").sort_items();
				}




			}, 500);


			///////////////////////////////////////////////// ajouter un projet /////////////////////////////////////////////////

			$(".add-project").click(function() {

				// ouvrir un champ formulaire
				$("body").addClass("is-overlaid");

				var newProjectInputField = $(".nouveauProjet").clone(true);
				fillPopOver( newProjectInputField, $(this), 300, 240 );

				$(".popover .nouveauProjet button").click( function(e) {

					if( $(this).hasClass( "is--disabled")) { return false; }

					$(this).text( $(this).attr("data-submitted"));
					$(this).addClass( "is--disabled");

					$popover = $(this).closest(".popover");
					e.preventDefault();

					sendActionToAnalytics("Nouveau projet ");

					var projName = $popover.find("#projectName").val();

					// ajouter en ajax un nouveau terme
					var data = {
						'action': 'add_tax_term',
						'security': ajaxnonce,
						'tax_term': projName,
						'userid'	: userid,
						'add-description' : true,
					};
					$.post(ajaxurl, data, function(response) {
						// recharger la page

						console.log( "Reload page ");
						console.log( "Response = " + response);
						$this = $(this);

						if( JSON.parse(response) !== "success") {
							$popover.find(".ajax-feedback").remove();
							$popover.find("#projectName").after( "<div class='ajax-feedback'>" + JSON.parse(response) + "</div>");
						} else {
							window.top.location.reload(true);
						}
					});
				});
			});


			///////////////////////////////////////////////// ouvrir le champ de recherche /////////////////////////////////////////////////
			$(".open-search").click(function() {

				// ouvrir un champ formulaire
				$("body").addClass("is-overlaid");

				var newProjectInputField = $(".champRecherche").clone(true);
				fillPopOver( newProjectInputField, $(this), 300, 240 );

				$(".popover .champRecherche button").click( function(e) {

					$popover = $(this).closest(".popover");
					sendActionToAnalytics("Champ de recherche");

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

				console.log("clicked save post");
				$("body").find(".fee-publish").click();
				sendActionToAnalytics("Fin édition nouveau post");

				$(document).on('fee-after-save', function() {
					$("body").addClass("is-overlaid");
					window.top.location.reload();
				});

			});


			$(".remove-post").click( function(e) {

				sendActionToAnalytics("Supprimer un post");

				e.preventDefault();

				var $thisPost = $(this).parents(".post");
				thisID = $thisPost.attr("data-id");

			var data = {
				'action': 'remove_post',
						'security': ajaxnonce,
						'post_id': thisID
			};
			$.post(ajaxurl, data, function(response) {
						$("body").addClass("is-overlaid");
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
			createTimeline();

			// si click sur le bouton "edit", alors lancer l'action d'éditer
			$("body").on( "click", ".button-edit-categories", function() {
				$(this).parents(".post").find(".edit-post").trigger("click");
			});

			window.addEventListener('message', function(event) {

			  if(event.origin === 'http://www.lopendoc.org')
			  {
					console.log( "détection de bubble event");
					$(".post").filter('[data-id="' + event.data.message + '"]').find(".button-right .edit-post").trigger("click");
			  }
			  else
			  {}
			}, false);

		}
	},

	single_post: {
		init: function() {
			console.log("init single_post");

			$(".edit-categories").on("click", function() {
				$(".category-list a").remove();
				$(".fee-button-categories").click();
			});

			// si click sur le bouton save et qu'on est dans un iframe, propagé un événement
			$(document).on('fee-after-save', function() {
				window.parent.postMessage({message: singleID}, 'http://www.lopendoc.org/');
			});


			// mettre à jour les categories quand on ferme le pop-up (maintenant pris en charge par FEE grace à la catégorie fee-categories
/*
			$( '.fee-category-modal' ).on( 'hide.bs.modal', function() {
					var _categories = [];

					$('#categorychecklist input[name="post_category[]"]:checked').each( function() {
						_categories.push( '<span class="category-term">' + $(this).parent().text() + '</span>' );
					});

					console.log( "_categories : " + _categories);
					$(".category-list").html( _categories);
			});
*/
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
