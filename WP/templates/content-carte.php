<article class="post" data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

	<?php
		if ( is_user_logged_in() ) {
	?>
	<div class="button-right">
		<div class="edit-post">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
			<path d="M98,50C98,23.533,76.467,2,50,2S2,23.533,2,50c0,26.467,21.533,48,48,48S98,76.467,98,50z M59.508,50.692L41.876,68.325  c-0.191,0.191-0.441,0.287-0.692,0.287s-0.501-0.096-0.692-0.287c-0.383-0.383-0.383-1.003,0-1.385L57.431,50L40.491,33.06  c-0.383-0.383-0.383-1.002,0-1.385s1.002-0.383,1.385,0l17.632,17.633C59.891,49.69,59.891,50.31,59.508,50.692z"/>
			</svg>
		</div>

		<div class="publish-post">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
			<path d="M98,50C98,23.533,76.467,2,50,2S2,23.533,2,50c0,26.467,21.533,48,48,48S98,76.467,98,50z M59.508,50.692L41.876,68.325  c-0.191,0.191-0.441,0.287-0.692,0.287s-0.501-0.096-0.692-0.287c-0.383-0.383-0.383-1.003,0-1.385L57.431,50L40.491,33.06  c-0.383-0.383-0.383-1.002,0-1.385s1.002-0.383,1.385,0l17.632,17.633C59.891,49.69,59.891,50.31,59.508,50.692z"/>
			</svg>
		</div>
	</div>
	<?php
		}
	?>

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

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

</article>

