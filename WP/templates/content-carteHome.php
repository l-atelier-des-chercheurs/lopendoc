<article class="post" data-post="<?php the_ID(); ?>" <?php post_class(); ?> style="">

	<header class="entry-header">
		<?php
  		the_title( '<h3 class="entry-title">', '</h3>' );
		?>

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

</article>

