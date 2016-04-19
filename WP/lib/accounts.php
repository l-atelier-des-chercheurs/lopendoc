<?php

/*
  les roles :
    project_contributor ->
                              peut créer un projet
                              demander à rejoindre un autre projet
                              commenter partout


*/

$newRoleResultas = add_role(
    'project_contributor',
    __( 'Project contributor', 'opendoc' ),
    array(
        'edit_posts'  => true,
        'delete_posts'=> true,
        'delete_published_posts' => true,
        'edit_published_posts'  => true,
        'publish_posts' => true,
        'upload_files'  => true,

        'read'         => true,  // true allows this capability
        'read_private_posts'         => true,  // true allows this capability

        'edit_others_posts' => true,
        'edit_private_posts' => true,
        'edit_published_posts' => true,

        'delete_others_posts' => true,
        'delete_private_posts' => true,
        'delete_published_posts' => true,

    )
);


/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function my_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			return $redirect_to;
		} else {
			return home_url();
		}
	} else {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );


/*
if ( !current_user_can('administrator') )
  remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
*/

function set_default_admin_color($user_id) {
	$args = array(
		'ID' => $user_id,
		'admin_color' => 'light'
	);
	wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');

function adjust_the_wp_menu() {
	if( current_user_can( 'administrator' ))
	  return;

  remove_menu_page( 'edit-comments.php' );          //Comments
  remove_menu_page( 'tools.php' );                  //Tools
  remove_menu_page( 'edit.php' );                   //Posts
    // $page[0] is the menu title
    // $page[1] is the minimum level or capability required
    // $page[2] is the URL to the item's file
}
add_action( 'admin_menu', 'adjust_the_wp_menu', 999 );

function remove_admin_bar_links() {
	if( current_user_can( 'administrator' ))
	  return;

  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('new-post');
  $wp_admin_bar->remove_menu('new-page');
  $wp_admin_bar->remove_menu('new-cpt');
  $wp_admin_bar->remove_menu('comments');         // Remove the comments link
  $wp_admin_bar->remove_menu('new-content');      // Remove the content link
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function disable_new_post() {
	if( current_user_can( 'administrator' ))
	  return;

  if ( get_current_screen()->post_type == 'post' ) {
    wp_die( __( 'Creating a post is made directly on the project\'s page.', 'opendoc'));
  }
}
add_action( 'load-post-new.php', 'disable_new_post' );

// Redirect any user trying to access some pages
function disable_access_to_admin_pages() {
	if( current_user_can( 'administrator' ))
	  return;

	global $pagenow;
	if ($pagenow === 'edit-comments.php' || $pagenow === 'post.php') {
    wp_die( __( 'This page is not available.', 'opendoc'));
  }
}
add_action('admin_init', 'disable_access_to_admin_pages');

function remove_dashboard_widgets() {
	if( current_user_can( 'administrator' ))
	  return;

  remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
  remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
  remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );


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
// add_action('init', 'prevent_login_page');

function blockusers_init() {
	if ( (is_admin()) && !current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
  	// do not block if project_contributor
  	if( !current_user_can( 'project_contributor')) {
  		wp_redirect( home_url() );
  		exit;
		}
	}
}
add_action( 'admin_init', 'blockusers_init' );


//* Password reset activation E-mail -> Body
add_filter( 'retrieve_password_message', 'wpse_retrieve_password_message', 10, 2 );
function wpse_retrieve_password_message( $message, $key ){
    $user_data = '';
    // If no value is posted, return false
    if( ! isset( $_POST['user_login'] )  ){
            return '';
    }
    // Fetch user information from user_login
    if ( strpos( $_POST['user_login'], '@' ) ) {

        $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    if( ! $user_data  ){
        return '';
    }
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    // Setting up message for retrieve password
    $message = "Vous avez demandé à renouveler votre mot de passe.";
    $message .= "<br/>";
    $message .= "Cliquez sur le lien suivant :";
    $message .= "<br/>";
    $message .= '<a href="';
    $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    $message .= '">';
    $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    $message .= '</a>';

  	$message =
  		"<strong>" . __("This is a mail from l'Opendoc", 'opendoc') . " | " . get_bloginfo('name') . "</strong>" .
  		"<br/>" .
  		"<br/>" .
  		$message .
  		"<br/>" .
  		"<br/>" .
  		"<small>" . __("The team at l'Opendoc.", 'opendoc') . "</small>"
  		;


    return $message;
}




function get_all_users_who_can_contribute() {
  $roles = array('author','project_contributor');
  $users = array();
  foreach ($roles as $role) {
      $args = array('role'=>$role);
      $usersofrole = get_users($args);
      $users = array_merge($usersofrole,$users);
  }
  return $users;
}


function get_all_project_contributors( $projetslug) {
	$users = get_all_users_who_can_contribute();
	$contributors = array();
  foreach( $users as $user) {
		$userID = $user->ID;
		$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
		$userProjects = explode('|', $hasProject);
		if( in_array( $projetslug, $userProjects)) {
			array_push( $contributors, $user);
		}
	}
	return $contributors;
}


function get_all_projects_user_can_contribute_to( $user) {

	$userID = $user->ID;
	$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
	$userProjects = explode('|', $hasProject);

  return $userProjects;
}


/********************************************************************************
                                  Cet utilisateur peut-il contribuer des projets ?
********************************************************************************/

function is_current_user_project_contributor() {
  return current_user_can('edit_posts');
}


/********************************************************************************
                                  Cet utilisateur peut-il éditer le projet en cours ?
********************************************************************************/


function user_can_edit_current_project() {

	if( isset($GLOBALS["project_contributor"]) && $GLOBALS["project_contributor"] == true) {
		return true;
	}

	// si super admin ou admin
	if( current_user_can('administrator')) {
		return true;
	}

	// si logged in
	if( is_user_logged_in() ) {
		// si contributeur à l'instance
		if( is_current_user_project_contributor()) {

    	$term = '';

      $projetslug = '';
      if( get_query_var( 'term' ) !== '') {
    		$projetslug = get_query_var( 'term' );
    	}
      else if( count( wp_get_object_terms( get_the_ID(), 'projets' )) > 0 ) {
      	$terms = get_the_terms( get_the_ID(), 'projets');
    		$term1 = array_pop($terms);
    		$projetslug = $term1->slug;
      }


      if( $projetslug != '') {
        if( can_user_edit_this_project( $projetslug)) {
    			$GLOBALS["project_contributor"] = true;
    			return true;
        }
      }
/*
    	if( $term != '') {
    		$currentuserid = wp_get_current_user()->ID;
    		$hasProjects = get_user_meta( $currentuserid, '_opendoc_user_projets', true );
    		$userProjects = explode('|', $hasProjects);

    		if( in_array( $term, $userProjects)) {
    			$GLOBALS["editor"] = true;
    			return true;
    		}
    	}
*/
    	return false;
		}
	}
	return false;
}


/********************************************************************************
                                  Cet utilisateur peut-il éditer ce projet ?
********************************************************************************/

function can_user_edit_this_project( $projetslug) {

//	error_log( "-- is authorized ?");
	if( current_user_can('administrator'))
		return true;


	if( !isset($GLOBALS["listeProjet"])) {
		$currentuserid = wp_get_current_user()->ID;
		$hasProjects = get_user_meta( $currentuserid, '_opendoc_user_projets', true );
		$userProjects = explode('|', $hasProjects);
		$GLOBALS["listeProjet"] = $userProjects;
	}

	if( $projetslug != '') {
		if( in_array( $projetslug, $GLOBALS["listeProjet"])) {
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

// loggedin = ajouter class au body
function current_user_loggedin($classes) {
	if( is_user_logged_in() && user_can_edit_current_project() ) {
      $classes[] = 'is-edition';
	}
	return $classes;
}
add_filter('body_class', 'current_user_loggedin');






