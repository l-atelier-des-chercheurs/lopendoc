<?php
/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}
add_filter('wp_title', 'roots_wp_title', 10);


if ( ! function_exists( 'is_login_page' ) ) {
  function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-signup.php'));
  }
}
function prevent_login_page() {
    if( is_login_page() && !( defined( 'DOING_AJAX' ) && DOING_AJAX )){
			wp_redirect( home_url() );
      exit();
    }
}
//add_action('init', 'prevent_login_page');


function blockusers_init() {
	if ( (is_admin()) && !current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'admin_init', 'blockusers_init' );



// custom typeface
function google_font(){
	//echo "<link href='http://fonts.googleapis.com/css?family=Fira+Sans:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>","\n";
	echo "<link href='//code.cdn.mozilla.net/fonts/fira.css' rel='stylesheet' type='text/css'>","\n";
}
add_action( 'wp_enqueue_scripts', 'google_font');


function doorbell_io() {
	ob_start();
	?>
<script type="text/javascript">
    window.doorbellOptions = {
        appKey: '2nmWabMvrp56nplrTF4KZhKSWLTQ2WEmiaMCj8g5la7bTLVKqNkx3I4km9gG9R63'
    };
    (function(d, t) {
        var g = d.createElement(t);g.id = 'doorbellScript';g.type = 'text/javascript';g.async = true;g.src = 'https://embed.doorbell.io/button/2120?t='+(new Date().getTime());(d.getElementsByTagName('head')[0]||d.getElementsByTagName('body')[0]).appendChild(g);
    }(document, 'script'));
</script>

	<?php
	$out = ob_get_clean();
	echo $out;
}
add_action( 'wp_footer', 'doorbell_io');










// Register projets tax
function projet_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Projets', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Projet', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Projets', 'text_domain' ),
		'all_items'                  => __( 'Tous les projets', 'text_domain' ),
		'parent_item'                => __( 'Projet parent', 'text_domain' ),
		'parent_item_colon'          => __( 'Projet parent:', 'text_domain' ),
		'new_item_name'              => __( 'Nouveau projet', 'text_domain' ),
		'add_new_item'               => __( 'Ajouter un projet', 'text_domain' ),
		'edit_item'                  => __( 'Éditer un projet', 'text_domain' ),
		'update_item'                => __( 'Mettre à jour un projet', 'text_domain' ),
		'separate_items_with_commas' => __( 'Séparer les projets avec une virgule', 'text_domain' ),
		'search_items'               => __( 'Chercher les projets', 'text_domain' ),
		'add_or_remove_items'        => __( 'Ajouter ou enlever un objet', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choisir parmi les plus utilisés', 'text_domain' ),
		'not_found'                  => __( 'Pas trouvé', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'projets', array( 'post' ), $args );
}
// Hook into the 'init' action
add_action( 'init', 'projet_taxonomy', 0 );



// register author taxonomy
function auteur_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Auteurs', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Auteur', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Auteurs', 'text_domain' ),
		'all_items'                  => __( 'Tous les auteurs', 'text_domain' ),
		'parent_item'                => __( 'Auteur parent', 'text_domain' ),
		'parent_item_colon'          => __( 'Auteur parent:', 'text_domain' ),
		'new_item_name'              => __( 'Nouvel auteur', 'text_domain' ),
		'add_new_item'               => __( 'Ajouter un auteur', 'text_domain' ),
		'edit_item'                  => __( 'Éditer un auteur', 'text_domain' ),
		'update_item'                => __( 'Mettre à jour un auteur', 'text_domain' ),
		'separate_items_with_commas' => __( 'Séparer les auteurs avec une virgule', 'text_domain' ),
		'search_items'               => __( 'Chercher les auteurs', 'text_domain' ),
		'add_or_remove_items'        => __( 'Ajouter ou enlever un auteur', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choisir parmi les plus utilisés', 'text_domain' ),
		'not_found'                  => __( 'Pas trouvé', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'auteur', array( 'post' ), $args );
}
// Hook into the 'init' action
add_action( 'init', 'auteur_taxonomy', 0 );



// cacher la barre admin
function habfna_hide_admin_bar_settings()
{
?>
	<style type="text/css">
		.show-admin-bar {
			display: none;
		}
	</style>
<?php
}
function habfna_disable_admin_bar()
{
	if(!current_user_can('administrator'))
	{
		add_filter( 'show_admin_bar', '__return_false' );
		add_action( 'admin_print_scripts-profile.php', 'habfna_hide_admin_bar_settings' );
	}
}
add_action('init', 'habfna_disable_admin_bar', 9);

// lien vers l'image à l'ajout
update_option('image_default_link_type','file');

// titre custom
function custom_bloginfo() {
	$blogname = get_bloginfo('name');
	$blogeditedname = substr( $blogname, stripos($blogname, "@"));
	return $blogeditedname;
}


// disable fee front-end editor on pages
function front_editor_disable() {
  global $wp_query;
  if ( is_page() ) {
    return true;
   }
}
add_filter('front_end_editor_disable', 'front_editor_disable');
function my_custom_init() {
	remove_post_type_support( 'page', 'front-end-editor' );
}
add_action( 'init', 'my_custom_init' );

// Add post thumbnails
add_theme_support('post-thumbnails');
set_post_thumbnail_size( 1200, 800, true );

add_action( 'wp_enqueue_scripts', 'mytheme_scripts' );
function mytheme_scripts() {
	wp_enqueue_style( 'dashicons' );
}

// suppression des emojis
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
update_option('use_smilies', false);


// project color
$new_general_setting = new new_general_setting();

class new_general_setting {
    function new_general_setting( ) {
        add_action( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'primary_color', 'esc_attr' );
        add_settings_field('primary_color', '<label for="primary_color">'.__('Couleur primaire' , 'primary_color' ).'</label>' , array(&$this, 'fields_html') , 'general' );

        register_setting( 'general', 'secondary_color', 'esc_attr' );
        add_settings_field('secondary_color', '<label for="secondary_color">'.__('Couleur secondaire' , 'secondary_color' ).'</label>' , array(&$this, 'fields_html2') , 'general' );

        register_setting( 'general', 'wp_custom_nonce', 'esc_attr' );
        add_settings_field('wp_custom_nonce', '<label for="wp_custom_nonce">'.__('Chaîne de sécurité pour vérifier l\'origine des requêtes ajax' , 'wp_custom_nonce' ).'</label>' , array(&$this, 'fields_html4') , 'general' );

        register_setting( 'general', 'mail_addressTC', 'esc_attr' );
        add_settings_field('mail_addressTC', '<label for="mail_addressTC">'.__('Adresse Mail pour contribuer (sous la forme adresse+leprojet@gmail.com)' , 'mail_addressTC' ).'</label>' , array(&$this, 'fields_html3') , 'general' );
    }
    function fields_html() {
        $value1 = get_option( 'primary_color', '' );
        echo '<input type="text" id="primary_color" name="primary_color" value="' . $value1 . '" />';
    }
    function fields_html2() {
        $value2 = get_option( 'secondary_color', '' );
        echo '<input type="text" id="secondary_color" name="secondary_color" value="' . $value2 . '" />';
		}
    function fields_html3() {
        $value3 = get_option( 'mail_addressTC', '' );
        echo '<input type="text" id="mail_addressTC" name="mail_addressTC" value="' . $value3 . '" />';
		}
    function fields_html4() {
        $value4 = get_option( 'wp_custom_nonce', '' );
        echo '<input type="text" id="wp_custom_nonce" name="wp_custom_nonce" value="' . $value4 . '" />';
		}
}

function private_or_publish($classes) {
	if( is_single() ) {
      $classes[] = get_post_status();
	}
	return $classes;
}
add_filter('body_class', 'private_or_publish');


// login logo wp
function login_lopendoc() { ?>
    <style type="text/css">
        body.login div#login h1 a {
						background: transparent;
						background: url("<?php echo get_template_directory_uri(); ?>/assets/img/logo_opendoc_SVG-01.svg");
						background-size: cover;

						width: 283px;
						background-repeat: no-repeat;

						margin-bottom: 44px;

						height: 55px;
						font-size: 24px;
						color: #000;
						text-transform: lowercase;
	        }
	      body {
<?php
  $primaireColor =  get_option( "primary_color" );
  if( empty($primaireColor) ) $primaireColor = "#EF444E";
	?>

		     background-color: <?php $primaireColor; ?>;
		    }


    </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_lopendoc' );

// admin colorbar wp
function adminbarcolor_opendoc() { ?>

<?php
  $primaireColor =  get_option( "primary_color" );
  if( empty($primaireColor) ) $primaireColor = "#EF444E";
	?>

    <style type="text/css">

		    #wpadminbar ul#wp-admin-bar-root-default>#wp-admin-bar-wp-logo {
			  	padding-left: 5px;
  				background-color: <?php echo $primaireColor; ?> !important;
			  }

        #wpadminbar #wp-admin-bar-wp-logo > .ab-item {
						background-color: #e5e5e5 !important;
						background: url("<?php echo get_template_directory_uri(); ?>/assets/img/logo_opendoc_SVG-01.svg");

						background-position: 50% 50%;
						background-repeat: no-repeat;
						padding-left: 20px !important;

						width: 140px;
						pointer-events: none;

	        }
        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon {
	       	display: none;
	      }
	      #wpadminbar ul#wp-admin-bar-root-default>#wp-admin-bar-wp-logo .ab-sub-wrapper {
		    	display: none;
		    }

		    input#mail_addressTC {
			  	width: 320px;
			  }
    </style>
<?php
	}
add_action( 'admin_enqueue_scripts', 'adminbarcolor_opendoc' );

// default setting : lien vers image média quand ajout d'image
function my_gallery_default_type_set_link( $settings ) {
    $settings['galleryDefaults']['link'] = 'file';
    return $settings;
}
add_filter( 'media_view_settings', 'my_gallery_default_type_set_link');








function user_can_edit() {

	// si super admin ou admin
	if( current_user_can('administrator')) {
		return true;
	}

	// si logged in
	if( is_user_logged_in() ) {
		// si auteur
		if( current_user_can('author')) {
			// si editor
			if(can_user_edit_project()) {
				return true;
			}
		}
	}
	return false;
}

function can_user_edit_project() {

	if( isset($GLOBALS["editor"]) && $GLOBALS["editor"] == true) {
		return true;
	}

	$term = '';

  if( get_query_var( 'term' ) !== '') {
		$term = get_query_var( 'term' );
	}
  else if( count( wp_get_object_terms( get_the_ID(), 'projets' )) > 0 ) {
  	$terms = get_the_terms( get_the_ID(), 'projets');
		$term1 = array_pop($terms);
		$term = $term1->slug;
  }

	if( $term != '') {
		$currentuserid = wp_get_current_user()->ID;
		$hasProjects = get_user_meta( $currentuserid, '_opendoc_user_projets', true );
		$userProjects = explode('|', $hasProjects);

		if( in_array( $term, $userProjects)) {
			$GLOBALS["editor"] = true;
			return true;
		}
	}
	return false;
}

function can_user_edit_this_project($projet) {

//	error_log( "-- is authorized ?");
	if( current_user_can('administrator'))
		return true;


	if( !isset($GLOBALS["listeProjet"])) {
		$currentuserid = wp_get_current_user()->ID;
		$hasProjects = get_user_meta( $currentuserid, '_opendoc_user_projets', true );
		$userProjects = explode('|', $hasProjects);
		$GLOBALS["listeProjet"] = $userProjects;
	}

//	error_log( "-- listeprojets " . $GLOBALS["listeProjet"]);
//	error_log( "-- projet in array listeprojets ? " . in_array( $projet, $GLOBALS["listeProjet"]));

	if( $projet != '') {
		if( in_array( $projet, $GLOBALS["listeProjet"])) {
			return true;
		}
	}
	return false;

}


// admin ou pas ajouter class au body
function superadmin_ornot($classes) {
	if( current_user_can('manage_options') ) {
      $classes[] = 'is-admin';
	}
	if( current_user_can('superadmin') ) {
      $classes[] = 'is-superadmin';
	}
	return $classes;
}
add_filter('body_class', 'superadmin_ornot');

// admin ou pas ajouter class au body
function current_user_loggedin($classes) {
	if( is_user_logged_in() && user_can_edit() ) {
      $classes[] = 'is-edition';
	}
	return $classes;
}
add_filter('body_class', 'current_user_loggedin');






add_action( 'cmb2_init', 'opendoc_contributeur_projets' );
/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function opendoc_contributeur_projets() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_opendoc_user_';

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => __( 'User Profile Metabox', 'opendoc' ),
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
    'show_on_cb' => 'cmb2_only_show_to_admin', // function should return a bool value
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
	) );
	$cmb_user->add_field( array(
		'name'     => __( 'Project\'s list where a user is author. Write slugs here.', 'opendoc' ),
		'desc'     => __( 'séparés par une virgule', 'opendoc' ),
		'id'       => $prefix . 'projets',
		'type'     => 'title',
    'type' => 'textarea_small'
	) );
}

function cmb2_only_show_to_admin( $field ) {
    // Returns true if current user's ID is 1, else false
    return current_user_can('administrator');
}

function set_default_admin_color($user_id) {
	$args = array(
		'ID' => $user_id,
		'admin_color' => 'light'
	);
	wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');

if ( !current_user_can('administrator') )
  remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// envoyer un mail à tous les contributeurs d'un projet
function sendMailToAllProjectContributors( $projet, $sujet = '', $content = '') {
	$users = get_users('role=author');
	$contributors = array();
  foreach ($users as $user) {
		$userID = $user->ID;
		$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
		$userProjects = explode('|', $hasProject);
		$ifchecked = '';
		if( in_array( $projet, $userProjects)) {
			array_push( $contributors, $user->user_email);
		}
	}

	$sujet = str_replace("'", " ", $sujet);

  $mailToContribute =  get_option( "mail_addressTC" );
	$mailToContribute = str_replace("leprojet", $projet, $mailToContribute);

	sendMailTo( $mailToContribute, $contributors, $sujet, $content);

}

function sendMailTo( $frommail, $contributors, $sujet, $content) {
	$content =
		"<strong>" . __("This is a mail from l'Opendoc", 'opendoc') . " | " . get_bloginfo('name') . "</strong>" .
		"<br/>" .
		"<br/>" .
		$content .
		"<br/>" .
		"<br/>" .
		"<small>" . __("The team at l'Opendoc.", 'opendoc') . "</small>"
		;

	$sender = get_bloginfo('name') . " <$frommail>";

	// ça bug et je sais pas pourquoi
/* 	$headers[] = 'From: l\'Opendoc'; */
/* 	$headers[] = 'Content-Type: text/html'; */
/*
	foreach( $contributors as $contributor):
		$headers[] = 'Bcc: ' . $contributor;
	endforeach;
*/

/* 	error_log('plop'); */
/* 	error_log( 'header : ' . implode( "|", $headers)); */
	wp_mail( $contributors, $sujet, $content);
}
add_filter( 'wp_mail_content_type', function( $content_type ) {
	return 'text/html';
});

// rediriger vers la page taxonomy si pas un éditeur du projet, ou pas administrateur !
// ATTENTION : pour les éditeurs et admin, ne surtout pas le faire car sinon ça casse le embed iframe (et ça c'est nul)
function redirect_single_to_tax() {

	global $post;
  // Only modify custom taxonomy template redirect
	if ( is_single() ) {

		$terms = get_the_terms( get_the_ID(), 'projets');
		if( $terms && ! is_wp_error( $terms ) ) {
			$term = array_pop($terms);
			$projet = $term->slug;

			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projet))
				||
				current_user_can('administrator')
				||
				(isset( $_GET['comments']) && $_GET['comments'] === 'show')
			) {
				return;
	    }

			wp_safe_redirect( get_term_link( $term));
			exit;
		}

  }
}
add_action( 'template_redirect', 'redirect_single_to_tax' );

// logger les modifications d'un projet dans un champ _opendoc_edits
function logActionsToProject( $projet, $string) {
	$tax = 'projets';
	$args = array(
	    'tax_query'         => array(
	        array(
	            'taxonomy'  => $tax,
	            'field'     => 'slug',
	            'terms'     => $projet,
	        ),
	    ),
	    'post_type'      => 'post', // set the post type to page
	    'posts_per_page' => -1,
			'order' => 'DESC',
			'tags'	 => 'featured',
		);
	$description = new WP_Query($args);
	if ( $description->have_posts() ) {
		// The Loop
		while ($description->have_posts()) {
			$description->the_post();
			$featured =  has_tag('featured');
			if( $featured == true ) {
				$descriptionPostID = get_the_ID();
				break;
			}

		}
	}

	// préfixer la string par l'heure
	$editedTime = "<span class='timeEdited'>" . date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) . "</span>";
	$string = $editedTime . $string;

	$editLog = get_post_meta( $descriptionPostID, '_opendoc_editlog', true);
	if(is_array($editLog))
	    array_push( $editLog, $string);
	else
	    $editLog = array($string);
	update_post_meta( $descriptionPostID, '_opendoc_editlog', $editLog);
}









// copie de wp_check_post_lock
if ( ! function_exists( 'is_post_lock_admin' ) ) {
	function is_post_lock_admin( $post_id ) {
		if ( !$post = get_post( $post_id ) )
			return false;
		if ( !$lock = get_post_meta( $post->ID, '_edit_lock', true ) )
			return false;
		$lock = explode( ':', $lock );
		$time = $lock[0];
		$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $post->ID, '_edit_last', true );
		/** This filter is documented in wp-admin/includes/ajax-actions.php */
		$time_window = apply_filters( 'wp_check_post_lock_window', 150 );
		if ( $time && $time > time() - $time_window && $user != get_current_user_id() )
			return $user;
		return false;
	}
}


// from https://gist.github.com/georgiecel/9445357
class opendoc_walker extends Walker_Comment {
	var $tree_type = 'comment';
	var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );

	// constructor – wrapper for the comments list
	function __construct() { ?>

		<section class="comments-list">

	<?php }

	// start_lvl – wrapper for child comments list
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 2; ?>

		<section class="child-comments comments-list">

	<?php }

	// end_lvl – closing wrapper for child comments list
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 2; ?>

		</section>

	<?php }

	// start_el – HTML for comment template
	function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;
		$parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' );

		if ( 'article' == $args['style'] ) {
			$tag = 'article';
			$add_below = 'comment';
		} else {
			$tag = 'article';
			$add_below = 'comment';
		} ?>

		<article <?php comment_class(empty( $args['has_children'] ) ? '' :'parent') ?> id="comment-<?php comment_ID() ?>" itemprop="comment" itemscope itemtype="http://schema.org/Comment">

		<div class="comment-body" >

			<div class="comment-meta post-meta" role="complementary">
				<div class="comment-author">
					<?php echo get_comment_author(); ?>
				</div>
				<div class="comment-metadata">
					<time class="comment-meta-item" datetime="<?php comment_date('Y-m-d') ?>T<?php comment_time('H:iP') ?>" itemprop="datePublished"><?php comment_date('jS F Y') ?>, <?php comment_time() ?>
					</time>



					<?php
/*
						if( $args['can_spam'] === "true") {
					?>
					<button class='send-to-spam' data-commentID='<?php comment_ID(); ?>'>
						<?php _e( 'Spam', 'opendoc'); ?>
					</button>
					<?php }

*/
					?>

					<?php edit_comment_link('<p class="comment-meta-item edit-link">Edit this comment</p>','',''); ?>

					<?php if ($comment->comment_approved == '0') : ?>
					<p class="comment-meta-item">Your comment is awaiting moderation.</p>
					<?php endif; ?>
				</div>
			</div>
			<div class="comment-content post-content" itemprop="text">
				<?php comment_text() ?>
			</div>

			<div class="reply">
				<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</div>
		</div>

	<?php }

	// end_el – closing HTML for comment template
	function end_el(&$output, $comment, $depth = 0, $args = array() ) { ?>

		</article>

	<?php }

	// destructor – closing wrapper for the comments list
	function __destruct() { ?>

		</section>
	<?php }
}


// autoriser les json dans l'interface
function allow_new_mime_types($mimes) {
  $mimes['json']  =     'application/javascript';
  $mimes['stl']   =     'application/vnd.ms-pki.stl';
  $mimes['eps']   =      'application/eps';
  return $mimes;
}
add_filter('upload_mimes', 'allow_new_mime_types');










// notifier les utilisateurs en cas de nouveau commentaire, du plugin
// https://github.com/valeriosouza/post-notification/blob/master/notify-users-e-mail.php
/**
 * Nofity users when publish a comment.
 *
 * @param int    $id Comment ID.
 * @param string $new_status New status of comment.
 * @param string $old_status Optional old status of comment.
 *
 * @return void
 */
function send_notification_comment( $id) {
	$comment         = get_comment( $id );
	$post			 = get_post($comment->comment_post_ID);

	$userWhoCommented = get_comment_author($comment);

	$projet = array_pop(wp_get_object_terms( $post->ID, 'projets'));
	$projetslug = $projet->slug;
	$projectLink = get_term_link( $projetslug, 'projets');

	sendMailToAllProjectContributors( $projetslug,
		html_entity_decode( get_bloginfo('name')),
		"<strong>" . $userWhoCommented . "</strong>" . " " .
			__("has added a comment in", 'opendoc') . " " .
			"<strong>" . $projetslug . "</strong>" . " " .
			__("to the post titled", 'opendoc') . " " .
			"<strong>" . $post->post_title . "</strong>" . " " .
			"<br/>" .
			__("Approve and reply by visiting", 'opendoc') . " " .
			esc_url( $projectLink )
		);

//	$emails          = get_comment_author_email($comment).','.get_the_author_meta('user_email',$post->post_author );
/*
	$subject_comment = $this->apply_comment_placeholders( $settings['subject_comment'], $comment );
	$body_comment    = $this->apply_comment_placeholders( $settings['body_comment'], $comment );
	$headers 		 = array(
		'Content-Type: text/html; charset=UTF-8',
		'Bcc: ' . $emails
	);
	// Send the emails.
	if ( apply_filters( 'notify_users_email_use_wp_mail', true ) ) {
		wp_mail( '', $subject_comment, $body_comment, $headers );
	} else {
		do_action( 'notify_users_email_custom_mail_engine', $emails, $subject_comment, $body_comment );
	}
*/
}
add_action( 'wp_insert_comment', 'pre_send_notification_new_comment', 10, 2 );

function pre_send_notification_new_comment( $id, $comment ) {
	send_notification_comment( $id );
}


add_filter( 'run_wptexturize', '__return_false' );
