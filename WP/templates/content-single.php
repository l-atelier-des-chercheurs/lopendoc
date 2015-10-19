<?php while (have_posts()) : the_post();
	$ID = get_the_ID();
	$status = get_post_status($ID);
?>

<?php if( has_tag('featured')) { ?>
	<div class="descriptionContainer" data-status="<?php echo $status; ?>">
<?php } else { ?>
	<div class="postContainer" data-status="<?php echo $status; ?>">
<?php } ?>
		<?php

		if ( user_can_edit() ) {
			get_template_part('templates/content-modules/private-publish');
		}
		get_template_part('templates/content-carte');
		?>
	</div>
<?php endwhile; ?>
