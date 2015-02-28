<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}
add_filter('excerpt_more', 'roots_excerpt_more');

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

/**
 * Check to see if the current page is the login/register page
 * Use this in conjunction with is_admin() to separate the front-end from the back-end of your theme
 * @return bool
 */
/*
if ( ! function_exists( 'is_login_page' ) ) {
  function is_login_page() {
    return in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) );
  }
}
*/

function blockusers_init() {
	if ( (is_admin()) && !current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'blockusers_init' );

// image size
update_option('medium_size_w', 800);
update_option('medium_size_h', 600);
update_option('thumbnail_size_w', 600);
update_option('thumbnail_size_h', 400);


// custom typeface
function google_font(){
	echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>","\n";
	echo "<link href='http://fonts.googleapis.com/css?family=Share:400,400italic,700,700italic' rel='stylesheet' type='text/css'>","\n";
}
//add_action( 'wp_enqueue_scripts', 'google_font');




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
		'hierarchical'               => false,
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

// enlever le Privé
function the_title_trim($title) {
	// Might aswell make use of this function to escape attributes
	$title = attribute_escape($title);
	// What to find in the title
	$findthese = array(
		'#Protégé:#', // # is just the delimeter
		'#Privé : #'
	);
	// What to replace it with
	$replacewith = array(
		'',
		'' // What to replace private with
	);
	// Items replace by array key
	$title = preg_replace($findthese, $replacewith, $title);
	return $title;
}
add_filter('the_title', 'the_title_trim');
add_filter('private_title_format', 'blank');
function blank($title) {
       return '%s';
}

// lien vers l'image à l'ajout
update_option('image_default_link_type','file');

// titre custom
function custom_bloginfo() {
	$blogname = get_bloginfo('name');
	$blogeditedname = substr( $blogname, stripos($blogname, "@"));
	return $blogeditedname;
}


// disable on pages
function front_editor_disable() {
  global $wp_query;

  if ( is_page() ) {
    return true;
   }
}
add_filter('front_end_editor_disable', 'front_editor_disable');


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
    }
    function fields_html() {
        $value1 = get_option( 'primary_color', '' );
        echo '<input type="text" id="primary_color" name="primary_color" value="' . $value1 . '" />';
    }
    function fields_html2() {
        $value2 = get_option( 'secondary_color', '' );
        echo '<input type="text" id="secondary_color" name="secondary_color" value="' . $value2 . '" />';
		}
}

function private_or_publish($classes) {
	if( is_single() ) {
      $classes[] = get_post_status();
	}
	return $classes;
}
add_filter('body_class', 'private_or_publish');

// admin ou pas ajouter class au body
function superadmin_ornot($classes) {
	if( current_user_can( 'manage_options' ) ) {
      $classes[] = 'superadmin';
	}
	return $classes;
}
add_filter('body_class', 'superadmin_ornot');



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
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_lopendoc' );

