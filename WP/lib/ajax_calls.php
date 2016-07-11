<?php

/******* ajax stuff *******/
add_action ( 'wp_head', 'add_frontend_ajax_javascript_file' );
function add_frontend_ajax_javascript_file(){ ?>
  <script type="text/javascript">
    var ajaxurl = <?php echo json_encode( admin_url( "admin-ajax.php" ) ); ?>;
    var ajaxnonce = <?php echo json_encode( wp_create_nonce( get_option( "wp_custom_nonce" ) ) ); ?>;
    var username = "not-logged-in";
    var instanceName = "<?php echo bloginfo('name'); ?>";

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

    <?php
      $current_user = wp_get_current_user();
      if ( is_user_logged_in() ) {
			//echo 'Username: ' . $current_user->user_login . "\n"; echo 'User display name: ' . $current_user->display_name . "\n";
			if( user_can_edit_current_project()) { ?>
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
				( is_current_user_project_contributor() && can_user_edit_this_project($projetslug))
			) {
				$userid = $_POST['userid'];
				$newpost = array(
					'post_title'					=> __('New post title', 'opendoc'),
					'post_content'				=> __('New post content.', 'opendoc'),
					'post_status'					=> 'private',
					'post_author'					=> $userid,
				);
				$newpostID = wp_insert_post( $newpost);
				wp_set_object_terms( $newpostID, $projetslug, 'projets');
	      echo get_permalink( $newpostID);

				$userWhoEdited = get_user_by( 'id', $userid);
				$usernameWhoEdited = $userWhoEdited->display_name;

	      $projectLink = get_term_link( $projetslug, 'projets');
				sendMailToAllProjectContributors( $projetslug,
					html_entity_decode( get_bloginfo('name')),
					"<strong>" . $usernameWhoEdited . "</strong>" . " " .
						__("has added a new post to", 'opendoc') . " " .
						"<strong>" . $projetslug . "</strong>" .
						"<br/>" .
						__("You can see this post and contribute by visiting", 'opendoc') . " " .
						esc_url( $projectLink )
					);

				$user = get_user_by( 'id', get_current_user_id());
				$username = $user->display_name;
				wp_set_object_terms( $newpostID, $username, 'auteur');
				logActionsToProject( $projetslug, "<span class='edit-by-author'>$username</span>" . __("Has created a new post", 'opendoc'));

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

      $projetTerms = wp_get_object_terms( $_POST['post_id'], 'projets');
			$projet = array_pop( $projetTerms);
			$projetslug = $projet->slug;

			if (
				(is_current_user_project_contributor() && can_user_edit_this_project($projetslug))
			) {

		    $post = array();
		    $post['ID'] = $_POST['post_id'];
	      $post['post_status'] = $_POST['post_status'];
				wp_update_post($post);
	      $post = get_post( $_POST['post_id'] );
	      $newStatus = get_post_status($_POST['post_id']);
	      echo json_encode( $newStatus);

				if( $newStatus == 'publish')
					$newStatus = __("public", 'lopendoc');
				if( $newStatus == 'private')
					$newStatus = __("private", 'lopendoc');

				$user = get_user_by( 'id', get_current_user_id());
				$username = $user->display_name;
				logActionsToProject( $projetslug, "<span class='edit-by-author'>$username</span>" . __("Changed post status to ", 'opendoc') . '<strong>' . $newStatus . '</strong>' .  __(" for post ", 'opendoc') . '<strong>' . get_the_title( $_POST['post_id']) . '</strong>' );
      }
    }

    die();
}

// ajouter un log dans le champ edit car un post vient d'être édité
add_action( 'wp_ajax_edit_log_postedited', 'ajax_edit_log_postedited' );
function ajax_edit_log_postedited()
{

/*
					'action': 'edit_log_postedited',
					'post_id' : thisPostID,
					'security': ajaxnonce,
					'projet': projet,
*/

    if(!empty($_POST['projet']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ) )
    {
	    $projetslug = $_POST['projet'];
			if (
				(is_current_user_project_contributor() && can_user_edit_this_project($projetslug))
			) {
				$user = get_user_by( 'id', get_current_user_id());
				$username = $user->display_name;

				//error_log( 'new auteur '. $user->ID . ' pour le post ' . $_POST['post_id'] . ' ');
				wp_add_object_terms( $_POST['post_id'], $username, 'auteur');
				logActionsToProject( $projetslug, "<span class='edit-by-author'>$username</span>" . __("Edited post ", 'opendoc') . '<strong>' . get_the_title( $_POST['post_id']) . '</strong>' );
				// ajout de l'utilisateur dans la liste des terms
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
			$postTitle = get_the_title( $_POST['post_id']);

			if (
				(is_current_user_project_contributor() && can_user_edit_this_project($projetslug))
			) {
				wp_trash_post( $_POST['post_id'] );
        echo '';
				$user = get_user_by( 'id', get_current_user_id());
				$username = $user->display_name;
				logActionsToProject( $projet, "<span class='edit-by-author'>$username</span>" . __("Removed post ", 'opendoc') . '<strong>' . $postTitle . '</strong>' );
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

			if ( !is_current_user_project_contributor()){ die(); }

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
				'post_content'				=> __('Write here a description for your project and add a picture. They will be used to show your project on the front page.', 'opendoc'),
				'post_status'					=> 'publish',
				'post_author'					=> $userid,
				'tags_input'					=> 'featured'
			);
			$newpostID = wp_insert_post( $newpost);
			wp_set_object_terms( $newpostID, $projetslug, 'projets');
			$user = get_user_by( 'id', get_current_user_id());
			$username = $user->display_name;
			wp_set_object_terms( $newpostID, $username, 'auteur');
			logActionsToProject( $projetslug, "<span class='edit-by-author'>$username</span>" . __("Created this project", 'opendoc'));

			return "success";
		}
	}

	return "success";

}


// ajouter et supprimer des éditeurs à un projet (en mettant à jour un champ meta user '_opendoc_user_projets'
add_action( 'wp_ajax_edit_projet_authors', 'ajax_edit_projet_authors' );
function ajax_edit_projet_authors()
{

    if(!empty($_POST['projet']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ) )
    {

			$projet = $_POST['projet'];
	    // check si l'auteur du changement un est admin, un superadmin, ou un auteur qui a l'accès à ce projet
			if (
				(is_current_user_project_contributor() && can_user_edit_this_project($projet))
			){
				$newAuthorsString = $_POST['newauthors'];
				// liste des ID des utilisateurs à qui ajouter le projet
				$newAuthorsArray = explode(',', $newAuthorsString);

				// récupérer tous les auteurs
				// si leur userid est dans la liste, leur ajouter le projet, sinon le virer
				$users = get_all_users_who_can_contribute();

			  foreach ($users as $user) {
					$userid = $user->ID;
					$hasProjects = get_user_meta( $userid, '_opendoc_user_projets', true );
					$userProjects = explode('|', $hasProjects);

					// si dans la liste des projets existants d'un user il y a le projet, alors
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

							$userWhoEdited = get_user_by( 'id', get_current_user_id());
							$usernameWhoEdited = $userWhoEdited->display_name;
							logActionsToProject( $projet, "<span class='edit-by-author'>$usernameWhoEdited</span>" . __("Removed contributor ", 'opendoc') . '<strong>' . $user->user_login . '</strong>' );
						}

					} else {
						// par contre, s'il n'est pas dans la liste des projets qu'un user peut déjà éditer
						// et qu'il est mentionné dans les auteurs à ajouter
						if( in_array( $userid, $newAuthorsArray)) {
							array_push( $userProjects, $projet);
							$hasProjects = implode("|", $userProjects);
							update_user_meta( $userid, '_opendoc_user_projets', $hasProjects);

							$userWhoEdited = get_user_by( 'id', get_current_user_id());
							$usernameWhoEdited = $userWhoEdited->display_name;

							$userToAdd = get_user_by( 'id', $userid);
							$usernameOfUserToAdd = $userToAdd->display_name;
							$mailOfUserToAdd = $userToAdd->user_email;
							$projectLink = get_term_link( $projet, 'projets');

							logActionsToProject( $projet, "<span class='edit-by-author'>$usernameWhoEdited</span>" . __("Added contributor ", 'opendoc') . '<strong>' . $userToAdd->user_login . '</strong>' );

						  $mailToContribute =  get_option( "mail_addressTC" );
						  if( !empty($term)) {
								$mailToContribute = str_replace("leprojet", $projet, $mailToContribute);
							} else {
								$mailToContribute = '';
							}

							sendMailTo( $mailToContribute,
														$mailOfUserToAdd,
								html_entity_decode( get_bloginfo('name')),
									__("You have been added as a contributor to the project called", 'opendoc') . " " .
									"<strong>" . $projet . "</strong>" .
									"<br/>" .
									__("You can now add posts, edit the informations, see all the private posts and edit the contributor's list in the project's page at", 'opendoc') . " " .
									esc_url( $projectLink )

								);

						}
					}
				}
				echo "success";
	    }
	  }

    die();
}


// un utilisateur contributeur non-contributeur du projet demande à le devenir
// --> envoyer un mail à tous les contributeurs en place
// nécessit un *projet*,
add_action( 'wp_ajax_ask_to_become_author', 'ajax_ask_to_become_author' );
function ajax_ask_to_become_author()
{
  if(!empty($_POST['projet']) && check_ajax_referer( get_option( "wp_custom_nonce" ), 'security' ) )
  {
		$projetslug = $_POST['projet'];
    // check si l'auteur du changement est admin, un superadmin, ou un auteur qui a l'accès à ce projet
		if (
			( is_current_user_project_contributor() && !can_user_edit_this_project($projetslug))
		) {
			$userWhoEdited = get_user_by( 'id', get_current_user_id());
			$usernameWhoEdited = $userWhoEdited->display_name;
			$usernameMail = $userWhoEdited->user_email;
      $projectLink = get_term_link( $projetslug, 'projets');

/*
			sendMailToAllProjectContributors( $projetslug,
				html_entity_decode( get_bloginfo('name')),
					__("Another user has asked to become a contributor to one of your project.", 'opendoc') .
					"<br/>" .
					__("Username: ") .
					$usernameWhoEdited . "(" . $usernameMail . ")" .
					"<br/>" .
					__("Name of your project: ", 'opendoc') .
					$projetslug .
					"<br/>" .
					__("To add this user to your project, connect to l'Opendoc and click on the contributor icon in the project information: ", 'opendoc') .
					esc_url( $projectLink )
				);
*/

			sendMailToAllProjectContributors( $projetslug,
				html_entity_decode( get_bloginfo('name')),
					"<strong>" . $usernameWhoEdited . "</strong> (" . $usernameMail . ")" . " " .
					__("has asked to become a contributor to", 'opendoc') . " " .
					"<strong>" . $projetslug . "</strong>" .
					"<br/>" .
					__("To add this user to the list of contributors, click on the contributor icon in the project's information on the project's page at ", 'opendoc') .
					esc_url( $projectLink )

				);

			echo __("Your request has been sent.", 'opendoc');
	    die();
		}
	}
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
				(is_current_user_project_contributor() && can_user_edit_this_project($projetslug))
			) {


				wp_set_comment_status( $_POST['comment_id'], 'spam');

			}

		}
    die();

}
