<?php

/**
 * Register the form and fields for our front-end submission form
 */
function select_authorized_user() {

	$prefix = '_opendoc_projectauthorizedbyuser_';

	$cmb_user_select = new_cmb2_box( array(
		'id'           => 'select_users',
		'object_types' => array( 'post' ),
		'hookup'       => false,
	) );


	$cmb_user_select->add_field( array(
		'name'     => __( 'Add users to this project', 'cmb2' ),
		'desc'     => __( 'field description (optional)', 'cmb2' ),
		'id'       => $prefix . 'multitaxonomy',
		'type'     => 'taxonomy_multicheck',
		'taxonomy' => 'post_tag', // Taxonomy Slug
	) );
}
add_action( 'cmb2_init', 'select_authorized_user' );

