<footer class="content-info" role="contentinfo">
  <div class="container">
    <?php dynamic_sidebar('sidebar-footer'); ?>
  </div>
</footer>
<?php
if ( is_user_logged_in() ) {
	global $current_user; get_currentuserinfo();
	if( $current_user->user_login === "louis") {
		echo "-" . get_num_queries() . "queries in - ";
		echo timer_stop() . " seconds";
	}
}
?>
