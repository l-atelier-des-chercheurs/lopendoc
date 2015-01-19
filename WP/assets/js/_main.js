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



function updateView() {

	$("#rightView").empty();

	var postContent = '';
	$("#leftView").find(".post.publish").each(function() {
		$(this).clone().appendTo( $("#rightView") );
	});


}

function ajaxRefreshPage() {

	$("body").addClass("is-loading");

}

var switchEditionMode = {
	init: function() {




	},

	setSwitch: function() {

		$("body").toggleClass("is-edition");

	},

};



// Use this variable to set up the common and page specific functions. If you
// rename this variable, you will also need to rename the namespace below.
Roots = {

  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages

			console.log("edit post ajax");

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

					console.log("pageLink " + pageLink);

					$.get( pageLink, function( data ) {
						$thisPost.removeClass("is-edited");
						$data = $(data);

						var $thisContent = $data.find('.post .entry-title-and-content').html();

						console.log( "$thisContent");
						console.log(  $thisContent );

						$thisPost.find(".entry-title-and-content").empty().append($thisContent);

		      });

				} else {

					// sinon
					$thisPost.addClass("is-edited");
					$thisPost.find(".entry-header, .entry-content").remove();

					console.log("pageLink = " + pageLink);

					$thisPost.find(".entry-title-and-content").empty().append('<iframe class="edit-frame" src="' + pageLink + '#edit=true" style="border:0px;width:100%;height:100%;"></iframe>');

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

				return false;

			});

			///////////////////////////////////////////////// click sur éditer /////////////////////////////////////

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

							while ( data.search("##") !== -1 ) {
								project = data.substring( data.indexOf("##")+2 );
								projectName = project.substring( 0, project.indexOf("##") );

								data = project.substring( project.indexOf("##")+2 );

								if( projectName === projectTerm ) {
									countNewContent++;
								}

								console.log ( "project gotten = " + project);
							}



							$this.removeClass("is-loading");

							// récupérer le nombre de mails parsés
							$this.find(".results").remove();
							if ( countNewContent > 0 ) {
								$this.append(".results").html( countNewContent + " nouveaux message(s) pour le projet <em>" + projectTerm + "</em>. <a href=''>Rafraichissez la page.</a>");
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
