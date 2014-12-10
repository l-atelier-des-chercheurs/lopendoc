<?php
/*
 Page d'accueil : multi projets
*/
?>

<div id="wideView">

		<div class='colTitle'>
			<h1>Les Projets</h1>
		</div>

 <?php
	 $tax = 'projets';
 	 $tax_args = array(
 	 	 'orderby' => 'id',
 	 	 'order' => 'ASC'
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
					    'posts_per_page' => 5,
							'order' => 'DESC'
						);

						//  assigning variables to the loop
						$wp_query = new WP_Query($args);

						if ( $wp_query->have_posts() ) {

			?>

			<div class="colonneswrappers">
				<section  class="colonnes">
					<header class="page-header post">
							<?php
							    $term_link = get_term_link($term->slug, $tax);
							    $term_name = str_replace(', ', "</br>", $term->name);
								echo '<h2 class="titreProjet"><a href="'.$term_link.'">'.$term_name.'</a></h2>';
							?>
					</header>
					 <div class="colonnescontent">

						 <?php

							// The Loop
							while ($wp_query->have_posts()) : $wp_query->the_post();
								get_template_part('templates/content', 'carteHome');
							endwhile;


						?>
					 </div>
				 </section>
			 </div>
			 <?php

						}

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
