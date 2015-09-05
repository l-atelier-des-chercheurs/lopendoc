<?php
			  $ID = get_the_ID();
			  $status = get_post_status($ID);
?>
<section class="post" data-post="<?php $ID; ?>" <?php post_class(); ?> data-status="<?php echo $status; ?>" data-id="<?php echo $ID ?>" data-action="<?php echo esc_url(home_url('')); ?>/edit-page" data-singleurl="<?php echo esc_url( get_permalink() ); ?>">

<?php
if ( is_user_logged_in() ) {
	get_template_part('templates/content-modules/button-right');
}
?>
	<div class="entry-stuff">
		<div class="entry-meta">
			<?php get_template_part('templates/content-modules/meta'); ?>
		</div><!-- .entry-meta -->
		<div class="entry-title-and-content">
			<header class="entry-header">
			<?php
				the_title( '<h2 class="entry-title">', '</h2>' );
			?>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>

	</div>

</section>
