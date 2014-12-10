
<div id="centerView" class='viewport'>
	<div class="cropcontent">

		<div class='colTitle'>
		  <h1>
		    <?php echo roots_title(); ?>
		  </h1>
		</div>


	<?php
				if ( is_user_logged_in() ) {
	?>
			<button class="addPost">
				<h3>Nouveau post</h3>
			</button>
	<?php

			}

			while (have_posts()) : the_post();
				get_template_part('templates/content', 'carte');
			endwhile;

	?>

	  <nav class="post-nav">
	    <ul class="pager">
	      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
	      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
	    </ul>
	  </nav>

	</div>
</div>
