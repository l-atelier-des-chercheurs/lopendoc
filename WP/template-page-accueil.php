<?php
/*
Template Name: Accueil avec cartes
*/
?>

<div id="wideView">

		<div class='colTitle'>
			<h1>
				<?php echo roots_title( ); ?>
			</h1>
		</div>

 <?php
	 $tax = 'projets';
 	 $tax_args = array(
 	 	 'orderby' => 'id',
 	 	 'order' => 'DESC',
 	 	 'hide_empty' => false
	 );
	 $terms = get_terms( $tax, $tax_args);
	 $count = count($terms);

	 if ( $count > 0 ){
		 foreach ( $terms as $term ) {

						$args = array(
						  'tax_query' => array(
						      array(
						        'taxonomy' => $tax,
						        'field' => 'slug',
						        'terms' => $term->slug,
						      )
						  ),
					    'post_type'      => 'post', // set the post type to page
					    'posts_per_page' => 1,
							'order' => 'DESC',
							'tag' => 'featured'
						);

						//  assigning variables to the loop
						$wp_query = new WP_Query($args);

						$lastPostDate = $wp_query->posts[0]->post_modified;

			?>

			<div class="colonneswrappers" data-lastpostdate="<?php echo $lastPostDate; ?>">
				<section  class="colonnes">
					<header class="page-header">
							<?php
							    $term_link = get_term_link($term->slug, $tax);
							    $term_name = str_replace(', ', "</br>", $term->name);
								echo '<h2 class="titreProjet"><a href="'.$term_link.'">'.$term_name.'</a></h2>';
							?>
					</header>
					 <div class="colonnescontent">

						<?php

							if ( $wp_query->have_posts() ) {

								// The Loop
								while ($wp_query->have_posts()) : $wp_query->the_post();
									?>

									<div data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

									<!--
										<div class="entry-header">
											<?php
									  		the_title( '<h3 class="entry-title">', '</h3>' );
											?>
										</div>
									-->

										<div class="entry-content">
											<?php the_content(); ?>
										</div><!-- .entry-content -->

									</div>

									<?php
								endwhile;

							} else {
								?>

								<div class="post">

									<div class="entry-content">
										<p>
											<small>Aucune description actuellement pour ce projet, pour en ajouter une envoyez un mail à l'adresse mail du projet avec comme sujet <em>Description</em>.</small>
										</p>
									</div>
								</div>

								<?php
							}

						?>
					 </div>
				 </section>
			 </div>
			 <?php


							wp_reset_postdata();

		 }
 }


/*
			if ( is_user_logged_in() ) {
				$getallposts = new WP_Query( array(
				    'post_type'      => 'post', // set the post type to page
				    'posts_per_page' => 15,
						'post_status'      => 'publish, private', // set the post type to page
				));
			} else {
				$getallposts = new WP_Query( array(
				    'post_type'      => 'post', // set the post type to page
				    'posts_per_page' => 15,
						'post_status'    => 'publish', // set the post type to page
				));
			}

			while ($getallposts->have_posts()) : $getallposts->the_post();

				get_template_part('templates/content', 'carteHome');

			endwhile;
*/
 ?>
	</div>
</div>
