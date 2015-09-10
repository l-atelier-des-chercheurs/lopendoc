<div class="editProjetAuteurs">
	<ul action=''>
		<?php

		$users = get_users('role=author');
	  foreach ($users as $user) {
			$userID = $user->ID;
			$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
			echo "userID : " . $userID . " hasProjects " . $hasProject ;
			echo "</br>";
		}

	  foreach ($users as $user) {
			$userID = $user->ID;
			$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
			$userProjects = explode(',', $hasProject);


			$ifchecked = '';
			if( in_array( $term, $userProjects)) {
				$ifchecked = 'checked="checked" ';
			}
			echo '<div class="checkbox"><label>';
			echo '<input type="checkbox" name="author" value="' . $userID . '" ' . $ifchecked . '>' . $user->nickname;
			echo '</label></div>';
			unset($ifchecked);
		}

	?>
	</ul>
	<button type="button" class=" submit-updateAuthors">
		Ajouter/suprimer des éditeurs au projet
	</button>
</div>

