<?php
/*
Template Name: Flux
*/
?>

<div id="centerView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Flux</h3>
		</div>


	<?php
				if ( is_user_logged_in() ) {
	?>
			<button class="addPost">
				<h3>Nouveau post</h3>
			</button>
	<?php
			}

			if ( is_user_logged_in() ) {
				$getallposts = new WP_Query( array(
				    'post_type'      => 'post', // set the post type to page
				    'posts_per_page' => 15,
						'post_status'      => 'publish, private', // set the post type to page
				));
			} else {
				$getallposts = new WP_Query( array(
				    'post_type'      => 'post', // set the post type to page
				    'posts_per_page' => 15,
						'post_status'      => 'publish', // set the post type to page
				));
			}

			while ($getallposts->have_posts()) : $getallposts->the_post();

				get_template_part('templates/content', 'carte');

			endwhile; ?>
	</div>
</div>
