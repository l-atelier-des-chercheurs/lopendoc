<?php

/*
 * WARNING - WARNING - WARNING
 * Do not put any custom filter code in the Postie directory. The standard WordPress
 * upgrade process will delete your code.
 *
 * Instead copy filterPostie.php.sample to the wp-content directory and rename it
 * to filterPostie.php and edit to your hearts content.
 *
 * Another option is to create your own plugin or add this code to your theme.
 */

/*
 * Any filter function you write should accept one argument, which is the post
  array, which contains the following fields:
  'post_author'
  'comment_author'
  'comment_author_url'
  'user_ID'
  'email_author'
  'post_date'
  'post_date_gmt'
  'post_content'
  'post_title'
  'post_modified'
  'post_modified_gmt'
  'ping_status'
  'post_category'
  'tags_input'
  'comment_status'
  'post_name'
  'post_excerpt'
  'ID'
  'customImages'
  'post_status'

  Your function can modify any of these fields. It should then return the array
  back. If you return null then the post will not be created.
 */

/*
add_filter('postie_post', 'filter_title');
add_filter('postie_post', 'filter_content');
add_filter('postie_post', 'auto_tag');

add_filter('postie_filter_email', 'change_email');
*/
//add_filter('postie_post', 'add_custom_field');







/*

	Récupérer le plus après l'arobase

*/
add_filter('postie_filter_email2', 'plus_filter', 10, 3);

function plus_filter( $from, $toEmail, $replytoEmail) {
	global $project;
  DebugEcho("step-01");
  DebugDump("from " . $from);
  DebugDump("toEmail " . $toEmail);
  DebugDump("replytoEmail " . $replytoEmail);

  $fromField = $from;
  $toField = $toEmail;

  $posPlus = strpos($toField, '+');

  if ( $posPlus !== false ) {
    $toEmailFromPlus = substr($toField, $posPlus +1);

    $projectTerm = stristr( $toEmailFromPlus, '@', true );

		$project = $projectTerm;
	} else {


	}

  return $fromField;
}




/*

	Récupérer le nom de l'expéditeur avant le @

*/

add_filter('postie_filter_email2', 'get_mail_auteur', 10, 3);

function get_mail_auteur( $from, $toEmail, $replytoEmail) {
	global $auteur;
  DebugEcho("step-01b");
  DebugDump("from " . $from);
  DebugDump("toEmail " . $toEmail);
  DebugDump("replytoEmail " . $replytoEmail);

  $fromField = $from;
  $toField = $toEmail;

  $posAt = strpos($fromField, '@');

  if ( $posAt !== false ) {
    $auteur = substr($fromField, 0, $posAt);
	}

  return $fromField;
}






/*

	Si le sujet contient uniquement "Description"
	Alors passer le post en sticky

*/

function sticky_or_not( $post, $post_part_to_check, $isTerm) {

	DebugEcho("Check Description sticky");
	DebugEcho("--> post[post_part_to_check] " . $post[$post_part_to_check] );
	DebugEcho("--> isTerm " . $isTerm );

	if( stripos($post[$post_part_to_check], $isTerm) !== false ) {

		$post['post_status'] = 'publish';

		// A FAIRE : le passer en sticky //////////////////////////////////////////////
		array_push($post['tags_input'], 'featured');

		DebugEcho("Published / stickied");

	} else {

		$post['post_status'] = 'private';
		$post['meta_featured'] = 'false';
		DebugEcho("Private");

	}

	return $post;

}

function make_sticky_descriptions($post) {

	// Check for categories and use in type
	$post = sticky_or_not($post,'post_title','Description');
	return $post;

}
add_filter('postie_post_before', 'make_sticky_descriptions');

/*
	Check sujet si contient [projet]
*/

function check_in_mail( $post, $post_part_to_check, $isTerm) {
	global $project;

	EchoInfo("Check Description projet (#)");
  EchoInfo("--> post[post_part_to_check] " . $post[$post_part_to_check] );
  EchoInfo("--> isTerm " . $isTerm );

  EchoInfo("Found ? " . strpos($post[$post_part_to_check], $isTerm) );

	if( strpos($post[$post_part_to_check], $isTerm) !== false ) {

	  $posCrochet = strpos($post[$post_part_to_check], $isTerm) + 1;

    $toTitreFromCrochet = substr($post[$post_part_to_check], $posCrochet);

    $projectTerm = stristr( $toTitreFromCrochet, '#', true );

    $project = $projectTerm;

		DebugEcho("Trouvé un projet dans le sujet intitulé --- ");

		DebugEcho( $project );

    DebugEcho(("---"));

		$post['post_title'] = str_replace( '#' . $project . '#', '', $post[$post_part_to_check] );

	}
	return $post;
}

function check_other_project($post) {
	// Check for categories and use in type
	$post = check_in_mail($post,'post_title','#');
	return $post;
}

add_filter('postie_post_before', 'check_other_project');




/*

	Ajouter les termes au post


*/
add_filter('postie_post_after', 'tax_tag');

function tax_tag($post) {
	global $project;
	global $auteur;

  DebugEcho("step-02");
  DebugEcho("project " . $project );
  DebugEcho("auteur " . $auteur );

/*
  $projectField = array( 'projets' =>  array( $project ) );
  $post['tax_input'] = $projectField;
*/

	if( strlen( $project ) > 0 ) {
	  EchoInfo( "Ajout au post " . $post['ID'] );
	  EchoInfo( "Du projet " . $project );
	  EchoInfo( "##" . $project . "##" );
		wp_set_object_terms( $post['ID'], array($project), 'projets');
	  DebugEcho( "Vérification du post ---" );
	  DebugEcho( $post );
	}

	if( strlen( $auteur ) > 0 ) {
	  EchoInfo( "Ajout au post " . $post['ID'] );
	  EchoInfo( "De l'auteur " . $auteur );
	  EchoInfo( "--" . $auteur . "--" );
		wp_set_object_terms( $post['ID'], array($auteur), 'auteur');
	  DebugEcho( "Vérification du post ---" );
	  DebugEcho( $post );
	}

  return $post;
}








function filter_content($post) {
    //this function prepends a link to bookmark the category of the post
    $this_cat = get_the_category($post['ID']);
    //var_dump($this_cat);
    $link = '<a href="' . get_category_link($this_cat[0]->term_id) .
            '">Bookmark this category</a>' . "\n";
    $post['post_content'] = $link . $post['post_content'];
    return $post;
}

function filter_title($post) {
    //this function appends "(via postie)" to the title (subject)
    $post['post_title'] = $post['post_title'] . ' (via postie)';
    return $post;
}

function add_custom_field($post) {
    add_post_meta($post['ID'], 'postie', 'postie');
    return $post;
}

//changes the email to the admin email if the sender was from the example.com domain
function change_email($email) {
    if (stristr($email, '@example.com'))
        return get_option("admin_email");
    return $email;
}

function auto_tag($post) {
    // this function automatically inserts tags for a post
    $my_tags = array('cooking', 'latex', 'wordpress');
    foreach ($my_tags as $my_tag) {
        if (stripos($post['post_content'], $my_tag) !== false)
            array_push($post['tags_input'], $my_tag);
    }
    return $post;
}


?>
