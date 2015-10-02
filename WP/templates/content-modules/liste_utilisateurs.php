<div class="editProjetAuteurs">
	<ul action=''>
		<h3>
			<?php _e('Edit contributors', 'opendoc'); ?>
		</h3>
		<p>
			<?php _e('Add or remove other users who can contribute to this project', 'opendoc'); ?>
		</p>

		<?php

		$users = get_users('role=author');
	  foreach ($users as $user) {
			$userID = $user->ID;
			$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
			$userProjects = explode('|', $hasProject);


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
	<button type="button" class="submit-updateAuthors" data-submitted="<?php _e('Updating...', 'opendoc'); ?>">
		<?php _e('Update', 'opendoc'); ?>
	</button>
</div>

