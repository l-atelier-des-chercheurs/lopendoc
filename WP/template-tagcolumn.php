<?php
/*
Template Name: Column by tags
*/
?>

<?php get_template_part('templates/page', 'header'); ?>

 <?php
	 $tax = 'tag';
 	 $tax_args = array(
 	 	 'orderby' => 'count',
 	 	 'order' => 'DESC'
	 );

		$tags = get_tags($tax_args);
		foreach ($tags as $tag) {

			$tag_name = $tag->name;

					$args = array(
						  'tag' 		=> $tag->slug,
					    'post_type'      => 'post', // set the post type to page
					    'posts_per_page' => -1,
							'order' => 'DESC'
						);

						//  assigning variables to the loop
						$wp_query = new WP_Query($args);


						if ( $wp_query->have_posts() ) {

							$tag_id = $tag->term_id;

							?>

								<section  class="colonnes" data-tag="<?php echo $tag_name; ?>">
									<header class="page-header post">
											<?php
										    $tag_link = get_tag_link($tag);
												echo '<div class="count">' . $tag->count . '</div>';
												echo '<h2 class="titreProjet"><a href="'.$tag_link.'">'.$tag_name.'</a></h2>';

											?>
									</header>
									 <div class="colonnescontent">

										 <?php

											// The Loop
											while ($wp_query->have_posts()) : $wp_query->the_post();
												?>

												<article <?php post_class(); ?>>
												  <header>
												    <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
												  </header>
												  <div class="entry-summary">

												    <?php
/*
												    $args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID );
												    $attachments = get_posts($args);
												    if ($attachments) {
												        foreach ( $attachments as $attachment ) {
												            the_attachment_link( $attachment->ID , true );
												        }
												    }
*/

															the_content();
												    ?>



												  </div>
												</article>

												<?php

											endwhile;

										?>
										</div>
								 </section>
							 <?php

						}

				}

			wp_reset_postdata();
