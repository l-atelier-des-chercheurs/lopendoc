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
<header id="presentationActions">


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
/*
			?>
			<div class="descriptionContainer">
				<p class="absence_de_description">
					<?php _e('No description for this project yet. To add one send an email to this project\'s mail with the subject line <strong>Description</strong>.', 'opendoc'); ?>
				</p>
			</div>
			<?php
*/
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



</header>
<main class="main" role="main">
	<div id="colonnesOptionsTopBar">

		<div class='category-list category-filters'>
			<div class="legende">
		  	<?php	_e("Filter by categories: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
			</div>
		</div>

		<?php
		if ( user_can_edit() ) {
			?>

			<div class="edit-all-project">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve" class="options add-post"

				  data-toggle="tooltip" data-placement="bottom" title="<?php _e('Add a post', 'opendoc'); ?>" data-toggle-tooltip-color="#ef474b"

					 >
					<polygon style="fill:#FCB421;" points="55.2,36.2 49.6,50.1 34,54.8 9,49.3 9,29.9 19.3,17.2 34.4,10 48.9,25.6 	"/>
					<path style="fill:#EF474B;" d="M10.2,10.3C-2,22.5-2,42.3,10.2,54.5c12.2,12.2,31.9,12.2,44.1,0c12.2-12.2,12.2-31.9,0-44.1
						C42.1-1.8,22.4-1.8,10.2,10.3z M50.2,28.5l0,7.8l-14,0l0,14h-7.8l0-14l-14,0l0-7.8l14,0l0-14l7.8,0l0,14H50.2z"/>
				</svg>

				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve"

					class="options edit-authors"
				  data-toggle="tooltip" data-placement="bottom" title="<?php _e('Edit project contributors', 'opendoc'); ?>" data-toggle-tooltip-color="#fcb421"

					 >
				<g id="fond">
					<polygon style="fill:#FCB421;" points="55.2,36.2 49.6,50.1 34,54.8 9,49.3 9,29.9 19.3,17.2 34.4,10 48.9,25.6 	"/>
					<path style="fill:#FCB421;" d="M32.1,63.5c17.2,0,31.2-14,31.2-31.2S49.4,1,32.1,1S0.9,15,0.9,32.2S14.9,63.5,32.1,63.5z"/>
				</g>
				<g id="flc">
					<g>
						<path style="fill:#293275;" d="M40.9,39.6c-1-1.6-2.3-2.7-4.1-3.3c0,0-1.4-0.7-5-0.7c-3.6,0-5,0.7-5,0.7c-1.8,0.6-3.1,1.7-4.1,3.3
							c-0.4,0.6-0.6,1.9-0.7,2.6c0,0.2,0,0.4,0,0.6V44v-0.8c0,1.3,0.9,2.4,2,2.4h15.5c1.1,0,2-1.1,2-2.4V44v-1.2c0-0.2,0-0.4,0-0.6
							C41.6,41.6,41.4,40.3,40.9,39.6 M26.4,26.7c0,3,1.9,7.5,5.5,7.5c3.5,0,5.5-4.5,5.5-7.5s-2.4-5.5-5.5-5.5
							C28.8,21.2,26.4,23.6,26.4,26.7"/>
						<g>
							<path style="fill:#293275;" d="M25.8,35c-0.9-1.1-1.9-1.9-3.3-2.3c0,0-1.3-0.7-4.6-0.7c-3.3,0-4.6,0.7-4.6,0.7
								c-1.7,0.6-2.9,1.5-3.9,3.1c-0.4,0.6-0.6,1.8-0.6,2.4c0,0.2,0,0.4,0,0.6V39c0,1.2,0.8,2.2,1.9,2.2H20c0.1-0.8,0.4-1.6,0.7-2.1
								C22,37.1,23.6,35.7,25.8,35z"/>
							<path style="fill:#293275;" d="M17.9,30.6c3.3,0,5.1-4.2,5.1-7c0-2.8-2.3-5.1-5.1-5.1c-2.8,0-5.1,2.3-5.1,5.1
								C12.7,26.4,14.5,30.6,17.9,30.6z"/>
						</g>
						<g>
							<path style="fill:#293275;" d="M37.9,35c0.9-1.1,1.9-1.9,3.3-2.3c0,0,1.3-0.7,4.6-0.7c3.3,0,4.6,0.7,4.6,0.7
								c1.7,0.6,2.9,1.5,3.9,3.1c0.4,0.6,0.6,1.8,0.6,2.4c0,0.2,0,0.4,0,0.6V39c0,1.2-0.8,2.2-1.9,2.2h-9.3c-0.1-0.8-0.4-1.6-0.7-2.1
								C41.7,37.1,40.1,35.7,37.9,35z"/>
							<path style="fill:#293275;" d="M45.9,30.6c-3.3,0-5.1-4.2-5.1-7c0-2.8,2.3-5.1,5.1-5.1c2.8,0,5.1,2.3,5.1,5.1
								S49.2,30.6,45.9,30.6z"/>
						</g>
					</g>
				</g>
				</svg>



			</div>

			<?php
		}
?>
	</div>
	<section id="projetContainer" class="taxProj filter-elements" data-taxonomy="<?php echo $tax; ?>" data-term="<?php echo $term; ?>">
	<?php
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
		}

		// si il y a un post plus récent que description
		if( $modDateRecent > $modDateDescription) {
			// et si on a un ID pour le post description
			if( $descriptionPostID != -1) {
				//echo "MODIF DESCRIPTION EDIT DATE";
		    $updateDescription['ID'] = $descriptionPostID;
	      $updateDescription['post_modified'] = $modDateRecent;
				wp_update_post( $updateDescription);
			}
		}

		unset( $whoIsLockingID);

	?>

	</section>
</main>