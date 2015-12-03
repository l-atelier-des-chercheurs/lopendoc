<div class="popoverContent refreshMails">
	<h3>
		<?php _e('Fetch content by mail', 'opendoc'); ?>
	</h3>
	<p>
		<?php _e('Get content posted to ', 'opendoc');
		  $mailToContribute =  get_option( "mail_addressTC" );
		  if( !empty($term)) {
    		$mailToContribute = str_replace("leprojet", $term, $mailToContribute);
    	} else {
    		$mailToContribute = str_replace("+leprojet", "", $mailToContribute);
    	}
			echo "<a target='_blank' href='mailto:" . $mailToContribute . "'>" . $mailToContribute . "</a>";
		?>
	</p>

	<?php
?>
	<button type="button" class="submit-refreshPostie" data-submit="<?php _e('Update', 'opendoc'); ?>" data-submitted="<?php _e('Updating...', 'opendoc'); ?>">
		<?php _e('Update', 'opendoc'); ?>
	</button>
</div>

