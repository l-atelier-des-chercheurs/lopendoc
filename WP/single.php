<?php
	$terms = wp_get_post_terms( get_the_ID(), 'projets');
	$term = array_pop( $terms);
?>

<div class='colTitle'>
	<h1 class="entry-title">
		<?php
			if( has_term('projets') )
				echo '<a href="' . get_term_link($term->slug, 'projets') . '">' . $term->name . '</a>,';
			?>
	</h1>
</div>


<?php get_template_part('templates/content', 'single'); ?>
