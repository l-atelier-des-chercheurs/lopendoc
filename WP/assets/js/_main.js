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


// Use this variable to set up the common and page specific functions. If you
// rename this variable, you will also need to rename the namespace below.
var Roots = {

  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages

			$(".post .button-right").each(function() {


			});

			$(".post .button-right .edit-post").click( function() {
				console.log("edit-post click");
				var $thisPost = $(this).parents(".post");
				var pageLink = $thisPost.find(".entry-title a").attr("href");
				$thisPost.find(".entry-header, .entry-content").remove();

				$thisPost.addClass("is-edited");

				console.log("pageLink = " + pageLink);

				$thisPost.append('<iframe class="edit-frame" src="' + pageLink + '#edit=true" style="border:0px;width:100%;height:100%;"></iframe>');
			});

			$(".post .button-right .publish-post").each(function() {
				$(this).click( function() {
					$(this).parent(".post").toggleClass("publish");
					updateView();
				});
			});

			$(".addPost").click(function() {
				$("#wp-admin-bar-new-post .ab-item").trigger("click");
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
