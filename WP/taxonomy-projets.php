<?php
		/*********************************************************** DESCRIPTION FIELD *********************/
		$tax = get_query_var( 'taxonomy' );
		$term = get_query_var( 'term' );

		/*********************************************************** CONTENUS *********************/
		$args = array(
		    'tax_query'         => array(
		        array(
		            'taxonomy'  => $tax,
		            'field'     => 'slug',
		            'terms'     => $term,
		        ),
		    ),
		    'post_type'      => 'post', // set the post type to page
		    'posts_per_page' => -1,
				'order' => 'DESC',
			);


		$allposts = new WP_Query($args);
		$descriptionPostID = -1;


		if ( $allposts->have_posts() ) {
			// The Loop
			while ($allposts->have_posts()) {
				$allposts->the_post();
				$featured =  has_tag('featured');
				if( $featured == true ) {
					$descriptionPostID = get_the_ID();
					break;
				}

			}
		}

		if( user_can_edit()) {
			get_template_part('templates/content-modules/liste_utilisateurs');
		}
	?>

		<?php
/*
		$users = get_users('role=author');
	  foreach ($users as $user) {
			$userID = $user->ID;
			$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
			echo "userID : " . $userID . " hasProjects " . $hasProject ;
			echo "</br>";
		}
*/
		?>
	<article class="projetContainer taxProj filter-elements" data-taxonomy="<?php echo $tax; ?>" data-term="<?php echo $term; ?>">

<!--
		<div class='colTitle'>
			<h1 class="entry-title">
				<?php echo roots_title( ); ?>
			</h1>
		</div>
-->
		<?php
			if( $descriptionPostID != -1) {
				$post = get_post($descriptionPostID);
				?>
			<div class="descriptionContainer">
				<?php get_template_part('templates/content-carte'); ?>
			</div>
		<?php
		} else {
			?>
			<div class="descriptionContainer">
				<p class="absence_de_description">
					<?php _e('No description for this project yet. To add one send an email to this project\'s mail with the subject line <strong>Description</strong>.', 'opendoc'); ?>
				</p>
			</div>
			<?php
		}

		if ( user_can_edit() ) {
		  $mailToContribute =  get_option( "mail_addressTC" );
	    if( !empty($mailToContribute) ) {
			?>
			<div class="pageText">
				<?php
					echo "<p class='instructions'>";
	      	$mailToContribute = str_replace("leprojet", $term, $mailToContribute);
	    		_e("To contribute, send an email to ", 'opendoc');
					echo "<a target='_blank' href='mailto:" . $mailToContribute . "'>" . $mailToContribute . "</a>";
	    		_e(", or click on <strong>Add a post</strong>.", 'opendoc');
		    	echo "</p>";
		    ?>
			</div>

		<?php
			}
		}
		?>
		<div class='module-large category-list category-filters'>
			<div class="legende">
		  	<?php	_e("Filter by categories: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
			</div>
		</div>
		<?php
		if ( user_can_edit() ) {
			?>
			<div class="module-large topIcons">
				<div class="legende">
			  	<?php	_e("Actions: ", 'opendoc'); ?>
				</div>
				<div class="contenu">
					<button class="button add-post">
						<?php _e('Add a post', 'opendoc'); ?>
					</button>
					<button class="button edit-authors">
						<?php _e('Edit project contributors', 'opendoc'); ?>
					</button>
				</div>
			</div>
			<?php
		}



		$modDateRecent = 0;
		$modDateDescription = 0;
		$allposts->rewind_posts();

		if ( $allposts->have_posts() ) {
			// The Loop
			while ($allposts->have_posts()) {

				$allposts->the_post();

				$modDatePost = get_the_modified_date('U');
				if( $modDateRecent < $modDatePost)
					$modDateRecent = $modDatePost;

				$featured =  has_tag('featured');
				if( $featured == true ) {
					$modDateDescription = get_the_modified_date('U');
					continue;
				}

			  $ID = get_the_ID();
			  $status = get_post_status();

			  $whoIsLockingID = is_post_lock_admin($ID);
			  if( !empty( $whoIsLockingID)) {
					$whoIsLocking = get_user_meta( $whoIsLockingID, 'nickname', true);
					$lockingSentence = __('Post currently edited by ', 'opendoc');
				}

				?>

				<div class="postContainer"
					data-status="<?php echo $status; ?>"
					<?php if( !empty($whoIsLockingID) ) { echo " data-isLocked='$whoIsLocking'"; } ?>
						>
					<?php
					if ( user_can_edit() ) {
						get_template_part('templates/content-modules/private-publish');
					}
					get_template_part('templates/content-carte');
					?>
				</div>
				<?php

			}

			if ($allposts->max_num_pages > 1) { ?>
			  <nav class="post-nav">
			    <ul class="pager">
			      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
			      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
			    </ul>
			  </nav>
			<?php
			}

		}

		// si il y a un post plus récent que description
		if( $modDateRecent > $modDateDescription) {
			// et si on a un ID pour le post description
			if( $descriptionPostID != -1) {
				echo "MODIF DESCRIPTION EDIT DATE";
		    $updateDescription['ID'] = $descriptionPostID;
	      $updateDescription['post_modified'] = $modDateRecent;
				wp_update_post( $updateDescription);
			}
		}

		unset( $whoIsLockingID);

	?>


</article>