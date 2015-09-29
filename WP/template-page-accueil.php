<?php
/*
Template Name: Accueil avec cartes
*/
?>

<header id="presentationActions">
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
</header>

<main class="main" role="main">
	<!-- barre du haut de la page -->
	<div id="colonnesOptionsTopBar">

		<div class='sort-list'>
			<div class="legende">
		  	<?php	_e("sort projects by&nbsp;: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
				<span class="sort-term" data-type="edited">
			  	<?php	_e("edited", 'opendoc'); ?>
				</span>
				<span class="sort-term" data-type="created">
			  	<?php	_e("created", 'opendoc'); ?>
				</span>
				<span class="sort-term" data-type="ab">
		  		<?php	_e("alphabetical", 'opendoc'); ?>
				</span>
			</div>
		</div>

		<div class="edit-all-project">

			<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
				 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve" class="add-project">
			<g id="fond">
				<polygon style="fill:#FCB421;" points="55.2,36.2 49.6,50.1 34,54.8 9,49.3 9,29.9 19.3,17.2 34.4,10 48.9,25.6 	"/>
				<path style="fill:#293275;" d="M32.1,63.5c17.2,0,31.2-14,31.2-31.2S49.4,1,32.1,1S0.9,15,0.9,32.2S14.9,63.5,32.1,63.5z"/>
				<path style="fill:#EF474B;" d="M10.2,10.3C-2,22.5-2,42.3,10.2,54.5c12.2,12.2,31.9,12.2,44.1,0c12.2-12.2,12.2-31.9,0-44.1
					C42.1-1.8,22.4-1.8,10.2,10.3z M50.2,28.5l0,7.8l-14,0l0,14h-7.8l0-14l-14,0l0-7.8l14,0l0-14l7.8,0l0,14H50.2z"/>
			</g>
			<g id="flc">
			</g>
			</svg>


			<span class="dashicons dashicons-search open-search"></span>
		</div>
	</div>

	<!-- barre sur le côté de la page -->
	<div id="colonnesOptionsSidebar">

		<div class="module-side topIcons">
			<div class="legende">
		  	<?php	_e("Actions: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
	<?php if ( is_user_logged_in() ) { ?>
				<button class="button add-project" data-open-popover="nouveauProjet">
					<?php _e('Add a project', 'opendoc'); ?>
				</button>
	<?php } ?>
				<button class="button open-search" data-open-popover="nouveauProjet">
					<?php _e('Search', 'opendoc'); ?>
				</button>
			</div>
		</div>

		<div class="module-side category-list category-filters">
			<div class="legende">
		  	<?php	_e("Filter by categories: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
			</div>
		</div>


		<div class="popoverContent nouveauProjet">
			<h3>
				<?php _e('Add a project', 'opendoc'); ?>
			</h3>
			<p>
				<?php _e('Write your project\'s name', 'opendoc'); ?>
			</p>

	    <input type="text" name="userInput" id="projectName">
	    <button>
				<?php _e('Add the project', 'opendoc'); ?>
	    </button>
		</div>

		<div class="popoverContent champRecherche">
			<h3>
				<?php _e('Search on l\'opendoc', 'opendoc'); ?>
			</h3>
			<?php get_template_part('templates/searchform'); ?>
		</div>

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
	?>


	<section id="colonnesContainer" class="filter-elements grilleProjets">

		<div class="colonnesContainerInside">
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
			  'tag'		=> 'featured',

	      'nopaging' => true,

			);
			$get_description = new WP_Query($args);

			$lastPostDate = '1000000000';
			$lastPostDateHuman = '';
			$createdDateHuman = '';
			$timeCreated = '';

			$descriptionPostID = -1;

			if ( $get_description->have_posts() ) {
				// The Loop
				$index = 0;
				while ( $get_description->have_posts()) : $get_description->the_post();
					// chercher le featured, le récupérer puis break
					$descriptionPostID = get_the_ID();
					$createdDateHuman = get_the_date('d/m/Y');
					$timeCreated = get_the_date('U');
					$lastPostDate = get_the_modified_date('U');
					$lastPostDateHuman = get_the_modified_date('d/m/Y');

				endwhile;
			}

	    $term_link = get_term_link($term->slug, $tax);
	    $term_name = $term->name;
	    $term_slug = $term->slug;
	    $term_count = $term->count;

	    // calculer lastpostdate en partant de now
	    $nowts = current_time('U');

	    $timeSinceLastPostDate = $nowts - $lastPostDate;
	    $timeCreated = $nowts - $timeCreated;

			?>

			<div class="colonneswrappers make-it-col"
				<?php if( $lastPostDate != '') {						echo " data-lastpostdate='$lastPostDate'"; } ?>
				<?php if( $timeSinceLastPostDate != '') { 	echo " data-timesincelastpostdate='$timeSinceLastPostDate'"; } ?>
				<?php if( $timeCreated != '') { 					 	echo " data-timecreated='$timeCreated'"; } ?>
				<?php if( $term_name != '') { 							echo " data-name='" . strtoupper( $term_name) . "'"; } ?>
				<?php if( has_post_thumbnail()) { 	 				echo " data-hasthumb "; } ?>
				<?php if( $term_count != '') { 							echo " data-count='" . $term_count . "'"; } ?>
					>
				<section  class="colonnes" >

					 <div class="colonnescontent">

						<?php

								$img = "";
								$alltags = '';
								$htmlTags = '';

									$post = get_post($descriptionPostID);

									if (has_category()) {
										$tags = get_the_category();
										$htmlTags = '<div class="category-list metablock">';
										$htmlTags .= '<div class="legende">';
										$htmlTags .= __('Categories: ', 'opendoc');
										$htmlTags .= '</div>';
										$htmlTags .= '<div class="contenu">';


										foreach ( $tags as $tag ) {
											if( $tag->slug === 'non-classe') continue;

											$htmlTags .= "<span class='category-term' data-categorie='{$tag->slug}' data-categorieid='" . intval($tag->term_id)%6 . "'>";
											$htmlTags .= "{$tag->name}</span>";
											$alltags .= $tag->slug . " ";
											$hascategory = true;
										}
										$htmlTags .= '</div>';
										$htmlTags .= '</div>';
									}

									if( $descriptionPostID != -1)
										$description_content = get_the_content();

									if( has_post_thumbnail()) {
										$post_thumbnail_id = get_post_thumbnail_id( );
										$img = wp_get_attachment_image_src( $post_thumbnail_id, 'medium');
										$img = $img[0];
									}

								?>

								<?php if( isset($img) && !empty($img)) { ?>
									<a class="postThumbLink" href="<?php echo $term_link; ?>">
											<div class="headerImg" style="background-image: url(<?php echo $img; ?>)">
												<?php echo '<img src="' . $img . '">'; ?>
											</div>
									</a>
								<?php } ?>

									<div data-post="<?php the_ID(); ?>" <?php post_class(); ?> data-allcategories="<?php if( isset( $alltags)) echo $alltags; ?>" style="" data-isprojecteditor="<?php echo can_user_edit_this_project( $term_slug) ? 'true' : ''; ?>">

										<a href="<?php echo $term_link; ?>">
											<header class="headerProject">
													<?php
														echo '<h2 class="titreProjet">'.$term_name.'</h2>';
													?>
													<?php
														if( can_user_edit_this_project($term_slug)) {
															?>
															<svg class="icons edit-post" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve" data-toggle="tooltip" data-placement="left" title="<?php _e('Editor of this project', 'opendoc'); ?>" data-toggle-tooltip-color="#ef474b">
																	<g>
																		<circle fill="#EF474B" cx="32.2" cy="32.3" r="31.2"></circle>
																		<path fill="#293275" d="M51,20.2l-8.6-8.6L17.9,36.2l0,0l-4.3,12.9l12.9-4.3l0,0L51,20.2z M20.7,45.6L17.2,42l1.5-4.6l6.7,6.6
																			L20.7,45.6z"></path>
																	</g>
																</svg>
															<?php
														}
													?>
											</header>
										</a>

										<div class="entry-content">
											<?php
												if( isset( $description_content) && !empty($description_content)) {
													$content = apply_filters( 'the_content', $description_content );
													echo str_replace( ']]>', ']]&gt;', $content );
												}
											?>
										</div><!-- .entry-content -->

										<?php if( $lastPostDateHuman !== '' || isset($hascategory) ) { ?>
											<div class="entry-meta">
												<?php if( $lastPostDateHuman !== '') { ?>
													<time class="metablock half modDate edited" datetime="<?php echo get_the_modified_time('c'); ?>"
													data-toggle="tooltip" data-placement="top" title="<?php echo get_the_modified_time(''); ?>" data-toggle-tooltip-color="#3C3C3C">
														<div class="legende">
															<?php _e('Edited: ', 'opendoc'); ?>
														</div>
														<div class="contenu">
															<?php echo $lastPostDateHuman;?>
														</div>
													</time>
												<?php } ?>
												<?php if( $createdDateHuman !== '') { ?>
													<time class="metablock half createdDate added" datetime="<?php echo get_the_time('c'); ?>"
													data-toggle="tooltip" data-placement="top" title="<?php echo get_the_time(''); ?> " data-toggle-tooltip-color="#3C3C3C"
													>
														<div class="legende">
															<?php _e('Created: ', 'opendoc'); ?>
														</div>
														<div class="contenu">
															<?php echo $createdDateHuman;?>
														</div>
													</time>
												<?php } ?>


												<?php
													if( isset($hascategory)) {
														?>
													<?php echo $htmlTags; ?>
												<?php } ?>
											</div><!-- .entry-meta -->
										<?php
											unset( $hascategory);

										}

									wp_reset_postdata();

									?>
								</div><!-- .post -->
					 </div>
				</section>
			</div>
		<?php
		}
	}
	?>
	</div>
	</section>

	<button class="button open-filters">
  	<?php	_e("Filter", 'opendoc'); ?>
	</button>

</main>