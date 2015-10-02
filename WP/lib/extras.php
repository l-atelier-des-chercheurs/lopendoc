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

/**
 * Check to see if the current page is the login/register page
 * Use this in conjunction with is_admin() to separate the front-end from the back-end of your theme
 * @return bool
 */

if ( ! function_exists( 'is_login_page' ) ) {
  function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-signup.php'));
  }
}
function prevent_login_page() {
    if( is_login_page()){
			wp_redirect( home_url() );
      exit();
    }

}
add_action('init', 'prevent_login_page');


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
	echo "<link href='http://fonts.googleapis.com/css?family=Fira+Sans:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>","\n";
}
add_action( 'wp_enqueue_scripts', 'google_font');


// bloquer l'accès aux médias des autres
add_filter( 'posts_where', 'hide_attachments_wpquery_where' );
function hide_attachments_wpquery_where( $where ){
	global $current_user;
	if( !current_user_can( 'manage_options' ) ) {
		if( is_user_logged_in() ){
			if( isset( $_POST['action'] ) ){
				// library query
				if( $_POST['action'] == 'query-attachments' ){
					$where .= ' AND post_author='.$current_user->data->ID;
				}
			}
		}
	}

	return $where;
}


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

function my_custom_init() {
	remove_post_type_support( 'page', 'front-end-editor' );
}
add_action( 'init', 'my_custom_init' );


// Add post thumbnails
// http://codex.wordpress.org/Post_Thumbnails
// http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
// http://codex.wordpress.org/Function_Reference/add_image_size
add_theme_support('post-thumbnails');
set_post_thumbnail_size( 1200, 800, true );




add_action( 'wp_enqueue_scripts', 'mytheme_scripts' );
/**
 * Enqueue Dashicons style for frontend use
 */
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

/******* ajax stuff *******/
add_action ( 'wp_head', 'add_frontend_ajax_javascript_file' );
function add_frontend_ajax_javascript_file(){ ?>
  <script type="text/javascript">
    var ajaxurl = <?php echo json_encode( admin_url( "admin-ajax.php" ) ); ?>;
    var ajaxnonce = <?php echo json_encode( wp_create_nonce( get_option( "wp_custom_nonce" ) ) ); ?>;
    var username = "not-logged-in";

	  <?php
		  $primaireColor =  get_option( "primary_color" );
			if( empty($primaireColor) ) $primaireColor = "#EF444E";
			echo "var couleurPrimaire = '$primaireColor';";

		  $secondaireColor =  get_option( "secondary_color" );
			if( empty($secondaireColor) ) $secondaireColor = "#FCB248";
			echo "var couleurSecondaire = '$secondaireColor';";
		?>


    var nomProjet = "<?php
	    if( get_query_var( 'term' ) !== '')
	    	echo get_query_var('term');
	    else if( count( wp_get_object_terms( get_the_ID(), 'projets' )) > 0 ) {
	    	$terms = get_the_terms( get_the_ID(), 'projets');
				$term = array_pop($terms);
				echo $term->slug;
	    }
	    ?>";
    var singleID = "<?php echo get_the_ID(); ?>";

    <?php global $current_user; get_currentuserinfo(); ?>
		<?php if ( is_user_logged_in() ) {
			//echo 'Username: ' . $current_user->user_login . "\n"; echo 'User display name: ' . $current_user->display_name . "\n";
			if( user_can_edit()) { ?>
		    var canuseredit = true;
			<?php } else { ?>
		    var canuseredit = false;
			<?php } ?>
				var username = "<?php echo $current_user->user_login; ?>";
				userid = "<?php echo $current_user->ID; ?>";
			<?php
			}
			else {
				//wp_loginout();
			}


/*
    var myarray = <?php echo json_encode( array(
         'foo' => 'bar',
         'available' => TRUE,
         'ship' => array( 1, 2, 3, ),
       ) ); ?>
*/

			?>
  </script><?php
}


add_action( 'wp_ajax_get_post_information', 'ajax_get_post_information' );
function ajax_get_post_information()
{
    if(!empty($_POST['post_id']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {
      $post = get_post( $_POST['post_id'] );
      echo json_encode( $post );
    }

    die();
}


// ajouter post pour un projet
add_action( 'wp_ajax_create_private_post_with_tax', 'ajax_create_private_post_with_tax' );
function ajax_create_private_post_with_tax()
{
    if(!empty($_POST['userid']) && !empty($_POST['term']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {

			$projetslug = $_POST['term'];
			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projetslug))
				||
				current_user_can('administrator')
			) {

				$userid = $_POST['userid'];
				$newpost = array(
					'post_title'					=> __('Untitled post', 'opendoc'),
					'post_content'				=> __('-', 'opendoc'),
					'post_status'					=> 'private',
					'post_author'					=> $userid,
				);
				$newpostID = wp_insert_post( $newpost);
				wp_set_object_terms( $newpostID, $_POST['term'], 'projets');
				wp_set_object_terms( $newpostID, $userid, 'auteur');
	      echo get_permalink( $newpostID);
	    }
    }

    die();
}

// changer la visibilité du post
add_action( 'wp_ajax_change_post_visibility', 'ajax_change_post_visibility' );
function ajax_change_post_visibility()
{

    if(!empty($_POST['post_id']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ) )
    {

			$projet = array_pop(wp_get_object_terms( $_POST['post_id'], 'projets'));
			$projetslug = $projet->slug;

			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projetslug))
				||
				current_user_can('administrator')
			) {

		    $post = array();
		    $post['ID'] = $_POST['post_id'];
	      $post['post_status'] = $_POST['post_status'];
				wp_update_post($post);
	      $post = get_post( $_POST['post_id'] );
	      echo json_encode( get_post_status($_POST['post_id']) );

      }
    }

    die();
}

// supprimer un post
add_action( 'wp_ajax_remove_post', 'ajax_remove_post' );
function ajax_remove_post()
{

    if(!empty($_POST['post_id']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {

			$projet = array_pop(wp_get_object_terms( $_POST['post_id'], 'projets'));
			$projetslug = $projet->slug;

			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projetslug))
				||
				current_user_can('administrator')
			) {

				wp_trash_post( $_POST['post_id'] );
        echo json_encode( get_post_status($_POST['post_id']) );
      }
    }

    die();
}


// ajouter une taxonomie
add_action( 'wp_ajax_add_tax_term', 'ajax_add_tax_term' );
function ajax_add_tax_term()
{

    if(!empty($_POST['tax_term']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {

			if ( !current_user_can( 'edit_posts' )){ die(); }

	    $leprojet = $_POST['tax_term'];
			$leuserid = $_POST['userid'];
			$leaddDescription = $_POST['add-description'];

			echo json_encode( addTermAndCreateDescription( $leprojet, $leuserid, $leaddDescription));

    }

    die();
}

function addTermAndCreateDescription( $projet, $userid, $addDescription) {

	if( term_exists($projet, 'projets')) {
		return __('This name is already taken.', 'opendoc');
	}

	$term_info = wp_insert_term(
    $projet,
    'projets'
	);

	extract( $term_info);
	$projetslug = get_term_by( 'id', $term_id, 'projets')->slug;
	error_log( 'slug : ' . $projetslug);

	// s'il y a un userid, ajouter ce projet à cet userid
	if( !empty($userid)) {
		//récupération du slug
		$hasProjects = get_user_meta( $userid, '_opendoc_user_projets', true );
		error_log( 'utilisateur : ' . $userid . ' has project(s) : ' . $hasProjects);
		if( $hasProjects !== '') {
			$userProjects = explode('|', $hasProjects);
			array_push( $userProjects, $projetslug);
			$hasProjects = implode('|', $userProjects);
			update_user_meta( $userid, '_opendoc_user_projets', $hasProjects);
		} else {
			update_user_meta( $userid, '_opendoc_user_projets', $projetslug);
		}
	}

	// et créer un post description pour ce projet
	if( !empty($addDescription)) {

		if( $addDescription == true) {
			error_log('ajout projet description');
			$newpost = array(
				'post_title'					=> $projet,
				'post_content'				=> __('No content for this project yet.', 'opendoc'),
				'post_status'					=> 'publish',
				'post_author'					=> $userid,
				'tags_input'					=> 'featured'
			);
			$newpostID = wp_insert_post( $newpost);
			wp_set_object_terms( $newpostID, $projetslug, 'projets');
			wp_set_object_terms( $newpostID, $userid, 'auteur');
			return "success";
		}
	}

	return "success";

}


// ajouter et supprimer des éditeurs à un projet (en mettant à jour un champ meta user '_opendoc_user_projets'
add_action( 'wp_ajax_edit_projet_authors', 'ajax_edit_projet_authors' );
function ajax_edit_projet_authors()
{
/*
    error_log("debug ajout meta autheur");
    error_log("projet ? " . $_POST['projet']);
    error_log("validité nonce ? " . check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ));
*/

    if(!empty($_POST['projet']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ) )
    {

			$projet = $_POST['projet'];
	    // check si l'auteur du changement un est admin, un superadmin, ou un auteur qui a l'accès à ce projet
			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projet))
				||
				current_user_can('administrator')
			) {

				$newAuthorsString = $_POST['newauthors'];

				// liste des ID des utilisateurs à qui ajouter le projet
				$newAuthorsArray = explode(',', $newAuthorsString);

				// récupérer tous les auteurs
				// si leur userid est dans la liste, leur ajouter le projet, sinon le virer
				$users = get_users('role=author');

			  foreach ($users as $user) {
					$userid = $user->ID;
					$hasProjects = get_user_meta( $userid, '_opendoc_user_projets', true );
					$userProjects = explode('|', $hasProjects);

					// si dans la liste des projets existants d'un user il y a projet, alors
					if( in_array( $projet, $userProjects)) {
						// si son ID figure dans la liste à éditer
						if( in_array( $userid, $newAuthorsArray)) {
							// ne rien faire
						} else {
							// mais si son ID n'y est pas, c'est qu'il ne doit plus avoir ce projet
							$pos = array_search($projet, $userProjects);
							unset($userProjects[$pos]);
							$hasProjects = implode("|", $userProjects);
							update_user_meta( $userid, '_opendoc_user_projets', $hasProjects);
						}

					} else {
						// par contre, s'il n'est pas dans la liste des projets qu'un user peut déjà éditer
						// et qu'il est mentionné dans les auteurs à ajouter
						if( in_array( $userid, $newAuthorsArray)) {
							array_push( $userProjects, $projet);
							$hasProjects = implode("|", $userProjects);
							update_user_meta( $userid, '_opendoc_user_projets', $hasProjects);
						}
					}
				}
				echo "success";
	    }
	  }

    die();
}

// log out en ajax en cliquant sur le bouton déconnecter
add_action( 'wp_ajax_logout_user', 'ajax_logout_user' );
function ajax_logout_user()
{
    if(!empty($_POST['userid']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {
	    wp_clear_auth_cookie();
	    wp_logout();
	    ob_clean(); // probably overkill for this, but good habit
	    wp_die();
		}
    die();

}


// spam comment
add_action( 'wp_ajax_spam_comment', 'ajax_spam_comment' );
function ajax_spam_comment()
{

    if(!empty($_POST['comment_id'])
    	 &&
    	 !empty($_POST['post_id'])
    	 &&
    	 check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ))
    {

			$projet = array_pop(wp_get_object_terms( $_POST['post_id'], 'projets'));
			$projetslug = $projet->slug;

			if (
				(current_user_can( 'edit_posts' ) && can_user_edit_this_project($projetslug))
				||
				current_user_can('administrator')
			) {


				wp_set_comment_status( $_POST['comment_id'], 'spam');

			}

		}
    die();

}





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


// hide private posts
add_filter('posts_where', 'no_privates');
function no_privates($where) {
    if( current_user_can( 'edit_posts') ) return $where;
    global $wpdb;
    return " $where AND {$wpdb->posts}.post_status != 'private' ";
}

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