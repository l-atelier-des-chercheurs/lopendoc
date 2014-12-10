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
add_filter('postie_post', 'add_custom_field');
add_filter('postie_post', 'auto_tag');

add_filter('postie_filter_email', 'change_email');
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

  $posPlus = strpos($toField, '+') + 1;

  if ( $posPlus !== false ) {
    $toEmailFromPlus = substr($toField, $posPlus);

    $projectTerm = stristr( $toEmailFromPlus, '@', true );

		$project = $projectTerm;
	}

  return $fromField;
}

add_filter('postie_post', 'tax_tag');

function tax_tag($post) {
	global $project;
  DebugEcho("step-02");
  DebugEcho("project " . $project );

  $projectField = array( 'projets' =>  array( $project ) );
  $post['tax_input'] = $projectField;

  return $post;
}






/*

	Si le sujet contient uniquement "Description"
	Alors passer le post en sticky

*/

function sticky_or_not($post,$post_part_to_check,$isTerm) {

	DebugEcho("Check Description sticky");
  DebugEcho("post[post_part_to_check] " . $post[$post_part_to_check] );
  DebugEcho("isTerm " . $isTerm );

	if( strcasecmp($post[$post_part_to_check], $isTerm) == 0 ) {

		$post['post_status'] = 'publish';
		$post['is_sticky'] = 'true';

		DebugEcho("stickied");

    DebugEcho(("Post postie_post filter BEFORE"));
    DebugDump($post);

	}


/*
	foreach ($post[$post_part_to_check] as $key=>$cat_or_tag) {
		if (term_exists($cat_or_tag, $taxonomy_name)) {
			wp_set_object_terms( $post['ID'], $cat_or_tag, $taxonomy_name, true );
			unset($post[$post_part_to_check][$key]);
		}
	}
*/
		return $post;
}
function make_sticky_descriptions($post) {
	// Check for categories and use in type
	$post = sticky_or_not($post,'post_title','Description');
	return $post;
}
add_filter('postie_post_before', 'make_sticky_descriptions');









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
