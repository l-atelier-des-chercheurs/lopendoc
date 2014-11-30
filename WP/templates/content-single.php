<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class('post'); ?>>
		<header class="entry-header">
      <h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-meta">
				<?php get_template_part('templates/entry-meta'); ?>
			</div><!-- .entry-meta -->
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
