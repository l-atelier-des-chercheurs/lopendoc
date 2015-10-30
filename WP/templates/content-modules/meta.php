
<?php
	//$auteurs = get_the_author();
	$term = get_query_var( 'term' );

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

		if( count($contributeursName) > 0) :
			$htmlTags = '<div class="auteurs auteurs-list">';
			$htmlTags .= '<div class="legende">';
			if( count($contributeursName) == 1)
				$htmlTags .= __('Project contributor: ', 'opendoc');
			else
				$htmlTags .= __('Project contributors: ', 'opendoc');
			$htmlTags .= '</div>';
			$htmlTags .= '<div class="contenu">';

	 		foreach( $contributeursName as $contributeurName) {
				$htmlTags .= '<span class="auteur">';
				$htmlTags .= $contributeurName;
				$htmlTags .= '</span>';
	 		}

			$htmlTags .= '</div>';
			$htmlTags .= '</div>';
			echo $htmlTags;
		endif;

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

	if ($tags) {
		$htmlTags = '<div class="category-list">';
		$htmlTags .= '<div class="legende edit-me">';
		$htmlTags .= __('Categories: ', 'opendoc');
		$htmlTags .= '</div>';
		$htmlTags .= '<div class="contenu fee-categories">';

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

				data-toggle="tooltip" data-placement="right" title="' . __( 'Edit categories', 'opendoc')  . '" data-toggle-tooltip-color="#ef474b"

			>' . __( 'Edit', 'opendoc' ) . "</span>";
		}

		$htmlTags .= '</div>';
		$htmlTags .= '</div>';
	}
?>
<div class="categories edit-categories" data-allcategories="<?php echo $alltags; ?>">
<?php
	echo $htmlTags;
?>
</div>
<?php
	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
?>
<div class="comments-meta">

	<div class="legende">
		<?php _e('Comments: ', 'opendoc'); ?>
	</div>

	<div class="comments-link contenu">
		<?php comments_popup_link( __( 'Add a comment', 'opendoc' ), __( '1 comment', 'opendoc' ), __( '% comments', 'opendoc' ) ); ?>
	</div>

<!-- <div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></div> -->

</div>
<?php
	endif;
// 	edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
?>

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

<!-- 	data-toggle="tooltip" data-placement="top" title="<?php _e('Edited on ', 'opendoc'); echo get_the_modified_date('d/m/Y'); ?> " data-toggle-tooltip-color="#293275" -->

<!-- <p class="byline author vcard"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p> -->
