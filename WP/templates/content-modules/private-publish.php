<?php
			  $ID = get_the_ID();
			  $status = get_post_status($ID);
?>
<div class="publish-private-post" data-status="<?php echo $status; ?>">
	<div class="mode-switcher private">
		<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
		<div class="icons">
			<svg class="fond" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
				<path fill="#F2682C" d="M19,37c9.9,0,18-8.1,18-18S28.9,1,19,1S1,9.1,1,19S9.1,37,19,37z"/>
			</svg>
			<svg class="flc flc-gauche" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve"

				data-toggle="tooltip" data-placement="bottom" title="<?php _e('Make this private', 'opendoc'); ?>" data-toggle-tooltip-color="#fbb41d"

				>

				<rect x="10" y="17.5" fill="#FBB41D" width="20" height="3"/>

				<rect x="6.8" y="13.1" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 35.1226 14.7814)" fill="#FBB41D" width="15.3" height="3.1"/>

				<rect x="6.8" y="21.8" transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 8.2525 50.0887)" fill="#FBB41D" width="15.3" height="3.1"/>
			</svg>
		</div>

	</div>
	<div class="mode-switcher publish">

		<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
		<div class="icons">
			<svg class="fond" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
				<path fill="#4CC0B4" d="M19,37c9.9,0,18-8.1,18-18S28.9,1,19,1S1,9.1,1,19S9.1,37,19,37z"/>
			</svg>

			<svg class="flc flc-droite" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve"

				data-toggle="tooltip" data-placement="bottom" title="<?php _e('Make this public', 'opendoc'); ?>" data-toggle-tooltip-color="#293275"

				>

				<rect x="8" y="17.5" fill="#293275" width="20" height="3" fill="#293275"/>
				<rect x="15.8" y="21.8" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -9.6175 23.4516)" fill="#293275" width="15.3" height="3.1"/>
				<rect x="15.8" y="13.1" transform="matrix(0.7071 0.7071 -0.7071 0.7071 17.2527 -12.3218)" fill="#293275" width="15.3" height="3.1"/>
			</svg>


		</div>

	</div>
</div>
