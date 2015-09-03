<?php
/*
Template Name: Accueil avec cartes
*/
?>

<?php

?>



<div id="wideView">

		<div class='colTitle'>
			<h1>
				<?php echo roots_title( ); ?>
			</h1>
		</div>
		<?php if( get_post()->post_content != '') { ?>
		<div class="pageText">
			<?php echo( get_post()->post_content); ?>
		</div>
		<?php } ?>

	<?php
	  if ( is_user_logged_in() ) {

				?>

			<div class="topIcons">
				<div class="button add-project">
					<?php _e('Add a project', 'opendoc'); ?>
				</div>
			</div>
			<div class="nouveauProjet">
				<h3>
					<?php _e('Add a project', 'opendoc'); ?>
				</h3>
				<p>
					<?php _e('Write your project\'s name', 'opendoc'); ?>
				</p>

        <textarea name="userInput" id="projectName"></textarea>
        <button>
					<?php _e('Add the project', 'opendoc'); ?>
        </button>
			</div>
			<?php
		}

	 $tax = 'projets';
 	 $tax_args = array(
 	 	 'orderby' => 'id',
 	 	 'order' => 'DESC',
 	 	 'hide_empty' => false
	 );
	 $terms = get_terms( $tax, $tax_args);
	 $count = count($terms);

	?>
		<div id="colonnesContainer">
	<?php

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
						);
						$get_last_post = new WP_Query($args);
						$lastPostDate = '';
						if ( $get_last_post->have_posts() ) {
							// The Loop
							while ( $get_last_post->have_posts()) : $get_last_post->the_post();
								$lastPostDate = get_the_modified_date('U');
							endwhile;
						}


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
						$get_description = new WP_Query($args);

			?>

			<div class="colonneswrappers" data-lastpostdate="<?php if( $lastPostDate!= '') echo $lastPostDate; ?>">
				<section  class="colonnes">
					<header class="page-header">
							<?php
							    $term_link = get_term_link($term->slug, $tax);
							    $term_name = str_replace(', ', "</br>", $term->name);
								echo '<a href="'.$term_link.'"><h2 class="titreProjet">'.$term_name.'</h2></a>';
							?>
					</header>
					 <div class="colonnescontent">

						<?php
							if ( $get_description->have_posts() ) {
								// The Loop
								while ( $get_description->have_posts()) : $get_description->the_post();

// 									echo "plop : " . get_the_modified_date();
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
											<small>
												<?php _e('No description for this project yet.', 'opendoc'); ?>
											</small>
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

		?>
			</div>
		<?php

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
