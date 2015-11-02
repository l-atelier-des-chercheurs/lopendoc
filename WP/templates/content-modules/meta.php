<time class="metablock half createdDate added" datetime="<?php echo get_the_time('c'); ?>"
data-toggle="tooltip" data-placement="top" title="<?php echo get_the_time(''); ?> " data-toggle-tooltip-color="#293275"
>
	<div class="legende">
		<?php _e('Created: ', 'opendoc'); ?>
	</div>
	<div class="contenu">
		<?php	echo get_the_date('d/m/Y'); ?>
	</div>
</time>

<time class="metablock half modDate edited" datetime="<?php echo get_the_modified_time('c'); ?>"
data-toggle="tooltip" data-placement="top" title="<?php echo get_the_modified_time(''); ?> " data-toggle-tooltip-color="#293275"
>
	<div class="legende">
		<?php _e('Edited: ', 'opendoc'); ?>
	</div>
	<div class="contenu">
		<?php	echo get_the_modified_date('d/m/Y'); ?>
	</div>
</time>

<?php
	//$auteurs = get_the_author();
	$term = get_query_var( 'term' );
	if( empty($term)) {
		$term = array_pop(wp_get_object_terms( get_the_ID(), 'projets'))->slug;
	}

	// si on est sur le post "description", on veut les contributeurs du projet
	if( $term && has_tag('featured')) {
		$users = get_users('role=author');
		$contributeursName = array();
	  foreach ($users as $user) {
			$userID = $user->ID;
			$hasProject = get_user_meta( $userID, '_opendoc_user_projets', true );
			$userProjects = explode('|', $hasProject);
			$ifchecked = '';
			if( in_array( $term, $userProjects)) {
				$contributeursName[] = $user->display_name;
			}
		}

		$htmlTags = '<div class="auteurs auteurs-list">';

		if( count($contributeursName) > 0) :
			$htmlTags .= '<div class="legende">';
			if( count($contributeursName) == 1)
				$htmlTags .= __('Contributor: ', 'opendoc');
			else
				$htmlTags .= __('Contributors: ', 'opendoc');
			$htmlTags .= '</div>';
			$htmlTags .= '<div class="contenu">';

	 		foreach( $contributeursName as $contributeurName) {
				$htmlTags .= '<span class="auteur">';
				$htmlTags .= $contributeurName;
				$htmlTags .= '</span>';
	 		}

			$htmlTags .= '</div>';
		endif;
		if( user_can_edit()):
			$htmlTags .= '

				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 64.5 64.5" style="enable-background:new 0 0 64.5 64.5;" xml:space="preserve"

					class="options edit-authors"
				  data-toggle="tooltip" data-placement="bottom" title="' . __('Edit project contributors', 'opendoc') . '" data-toggle-tooltip-color="#fcb421"
					 >
				<g id="fond">
					<polygon style="fill:#FCB421;" points="55.2,36.2 49.6,50.1 34,54.8 9,49.3 9,29.9 19.3,17.2 34.4,10 48.9,25.6 	"/>
					<path style="fill:#FCB421;" d="M32.1,63.5c17.2,0,31.2-14,31.2-31.2S49.4,1,32.1,1S0.9,15,0.9,32.2S14.9,63.5,32.1,63.5z"/>
				</g>
				<g id="flc">
					<g>
						<path style="fill:#293275;" d="M40.9,39.6c-1-1.6-2.3-2.7-4.1-3.3c0,0-1.4-0.7-5-0.7c-3.6,0-5,0.7-5,0.7c-1.8,0.6-3.1,1.7-4.1,3.3
							c-0.4,0.6-0.6,1.9-0.7,2.6c0,0.2,0,0.4,0,0.6V44v-0.8c0,1.3,0.9,2.4,2,2.4h15.5c1.1,0,2-1.1,2-2.4V44v-1.2c0-0.2,0-0.4,0-0.6
							C41.6,41.6,41.4,40.3,40.9,39.6 M26.4,26.7c0,3,1.9,7.5,5.5,7.5c3.5,0,5.5-4.5,5.5-7.5s-2.4-5.5-5.5-5.5
							C28.8,21.2,26.4,23.6,26.4,26.7"/>
						<g>
							<path style="fill:#293275;" d="M25.8,35c-0.9-1.1-1.9-1.9-3.3-2.3c0,0-1.3-0.7-4.6-0.7c-3.3,0-4.6,0.7-4.6,0.7
								c-1.7,0.6-2.9,1.5-3.9,3.1c-0.4,0.6-0.6,1.8-0.6,2.4c0,0.2,0,0.4,0,0.6V39c0,1.2,0.8,2.2,1.9,2.2H20c0.1-0.8,0.4-1.6,0.7-2.1
								C22,37.1,23.6,35.7,25.8,35z"/>
							<path style="fill:#293275;" d="M17.9,30.6c3.3,0,5.1-4.2,5.1-7c0-2.8-2.3-5.1-5.1-5.1c-2.8,0-5.1,2.3-5.1,5.1
								C12.7,26.4,14.5,30.6,17.9,30.6z"/>
						</g>
						<g>
							<path style="fill:#293275;" d="M37.9,35c0.9-1.1,1.9-1.9,3.3-2.3c0,0,1.3-0.7,4.6-0.7c3.3,0,4.6,0.7,4.6,0.7
								c1.7,0.6,2.9,1.5,3.9,3.1c0.4,0.6,0.6,1.8,0.6,2.4c0,0.2,0,0.4,0,0.6V39c0,1.2-0.8,2.2-1.9,2.2h-9.3c-0.1-0.8-0.4-1.6-0.7-2.1
								C41.7,37.1,40.1,35.7,37.9,35z"/>
							<path style="fill:#293275;" d="M45.9,30.6c-3.3,0-5.1-4.2-5.1-7c0-2.8,2.3-5.1,5.1-5.1c2.8,0,5.1,2.3,5.1,5.1
								S49.2,30.6,45.9,30.6z"/>
						</g>
					</g>
				</g>
				</svg>';
		endif;

		$htmlTags .= '</div>';
		echo $htmlTags;
	}	else {
		// sinon, on veut les éditeurs du post
		$allAuteurs = get_the_terms( get_the_ID(), 'auteur' );
		if ( $allAuteurs && ! is_wp_error( $allAuteurs ) ) :
			$auteursName = array();
			foreach ( $allAuteurs as $auteur ) {
				// $auteursName[] = get_user_by( 'id', $auteur->name)->display_name;
				$auteurName = $auteur->name;
				if( !is_numeric($auteurName))
					$auteursName[] = $auteurName;
			}
			if( count($auteursName) > 0) :
				//echo join( ", ", $auteursName );

				$htmlTags = '<div class="auteurs auteurs-list">';
				$htmlTags .= '<div class="legende">';
				if( count($auteursName) == 1)
					$htmlTags .= __('Author: ', 'opendoc');
				else
					$htmlTags .= __('Authors: ', 'opendoc');
				$htmlTags .= '</div>';
				$htmlTags .= '<div class="contenu">';

		 		foreach( $auteursName as $auteurName) {
					$htmlTags .= '<span class="auteur">';
					$htmlTags .= $auteurName;
					$htmlTags .= '</span>';
		 		}

				$htmlTags .= '</div>';
				$htmlTags .= '</div>';
				echo $htmlTags;
			endif;
		endif;
	}
?>

<?php

	$tags = get_the_category();

	$hasTags = false;
	foreach ( $tags as $tag ) {
		if( !$tag->slug === 'non-classe') {
			$hasTags = true;
			break;
		}
	}

	if ( $hasTags || user_can_edit()) {
		$htmlTags = '<div class="category-list">';
/*
		$htmlTags .= '<div class="legende edit-me">';
		$htmlTags .= __('Tags: ', 'opendoc');
		$htmlTags .= '</div>';
*/
		$htmlTags .= '<div class="fee-categories">';

		$alltags = '';
		foreach ( $tags as $tag ) {
			if( $tag->slug === 'non-classe') continue;

			$htmlTags .= "<span class='category-term' data-categorie='{$tag->slug}' data-categorieid='" . intval($tag->term_id)%6 . "'>";
			$htmlTags .= "{$tag->name}</span>";
			$alltags .= $tag->slug . " ";
		}

		if( user_can_edit()) {
		// bouton édit
			$htmlTags .= '<span class="button-edit-categories"
			>' . __( 'Edit categories', 'opendoc' ) . "</span>";
		}

		$htmlTags .= '</div>';
		$htmlTags .= '</div>';
		?>

		<div class="categories edit-categories" data-allcategories="<?php echo $alltags; ?>">
		<?php
			echo $htmlTags;
		?>
		</div>
		<?php
	}
?>



<?php
	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
?>
<div class="comments-meta">

<!--
	<div class="legende">
		<?php _e('Comments: ', 'opendoc'); ?>
	</div>
-->

	<div class="comments-link">
		<?php comments_popup_link( __( 'Add a comment', 'opendoc' ), __( '1 comment', 'opendoc' ), __( '% comments', 'opendoc' ) ); ?>
	</div>

<!-- <div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></div> -->

</div>
<?php
	endif;
// 	edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
?>

<!-- 	data-toggle="tooltip" data-placement="top" title="<?php _e('Edited on ', 'opendoc'); echo get_the_modified_date('d/m/Y'); ?> " data-toggle-tooltip-color="#293275" -->

<!-- <p class="byline author vcard"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p> -->
