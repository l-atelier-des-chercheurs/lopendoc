<?php

//* Password reset activation E-mail -> Body
add_filter( 'retrieve_password_message', 'wpse_retrieve_password_message', 10, 2 );
function wpse_retrieve_password_message( $message, $key ){
    $user_data = '';
    // If no value is posted, return false
    if( ! isset( $_POST['user_login'] )  ){
            return '';
    }
    // Fetch user information from user_login
    if ( strpos( $_POST['user_login'], '@' ) ) {

        $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    if( ! $user_data  ){
        return '';
    }
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    // Setting up message for retrieve password
    $message = "Vous avez demandé à renouveller votre mot de passe.";
    $message .= "<br/>";
    $message .= "Cliquez sur le lien suivant :";
    $message .= "<br/>";
    $message .= '<a href="';
    $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    $message .= '">';
    $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    $message .= '</a>"';

  	$message =
  		"<strong>" . __("This is a mail from l'Opendoc", 'opendoc') . " | " . get_bloginfo('name') . "</strong>" .
  		"<br/>" .
  		"<br/>" .
  		$message .
  		"<br/>" .
  		"<br/>" .
  		"<small>" . __("The team at l'Opendoc.", 'opendoc') . "</small>"
  		;


    return $message;
}






