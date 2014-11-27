<?php
/*
Template Name: Flux
*/

$getallposts = new WP_Query( array(
    'post_type'      => 'post', // set the post type to page
)); ?>



<div id="leftView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Documents</h3>
		</div>

		<button class="addPost">
			<h3>Nouveau post</h3>
		</button>


		<?php

			global $wp_query;
			$wp_query->in_the_loop = true;


			while ($getallposts->have_posts()) : $getallposts->the_post() ?>
			<article class="post" data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

				<div class="make-it-publish">

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
						<?php get_template_part('templates/entry-meta'); ?>
					</div><!-- .entry-meta -->

					<div class="entry-meta">
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->

<!--
					<div class="content fee-group">
						<div class="fee-buttons"></div>
						<header class="name">
							<div data-auteur="<?php echo get_the_author_meta('ID')?>" class="meta">
								<auteur><?php echo get_the_author(); ?></auteur>
								<time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?></time>
							</div>
							<a title="Permanent Link to <?php the_title_attribute(); ?>" href="<?php the_permalink() ?>"><h2><?php the_title(); ?></h2></a>

						</header>
						<div class="entry-summary description">
							<?php the_content(); ?>
						</div>
					</div>
-->

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
