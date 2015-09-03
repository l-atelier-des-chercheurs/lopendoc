<?php
/*
Template Name: Flux
*/

$getallposts = new WP_Query( array(
    'post_type'      => 'post', // set the post type to page
    'posts_per_page' => -1,
)); ?>



<div id="leftView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Documents</h3>
		</div>

		<button class="addPost">
			<h3>+</h3>
		</button>

		<?php

			while ($getallposts->have_posts()) : $getallposts->the_post() ?>
			<article class="post" data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

				<div class="make-it-publish">
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
					<path d="M98,50C98,23.533,76.467,2,50,2S2,23.533,2,50c0,26.467,21.533,48,48,48S98,76.467,98,50z M59.508,50.692L41.876,68.325  c-0.191,0.191-0.441,0.287-0.692,0.287s-0.501-0.096-0.692-0.287c-0.383-0.383-0.383-1.003,0-1.385L57.431,50L40.491,33.06  c-0.383-0.383-0.383-1.002,0-1.385s1.002-0.383,1.385,0l17.632,17.633C59.891,49.69,59.891,50.31,59.508,50.692z"/>
					</svg>
				</div>

				<header class="entry-header">
					<?php
						if ( is_single() ) :
							the_title( '<h2 class="entry-title">', '</h2>' );
						else :
							the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
						endif;
					?>

					<div class="entry-meta">
						<?php get_template_part('templates/content-modules/entry-meta'); ?>
					</div><!-- .entry-meta -->

				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->

			</article>

		<?php endwhile; ?>
	</div>
</div>

<div id="rightView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Article</h3>
		</div>



	</div>
</div>
