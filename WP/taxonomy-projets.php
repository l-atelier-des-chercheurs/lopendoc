<?php

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

/*
	// si requête POST
  if ('POST' === $_SERVER['REQUEST_METHOD']
      && ! empty($_POST['action'])
      && isset($_POST['post_id'])
  )  {

    $post = array();
    $post['ID'] = $_POST['post_id'];

	  // si changer la visibilité du post :
	  if ( $_POST['action'] === 'update_post_visibility' ) {

      switch ($_POST['visibility']) {
          case 'private':
              $post['post_status'] = 'private';
              break;
          case 'publish':
              $post['post_status'] = 'publish';
              $post['post_password'] = '';
              break;
          case 'password':
              $post['post_status'] = 'publish';
              $post['post_password'] = $_POST['post_password'];
              break;
      }
      wp_update_post($post);
  	} else
	  if ( $_POST['action'] === 'remove_post' ) {
			wp_trash_post( $post['ID'] );
  	} else
	  if ( $_POST['action'] === 'set_taxonomy' ) {
			wp_set_object_terms( $post['ID'], array($term), 'projets');
  	}

  }
*/


	$args = array(
    'tax_query'         => array(
        'relation'      => 'AND',
        array(
            'taxonomy'  => $tax,
            'field'     => 'slug',
            'terms'     => $term,
        ),
        array(
            'taxonomy'  => 'post_tag',
            'field'     => 'slug',
            'terms'     => 'featured'
        ),
    ),
    'post_type'      => 'post', // set the post type to page
    'posts_per_page' => 1,
		'order' => 'DESC',
	);

	/*********************************************************** DESCRIPTION FIELD *********************/
	$wp_query = new WP_Query($args);

	if ( $wp_query->have_posts() ) {

			// The Loop
			while ($wp_query->have_posts()) : $wp_query->the_post();

			  $ID = get_the_ID();
			  $status = get_post_status($ID);

				?>

				<div class="descriptionContainer">

					<section class="post" data-post="<?php $ID; ?>" <?php post_class(); ?> data-status="<?php echo $status; ?>" data-id="<?php echo $ID ?>" data-action="<?php echo esc_url(home_url('')); ?>/edit-page" data-singleurl="<?php echo esc_url( get_permalink() ); ?>">

					<?php
						if ( is_user_logged_in() ) {
							?>

								<?php get_template_part('templates/entry-button-right'); ?>

					<?php
					}
					?>

						<div class="entry-stuff">

							<div class="entry-meta">
								<?php get_template_part('templates/entry-meta'); ?>
							</div><!-- .entry-meta -->

							<div class="entry-title-and-content">
								<header class="entry-header">

								<?php
									// est Description donc pas d'intérêt
									the_title( '<h2 class="entry-title">', '</h2>' );
								?>

								</header><!-- .entry-header -->

								<div class="entry-content">
									<?php the_content(); ?>
								</div>
							</div>

						</div>



					</section>

				</div>

			<?
			endwhile;

	} else {
		?>
		<div class="descriptionContainer">
			<p>Aucune description actuellement pour ce projet, pour en ajouter une envoyez un mail à l'adresse mail du projet avec comme sujet le mot <strong>Description</strong>.</p>
		</div>
		<?php
	}

	wp_reset_query();
?>


	<article class="projetContainer taxProj" data-taxonomy="<?php echo $tax; ?>" data-term="<?php echo $term; ?>">
		<div class="colTitle">
			<h2 class="entry-title">
				<?php echo roots_title( ); ?>
			</h2>
			<?php
			  if ( is_user_logged_in() ) {
					      $mailToContribute =  get_option( "mail_addressTC" );
					      if( !empty($mailToContribute) ) {
									echo "<h3 class='instructions'>";
						      $mailToContribute = str_replace("leprojet", $term, $mailToContribute);
						    	echo "Pour contribuer, envoyez un mail à <a href='mailto:" . $mailToContribute . "' target='_blank'>" . $mailToContribute . "</a>, ou cliquez sur le bouton <em>Ajoutez un post</em> ci-dessous.";
						    	echo "</h3>";
						    }


				}
			?>

		</div>
<?php
  if ( is_user_logged_in() ) {

			?>

		<div class="topIcons">
			<div class="button add-post">
				Ajouter un post
			</div>
		</div>
		<?php
	}

	/*********************************************************** CONTENUS *********************/

	$args = array(
    'tax_query'         => array(
        'relation'      => 'AND',
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

	$new_wp_query = new WP_Query($args);

	if ( $new_wp_query->have_posts() ) {

		// The Loop
		while ($new_wp_query->have_posts()) : $new_wp_query->the_post();

		 	$featured =  has_tag('featured');
	 		if( $featured == true ) {
				continue;
			}

			get_template_part('templates/content', 'carte');


		endwhile;

		//echo 'lastPosttime : ' . $lastPosttime;

		?>
		<?php if ($new_wp_query->max_num_pages > 1) : ?>
		  <nav class="post-nav">
		    <ul class="pager">
		      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
		      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
		    </ul>
		  </nav>
		<?php endif;

	}


?>


</article>
