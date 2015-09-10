<?php
/*
Template Name: Accueil avec cartes
*/
?>

<?php

?>




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
		<div class='category-list category-filters'>
			<span class="legende">
		  	<?php	_e("Filter by categories: ", 'opendoc'); ?>
			</span>

			<span class="contenu">
			</span>
		</div>


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
	<div id="colonnesContainer" class="filter-elements">
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
					  'orderby'=> 'modified',
					  'order' => 'DESC',
					);
					$get_last_post = new WP_Query($args);
					$lastPostDate = '1000000000';
					$lastPostDateHuman = '';
					if ( $get_last_post->have_posts() ) {
						// The Loop
						while ( $get_last_post->have_posts()) : $get_last_post->the_post();
							$lastPostDate = get_the_modified_date('U');
							$lastPostDateHuman = get_the_modified_date();
							break;
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

			    $term_link = get_term_link($term->slug, $tax);
			    $term_name = str_replace(', ', "</br>", $term->name);

		?>

		<div class="colonneswrappers"
			<?php if( $lastPostDate!= '') { ?> data-lastpostdate=" <?php echo $lastPostDate; } ?>"
				>
			<section  class="colonnes">

				 <div class="colonnescontent">

					<?php
						if ( $get_description->have_posts() ) {
							// The Loop
							while ( $get_description->have_posts()) : $get_description->the_post();

								$tags = get_the_category();
								if ($tags) {
									$htmlTags = '<div class="category-list">';
									$htmlTags .= '<span class="legende">';
									$htmlTags .= __('Categories: ', 'opendoc');
									$htmlTags .= '</span>';
									$htmlTags .= '<span class="contenu">';

									$alltags = '';

									foreach ( $tags as $tag ) {
						// 				$tag_link = get_category( $tag->term_id );

										$htmlTags .= "<span class='category-term' data-categorie='{$tag->slug}'>";
										$htmlTags .= "{$tag->name}</span>";
										$alltags .= $tag->slug . " ";
									}
									$htmlTags .= '</span>';
									$htmlTags .= '</div>';
								}


								$description_content = get_the_content();
								$img = "";

								if( has_post_thumbnail()) {
									$post_thumbnail_id = get_post_thumbnail_id( );
									$img = wp_get_attachment_image_src( $post_thumbnail_id, 'medium');
									$img = $img[0];
								}

								?>

								<a href="<?php echo $term_link; ?>">
									<?php if( $img != "") { ?>
										<div class="headerImg" style="background-image: url(<?php echo $img; ?>)">
											<?php echo '<img src="' . $img . '">'; ?>
										</div>
									<?php } ?>
								</a>

								<div data-post="<?php the_ID(); ?>" <?php post_class(); ?> data-allcategories="<?php echo $alltags; ?>" style="">

									<a href="<?php echo $term_link; ?>">
										<header class="headerProject">
												<?php
													echo '<h2 class="titreProjet">'.$term_name.'</h2>';
												?>
										</header>
									</a>

									<div class="entry-content">
										<?php
											echo $description_content;
										?>
									</div><!-- .entry-content -->

									<div class="entry-meta">
										<?php if( $lastPostDateHuman !== '') { ?>
											<div class="modDate">
												<span class="legende">
													<?php _e('Last edit: ', 'opendoc'); ?>
												</span>
												<span class="contenu">
													<?php echo $lastPostDateHuman;?>
												</span>
											</div>
										<?php }
										if( $tags != '') {
												?>
											<?php echo $htmlTags; ?>
										<?php } ?>
									</div>


								</div>

								<?php
							endwhile;

						} else {
							?>

							<div class="post">
									<a href="<?php echo $term_link; ?>">
										<header class="headerProject">
												<?php
													echo '<h2 class="titreProjet">'.$term_name.'</h2>';
												?>
										</header>
									</a>

								<div class="entry-content">
									<p>
										<small>
										<?php
											// si le projet n'a aucun poste modifié en dernier, c'est qu'il n'en a pas...
											if( $lastPostDateHuman == '') {
												_e('No content for this project yet.', 'opendoc');
											} else {
												_e('No description for this project yet.', 'opendoc');
											} ?>
										</small>
									</p>
								</div>

								<div class="entry-meta">
									<?php if( $lastPostDateHuman !== '') { ?>
										<span class="modDate">
											<?php _e('Last edited on ', 'opendoc'); ?>
											<?php echo $lastPostDateHuman;?>
										</span>
									<?php } ?>
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
