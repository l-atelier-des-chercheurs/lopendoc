<?php

  $ID = get_the_ID();
  $status = get_post_status($ID);

?>

<div class="postContainer">
<?php
	if ( is_user_logged_in() ) {
		?>
		<div class="publish-private-post" data-status="<?php echo $status; ?>">
			<div class="mode-switcher private">

				<div class="icons">
					<svg class="fond" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
						<path fill="#F2682C" d="M19,37c9.9,0,18-8.1,18-18S28.9,1,19,1S1,9.1,1,19S9.1,37,19,37z"/>
					</svg>
					<svg class="flc flc-gauche" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
						<rect x="10" y="17.5" fill="#FBB41D" width="20" height="3"/>

						<rect x="6.8" y="13.1" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 35.1226 14.7814)" fill="#FBB41D" width="15.3" height="3.1"/>

						<rect x="6.8" y="21.8" transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 8.2525 50.0887)" fill="#FBB41D" width="15.3" height="3.1"/>
					</svg>
				</div>
				Passer en privé

			</div>
			<div class="mode-switcher publish">

				<div class="icons">

					<svg class="fond" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
						<path fill="#4CC0B4" d="M19,37c9.9,0,18-8.1,18-18S28.9,1,19,1S1,9.1,1,19S9.1,37,19,37z"/>
					</svg>

					<svg class="flc flc-droite" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
						<rect x="8" y="17.5" fill="#293275" width="20" height="3" fill="#293275"/>
						<rect x="15.8" y="21.8" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -9.6175 23.4516)" fill="#293275" width="15.3" height="3.1"/>
						<rect x="15.8" y="13.1" transform="matrix(0.7071 0.7071 -0.7071 0.7071 17.2527 -12.3218)" fill="#293275" width="15.3" height="3.1"/>
					</svg>


				</div>

				Passer en public

			</div>
		</div>
		<?php
	}
?>

	<section class="post" data-post="<?php $ID; ?>" <?php post_class(); ?> data-status="<?php echo $status; ?>" data-id="<?php echo $ID ?>" data-action="<?php echo esc_url(home_url('')); ?>/edit-page" data-singleurl="<?php echo esc_url( get_permalink() ); ?>">

	<?php
	if ( is_user_logged_in() ) {


/*
        Visibility:
        <form id="update_post_visibility" name="update_post_visibility" method="post" action="<?php echo esc_url(home_url('')); ?>/edit-page">
            <input id="visibility-radio-public" type="radio" <?php if (('publish' === $status) && ! post_password_required($ID)) echo 'checked="checked" '; ?>value="public" name="visibility" />
            <label for="visibility-radio-public">Public</label>
            <br />
            <input id="visibility-radio-password" type="radio" <?php if (('publish' === $status) && post_password_required($ID)) echo 'checked="checked" '; ?>value="password" name="visibility">
            <label for="visibility-radio-password">Password:</label>
            <br />
            <input id="post_password" type="text" value="" name="post_password">
            <br />
            <input id="visibility-radio-private" type="radio" <?php if ('private' === $status) echo 'checked="checked" '; ?>value="private" name="visibility">
            <label for="visibility-radio-private">Private</label>
            <br />
            <input type="hidden" name="post_id" value="<?php echo $ID; ?>" />
            <input type="hidden" name="action" value="update_post_visibility" />
            <input id="submit" type="submit" value="Update" name="submit" />
        </form>
*/

		?>


	<?php get_template_part('templates/entry-button-right'); ?>

	<?php
	}
	?>

		<div class="entry-stuff">

			<div class="entry-meta">
				<?php get_template_part('templates/entry-meta'); ?>
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

</div>
