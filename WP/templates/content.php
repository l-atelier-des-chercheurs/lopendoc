<?php
	$ID = get_the_ID();
	$status = get_post_status($ID);
?>
<?php
	get_template_part('templates/content-carte');
?>
