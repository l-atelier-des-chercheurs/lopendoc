<?php

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

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
			while( $wp_query->have_posts() ) {

				$wp_query->the_post();

			  $ID = get_the_ID();
			  $status = get_post_status($ID);

				?>

				<div class="descriptionContainer">

					<section class="post" data-post="<?php echo $ID; ?>" <?php post_class(); ?> data-status="<?php echo $status; ?>" data-id="<?php echo $ID ?>" data-action="<?php echo esc_url(home_url('')); ?>/edit-page" data-singleurl="<?php echo esc_url( get_permalink() ); ?>">

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

		<?php
			}

	} else {
		?>
		<div class="descriptionContainer">
			<p>
				<?php _e('No description for this project yet. To add one send an email to this project\'s mail with the subject line <strong>Description</strong>.', 'opendoc'); ?>
			</p>
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
			    		_e("To contribute, send an email to ", 'opendoc');
							echo "<a target='_blank' href='mailto:" . $mailToContribute . "'>" . $mailToContribute . "</a>";
			    		_e(", or click on <strong>Add a post</strong>.", 'opendoc');
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
				<?php _e('Add a post', 'opendoc'); ?>
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
		while ($new_wp_query->have_posts()) {
			$new_wp_query->the_post();

		 	$featured =  has_tag('featured');
	 		if( $featured == true ) {
				continue;
			}

			get_template_part('templates/content', 'carte');
		}

		if ($new_wp_query->max_num_pages > 1) { ?>
		  <nav class="post-nav">
		    <ul class="pager">
		      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
		      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
		    </ul>
		  </nav>
		<?php
		}

	}


?>


</article>