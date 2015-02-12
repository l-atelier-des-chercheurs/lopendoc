<header class="banner navbar navbar-default navbar-fixed-top" role="banner" style="">
  <div class="navbar-container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <?php
	      $primaireColor =  get_option( "primary_color" );
	      if( empty($primaireColor) ) $primaireColor = "#EF444E";
	      $secondaireColor =  get_option( "secondary_color" );
	      if( empty($secondaireColor) ) $secondaireColor =  "#FCB248";
	    ?>

      <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
           viewBox="0 0 820.6 183.9" enable-background="new 0 0 820.6 183.9" xml:space="preserve">
           <g class="forme-opaque">
            <rect x="417.8" y="48.9" fill="<?php echo $secondaireColor; ?>" width="87.3" height="87.3"/>
            <rect x="567.6" y="7.4" fill="<?php echo $secondaireColor; ?>" width="44.5" height="128.6"/>
            <circle fill="<?php echo $secondaireColor; ?>" cx="155.6" cy="92.6" r="44.5"/>
            <circle fill="<?php echo $secondaireColor; ?>" cx="670.4" cy="92.6" r="44.5"/>
            <rect x="212.5" y="48.5" fill="<?php echo $secondaireColor; ?>" width="44.5" height="128.6"/>
            <rect x="6.2" y="6.9" fill="<?php echo $secondaireColor; ?>" width="44.5" height="128.6"/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="400.7,49.3 357,92.9 313.4,49.3 "/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="313.4,136.6 357,92.9 400.7,136.6 "/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="814.7,49.3 771,92.9 727.4,49.3 "/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="727.4,136.6 771,92.9 814.7,136.6 "/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="727.4,49.3 771,92.9 727.4,136.6 "/>
            <polyline fill="<?php echo $secondaireColor; ?>" points="313.4,49.3 357,92.9 313.4,136.6 "/>
           </g>
           <g class="forme-alpha">
            <polyline opacity="0.8" fill="<?php echo $primaireColor; ?>" points="727.4,49.3 771,92.9 727.4,136.6 "/>
            <polygon opacity="0.8" fill="<?php echo $primaireColor; ?>" points="63.5,7.6 84.8,50 105.9,7.5 "/>
            <polyline opacity="0.8" fill="<?php echo $primaireColor; ?>" points="417.8,48.9 479.5,48.9 479.5,110.6 "/>
            <path opacity="0.8" fill="<?php echo $primaireColor; ?>" d="M612.1,91.6c0,24.6-19.9,44.5-44.5,44.5c-24.6,0-44.5-19.9-44.5-44.5
              c0-24.5,19.9-44.5,44.5-44.5C592.2,47.2,612.1,67.1,612.1,91.6z"/>
            <path opacity="0.8" fill="<?php echo $primaireColor; ?>" d="M301.4,92.4c0,24.6-19.9,44.5-44.5,44.5c-24.6,0-44.5-19.9-44.5-44.5
              c0-24.5,19.9-44.5,44.5-44.5C281.5,47.9,301.4,67.8,301.4,92.4z"/>
            <polyline opacity="0.8" fill="<?php echo $primaireColor; ?>" points="67.9,135.6 6.2,135.6 6.2,73.9 "/>
            <circle opacity="0.8" fill="<?php echo $primaireColor; ?>" cx="670.4" cy="92" r="22.2"/>
            <circle opacity="0.8" fill="<?php echo $primaireColor; ?>" cx="155.6" cy="92" r="22.2"/>
            <polygon opacity="0.8" fill="<?php echo $primaireColor; ?>" points="313.4,136.6 400.7,92.9 313.4,49.3 "/>
           </g>
        </svg>
        <div class="lopendocInstance">
					<?php echo custom_bloginfo(); ?>
        </div>
      </a>
    </div>

    <nav class="collapse navbar-collapse" role="navigation">

			<div class="actionsContainer">
				<ul class="action-button actions">
					<li class="button switch-edition">
						Mode édition
					</li>
					<li class="button refresh-postie">
						Rafraîchir
					</li>

				<?php
					if ( !is_user_logged_in() ) {
						?>
					<li class="button login-field">
						Inscription / Connexion
					</li>
				<?php
					} else {
				?>
					<li class="button deconnexion-field">
						Déconnexion
					</li>

				<?php
					}
				?>


				</ul>

<!--
      <?php
        if (has_nav_menu('primary_navigation')) :
          wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav'));
        endif;
      ?>
-->
			</div>
    </nav>
  </div>
</header>
