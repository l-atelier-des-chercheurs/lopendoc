<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>

	<script type="text/javascript">
		if( window.top !== window.self ) {
			document.getElementsByTagName('body')[0].className += ' iframe';
		}
		if(window.location.href.indexOf("fee=visible") > -1) {
			document.getElementsByTagName('body')[0].className += ' show-editor';
		}
		if(window.location.href.indexOf("type=newpost") > -1) {
			document.getElementsByTagName('body')[0].className += ' new-post';
		}
	</script>


  <?php
    do_action('get_header');
    get_template_part('templates/header');
  ?>

  <div class="wrap" role="document">
    <div class="content row">

      <main class="main" role="main">
        <?php include roots_template_path(); ?>
      </main><!-- /.main -->
      <?php
	      if (roots_display_sidebar()) : ?>
	        <aside class="sidebar" role="complementary">
						<div class="login">
	          	<?php include roots_sidebar_path(); ?>
						</div>
	        </aside>
      <?php
	      endif;
      ?>


    </div><!-- /.content -->
  </div><!-- /.wrap -->

  <?php get_template_part('templates/footer'); ?>

  <?php wp_footer(); ?>

  <?php get_template_part('templates/popover'); ?>
  <?php get_template_part('templates/popovergallerie'); ?>

</body>
</html>
