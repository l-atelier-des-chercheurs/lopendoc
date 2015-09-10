
<?php
	$tags = get_the_category();
	if ($tags) {
		$htmlTags = '<div class="category-list fee-categories">';
		$alltags = '';
		foreach ( $tags as $tag ) {
// 				$tag_link = get_category( $tag->term_id );

			$htmlTags .= "<span class='category-term' data-categorie='{$tag->slug}'>";
			$htmlTags .= "{$tag->name}</span>";
			$alltags .= $tag->slug . " ";
		}
		$htmlTags .= '</div>';
	}
?>

<div class="categories edit-categories" data-allcategories="<?php echo $alltags; ?>">
	<svg class="icons category-icon edit-me" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve"

				data-toggle="tooltip" data-placement="right" title="<?php _e('Edit this post\'s categories', 'opendoc'); ?>" data-toggle-tooltip-color="#f2682c"

				>
		<g>
			<path fill="#F2682C" d="M32.1,1.2c8.4,0,16.2,3.1,22.2,9c5.9,5.9,9.2,13.6,9.2,22c0,8.3-3.2,16.1-9.1,22c-5.9,5.8-13.7,9.1-22,9.1
				c-8.4,0-16.2-3.3-22.2-9.2C4.3,48.1,1,40.3,1,31.9V1.2H32.1z M17.1,21.9c3,0,5.4-2.4,5.4-5.4c0-3-2.4-5.4-5.4-5.4
				c-3,0-5.4,2.4-5.4,5.4C11.7,19.5,14.1,21.9,17.1,21.9z"/>
			<text transform="matrix(-1 0 0 1 49.0928 49.9068)" fill="#FCB421" font-family="'Rockwell-ExtraBold'" font-size="38.1519">#</text>
		</g>
	</svg>

	<?php
		echo $htmlTags;
	?>

</div>
<?php
	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
?>
<div class="comments is-disabled">
	<svg class="icons comment-icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 viewBox="0 0 64.5 64.5" enable-background="new 0 0 64.5 64.5" xml:space="preserve">
	<g>
		<path fill="#2B7EC2" d="M32.1,62.7c8.3,0,16.2-3.1,22.1-9c5.9-5.9,9.2-13.6,9.2-21.9c0-8.3-3.2-16.1-9-22c-5.9-5.8-13.7-9-22-9
			C24,0.8,16.2,4.1,10.3,10c-5.9,5.9-9.1,13.8-9.1,22.1v30.6H32.1z"/>
		<rect x="11.1" y="19.9" fill="#293275" width="42.5" height="5.6"/>
		<rect x="11.1" y="30.1" fill="#293275" width="42.5" height="5.6"/>
		<rect x="11.1" y="40.3" fill="#293275" width="42.5" height="5.6"/>
	</g>
	</svg>

	<div class="comments-link">
		<?php comments_popup_link( __( 'Add a comment', 'opendoc' ), __( '1 comment', 'opendoc' ), __( '% comments', 'opendoc' ) ); ?>
	</div>

<!-- <div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></div> -->

</div>
<?php
	endif;
// 	edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
?>

<time class="added" datetime="<?php echo get_the_time('c'); ?>"
	data-toggle="tooltip" data-placement="top" title="<?php _e('Edited on ', 'opendoc'); echo get_the_modified_date('d/m/Y'); ?> " data-toggle-tooltip-color="#293275"
><?php
	echo _e("Added on ", 'opendoc');
	echo get_the_date('d/m/Y'); ?>
</time>
<!-- <p class="byline author vcard"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p> -->

<?php
	$term_list = wp_get_post_terms( get_the_ID(), 'auteur');
	if( !empty($term_list) ) {
		echo '<div class="auteur">';
		echo (array_pop( $term_list ) -> name);
		echo '</div>';
	}
?>
