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
add_action( 'admin_init', 'blockusers_init' );

// image size
update_option('large_size_w', 1600);
update_option('large_size_h', '');
update_option('medium_size_w', 800);
update_option('medium_size_h', '');
update_option('thumbnail_size_w', 400);
update_option('thumbnail_size_h', '');


// custom typeface
function google_font(){
	echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>","\n";
	echo "<link href='http://fonts.googleapis.com/css?family=Share:400,400italic,700,700italic' rel='stylesheet' type='text/css'>","\n";
}
//add_action( 'wp_enqueue_scripts', 'google_font');



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
add_action( 'wp_enqueue_scripts', 'doorbell_io');


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
	$title = esc_attr($title);
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

// admin ou pas ajouter class au body
function current_user_loggedin($classes) {
	if( is_user_logged_in() ) {
      $classes[] = 'is-edition';
	}
	return $classes;
}
add_filter('body_class', 'current_user_loggedin');

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

/******* ajax stuff *******/
add_action ( 'wp_head', 'add_frontend_ajax_javascript_file' );
function add_frontend_ajax_javascript_file(){ ?>
  <script type="text/javascript">
    var ajaxurl = <?php echo json_encode( admin_url( "admin-ajax.php" ) ); ?>;
    var ajaxnonce = <?php echo json_encode( wp_create_nonce( "itr_ajax_nonce" ) ); ?>;
    var username = "not-logged-in";
    <?php global $current_user; get_currentuserinfo(); ?>
		<?php if ( is_user_logged_in() ) {
			//echo 'Username: ' . $current_user->user_login . "\n"; echo 'User display name: ' . $current_user->display_name . "\n";
			?>
				username = "<?php echo $current_user->user_login; ?>";
			<?php
			}
			else {
				//wp_loginout();
				}
			?>
/*
    var myarray = <?php echo json_encode( array(
         'foo' => 'bar',
         'available' => TRUE,
         'ship' => array( 1, 2, 3, ),
       ) ); ?>
*/
  </script><?php
}



add_action( 'wp_ajax_get_post_information', 'ajax_get_post_information' );
function ajax_get_post_information()
{
    if(!empty($_POST['post_id']))
    {
        $post = get_post( $_POST['post_id'] );
        echo json_encode( $post );
    }

    die();

/*
    var data = {
        'action': 'get_post_information',
				'post_id': 120
    };

    $.post(ajaxurl, data, function(response) {
        alert('Server response from the AJAX URL ' + response);
    });
*/
}


// ajouter taxonomy
add_action( 'wp_ajax_add_taxonomy_to_post', 'ajax_add_taxonomy_to_post' );
function ajax_add_taxonomy_to_post()
{
    if(!empty($_POST['post_id']))
    {
				wp_set_object_terms( $_POST['post_id'], $_POST['term'], 'projets');
				echo "check postid = ".$_POST['post_id'] . " term = " . $_POST['term'];
    }

    die();
}

// changer la visibilité du post
add_action( 'wp_ajax_change_post_visibility', 'ajax_change_post_visibility' );
function ajax_change_post_visibility()
{

    if(!empty($_POST['post_id']))
    {
		    $post = array();
		    $post['ID'] = $_POST['post_id'];
        $post['post_status'] = $_POST['post_status'];
				wp_update_post($post);
        $post = get_post( $_POST['post_id'] );
        echo json_encode( get_post_status($_POST['post_id']) );
    }

    die();
}

// supprimer un post
add_action( 'wp_ajax_remove_post', 'ajax_remove_post' );
function ajax_remove_post()
{

    if(!empty($_POST['post_id']))
    {
				wp_trash_post( $_POST['post_id'] );
        echo json_encode( get_post_status($_POST['post_id']) );
    }

    die();
}

// ajouter une taxonomie
add_action( 'wp_ajax_add_tax_term', 'ajax_add_tax_term' );
function ajax_add_tax_term()
{

    if(!empty($_POST['tax_term']))
    {
			wp_insert_term(
        $_POST['tax_term'],
        'projets'
			);
    }

    die();
}
