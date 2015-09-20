<?php
  $ID = get_the_ID();
  $status = get_post_status($ID);
  $whoIsLockingID = is_post_lock_admin($ID);
  if( !empty( $whoIsLockingID)) {
		$whoIsLocking = get_user_meta( $whoIsLockingID, 'nickname', true);
	}
?>
<article class="post" id="<?php echo $post->post_name ?>" <?php post_class(); ?> data-status="<?php echo $status; ?>" data-id="<?php echo $ID ?>" data-action="<?php echo esc_url(home_url('')); ?>/edit-page" data-singleurl="<?php echo esc_url( get_permalink() ); ?>">

	<?php
	if ( user_can_edit() ) {
		get_template_part('templates/content-modules/button-right');
	}
	?>

	<div class="entry-stuff">

		<div class="entry-meta">
			<?php if( !empty($whoIsLockingID) ) { ?>
				<div class="is-editing">
					<div class="legende">
						<?php
							_e('Post currently edited by ', 'opendoc');
						?>
					</div>
					<div class="contenu">
						<?php echo $whoIsLocking; ?>
					</div>
				</div>
			<?php } ?>
			<?php get_template_part('templates/content-modules/meta'); ?>
		</div><!-- .entry-meta -->

		<div class="entry-title-and-content">
			<header class="entry-header">
			<?php
				the_title( '<h2 class="entry-title">', '</h2>' );
			?>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>

		<?php if( has_post_thumbnail() ) { ?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail('medium'); ?>
			</div>
		<?php } ?>

		<footer class="entry-footer">
			<?php
				if( isset( $_GET['comments'])) {
					$getcomments = $_GET['comments'];
					if( $getcomments === "show") {
						global $withcomments;
						$withcomments = true;
						comments_template('/templates/comments-modules/comments.php');
					}
				}
			?>
		</footer>

	</div>

</article>
