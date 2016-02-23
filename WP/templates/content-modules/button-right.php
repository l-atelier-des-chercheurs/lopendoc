<div class="button-right">

	<svg class="icons edit-post" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve"

		 data-toggle="tooltip" data-placement="right" title="<?php _e('Toggle edit mode', 'opendoc'); ?>" data-toggle-tooltip-color="#ef474b"

		 >

		<g>
			<circle fill="#EF474B" cx="32.2" cy="32.3" r="31.2"/>
			<path fill="#293275" d="M51,20.2l-8.6-8.6L17.9,36.2l0,0l-4.3,12.9l12.9-4.3l0,0L51,20.2z M20.7,45.6L17.2,42l1.5-4.6l6.7,6.6
				L20.7,45.6z"/>
		</g>
	</svg>


	<svg class="icons is-disabled save-modifications" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve"
		 data-toggle="tooltip" data-placement="bottom" title="<?php _e('Accept modifications', 'opendoc'); ?>" data-toggle-tooltip-color="#fcb421"

		 >
<g id="fond">
	<path fill="#FCB421" d="M32.1,63.5c17.2,0,31.2-14,31.2-31.2S49.4,1,32.1,1S0.9,15,0.9,32.2S14.9,63.5,32.1,63.5z"/>
</g>
<g id="flc">
	<g>

			<rect x="9.7" y="34.7" transform="matrix(0.7071 0.7071 -0.7071 0.7071 33.6223 -4.0188)" fill="#FFFFFF" width="23.9" height="7.8"/>

			<rect x="19.1" y="29.9" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 88.3815 30.9903)" fill="#FFFFFF" width="37.4" height="7.8"/>
	</g>
</g>
	</svg>

	<?php
	if ( user_can_edit()) {
		?>

		<svg class="icons remove-post" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve"

			 data-toggle="tooltip" data-placement="right" title="<?php _e('Remove this post', 'opendoc'); ?>"  data-toggle-tooltip-color="#fcb421"

			 >

		<g>
			<polygon fill="#EF474B" points="11.4,21.9 24.9,11.4 50.2,13.9 51,32.4 51.4,47.6 34.2,56.9 16.1,52.2 8.5,32.4 	"/>
			<path fill="#FCB421" d="M32.3,1.2C15,1.2,1.1,15.2,1.1,32.4c0,17.2,14,31.2,31.2,31.2c17.2,0,31.2-14,31.2-31.2
				C63.5,15.2,49.5,1.2,32.3,1.2z M47.7,42.3l-5.5,5.5l-9.9-9.9l-9.9,9.9l-5.5-5.5l9.9-9.9l-9.9-9.9l5.5-5.5l9.9,9.9l9.9-9.9l5.5,5.5
				l-9.9,9.9L47.7,42.3z"/>
		</g>
		</svg>

		<?php
	}
	?>

<!--
	<img class="icons" src="<?php echo get_template_directory_uri(); ?>/assets/img/tags.svg">
	<img class="icons" src="<?php echo get_template_directory_uri(); ?>/assets/img/commentaire.svg">
-->
</div>
