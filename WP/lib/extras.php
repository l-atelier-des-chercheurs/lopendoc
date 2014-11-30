<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}
add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}
add_filter('wp_title', 'roots_wp_title', 10);



// custom typeface
function google_font(){
	echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>","\n";
}
add_action( 'wp_enqueue_scripts', 'google_font');



// Register Custom Taxonomy
function projet_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Projets', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Projet', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Projets', 'text_domain' ),
		'all_items'                  => __( 'Tous les projets', 'text_domain' ),
		'parent_item'                => __( 'Projet parent', 'text_domain' ),
		'parent_item_colon'          => __( 'Projet parent:', 'text_domain' ),
		'new_item_name'              => __( 'Nouveau projet', 'text_domain' ),
		'add_new_item'               => __( 'Ajouter un projet', 'text_domain' ),
		'edit_item'                  => __( 'Éditer un projet', 'text_domain' ),
		'update_item'                => __( 'Mettre à jour un projet', 'text_domain' ),
		'separate_items_with_commas' => __( 'Séparer les projets avec une virgule', 'text_domain' ),
		'search_items'               => __( 'Chercher les projets', 'text_domain' ),
		'add_or_remove_items'        => __( 'Ajouter ou enlever un objet', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choisir parmi les plus utilisés', 'text_domain' ),
		'not_found'                  => __( 'Pas trouvé', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'projets', array( 'post' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'projet_taxonomy', 0 );