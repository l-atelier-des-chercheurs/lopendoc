<footer class="content-info" role="contentinfo">
  <div class="container">
    <?php dynamic_sidebar('sidebar-footer'); ?>
  </div>
</footer>
<?php
if( current_user_can('manage_options')) {
	echo get_num_queries(); ?> queries in <?php timer_stop(1); ?> seconds.
<?php }
