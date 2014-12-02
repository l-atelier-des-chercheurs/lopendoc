<?php
/*
Template Name: Flux
*/

if ( is_user_logged_in() ) {
 ?>

<div id="leftView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Documents</h3>
		</div>

		<button class="addPost">
			<h3>Nouveau post</h3>
		</button>


		<?php


			$getallposts = new WP_Query( array(
			    'post_type'      => 'post', // set the post type to page
			    'posts_per_page' => 15,
			));

			while ($getallposts->have_posts()) : $getallposts->the_post();

				get_template_part('templates/content', 'carte');

			endwhile; ?>
	</div>
</div>


<?php
	}
?>


<div id="rightView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
			<h3>Article</h3>
		</div>

		<?php


			$getallposts = new WP_Query( array(
			    'post_type'      => 'post', // set the post type to page
			    'post_status'      => 'publish', // set the post type to page
			    'posts_per_page' => 15,
			));

			while ($getallposts->have_posts()) : $getallposts->the_post();

				get_template_part('templates/content', 'carte');

			endwhile;
		?>

	</div>
</div>
