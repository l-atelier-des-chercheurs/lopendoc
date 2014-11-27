<?php
/*
Template Name: Flux
*/

$getallposts = new WP_Query( array(
    'post_type'      => 'post', // set the post type to page
)); ?>



<div id="cropwindow">
	<div id="cropcontent">
		<?php

			global $wp_query;
			$wp_query->in_the_loop = true;

			while ($getallposts->have_posts()) : $getallposts->the_post() ?>
			<article data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

				<header class="entry-header">
					<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) ) : ?>
					<div class="entry-meta">
						<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
					</div>
					<?php
						endif;

						if ( is_single() ) :
							the_title( '<h1 class="entry-title">', '</h1>' );
						else :
							the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
						endif;
					?>

					<div class="entry-meta">
						<?php get_template_part('templates/entry-meta'); ?>
					</div><!-- .entry-meta -->

					<div class="entry-meta">
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php
						the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
					?>
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
