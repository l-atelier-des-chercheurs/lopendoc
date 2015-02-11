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
	sketch = $this.text().replace(/«/g, "\"").replace(/»/g, "\"").replace("void setup() {", "void setup() { noLoop();").replace("void setup(){", "void setup(){ noLoop();").replace("void setup () {", "void setup(){ noLoop();").replace("void setup (){", "void setup(){ noLoop();");

	//.replace(/<br>/g, '').replace(/<p>/g, '').replace(/<\/p>/g, '')

	console.log( sketch );

	//sketch = "void setup() { size(200, 200); } void draw() { background(155); }";

	var processingSketch = $("<script type='application/processing'>" + sketch + "</script><canvas id=" + thisPostID + "></canvas>");


	//var newCanvas = document.createElement('canvas');

	//$this.prepend("<script src='https://cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.8/processing.min.js'></script>");

	$this.wrapInner("<pre class='thisCode brush:pde; gutter: false; '></pre>");

	$thisCode = $this.find(".thisCode");
	$thisCode.html( $thisCode.text().replace(/«/g, "\"").replace(/»/g, "\"") );

	SyntaxHighlighter.all();

/*
<script src="sh/shCore.js"></script>
<link rel=stylesheet href="sh/shCore.css">
<script src="sh/shBrushProcessing.js"></script>
<link rel=stylesheet href="sh/shProcessing2Theme.css">
<link rel=stylesheet href="sh/customStyleTricodeurInitiation.css">
<script>SyntaxHighlighter.all();</script>
*/

	$thisCode.before( processingSketch );


  var scripts = document.getElementsByTagName("script");
  var canvasArray = Array.prototype.slice.call(document.getElementsByTagName("canvas"));
  var canvas;
  for (var i = 0, j = 0; i < scripts.length; i++) {
    if (scripts[i].type === "application/processing") {
      var src = scripts[i].getAttribute("target");
      if (src && src.indexOf("#") > -1) {
        canvas = document.getElementById(src.substr(src.indexOf("#") + 1));
        if (canvas) {
          new Processing(canvas, scripts[i].text);
          for (var k = 0; k< canvasArray.length; k++)
          {
            if (canvasArray[k] === canvas) {
              // remove the canvas from the array so we dont override it in the else
              canvasArray.splice(k,1);
            }
          }
        }
      } else {
        if (canvasArray.length >= j) {
          new Processing(canvasArray[j], scripts[i].text);
        }
        j++;
      }
    }
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

function ajaxRefreshPage() {

	$("body").addClass("is-loading");

}

postViewRoutine = {

	init: function() {

		// fonctions propres à chaque post (a appliquer si infinite scroll), à ne pas appliquer si iframe

		if( !$("body").hasClass("iframe") ) {

			$(".entry-content a>img").each(function() {
				$this = $(this);
				$this.closest("a").attr("href", $this.attr("src") ).magnificPopup({type:'image'});
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

					$(".post .entry-content:not(.is-sketch):contains(void setup)").each( function() {

						$this = $(this);
						$this.addClass("is-sketch");

						// récupérer le sketch, le transformer en canvas

						textToCanvas( $this );

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



// Use this variable to set up the common and page specific functions. If you
// rename this variable, you will also need to rename the namespace below.
var Roots = {

  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages

			console.log("edit post ajax");

			postViewRoutine.init();




			///////////////////////////////////////////////// ajouter un post /////////////////////////////////////////////////

			$(".add-post").click(function() {
				$("#wp-admin-bar-new-post .ab-item").trigger("click");
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

								if( projectName === projectTerm ) {
									countNewContentForProject++;
								} else {
									countNewContent++;
								}

								//console.log ( "project gotten = " + project);
							}



							$this.removeClass("is-loading");

							// récupérer le nombre de mails parsés
							if ( countNewContent > 0 ) {

								$this.empty();

								if( countNewContentForProject > 0 ) {
									$this.append("<div class='results'>" + countNewContentForProject + " nouveaux message(s) pour le projet <em>" + projectTerm + "</em>. <a href=''>Rafraichissez la page.</a></div>");
								} else {

									$this.append("<div class='results'>" + countNewContent + " nouveaux message(s) pour le projet <em>" + projectTerm + "</em>. <a href=''>Rafraichissez la page.</a></div>");

								}

							}

	          }
	      });

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

		}
	}

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
