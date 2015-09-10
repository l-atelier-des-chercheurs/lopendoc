<?php
	global $post;
  $users = get_users('role=author');
?>

<div class="editProjetAuteurs">
	<ul action=''>
		<?php

		$auteurs = wp_get_post_terms( get_the_ID(), 'auteur');
		$currentuserlogin = wp_get_current_user()->user_login;

		if( !empty($auteurs) ) {
			$auteursName = array_pop( $auteurs ) -> name;
			$auteursArray = explode(',', $auteursName);
		}

	  foreach ($users as $user) {
			$userlogin =  $user->user_login;

			$ifchecked = '';

			if( !empty($auteursArray) ) {
			  foreach ($auteursArray as $auteur) {

	/*
					echo "auteur : ";
					echo $auteur . "</br>";
					echo "userlogin : " ;
					echo $userlogin . "</br>";
					echo "</br></br>";
	*/

					if( $auteur == $userlogin) {
						$ifchecked = 'checked="checked" ';

						if( $userlogin == $currentuserlogin) {
							$GLOBALS["editor"] = true;
						}
					}
				}
			}

			// ajouter et supprimer des éditeurs à la description d'une taxonomy
			// _opendoc_projecteditors à post Description
			// _opendocprojecteditors_

			echo '<input type="checkbox" name="author" value="' . $userlogin . '" ' . $ifchecked . '>' . $user->nickname . '<br>';
			unset($ifchecked);
	  } ?>
	</ul>
	<button type="button" class=" submit-updateAuthors">
	Ajouter/supprimer des éditeurs au projet
	</button>
</div>

