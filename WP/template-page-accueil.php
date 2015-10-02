<?php
/*
Template Name: Accueil avec cartes
*/
?>

<?php while (have_posts()) : the_post(); ?>
	<header id="presentationActions">
		<div class='colTitle'>
			<h1>
				<?php echo roots_title(); ?>
			</h1>
		</div>
		<div class="pageText">
			<?php the_content(); ?> <!-- Page Content -->
    </div>
	</header>
<?php endwhile; ?>
<?php wp_reset_query(); ?>

<main class="main" role="main">
	<!-- barre du haut de la page -->
	<div id="colonnesOptionsTopBar">

		<div class='sort-list'>
			<div class="legende">
		  	<?php	_e("sort projects by: ", 'opendoc'); ?>
			</div>
			<div class="contenu">
				<span class="sort-term" data-type="edited">
			  	<?php	_e("last modified", 'opendoc'); ?>
				</span>
				<span class="sort-term" data-type="created">
			  	<?php	_e("last created", 'opendoc'); ?>
				</span>
				<span class="sort-term" data-type="ab">
		  		<?php	_e("alphabetical", 'opendoc'); ?>
				</span>
			</div>
		</div>

		<div class="edit-all-project">

			<?php if (current_user_can( 'edit_posts')) { ?>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve" class="options add-project"

				  data-toggle="tooltip" data-placement="bottom" title="<?php _e('Add a project', 'opendoc'); ?>" data-toggle-tooltip-color="#ef474b"

					 >
					<polygon style="fill:#FCB421;" points="55.2,36.2 49.6,50.1 34,54.8 9,49.3 9,29.9 19.3,17.2 34.4,10 48.9,25.6 	"/>
					<path style="fill:#EF474B;" d="M10.2,10.3C-2,22.5-2,42.3,10.2,54.5c12.2,12.2,31.9,12.2,44.1,0c12.2-12.2,12.2-31.9,0-44.1
						C42.1-1.8,22.4-1.8,10.2,10.3z M50.2,28.5l0,7.8l-14,0l0,14h-7.8l0-14l-14,0l0-7.8l14,0l0-14l7.8,0l0,14H50.2z"/>
				</svg>
			<?php } ?>

			<!-- Generator: Adobe Illustrator 19.1.0, SVG Export Plug-In  -->
			<svg version="1.1"
				 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
				 x="0px" y="0px" viewBox="0 0 62.6 62.6" style="enable-background:new 0 0 62.6 62.6;"
				 xml:space="preserve" class="options open-search"

			  data-toggle="tooltip" data-placement="bottom" title="<?php _e('Search on l\'Opendoc', 'opendoc'); ?>" data-toggle-tooltip-color="#fcb421"

				 >
			<defs>
			</defs>
			<path style="fill:#FCB421;" d="M9.3,9.3c-12.2,12.2-12.2,31.9,0,44.1c12.2,12.2,31.9,12.2,44.1,0c12.2-12.2,12.2-31.9,0-44.1
				C41.3-2.9,21.5-2.9,9.3,9.3z"/>
			<g sketch:type="MSPage">
				<path sketch:type="MSShapeGroup" style="fill:#293275;" d="M33.6,38.7c-1.8,0.9-3.9,1.5-6.1,1.5c-7.4,0-13.4-6-13.4-13.4
					s6-13.4,13.4-13.4s13.4,6,13.4,13.4c0,2.8-0.8,5.4-2.3,7.5l9.4,9.4c0,0,1.6,1.6,0,3.2l-1.6,1.6c-1.6,1.6-3.2,0-3.2,0L33.6,38.7z"/>
			</g>
			<circle style="fill:#FCB421;" cx="27.5" cy="26.8" r="8.9"/>
			</svg>


		</div>
	</div>

	<!-- barre sur le côté de la page -->
	<div id="colonnesOptionsSidebar">

<!--
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
-->

		<div class="module-side category-list category-filters">
<!--
			<div class="legende">
		  	<?php	_e("Filter by categories: ", 'opendoc'); ?>
			</div>
-->
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
	    <button data-submitted="<?php  _e('Adding…', 'opendoc'); ?>">
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
			$descriptionTitle = '';

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
					$descriptionTitle = get_the_title();

				endwhile;
			}

	    $term_link = get_term_link($term->slug, $tax);
	    $term_name = $term->name;
	    $term_slug = $term->slug;
	    $term_count = $term->count;

	    // override de $term_name avec le titre de la description si elle existe
	    if( $descriptionTitle !== '')
	    	$term_name = $descriptionTitle;

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
												unset( $description_content);
											?>
										</div><!-- .entry-content -->

										<?php if( $lastPostDateHuman !== '' || isset($hascategory) ) { ?>
											<div class="entry-meta">
												<?php if( $term_count !== '') { ?>
													<div class="metablock">
														<div class="legende">
															<?php _e('Articles count:', 'opendoc'); ?>
														</div>
														<div class="contenu">
															<?php echo $term_count; ?>
														</div>
													</div>
												<?php } ?>
												<?php if( $lastPostDateHuman !== '') { ?>
													<div class="metablock half modDate edited"
													data-toggle="tooltip" data-placement="top" title="<?php echo get_the_modified_time(''); ?>" data-toggle-tooltip-color="#3C3C3C">
														<div class="legende">
															<?php _e('Edited: ', 'opendoc'); ?>
														</div>
														<time class="contenu" datetime="<?php echo get_the_modified_time('c'); ?>">
															<?php echo $lastPostDateHuman;?>
														</time>
													</div>
												<?php } ?>
												<?php if( $createdDateHuman !== '') { ?>
													<div class="metablock half createdDate added" datetime="<?php echo get_the_time('c'); ?>"
													data-toggle="tooltip" data-placement="top" title="<?php echo get_the_time(''); ?> " data-toggle-tooltip-color="#3C3C3C"
													>
														<div class="legende">
															<?php _e('Created: ', 'opendoc'); ?>
														</div>
														<time class="contenu">
															<?php echo $createdDateHuman;?>
														</time>
													</div>
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