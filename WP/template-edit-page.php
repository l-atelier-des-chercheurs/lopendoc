<?php
/*
Template Name: Edit page
*/
?>

edit page, rien à voir ici

<?php
    if ('POST' === $_SERVER['REQUEST_METHOD']
        && ! empty($_POST['action'])
        && 'update_post_visibility' === $_POST['action']
        && isset($_POST['post_id'])
    ) {
        $post = array();
        $post['ID'] = $_POST['post_id'];
        switch ($_POST['visibility']) {
            case 'private':
                $post['post_status'] = 'private';
                break;
            case 'publish':
                $post['post_status'] = 'publish';
                $post['post_password'] = '';
                break;
            case 'password':
                $post['post_status'] = 'publish';
                $post['post_password'] = $_POST['post_password'];
                break;
        }
        wp_update_post($post);
    }
?>