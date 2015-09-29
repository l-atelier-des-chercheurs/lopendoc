<?php get_template_part('templates/head'); ?>

<body <?php body_class(); ?>>

<script>

	document.documentElement.className = document.documentElement.className.replace('no-js','js');

		if( window.top !== window.self ) {
			document.getElementsByTagName('body')[0].className += ' iframe';
		}
		if(window.location.href.indexOf("fee=visible") > -1) {
			document.getElementsByTagName('body')[0].className += ' show-editor';
		}
		if(window.location.href.indexOf("type=newpost") > -1) {
			document.getElementsByTagName('body')[0].className += ' new-post';
		}
		if(window.location.href.indexOf("type=newproject") > -1) {
			document.getElementsByTagName('body')[0].className += ' new-project';
		}
		if(window.location.href.indexOf("type=description") > -1) {
			document.getElementsByTagName('body')[0].className += ' is-description';
		}
	</script>

  <div class="thisGrid">
    <div class="thisContainer">
      <div class="thisRow">
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
        <div class="col blue col-md-1"></div>
      </div>
      <div class="thisRow">
      </div>
    </div>
  </div>

  <?php
    do_action('get_header');
    get_template_part('templates/header');
  ?>

	  <?php include roots_template_path(); ?>

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

  </div><!-- /.wrap -->

  <?php get_template_part('templates/footer'); ?>

  <?php wp_footer(); ?>

  <?php get_template_part('templates/popover'); ?>
  <?php get_template_part('templates/popovergallerie'); ?>

</body>
</html>
