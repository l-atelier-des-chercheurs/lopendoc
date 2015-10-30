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

		// rétro-compatibilité Opendoc : créer un post description s'il n'y en a pas
		if( $descriptionPostID == -1) {
			$newpost = array(
				'post_title'					=> $term,
				'post_content'				=> __('No content for this project yet.', 'opendoc'),
				'post_status'					=> 'publish',
				'post_author'					=> get_current_user_id(),
				'tags_input'					=> 'featured'
			);
			$newpostID = wp_insert_post( $newpost);
			wp_set_object_terms( $newpostID, $term, 'projets');
			wp_set_object_terms( $newpostID, get_current_user_id(), 'auteur');
			$descriptionPostID = $newpostID;
		}

		if( user_can_edit()) {
			get_template_part('templates/content-modules/liste_utilisateurs');
		}
		if( current_user_can( 'edit_posts' )) {
			get_template_part('templates/content-modules/refresh_mails');
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
				echo "<h4 class='legende'>" . __( "Project information", 'opendoc') . "</h4>";
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

?>
			<div class="pageText">
<?php
				$logs = get_post_meta( $descriptionPostID, '_opendoc_editlog', true);
				if( !empty( $logs)) {
					$logs = array_reverse($logs);
					echo "<h4 class='legende accordion--toggle'>" . __( "Last edits to this project", 'opendoc') . "</h4>";
					echo "<ul class='contenu editlog'>";
					foreach($logs as $log):
						echo  "<li>$log</li>";
					endforeach;
					echo "</ul>";
				}

			  $mailToContribute =  get_option( "mail_addressTC" );
		    if( !empty($mailToContribute) ) {
					echo "<h4 class='legende accordion--toggle'>" . __( "To contribute", 'opendoc') . "</h4>";
					echo "<ul class='contenu instructions'>";

					$hasCheckInPlus = strpos($mailToContribute, "leprojet");
					if( $hasCheckInPlus > 0) {
	      		$mailToContribute = str_replace("leprojet", $term, $mailToContribute);
		      	echo "<li>" . __("send an email to ", 'opendoc') . "<a target='_blank' href='mailto:" . $mailToContribute . "'>" . $mailToContribute . "</a>";
		      } else {
			      echo "<li>" . __("send an email to ", 'opendoc') . "<a target='_blank' href='mailto:" . $mailToContribute . "'>" . $mailToContribute . "</a> " . __("with ", 'opendoc') . "<em>#" . $term . "#</em>" . __(" in the subject line.", 'opendoc');
		      }
		      echo "<li>" . __("click on the <em>plus sign</em> icon underneath", 'opendoc') . "</li>";
		    	echo "</ul>";
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
<!-- 		  	<?php	_e("Filter by categories: ", 'opendoc'); ?> -->
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

				<?php
				}
				?>

				<?php
				if( current_user_can( 'edit_posts' ) && !empty($mailToContribute)) {
				?>

<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve" class="options refresh-postie"
				  data-toggle="tooltip" data-placement="bottom" title="<?php _e('Get mails', 'opendoc'); ?>" data-toggle-tooltip-color="#ef474b"
	 >
<g id="fond">
	<path style="fill:#EF474B;" d="M32.1,63.5c17.2,0,31.2-14,31.2-31.2S49.4,1,32.1,1S0.9,15,0.9,32.2S14.9,63.5,32.1,63.5z"/>
</g>
<g id="flc">
	<polygon style="fill:#FCB421;" points="32.3,37.5 32.3,37.5 32.2,37.5 12.1,23.2 12.1,45.1 52.4,45.1 52.4,23.2 	"/>
	<polygon style="fill:#FCB421;" points="52.2,19.4 12.3,19.4 32.2,33.5 	"/>
</g>
</svg>

				<?php
				}
				?>
		<?php
		if ( user_can_edit() ) {
			?>

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

		$publicPostCount = 0;
		$privatePostCount = 0;

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

				if( $status == 'publish') $publicPostCount++;
				if( $status == 'private') $privatePostCount++;

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
		if( $descriptionPostID != -1) {
			update_post_meta( $descriptionPostID, '_publishedCount', $publicPostCount);
			if( user_can_edit())
				update_post_meta( $descriptionPostID, '_privateCount', $privatePostCount);
		}

		unset( $whoIsLockingID);

	?>

	</section>
</main>