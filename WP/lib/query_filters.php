<?php

// bloquer l'accès aux medias médias des autres utilisateurs, sauf pour ceux qui viennent de dodoc
add_filter( 'posts_where', 'hide_attachments_wpquery_where' );
function hide_attachments_wpquery_where( $where ){
	global $current_user;
	if( !current_user_can( 'manage_options' ) ) {
		if( is_user_logged_in() ){
			if( isset( $_POST['action'] ) ){
				// library query
				if( $_POST['action'] == 'query-attachments' ){

					// id du compte Dodoc : 189
					$where .= ' AND (post_author='.$current_user->data->ID.' OR post_author=189)';
//					$where .= ' AND post_author='.$current_user->data->ID;
				}
			}
		}
	}

	return $where;
}

// hide private posts http://stackoverflow.com/a/1016137
add_filter('posts_where', 'no_privates');
function no_privates($where) {
    if( current_user_can( 'edit_posts') ) return $where;
    global $wpdb;
    return " $where AND {$wpdb->posts}.post_status != 'private' ";
}



// enlever le Privé
function the_title_trim($title) {
	// Might aswell make use of this function to escape attributes
	$title = esc_attr($title);
	// What to find in the title
	$findthese = array(
		'#Protégé:#', // # is just the delimeter
		'#Privé : #'
	);
	// What to replace it with
	$replacewith = array(
		'',
		'' // What to replace private with
	);
	// Items replace by array key
	$title = preg_replace($findthese, $replacewith, $title);
	return $title;
}
add_filter('the_title', 'the_title_trim');
add_filter('private_title_format', 'blank');
function blank($title) {
       return '%s';
}
