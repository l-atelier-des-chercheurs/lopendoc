<?php
/*
  $Id: postie-functions.php 1025546 2014-11-14 06:40:07Z WayneAllen $
 */

//to turn on debug output add the following line to wp-config.php
//define('POSTIE_DEBUG', true);

class PostiePostModifiers {

    var $PostFormat = 'standard';

    function apply($postid) {

        if ($this->PostFormat != 'standard') {
            set_post_format($postid, $this->PostFormat);
        }
    }

}

if (!function_exists('mb_str_replace')) {

    function mb_str_replace($search, $replace, $subject, &$count = 0) {
        if (!is_array($subject)) {
            // Normalize $search and $replace so they are both arrays of the same length
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = array_pad(is_array($replace) ? array_values($replace) : array($replace), count($searches), '');

            foreach ($searches as $key => $search) {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        } else {
            // Call mb_str_replace for each subject in array, recursively
            foreach ($subject as $key => $value) {
                $subject[$key] = mb_str_replace($search, $replace, $value, $count);
            }
        }

        return $subject;
    }

}

function postie_environment() {
    DebugEcho("Postie Version: " . POSTIE_VERSION);
    DebugEcho("Wordpress Version: " . get_bloginfo('version'));
    DebugEcho("PHP Version: " . phpversion());
    DebugEcho("OS: " . php_uname());
    DebugEcho("Debug mode: " . (IsDebugMode() ? "On" : "Off"));
    DebugEcho("Time: " . date('Y-m-d H:i:s', time()) . " GMT");
    DebugEcho("Error log: " . ini_get('error_log'));

    if (isMarkdownInstalled()) {
        EchoInfo("You currently have the Markdown plugin installed. It will cause problems if you send in HTML email. Please turn it off if you intend to send email using HTML.");
    }

    if (!isPostieInCorrectDirectory()) {
        EchoInfo("Warning! Postie expects to be in its own directory named postie.");
    } else {
        EchoInfo("Postie is in " . plugin_dir_path(__FILE__));
    }

    if (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON) {
        EchoInfo("Alternate cron is enabled");
    }

    if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
        EchoInfo("WordPress cron is disabled. Postie will not run unless you have an external cron set up.");
    }

    EchoInfo("Cron: " . (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON === true ? "Of" : "On"));
    EchoInfo("Alternate Cron: " . (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON === true ? "On" : "Off"));

    if (defined('WP_CRON_LOCK_TIMEOUT') && WP_CRON_LOCK_TIMEOUT === true) {
        EchoInfo("Cron lock timeout is:" . WP_CRON_LOCK_TIMEOUT);
    }

    if (HasIconvInstalled()) {
        EchoInfo("iconv: installed");
    } else {
        EchoInfo("Warning! Postie requires that iconv be enabled.");
    }

    if (function_exists('imap_mime_header_decode')) {
        EchoInfo("imap: installed");
    } else {
        EchoInfo("Warning! Postie requires that imap be enabled if you are using IMAP, IMAP-SSL or POP3-SSL.");
    }

    if (HasMbStringInstalled()) {
        EchoInfo("mbstring: installed");
    } else {
        EchoInfo("Warning! Postie requires that mbstring be enabled.");
    }
}

function postie_disable_revisions($restore = false) {
    global $_wp_post_type_features, $_postie_revisions;

    if (!$restore) {
        $_postie_revisions = false;
        if (isset($_wp_post_type_features['post']) && isset($_wp_post_type_features['post']['revisions'])) {
            $_postie_revisions = $_wp_post_type_features['post']['revisions'];
            unset($_wp_post_type_features['post']['revisions']);
        }
    } else {
        if ($_postie_revisions) {
            $_wp_post_type_features['post']['revisions'] = $_postie_revisions;
        }
    }
}

/* this function is necessary for wildcard matching on non-posix systems */
if (!function_exists('fnmatch')) {

    function fnmatch($pattern, $string) {
        $pattern = strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'));
        return preg_match('/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')) . '$/i', $string);
    }

}

function LogInfo($v) {
    error_log("Postie: $v");
}

function EchoInfo($v) {
    if (php_sapi_name() == "cli") {
        print( "$v\n");
    } else {
        //flush the buffers
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        print( "<pre>" . htmlspecialchars($v) . "</pre>\n");
    }
    LogInfo($v);
}

function DebugDump($v) {
    if (IsDebugMode()) {
        $o = print_r($v, true);
        if (php_sapi_name() == "cli") {
            print( "$o\n");
        } else {
            //flush the buffers
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            print( "<pre>\n");
            EchoInfo($o);
            print( "</pre>\n");
        }
    }
}

function DebugEcho($v) {
    if (IsDebugMode()) {
        EchoInfo($v);
    }
}

function tag_Date(&$content, $message_date) {
    //don't apply any offset here as it is accounted for later
    $html = LoadDOM($content);
    if ($html !== false) {
        $es = $html->find('text');
        DebugEcho("tag_Date: html " . count($es));
        foreach ($es as $e) {
            //DebugEcho("tag_Date: ".trim($e->plaintext));
            $matches = array();
            if (1 === preg_match("/^date:\s?(.*)$/im", trim($e->plaintext), $matches)) {
                DebugEcho("tag_Date: found date tag $matches[1]");
                $newdate = strtotime($matches[1]);
                if (false !== $newdate) {
                    $t = date("H:i:s", $newdate);
                    DebugEcho("tag_Date: original time: $t");

                    $format = "Y-m-d";
                    if ($t != '00:00:00') {
                        $format.= " H:i:s";
                    }
                    $message_date = date($format, $newdate);
                    $content = str_replace($matches[0], '', $content);
                    break;
                }
            }
        }
    } else {
        DebugEcho("tag_Date: not html");
    }
    return $message_date;
}

function CreatePost($poster, $project, $mimeDecodedEmail, $post_id, &$is_reply, $config, $postmodifiers) {

    $fulldebug = IsDebugMode();
    $fulldebugdump = false;

    extract($config);

    $attachments = array(
        "html" => array(), //holds the html for each image
        "cids" => array(), //holds the cids for HTML email
        "image_files" => array() //holds the files for each image
    );

    if (array_key_exists('message-id', $mimeDecodedEmail->headers)) {
        DebugEcho("Message Id is :" . htmlentities($mimeDecodedEmail->headers["message-id"]));
        if ($fulldebugdump) {
            DebugDump($mimeDecodedEmail);
        }
    }

    filter_PreferedText($mimeDecodedEmail, $config['prefer_text_type']);
    if ($fulldebugdump) {
        DebugDump($mimeDecodedEmail);
    }

    $content = GetContent($mimeDecodedEmail, $attachments, $post_id, $poster, $config);
    if ($fulldebug) {
        DebugEcho("the content is $content");
    }

    $subject = GetSubject($mimeDecodedEmail, $content, $config);

    filter_RemoveSignature($content, $config);
    if ($fulldebug) {
        DebugEcho("post sig: $content");
    }

    $post_excerpt = tag_Excerpt($content, $config);
    if ($fulldebug) {
        DebugEcho("post excerpt: $content");
    }

    $postAuthorDetails = getPostAuthorDetails($subject, $content, $mimeDecodedEmail);
    if ($fulldebug) {
        DebugEcho("post author: $content");
    }

    $message_date = NULL;
    if (array_key_exists("date", $mimeDecodedEmail->headers) && !empty($mimeDecodedEmail->headers["date"])) {
        $cte = "";
        $cs = "";
        if (property_exists($mimeDecodedEmail, 'content-transfer-encoding') && array_key_exists('content-transfer-encoding', $mimeDecodedEmail->headers)) {
            $cte = $mimeDecodedEmail->headers["content-transfer-encoding"];
        }
        if (property_exists($mimeDecodedEmail, 'ctype_parameters') && array_key_exists('charset', $mimeDecodedEmail->ctype_parameters)) {
            $cs = $mimeDecodedEmail->ctype_parameters["charset"];
        }
        $message_date = HandleMessageEncoding($cte, $cs, $mimeDecodedEmail->headers["date"], $message_encoding, $message_dequote);
    }
    $message_date = tag_Date($content, $message_date);

    list($post_date, $post_date_gmt, $delay) = filter_Delay($content, $message_date, $config['time_offset']);
    if ($fulldebug) {
        DebugEcho("post date: $content");
    }

    filter_Ubb2HTML($content);
    if ($fulldebug) {
        DebugEcho("post ubb: $content");
    }

    $post_categories = tag_Categories($subject, $config['default_post_category'], $config['category_match']);
    if ($fulldebug) {
        DebugEcho("post category: $content");
    }

    $post_tags = tag_Tags($content, $config['default_post_tags']);
    if ($fulldebug) {
        DebugEcho("post tag: $content");
    }

    $comment_status = tag_AllowCommentsOnPost($content);
    if ($fulldebug) {
        DebugEcho("post comment: $content");
    }

    $post_status = tag_Status($content, $post_status);
    if ($fulldebug) {
        DebugEcho("post status: $content");
    }

    if ($config['converturls']) {
        $content = filter_Videos($content, $config['shortcode']); //videos first so linkify doesn't mess with them
        if ($fulldebug) {
            DebugEcho("post video: $content");
        }

        $content = filter_Linkify($content);
        if ($fulldebug) {
            DebugEcho("post linkify: $content");
        }
    }

    filter_VodafoneHandler($content, $attachments);
    if ($fulldebug) {
        DebugEcho("post vodafone: $content");
    }

    filter_ReplaceImageCIDs($content, $attachments, $config);
    if ($fulldebug) {
        DebugEcho("post cid: $content");
    }

    $customImages = tag_CustomImageField($content, $attachments, $config);
    if ($fulldebug) {
        DebugEcho("post custom: $content");
    }

    $post_type = tag_PostType($subject, $postmodifiers, $config);
    if ($fulldebug) {
        DebugEcho("post type: $content");
    }

    $id = GetParentPostForReply($subject);
    if (empty($id)) {
        DebugEcho("Not a reply");
        $id = $post_id;
        $is_reply = false;
        if ($config['add_meta'] == 'yes') {
            DebugEcho("Adding meta");
            if ($config['wrap_pre'] == 'yes') {
                DebugEcho("Adding <pre>");
                $content = $postAuthorDetails['content'] . "<pre>\n" . $content . "</pre>\n";
                $content = "<pre>\n" . $content . "</pre>\n";
            } else {
                $content = $postAuthorDetails['content'] . $content;
                $content = $content;
            }
        } else {
            if ($config['wrap_pre'] == 'yes') {
                DebugEcho("Adding <pre>");
                $content = "<pre>\n" . $content . "</pre>\n";
            }
        }
    } else {
        DebugEcho("Reply detected");
        $is_reply = true;
        // strip out quoted content
        $lines = explode("\n", $content);
        $newContents = '';
        foreach ($lines as $line) {
            if (preg_match("/^>.*/i", $line) == 0 &&
                    preg_match("/^(from|subject|to|date):.*?/i", $line) == 0 &&
                    preg_match("/^-+.*?(from|subject|to|date).*?/i", $line) == 0 &&
                    preg_match("/^on.*?wrote:$/i", $line) == 0 &&
                    preg_match("/^-+\s*forwarded\s*message\s*-+/i", $line) == 0) {
                $newContents.="$line\n";
            }
        }
        $content = $newContents;
        wp_delete_post($post_id);
    }

    if ($delay != 0 && $post_status == 'publish') {
        $post_status = 'future';
    }

    filter_Newlines($content, $config);
    if ($fulldebug) {
        DebugEcho("post newline: $content");
    }

    filter_Start($content, $config);
    if ($fulldebug) {
        DebugEcho("post start: $content");
    }

    filter_End($content, $config);
    if ($fulldebug) {
        DebugEcho("post end: $content");
    }

    DebugEcho("prefer_text_type: " . $config['prefer_text_type']);

    filter_ReplaceImagePlaceHolders($content, $attachments["html"], $config, $id, $config['image_placeholder'], true);
    if ($fulldebug) {
        DebugEcho("post body img: $content");
    }

    if ($post_excerpt) {
        filter_ReplaceImagePlaceHolders($post_excerpt, $attachments["html"], $config, $id, "#eimg%#", false);
        if ($fulldebug) {
            DebugEcho("post excerpt img: $content");
        }
    }

    DebugEcho("excerpt: $post_excerpt");

    if (trim($subject) == "") {
        $subject = $config['default_title'];
        DebugEcho("post parsing subject is blank using: " . $config['default_title']);
    }

    $details = array(
        'post_author' => $poster,
        'comment_author' => $postAuthorDetails['author'],
        'comment_author_url' => $postAuthorDetails['comment_author_url'],
        'user_ID' => $postAuthorDetails['user_ID'],
        'email_author' => $postAuthorDetails['email'],
        'post_date' => $post_date,
        'post_date_gmt' => $post_date_gmt,
        'post_content' => $content,
        'post_title' => $subject,
        'post_type' => $post_type, /* Added by Raam Dev <raam@raamdev.com> */
        'ping_status' => get_option('default_ping_status'),
        'post_category' => $post_categories,
        'tags_input' => $post_tags,
        'comment_status' => $comment_status,
        'post_name' => sanitize_title($subject),
        'post_excerpt' => $post_excerpt,
        'ID' => $id,
        'customImages' => $customImages,
        'post_status' => $post_status,
        'tax_input' => $project
    );
    return $details;
}

/**
 * This is the main handler for all of the processing
 */
function PostEmail($poster, $project, $mimeDecodedEmail, $config) {
    postie_disable_revisions();
    extract($config);

    /* in order to do attachments correctly, we need to associate the
      attachments with a post. So we add the post here, then update it */
    $tmpPost = array('post_title' => 'tmptitle', 'post_content' => 'tmpPost', 'post_status' => 'draft');
    $post_id = wp_insert_post($tmpPost);
    DebugEcho("tmp post id is $post_id");

    $is_reply = false;
    $postmodifiers = new PostiePostModifiers();

    $details = CreatePost($poster, $project, $mimeDecodedEmail, $post_id, $is_reply, $config, $postmodifiers);

    $details = apply_filters('postie_post', $details);
    $details = apply_filters('postie_post_before', $details);

    DebugEcho(("Post postie_post filter"));
    DebugDump($details);


    if (empty($details)) {
        // It is possible that the filter has removed the post, in which case, it should not be posted.
        // And if we created a placeholder post (because this was not a reply to an existing post),
        // then it should be removed
        if (!$is_reply) {
            wp_delete_post($post_id);
            EchoInfo("postie_post filter cleared the post, not saving.");
        }
    } else {
        DisplayEmailPost($details);

        $postid = PostToDB($details, $is_reply, $custom_image_field, $postmodifiers);

        if ($confirmation_email != '') {
            if ($confirmation_email == 'sender') {
                $recipients = array($details['email_author']);
            } elseif ($confirmation_email == 'admin') {
                $recipients = array(get_option("admin_email"));
            } elseif ($confirmation_email == 'both') {
                $recipients = array($details['email_author'], get_option("admin_email"));
            }
            MailToRecipients($mimeDecodedEmail, false, $recipients, false, false, $postid);
        }
    }
    postie_disable_revisions(true);
    DebugEcho("Done");
}

/** FUNCTIONS * */
/*
 * Added by Raam Dev <raam@raamdev.com>
 * Adds support for handling Custom Post Types by adding the
 * Custom Post Type name to the email subject separated by
 * $custom_post_type_delim, e.g. "Movies // My Favorite Movie"
 * Also supports setting the Post Format.
 */
function tag_PostType(&$subject, $postmodifiers, $config) {

    $post_type = $config['post_type'];
    $custom_post_type = $config['post_format'];
    $separated_subject = array();
    $separated_subject[0] = "";
    $separated_subject[1] = $subject;

    $custom_post_type_delim = "//";
    if (strpos($subject, $custom_post_type_delim) !== FALSE) {
        // Captures the custom post type in the subject before $custom_post_type_delim
        $separated_subject = explode($custom_post_type_delim, $subject);
        $custom_post_type = trim(strtolower($separated_subject[0]));
        DebugEcho("post type: found possible type '$custom_post_type'");
    }

    $known_post_types = get_post_types();

    if (in_array($custom_post_type, array_map('strtolower', $known_post_types))) {
        DebugEcho("post type: found type '$post_type'");
        $post_type = $custom_post_type;
        $subject = trim($separated_subject[1]);
    } elseif (in_array($custom_post_type, array_keys(get_post_format_slugs()))) {
        DebugEcho("post type: found format '$custom_post_type'");
        $postmodifiers->PostFormat = $custom_post_type;
        $subject = trim($separated_subject[1]);
    }

    return $post_type;
}

function filter_Linkify($text) {
    # It turns urls into links, and video urls into embedded players
    //DebugEcho("begin: filter_linkify");

    $html = LoadDOM($text);
    if ($html) {
        //DebugEcho("filter_linkify: " . $html->save());
        foreach ($html->find('text') as $element) {
            //DebugEcho("filter_linkify: " . $element->innertext);
            $element->innertext = make_links($element->innertext);
        }
        $ret = $html->save();
    } else {
        $ret = make_links($text);
    }

    //DebugEcho("end: filter_linkify");
    return $ret;
}

function LoadDOM($text) {
    return str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
}

function filter_Videos($text, $shortcode = false) {
    # It turns urls into links, and video urls into embedded players
    //DebugEcho("begin: filter_Videos");

    $html = LoadDOM($text);
    if ($html) {
        foreach ($html->find('text') as $element) {
            $element->innertext = linkifyVideo($element->innertext, $shortcode);
        }
        $ret = $html->save();
    } else {
        $ret = linkifyVideo($text, $shortcode);
    }

    //DebugEcho("end: filter_Videos");
    return $ret;
}

function linkifyVideo($text, $shortcode = false) {
    //$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
    //DebugEcho("linkify1: $text");
    // pad it with a space so we can match things at the start of the 1st line.
    $ret = ' ' . $text;
    if (strpos($ret, 'youtube') !== false) {
        // try to embed youtube videos
        $youtube = "#(^|[\n ]|>)[\w]+?://(www\.)?youtube\.com/watch\?v=([_a-zA-Z0-9-]+).*?([ \n]|$|<)#is";
        if ($shortcode) {
            $youtube_replace = "\\1[youtube \\3]\\4";
        } else {
            $youtube_replace = "\\1<embed width='425' height='344' allowfullscreen='true' allowscriptaccess='always' type='application/x-shockwave-flash' src='http://www.youtube.com/v/\\3&hl=en&fs=1' />\\4";
        }
        $ret = preg_replace($youtube, $youtube_replace, $ret);
        DebugEcho("youtube: $ret");
    }

    if (strpos($ret, 'vimeo') !== false) {
        // try to embed vimeo videos
        #    : http://vimeo.com/6348141
        $vimeo = "#(^|[\n ]|>)[\w]+?://(www\.)?vimeo\.com/([_a-zA-Z0-9-]+).*?([ \n]|$|<)#is";
        if ($shortcode) {
            $vimeo_replace = "\\1[vimeo \\3]\\4";
        } else {
            $vimeo_replace = "\\1<object width='400' height='300'><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='movie' value='http://vimeo.com/moogaloop.swf?clip_id=\\3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1' /><embed src='http://vimeo.com/moogaloop.swf?clip_id=\\3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1' type='application/x-shockwave-flash' allowfullscreen='true' allowscriptaccess='always' width='400' height='300'></embed></object>\\4";
        }
        $ret = preg_replace($vimeo, $vimeo_replace, $ret);
        DebugEcho("vimeo: $ret");
    }

    // Remove our padding..
    $ret = substr($ret, 1);
    return $ret;
}

function make_links($text) {
    return preg_replace(
            array(
        '/(?(?=<a[^>]*>.+<\/a>)
             (?:<a[^>]*>.+<\/a>)
             |
             ([^="\']?)((?:https?|ftp|bf2|):\/\/[^<> \n\r]+)
         )/iex',
        '/<a([^>]*)target="?[^"\']+"?/i',
        '/<a([^>]+)>/i',
        '/(^|\s)(www.[^<> \n\r]+)/iex',
        '/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)
       (\\.[A-Za-z0-9-]+)*)/iex'
            ), array(
        "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\">\\2</a>\\3':'\\0'))",
        '<a\\1',
        '<a\\1 >',
        "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\">\\2</a>\\3':'\\0'))",
        "stripslashes((strlen('\\2')>0?'<a href=\"mailto:\\0\">\\0</a>':'\\0'))"
            ), $text
    );
}

/* we check whether or not the email is a forwards or a redirect. If it is
 * a fwd, then we glean the author details from the body of the post.
 * Otherwise we get them from the headers
 */

function getPostAuthorDetails(&$subject, &$content, &$mimeDecodedEmail) {

    $theDate = $mimeDecodedEmail->headers['date'];
    $theEmail = RemoveExtraCharactersInEmailAddress($mimeDecodedEmail->headers["from"]);

    $regAuthor = get_user_by('email', $theEmail);
    if ($regAuthor) {
        $theAuthor = $regAuthor->user_login;
        $theUrl = $regAuthor->user_url;
        $theID = $regAuthor->ID;
    } else {
        $theAuthor = GetNameFromEmail($theEmail);
        $theUrl = '';
        $theID = '';
    }

    // see if subject starts with Fwd:
    $matches = array();
    if (preg_match("/(^Fwd:) (.*)/", $subject, $matches)) {
        DebugEcho("Fwd: detected");
        $subject = trim($matches[2]);
        if (preg_match("/\nfrom:(.*?)\n/i", $content, $matches)) {
            $theAuthor = GetNameFromEmail($matches[1]);
            $mimeDecodedEmail->headers['from'] = $theAuthor;
        }
        //TODO dosen't always work with HTML
        if (preg_match("/\ndate:(.*?)\n/i", $content, $matches)) {
            $theDate = $matches[1];
            DebugEcho("date in Fwd: $theDate");
            if (($timestamp = strtotime($theDate)) === false) {
                DebugEcho("bad date found: $theDate");
            } else {
                $mimeDecodedEmail->headers['date'] = $theDate;
            }
        }

        // now get rid of forwarding info in the content
        $lines = preg_split("/\r\n/", $content);
        $newContents = '';
        foreach ($lines as $line) {
            if (preg_match("/^(from|subject|to|date):.*?/i", $line, $matches) == 0 && preg_match("/^-+\s*forwarded\s*message\s*-+/i", $line, $matches) == 0) {
                $newContents.=preg_replace("/\r/", "", $line) . "\n";
            }
        }
        $content = $newContents;
    }
    $theDetails = array(
        'content' => "<div class='postmetadata alt'>On $theDate, $theAuthor" . " posted:</div>",
        'emaildate' => $theDate,
        'author' => $theAuthor,
        'comment_author_url' => $theUrl,
        'user_ID' => $theID,
        'email' => $theEmail
    );
    return $theDetails;
}

/* we check whether or not the email is a reply to a previously
 * published post. First we check whether it starts with Re:, and then
 * we see if the remainder matches an already existing post. If so,
 * then we add that post id to the details array, which will cause the
 * existing post to be overwritten, instead of a new one being
 * generated
 */

function GetParentPostForReply(&$subject) {
    global $wpdb;

    $id = NULL;
    DebugEcho("GetParentPostForReply: Looking for parent '$subject'");
    // see if subject starts with Re:
    $matches = array();
    if (preg_match("/(^Re:)(.*)/i", $subject, $matches)) {
        DebugEcho("GetParentPostForReply: Re: detected");
        $subject = trim($matches[2]);
        // strip out category info into temporary variable
        $tmpSubject = $subject;
        if (preg_match('/(.+): (.*)/', $tmpSubject, $matches)) {
            $tmpSubject = trim($matches[2]);
            $matches[1] = array($matches[1]);
        } else if (preg_match_all('/\[(.[^\[]*)\]/', $tmpSubject, $matches)) {
            $tmpSubject_matches = array();
            preg_match("/](.[^\[]*)$/", $tmpSubject, $tmpSubject_matches);
            $tmpSubject = trim($tmpSubject_matches[1]);
        } else if (preg_match_all('/-(.[^-]*)-/', $tmpSubject, $matches)) {
            preg_match("/-(.[^-]*)$/", $tmpSubject, $tmpSubject_matches);
            $tmpSubject = trim($tmpSubject_matches[1]);
        }
        DebugEcho("GetParentPostForReply: tmpSubject: $tmpSubject");
        $checkExistingPostQuery = "SELECT ID FROM $wpdb->posts WHERE  post_title = %s AND post_status = 'publish'";
        if ($id = $wpdb->get_var($wpdb->prepare($checkExistingPostQuery, $tmpSubject))) {
            if (is_array($id)) {
                $id = $id[count($id) - 1];
                DebugEcho("GetParentPostForReply: id: $id");
            } else {
                DebugEcho("GetParentPostForReply: No parent id found");
            }
        }
    } else {
        DebugEcho("GetParentPostForReply: No parent found");
    }
    return $id;
}

function postie_ShowReadMe() {
    include(POSTIE_ROOT . DIRECTORY_SEPARATOR . "postie_read_me.php");
}

/**
 *  This sets up the configuration menu
 */
function PostieMenu() {
    if (function_exists('add_options_page')) {
        if (current_user_can('manage_options')) {
            add_options_page("Postie", "Postie", 'manage_options', POSTIE_ROOT . "/postie.php", "ConfigurePostie");
        }
    }
}

/**
 * This handles actually showing the form. Called by WordPress
 */
function ConfigurePostie() {
    include(POSTIE_ROOT . DIRECTORY_SEPARATOR . "config_form.php");
}

/**
 * This function handles determining the protocol and fetching the mail
 * @return array
 */
function FetchMail($server = NULL, $port = NULL, $email = NULL, $password = NULL, $protocol = NULL, $offset = NULL, $test = NULL, $deleteMessages = true, $maxemails = 0, $email_tls = false) {
    $emails = array();
    if (!$server || !$port || !$email) {
        EchoInfo("Missing Configuration For Mail Server");
        return $emails;
    }
    if ($server == "pop.gmail.com") {
        EchoInfo("MAKE SURE POP IS TURNED ON IN SETTING AT Gmail");
    }
    switch (strtolower($protocol)) {
        case 'smtp': //direct
            $fd = fopen("php://stdin", "r");
            $input = "";
            while (!feof($fd)) {
                $input .= fread($fd, 1024);
            }
            fclose($fd);
            $emails[0] = $input;
            break;
        case 'imap':
        case 'imap-ssl':
        case 'pop3-ssl':
            if (!HasIMAPSupport()) {
                EchoInfo("Sorry - you do not have IMAP php module installed - it is required for this mail setting.");
            } else {
                $emails = IMAPMessageFetch($server, $port, $email, $password, $protocol, $offset, $test, $deleteMessages, $maxemails, $email_tls);
            }
            break;
        case 'pop3':
        default:
            $emails = POP3MessageFetch($server, $port, $email, $password, $protocol, $offset, $test, $deleteMessages, $maxemails);
    }

    return $emails;
}

/**
 * Handles fetching messages from an imap server
 */
function IMAPMessageFetch($server = NULL, $port = NULL, $email = NULL, $password = NULL, $protocol = NULL, $offset = NULL, $test = NULL, $deleteMessages = true, $maxemails = 0, $tls = false) {
    require_once("postieIMAP.php");
    $emails = array();
    $mail_server = &PostieIMAP::Factory($protocol);
    if ($tls) {
        $mail_server->TLSOn();
    }
    DebugEcho("Connecting to $server:$port ($protocol)" . ($tls ? " with TLS" : ""));
    if ($mail_server->connect(trim($server), $port, $email, $password)) {
        $msg_count = $mail_server->getNumberOfMessages();
    } else {
        EchoInfo("Mail Connection Time Out");
        EchoInfo("Common Reasons: Server Down, Network Issue, Port/Protocol MisMatch ");
        EchoInfo("The Server said:" . $mail_server->error());
        $msg_count = 0;
    }

    // loop through messages
    for ($i = 1; $i <= $msg_count; $i++) {
        $emails[$i] = $mail_server->fetchEmail($i);
        if ($deleteMessages) {
            $mail_server->deleteMessage($i);
        }
        if ($maxemails != 0 && $i >= $maxemails) {
            DebugEcho("Max emails ($maxemails)");
            break;
        }
    }
    if ($deleteMessages) {
        $mail_server->expungeMessages();
    }
    //clean up
    $mail_server->disconnect();
    return $emails;
}

/**
 * Retrieves email via POP3
 */
function POP3MessageFetch($server = NULL, $port = NULL, $email = NULL, $password = NULL, $protocol = NULL, $offset = NULL, $test = NULL, $deleteMessages = true, $maxemails = 0) {
    require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-pop3.php');

    $emails = array();
    $pop3 = new POP3();
    if (defined('POSTIE_DEBUG')) {
        $pop3->DEBUG = POSTIE_DEBUG;
    }

    DebugEcho("Connecting to $server:$port ($protocol)");

    if ($pop3->connect(trim($server), $port)) {
        $msg_count = $pop3->login($email, $password);
        if ($msg_count === false) {
            $msg_count = 0;
        }
    } else {
        if (strpos($pop3->ERROR, "POP3: premature NOOP OK, NOT an RFC 1939 Compliant server") === false) {
            EchoInfo("Mail Connection Time Out. Common Reasons: Server Down, Network Issue, Port/Protocol MisMatch");
        }
        EchoInfo("The Server said: $pop3->ERROR");
        $msg_count = 0;
    }
    DebugEcho("message count: $msg_count");

    // loop through messages
    //$msgs = $pop3->pop_list();
    //DebugEcho("POP3MessageFetch: messages");
    //DebugDump($msgs);

    for ($i = 1; $i <= $msg_count; $i++) {
        $m = $pop3->get($i);
        if ($m !== false) {
            if (is_array($m)) {
                $emails[$i] = implode('', $m);
                if ($deleteMessages) {
                    if (!$pop3->delete($i)) {
                        EchoInfo("POP3MessageFetch: cannot delete message $i: " . $pop3->ERROR);
                        $pop3->reset();
                        exit;
                    }
                }
            } else {
                DebugEcho("POP3MessageFetch: message $i not an array");
            }
        } else {
            EchoInfo("POP3MessageFetch: message $i $pop3->ERROR");
        }
        if ($maxemails != 0 && $i >= $maxemails) {
            DebugEcho("Max emails ($maxemails)");
            break;
        }
    }
    //clean up
    $pop3->quit();
    return $emails;
}

/**
 * This function handles putting the actual entry into the database
 * @param array - categories to be posted to
 * @param array - details of the post
 */
function PostToDB($details, $isReply, $customImageField, $postmodifiers) {
    $post_ID = 0;
    if (!$isReply) {
        $post_ID = wp_insert_post($details, true);
        if (is_wp_error($post_ID)) {
            EchoInfo("Error: " . $post_ID->get_error_message());
            wp_delete_post($details['ID']);
        }
        //evidently post_category was depricated at some point.
        //wp_set_post_terms($post_ID, $details['post_category']);
    } else {
        $comment = array(
            'comment_author' => $details['comment_author'],
            'comment_post_ID' => $details['ID'],
            'comment_author_email' => $details['email_author'],
            'comment_date' => $details['post_date'],
            'comment_date_gmt' => $details['post_date_gmt'],
            'comment_content' => $details['post_content'],
            'comment_author_url' => $details['comment_author_url'],
            'comment_author_IP' => '',
            'comment_approved' => 1,
            'comment_agent' => '',
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $details['user_ID']
        );

        $post_ID = wp_insert_comment($comment);
    }

    if ($post_ID) {
        if ($customImageField) {
            DebugEcho("Saving custom image fields");
            //DebugDump($details['customImages']);

            if (count($details['customImages']) > 1) {
                $imageField = 1;
                foreach ($details['customImages'] as $image) {
                    add_post_meta($post_ID, 'image' . $imageField, $image);
                    $imageField++;
                }
            }
        }

        $postmodifiers->apply($post_ID);

        do_action('postie_post_after', $details);
    }
    return $post_ID;
}

/**
 * This function determines if the mime attachment is on the BANNED_FILE_LIST
 * @param string
 * @return boolean
 */
function isBannedFileName($filename, $bannedFiles) {
    if (empty($filename) || empty($bannedFiles)) {
        return false;
    }
    foreach ($bannedFiles as $bannedFile) {
        if (fnmatch($bannedFile, $filename)) {
            EchoInfo("Ignoring attachment: $filename - it is on the banned files list.");
            return true;
        }
    }
    return false;
}

function GetContent($part, &$attachments, $post_id, $poster, $config) {
    extract($config);
    //global $charset, $encoding;
    DebugEcho('GetContent: ---- start');
    $meta_return = '';
    if (property_exists($part, "ctype_primary")) {
        DebugEcho("GetContent: primary= " . $part->ctype_primary . ", secondary = " . $part->ctype_secondary);
        //DebugDump($part);
    }

    DecodeBase64Part($part);

    //look for banned file names
    if (property_exists($part, 'ctype_parameters') && is_array($part->ctype_parameters) && array_key_exists('name', $part->ctype_parameters)) {
        if (isBannedFileName($part->ctype_parameters['name'], $banned_files_list)) {
            DebugEcho("GetContent: found banned filename");
            return NULL;
        }
    }

    if (property_exists($part, "ctype_primary") && $part->ctype_primary == "application" && $part->ctype_secondary == "octet-stream") {
        if (property_exists($part, 'disposition') && $part->disposition == "attachment") {
            //nothing
        } else {
            DebugEcho("GetContent: decoding application/octet-stream");
            $mimeDecodedEmail = DecodeMIMEMail($part->body);
            filter_PreferedText($mimeDecodedEmail, $prefer_text_type);
            foreach ($mimeDecodedEmail->parts as $section) {
                $meta_return .= GetContent($section, $attachments, $post_id, $poster, $config);
            }
        }
    }

    if (property_exists($part, "ctype_primary") && $part->ctype_primary == "multipart" && $part->ctype_secondary == "appledouble") {
        DebugEcho("GetContent: multipart appledouble");
        $mimeDecodedEmail = DecodeMIMEMail("Content-Type: multipart/mixed; boundary=" . $part->ctype_parameters["boundary"] . "\n" . $part->body);
        filter_PreferedText($mimeDecodedEmail, $prefer_text_type);
        filter_AppleFile($mimeDecodedEmail);
        foreach ($mimeDecodedEmail->parts as $section) {
            $meta_return .= GetContent($section, $attachments, $post_id, $poster, $config);
        }
    } else {
        $filename = "";
        if (property_exists($part, 'ctype_parameters') && is_array($part->ctype_parameters) && array_key_exists('name', $part->ctype_parameters)) {
            $filename = $part->ctype_parameters['name'];
        } elseif (property_exists($part, 'd_parameters') && is_array($part->d_parameters) && array_key_exists('filename', $part->d_parameters)) {
            $filename = $part->d_parameters['filename'];
        }
        $filename = sanitize_file_name($filename);
        $fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        DebugEcho("GetContent: file name '$filename'");
        DebugEcho("GetContent: extension '$fileext'");

        $mimetype_primary = "";
        $mimetype_secondary = "";

        if (property_exists($part, "ctype_primary")) {
            $mimetype_primary = strtolower($part->ctype_primary);
        }
        if (property_exists($part, "ctype_secondary")) {
            $mimetype_secondary = strtolower($part->ctype_secondary);
        }

        $typeinfo = wp_check_filetype($filename);
        //DebugDump($typeinfo);
        if (!empty($typeinfo['type'])) {
            DebugEcho("GetContent: secondary lookup found " . $typeinfo['type']);
            $mimeparts = explode('/', strtolower($typeinfo['type']));
            $mimetype_primary = $mimeparts[0];
            $mimetype_secondary = $mimeparts[1];
        } else {
            DebugEcho("GetContent: secondary lookup failed, checking configured extensions");
            if (in_array($fileext, $audiotypes)) {
                DebugEcho("GetContent: found audio extension");
                $mimetype_primary = 'audio';
                $mimetype_secondary = $fileext;
            } elseif (in_array($fileext, array_merge($video1types, $video2types))) {
                DebugEcho("GetContent: found video extension");
                $mimetype_primary = 'video';
                $mimetype_secondary = $fileext;
            } else {
                DebugEcho("GetContent: found no extension");
            }
        }

        DebugEcho("GetContent: mimetype $mimetype_primary/$mimetype_secondary");

        switch ($mimetype_primary) {
            case 'multipart':
                DebugEcho("GetContent: multipart: " . count($part->parts));
                //DebugDump($part);
                filter_PreferedText($part, $prefer_text_type);
                foreach ($part->parts as $section) {
                    //DebugDump($section->headers);
                    $meta_return .= GetContent($section, $attachments, $post_id, $poster, $config);
                }
                break;

            case 'text':
                DebugEcho("GetContent: ctype_primary: text");
                //DebugDump($part);

                $charset = "";
                if (property_exists($part, 'ctype_parameters') && array_key_exists('charset', $part->ctype_parameters) && !empty($part->ctype_parameters['charset'])) {
                    $charset = $part->ctype_parameters['charset'];
                    DebugEcho("GetContent: text charset: $charset");
                }

                $encoding = "";
                if (array_key_exists('content-transfer-encoding', $part->headers) && !empty($part->headers['content-transfer-encoding'])) {
                    $encoding = $part->headers['content-transfer-encoding'];
                    DebugEcho("GetContent: text encoding: $encoding");
                }

                if ($charset !== '' || $encoding !== '') {
                    //DebugDump($part);
                    $part->body = HandleMessageEncoding($encoding, $charset, $part->body, $message_encoding, $message_dequote);
                    if (!empty($charset)) {
                        $part->ctype_parameters['charset'] = ""; //reset so we don't double decode
                    }
                    //DebugDump($part);
                }
                if (array_key_exists('disposition', $part) && $part->disposition == 'attachment') {
                    DebugEcho("GetContent: text Attachement: $filename");
                    if (!preg_match('/ATT\d\d\d\d\d.txt/i', $filename)) {
                        $file_id = postie_media_handle_upload($part, $post_id, $poster, $generate_thumbnails);
                        if (!is_wp_error($file_id)) {
                            $file = wp_get_attachment_url($file_id);
                            $icon = chooseAttachmentIcon($file, $mimetype_primary, $mimetype_secondary, $icon_set, $icon_size);
                            $attachments["html"][$filename] = "<a href='$file'>" . $icon . $filename . '</a>' . "\n";
                            DebugEcho("GetContent: text attachment: adding '$filename'");
                        } else {
                            LogInfo($file_id->get_error_message());
                        }
                    } else {
                        DebugEcho("GetContent: text attachment: skipping '$filename'");
                    }
                } else {

                    //go through each sub-section
                    if ($mimetype_secondary == 'enriched') {
                        //convert enriched text to HTML
                        DebugEcho("GetContent: enriched");
                        $meta_return .= filter_Etf2HTML($part->body) . "\n";
                    } elseif ($mimetype_secondary == 'html') {
                        //strip excess HTML
                        DebugEcho("GetContent: html");
                        $meta_return .= filter_CleanHtml($part->body) . "\n";
                    } elseif ($mimetype_secondary == 'plain') {
                        DebugEcho("GetContent: plain text");
                        //DebugDump($part);

                        DebugEcho("GetContent: body text");
                        if ($allow_html_in_body) {
                            DebugEcho("GetContent: html allowed");
                            $meta_return .= $part->body;
                            //$meta_return = "<div>$meta_return</div>\n";
                        } else {
                            DebugEcho("GetContent: html not allowed (htmlentities)");
                            $meta_return .= htmlentities($part->body);
                        }
                        $meta_return = filter_StripPGP($meta_return);
                        //DebugEcho("meta return: $meta_return");
                    } else {
                        DebugEcho("GetContent: text Attachement wo disposition: $filename");
                        $file_id = postie_media_handle_upload($part, $post_id, $poster);
                        if (!is_wp_error($file_id)) {
                            $file = wp_get_attachment_url($file_id);
                            $icon = chooseAttachmentIcon($file, $mimetype_primary, $mimetype_secondary, $icon_set, $icon_size);
                            $attachments["html"][$filename] = "<a href='$file'>" . $icon . $filename . '</a>' . "\n";
                        } else {
                            LogInfo($file_id->get_error_message());
                        }
                    }
                }
                break;

            case 'image':
                DebugEcho("GetContent: image Attachement: $filename");
                $file_id = postie_media_handle_upload($part, $post_id, $poster, $generate_thumbnails);
                if (!is_wp_error($file_id)) {
                    //featured image logic
                    //set the first image we come across as the featured image
                    DebugEcho("GetContent: has_post_thumbnail: " . has_post_thumbnail($post_id));
                    //DebugEcho("get_the_post_thumbnail: " .get_the_post_thumbnail($post_id));

                    if ($featured_image && !has_post_thumbnail($post_id)) {
                        DebugEcho("GetContent: featured image: $file_id");
                        set_post_thumbnail($post_id, $file_id);
                    }
                    $file = wp_get_attachment_url($file_id);
                    $cid = "";
                    if (array_key_exists('content-id', $part->headers)) {
                        $cid = trim($part->headers["content-id"], "<>");
                        DebugEcho("GetContent: found cid: $cid");
                    }

                    $the_post = get_post($file_id);
                    $attachments["html"][$filename] = parseTemplate($file_id, $mimetype_primary, $imagetemplate, $filename);
                    if ($cid) {
                        $attachments["cids"][$cid] = array($file, count($attachments["html"]) - 1);
                        DebugEcho("GetContent: CID Attachement: $cid");
                    }
                } else {
                    LogInfo("image error: " . $file_id->get_error_message());
                }
                break;

            case 'audio':
                //DebugDump($part->headers);
                DebugEcho("GetContent: audio Attachement: $filename");
                $file_id = postie_media_handle_upload($part, $post_id, $poster, $generate_thumbnails);
                if (!is_wp_error($file_id)) {
                    $file = wp_get_attachment_url($file_id);
                    $cid = "";
                    if (array_key_exists('content-id', $part->headers)) {
                        $cid = trim($part->headers["content-id"], "<>");
                        DebugEcho("GetContent: audio Attachement cid: $cid");
                    }
                    if (in_array($fileext, $audiotypes)) {
                        DebugEcho("GetContent: using audio template: $mimetype_secondary");
                        $audioTemplate = $audiotemplate;
                    } else {
                        DebugEcho("GetContent: using default audio template: $mimetype_secondary");
                        $icon = chooseAttachmentIcon($file, $mimetype_primary, $mimetype_secondary, $icon_set, $icon_size);
                        $audioTemplate = '<a href="{FILELINK}">' . $icon . '{FILENAME}</a>';
                    }
                    $attachments["html"][$filename] = parseTemplate($file_id, $mimetype_primary, $audioTemplate, $filename);
                } else {
                    LogInfo("audio error: " . $file_id->get_error_message());
                }
                break;

            case 'video':
                DebugEcho("GetContent: video Attachement: $filename");
                $file_id = postie_media_handle_upload($part, $post_id, $poster, $generate_thumbnails);
                if (!is_wp_error($file_id)) {
                    $file = wp_get_attachment_url($file_id);
                    $cid = "";
                    if (array_key_exists('content-id', $part->headers)) {
                        $cid = trim($part->headers["content-id"], "<>");
                        DebugEcho("GetContent: video Attachement cid: $cid");
                    }
                    //DebugDump($part);
                    if (in_array($fileext, $video1types)) {
                        DebugEcho("GetContent: using video1 template: $fileext");
                        $videoTemplate = $video1template;
                    } elseif (in_array($fileext, $video2types)) {
                        DebugEcho("GetContent: using video2 template: $fileext");
                        $videoTemplate = $video2template;
                    } else {
                        DebugEcho("GetContent: using default template: $fileext");
                        $icon = chooseAttachmentIcon($file, $mimetype_primary, $mimetype_secondary, $icon_set, $icon_size);
                        $videoTemplate = '<a href="{FILELINK}">' . $icon . '{FILENAME}</a>';
                    }
                    $attachments["html"][$filename] = parseTemplate($file_id, $mimetype_primary, $videoTemplate, $filename);
                    //echo "videoTemplate = $videoTemplate\n";
                } else {
                    LogInfo($file_id->get_error_message());
                }
                break;

            default:
                DebugEcho("GetContent: found file type: " . $mimetype_primary);
                if (in_array($mimetype_primary, $supported_file_types)) {
                    //pgp signature - then forget it
                    if ($mimetype_secondary == 'pgp-signature') {
                        DebugEcho("GetContent: found pgp-signature - done");
                        break;
                    }
                    $file_id = postie_media_handle_upload($part, $post_id, $poster, $generate_thumbnails);
                    if (!is_wp_error($file_id)) {
                        $file = wp_get_attachment_url($file_id);
                        DebugEcho("GetContent: uploaded $file_id ($file)");
                        $icon = chooseAttachmentIcon($file, $mimetype_primary, $mimetype_secondary, $icon_set, $icon_size);
                        DebugEcho("GetContent: default: $icon $filename");
                        $attachments["html"][$filename] = parseTemplate($file_id, $mimetype_primary, $generaltemplate, $filename, $icon);
                        if (array_key_exists('content-id', $part->headers)) {
                            $cid = trim($part->headers["content-id"], "<>");
                            if ($cid) {
                                $attachments["cids"][$cid] = array($file, count($attachments["html"]) - 1);
                            }
                        } else {
                            DebugEcho("GetContent: No content-id");
                        }
                    } else {
                        LogInfo($file_id->get_error_message());
                    }
                } else {
                    DebugEcho("GetContent: Not in supported filetype list");
                    DebugDump($supported_file_types);
                }
                break;
        }
    }
    DebugEcho("GetContent: meta_return: " . substr($meta_return, 0, 500));
    DebugEcho("GetContent: ==== end");
    return $meta_return;
}

function filter_Ubb2HTML(&$text) {
// Array of tags with opening and closing
    $tagArray['img'] = array('open' => '<img src="', 'close' => '">');
    $tagArray['b'] = array('open' => '<b>', 'close' => '</b>');
    $tagArray['i'] = array('open' => '<i>', 'close' => '</i>');
    $tagArray['u'] = array('open' => '<u>', 'close' => '</u>');
    $tagArray['url'] = array('open' => '<a href="', 'close' => '">\\1</a>');
    $tagArray['email'] = array('open' => '<a href="mailto:', 'close' => '">\\1</a>');
    $tagArray['url=(.*)'] = array('open' => '<a href="', 'close' => '">\\2</a>');
    $tagArray['email=(.*)'] = array('open' => '<a href="mailto:', 'close' => '">\\2</a>');
    $tagArray['color=(.*)'] = array('open' => '<font color="', 'close' => '">\\2</font>');
    $tagArray['size=(.*)'] = array('open' => '<font size="', 'close' => '">\\2</font>');
    $tagArray['font=(.*)'] = array('open' => '<font face="', 'close' => '">\\2</font>');
// Array of tags with only one part
    $sTagArray['br'] = array('tag' => '<br>');
    $sTagArray['hr'] = array('tag' => '<hr>');

    foreach ($tagArray as $tagName => $replace) {
        $tagEnd = preg_replace('/\W/Ui', '', $tagName);
        $text = preg_replace("|\[$tagName\](.*)\[/$tagEnd\]|Ui", "$replace[open]\\1$replace[close]", $text);
    }
    foreach ($sTagArray as $tagName => $replace) {
        $text = preg_replace("|\[$tagName\]|Ui", "$replace[tag]", $text);
    }
    return $text;
}

// This function turns Enriched Text into something similar to HTML
// Very basic at the moment, only supports some functionality and dumps the rest
// FIXME: fix colours: <color><param>FFFF,C2FE,0374</param>some text </color>
function filter_Etf2HTML($content) {

    $search = array(
        '/<bold>/',
        '/<\/bold>/',
        '/<underline>/',
        '/<\/underline>/',
        '/<italic>/',
        '/<\/italic>/',
        '/<fontfamily><param>.*<\/param>/',
        '/<\/fontfamily>/',
        '/<x-tad-bigger>/',
        '/<\/x-tad-bigger>/',
        '/<bigger>/',
        '</bigger>/',
        '/<color>/',
        '/<\/color>/',
        '/<param>.+<\/param>/'
    );

    $replace = array(
        '<b>',
        '</b>',
        '<u>',
        '</u>',
        '<i>',
        '</i>',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        ''
    );
// strip extra line breaks
    $content = preg_replace($search, $replace, $content);
    return trim($content);
}

// This function cleans up HTML in the email
function filter_CleanHtml($content) {
    $html = str_get_html($content);
    if ($html) {
        DebugEcho("filter_CleanHtml: Looking for invalid tags");
        foreach ($html->find('script, style, head') as $node) {
            DebugEcho("filter_CleanHtml: Removing: " . $node->outertext);
            $node->outertext = '';
        }
        DebugEcho("filter_CleanHtml: " . $html->save());

        $html->load($html->save());

        $b = $html->find('body');
        if ($b) {
            DebugEcho("filter_CleanHtml: replacing body with div");
            $content = "<div>" . $b[0]->innertext . "</div>\n";
        }
    } else {
        DebugEcho("filter_CleanHtml: No HTML found");
    }
    return $content;
}

/**
 * Determines if the sender is a valid user.
 * @return integer|NULL
 */
function ValidatePoster(&$mimeDecodedEmail, $config) {
    $test_email = '';
    extract($config);
    $poster = NULL;
    $from = "";
    if (property_exists($mimeDecodedEmail, "headers") && array_key_exists('from', $mimeDecodedEmail->headers)) {
        $from = RemoveExtraCharactersInEmailAddress(trim($mimeDecodedEmail->headers["from"]));
        $from = apply_filters("postie_filter_email", $from);
        DebugEcho("ValidatePoster: post postie_filter_email $from");

        $toEmail = '';
        if (isset($mimeDecodedEmail->headers["to"])) {
            $toEmail = $mimeDecodedEmail->headers["to"];
        }

        $replytoEmail = '';
        if (isset($mimeDecodedEmail->headers["reply-to"])) {
            $replytoEmail = $mimeDecodedEmail->headers["reply-to"];
        }

        $from = apply_filters("postie_filter_email2", $from, $toEmail, $replytoEmail);
        DebugEcho("ValidatePoster: post postie_filter_email2 $from");
    } else {
        DebugEcho("No 'from' header found");
        DebugDump($mimeDecodedEmail->headers);
    }

    $resentFrom = "";
    if (property_exists($mimeDecodedEmail, "headers") && array_key_exists('resent-from', $mimeDecodedEmail->headers)) {
        $resentFrom = RemoveExtraCharactersInEmailAddress(trim($mimeDecodedEmail->headers["resent-from"]));
    }

    //See if the email address is one of the special authorized ones
    if (!empty($from)) {
        DebugEcho("Confirming Access For $from ");
        $user = get_user_by('email', $from);
        if ($user !== false) {
            $user_ID = $user->ID;
        }
    } else {
        $user_ID = "";
    }
    if (!empty($user_ID)) {
        $user = new WP_User($user_ID);
        if ($user->has_cap("post_via_postie")) {
            DebugEcho("$user_ID has 'post_via_postie' permissions");
            $poster = $user_ID;

            DebugEcho("ValidatePoster: pre postie_author $poster");
            $poster = apply_filters("postie_author", $poster);
            DebugEcho("ValidatePoster: post postie_author $poster");
        } else {
            DebugEcho("$user_ID does not have 'post_via_postie' permissions");
            $user_ID = "";
        }
    }
    if (empty($user_ID) && ($turn_authorization_off || isEmailAddressAuthorized($from, $authorized_addresses) || isEmailAddressAuthorized($resentFrom, $authorized_addresses))) {
        DebugEcho("ValidatePoster: looking up default user $admin_username");
        $user = get_user_by('login', $admin_username);
        if ($user === false) {
            EchoInfo("Your 'Default Poster' setting '$admin_username' is not a valid WordPress user (2)");
            $poster = 1;
        } else {
            $poster = $user->ID;
            DebugEcho("ValidatePoster: pre postie_author (default) $poster");
            $poster = apply_filters("postie_author", $poster);
            DebugEcho("ValidatePoster: post postie_author (default) $poster");
        }
        DebugEcho("ValidatePoster: found user '$poster'");
    }

    $validSMTP = isValidSmtpServer($mimeDecodedEmail, $smtp);

    if (!$poster || !$validSMTP) {
        EchoInfo('Invalid sender: ' . htmlentities($from) . "! Not adding email!");
        if ($forward_rejected_mail) {
            $admin_email = get_option("admin_email");
            if (MailToRecipients($mimeDecodedEmail, $test_email, array($admin_email), $return_to_sender)) {
                EchoInfo("A copy of the message has been forwarded to the administrator.");
            } else {
                EchoInfo("The message was unable to be forwarded to the adminstrator.");
            }
        }
        return '';
    }
    return $poster;
}
/*****************
	CUSTOM LOPENDOC FUNCTION
	****************/

function ValidateProject(&$mimeDecodedEmail, $config) {
    $test_email = '';
    extract($config);
    $poster = NULL;
    $from = "";
    $project = "";
    if (property_exists($mimeDecodedEmail, "headers") && array_key_exists('from', $mimeDecodedEmail->headers)) {
        $from = RemoveExtraCharactersInEmailAddress(trim($mimeDecodedEmail->headers["from"]));
        $from = apply_filters("postie_filter_email", $from);

        $toEmail = '';
        if (isset($mimeDecodedEmail->headers["to"])) {
            $toEmail = $mimeDecodedEmail->headers["to"];
        }

        $posPlus = strpos($toEmail, '+') + 1;

        if ( $posPlus !== false ) {
	        $toEmailFromPlus = substr($toEmail, $posPlus);

	        $projectTerm = stristr( $toEmailFromPlus, '@', true );

					$project = array( 'projets' =>  $projectTerm);
				}

        DebugEcho("ValidateProject: project $project");
    } else {
        DebugEcho("No header found");
        DebugDump($mimeDecodedEmail->headers);
    }

    return $project;
}

function isValidSmtpServer($mimeDecodedEmail, $smtpservers) {
    if (empty($smtpservers)) {
        return true;
    }

    foreach ((array) $mimeDecodedEmail->headers['received'] as $received) {
        EchoInfo("isValidSmtpServer: checking header $received");
        foreach ($smtpservers as $smtp) {
            if (stristr($received, $smtp) !== false) {
                DebugEcho("isValidSmtpServer: Sent from valid SMTP server.");
                return true;
            }
        }
    }

    EchoInfo("isValidSmtpServer: Sent from invalid SMTP server.");
    return false;
}

/**
 * Looks at the content for the start of the message and removes everything before that
 * If the pattern is not found everything is returned
 * @param string
 * @param string
 */
function filter_Start(&$content, $config) {
    $start = $config['message_start'];
    if ($start) {
        $pos = strpos($content, $start);
        if ($pos === false) {
            return $content;
        }
        DebugEcho("start filter $start");
        $content = substr($content, $pos + strlen($start), strlen($content));
    }
}

/**
 * Looks at the content for the start of the signature and removes all text
 * after that point
 * @param string
 * @param array - a list of patterns to determine if it is a sig block
 */
function filter_RemoveSignature(&$content, $config) {
    if ($config['drop_signature']) {
        if (empty($config['sig_pattern_list'])) {
            DebugEcho("filter_RemoveSignature: no sig_pattern_list");
            return;
        }
        //DebugEcho("looking for signature in: $content");

        $pattern = '/^(' . implode('|', $config['sig_pattern_list']) . ')\s?$/mi';
        DebugEcho("filter_RemoveSignature: pattern: $pattern");

        $html = LoadDOM($content);
        if ($html !== false) {
            filter_RemoveSignatureWorker($html->root, $pattern);
            $content = $html->save();
        } else {
            DebugEcho("filter_RemoveSignature: non-html");
            $arrcontent = explode("\n", $content);
            $strcontent = '';

            for ($i = 0; $i < count($arrcontent); $i++) {
                $line = trim($arrcontent[$i]);
                if (preg_match($pattern, trim($line))) {
                    DebugEcho("filter_RemoveSignature: signature found: removing");
                    break;
                }

                $strcontent .= $line;
            }
            $content = $strcontent;
        }
    }
}

function filter_RemoveSignatureWorker(&$html, $pattern) {
    $found = false;
    $matches = array();
    if (preg_match($pattern, trim($html->plaintext), $matches)) {
        DebugEcho("filter_RemoveSignatureWorker: signature found in base, removing");
        DebugDump($matches);
        $found = true;
        $i = stripos($html->innertext, $matches[1]);
        $presig = substr($html->innertext, 0, $i);
        DebugEcho("filter_RemoveSignatureWorker sig new text: '$presig'");
        $html->innertext = $presig;
    } else {
        //DebugEcho("filter_RemoveSignatureWorker: no matches {preg_last_error()} '$pattern' $html->plaintext");
        //DebugDump($matches);
    }

    foreach ($html->children() as $e) {
        //DebugEcho("sig: " . $e->plaintext);
        if (!$found && preg_match($pattern, trim($e->plaintext))) {
            DebugEcho("filter_RemoveSignatureWorker signature found: removing");
            $found = true;
        }
        if ($found) {
            $e->outertext = '';
        } else {
            $found = filter_RemoveSignatureWorker($e, $pattern);
        }
    }
    return $found;
}

/**
 * Looks at the content for the given tag and removes all text
 * after that point
 * @param string
 * @param filter
 */
function filter_End(&$content, $config) {
    $end = $config['message_end'];
    if ($end) {
        $pos = strpos($content, $end);
        if ($pos === false) {
            return $content;
        }
        DebugEcho("end filter: $end");
        $content = substr($content, 0, $pos);
    }
}

//filter content for new lines
function filter_Newlines(&$content, $config) {
    if ($config['filternewlines']) {
        DebugEcho("filter_Newlines: filternewlines");
        $search = array(
            "/\r\n/",
            "/\n\n/",
            "/\r\n\r\n/",
            "/\r/",
            "/\n/"
        );
        $replace = array(
            "LINEBREAK",
            "LINEBREAK",
            'LINEBREAK',
            'LINEBREAK',
            'LINEBREAK'
        );

        $result = preg_replace($search, $replace, $content);

        DebugEcho("filter_Newlines: convertnewline: " . $config['convertnewline']);
        if ($config['convertnewline']) {
            $content = preg_replace('/(LINEBREAK)/', "<br />\n", $result);
        } else {
            $content = preg_replace('/(LINEBREAK)/', " ", $result);
        }
    }
}

//strip pgp stuff
function filter_StripPGP($content) {
    $search = array(
        '/-----BEGIN PGP SIGNED MESSAGE-----/',
        '/Hash: SHA1/'
    );
    $replace = array(
        ' ',
        ''
    );
    return preg_replace($search, $replace, $content);
}

function HandleMessageEncoding($contenttransferencoding, $charset, $body, $blogEncoding = 'utf-8', $dequote = true) {
    $charset = strtolower($charset);
    $contenttransferencoding = strtolower($contenttransferencoding);

    DebugEcho("before HandleMessageEncoding");
    DebugEcho("email charset: $charset");
    DebugEcho("email encoding: $contenttransferencoding");
    //DebugDump($body);

    if ($contenttransferencoding == 'base64') {
        DebugEcho("HandleMessageEncoding: base64 detected");
        $body = base64_decode($body);
    }
    if ($dequote && $contenttransferencoding == 'quoted-printable') {
        DebugEcho("quoted-printable detected");
        $body = quoted_printable_decode($body);
    }

    DebugEcho("after HandleMessageEncoding");
    if (strtolower($charset) != strtolower($blogEncoding)) {
        if (!empty($charset) && strtolower($charset) != 'default' && strtolower($charset) != $blogEncoding) {
            DebugEcho("converting from $charset to $blogEncoding");
            //DebugEcho("before: $body");
            $body = iconv($charset, $blogEncoding . '//IGNORE//TRANSLIT', $body);
            //DebugEcho("after: $body");
        } else {
            $body = iconv($blogEncoding, $blogEncoding . '//IGNORE//TRANSLIT', $body);
        }
    }
    return $body;
}

/**
 * This function handles decoding base64 if needed
 */
function DecodeBase64Part(&$part) {
    if (array_key_exists('content-transfer-encoding', $part->headers)) {
        if (strtolower($part->headers['content-transfer-encoding']) == 'base64') {
            DebugEcho("DecodeBase64Part: base64 detected");
            //DebugDump($part);
            $decoded = base64_decode($part->body);
            if (isset($part->disposition) && $part->disposition == 'attachment') {
                $part->body = $decoded;
            } else if (property_exists($part, 'ctype_parameters') && is_array($part->ctype_parameters) && array_key_exists('charset', $part->ctype_parameters)) {
                $charset = strtolower($part->ctype_parameters['charset']);
                if ($charset != 'utf-8') {
                    DebugEcho("converted from: " . $charset);
                    $part->body = iconv($charset, 'UTF-8//TRANSLIT', $decoded);
                    $part->ctype_parameters['charset'] = 'default'; //so we don't double decode
                } else {
                    $part->body = $decoded;
                }
            } else {
                $part->body = $decoded;
            }
            $part->headers['content-transfer-encoding'] = '';
        }
    }
}

/**
 * Checks for the comments tag
 * @return boolean
 */
function tag_AllowCommentsOnPost(&$content) {
    $comments_allowed = get_option('default_comment_status'); // 'open' or 'closed'

    $matches = array();
    if (preg_match("/comments:([0|1|2])/i", $content, $matches)) {
        $content = preg_replace("/comments:$matches[1]/i", "", $content);
        if ($matches[1] == "1") {
            $comments_allowed = "open";
        } else if ($matches[1] == "2") {
            $comments_allowed = "registered_only";
        } else {
            $comments_allowed = "closed";
        }
    }
    return $comments_allowed;
}

function tag_Status(&$content, $currentstatus) {
    $poststatus = $currentstatus;
    $matches = array();
    if (preg_match("/status:\s*(draft|publish|pending|private)/i", $content, $matches)) {
        DebugEcho("tag_Status: found status $matches[1]");
        DebugDump($matches);
        $content = preg_replace("/$matches[0]/i", "", $content);
        $poststatus = $matches[1];
    }
    return $poststatus;
}

function filter_Delay(&$content, $message_date = NULL, $offset = 0) {
    $delay = 0;
    $matches = array();
    if (preg_match("/delay:(-?[0-9dhm]+)/i", $content, $matches) && trim($matches[1])) {
        DebugEcho("found delay: " . $matches[1]);
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $dayMatches = array();
        if (preg_match("/(-?[0-9]+)d/i", $matches[1], $dayMatches)) {
            $days = $dayMatches[1];
        }
        $hourMatches = array();
        if (preg_match("/(-?[0-9]+)h/i", $matches[1], $hourMatches)) {
            $hours = $hourMatches[1];
        }
        $minuteMatches = array();
        if (preg_match("/(-?[0-9]+)m/i", $matches[1], $minuteMatches)) {
            $minutes = $minuteMatches[1];
        }
        $delay = (($days * 24 + $hours) * 60 + $minutes) * 60;
        DebugEcho("calculated delay: $delay");
        $content = preg_replace("/delay:$matches[1]/i", "", $content);
    }
    if (empty($message_date)) {
        $dateInSeconds = time();
    } else {
        $dateInSeconds = strtotime($message_date);
    }
    $dateInSeconds += $delay;

    $post_date = gmdate('Y-m-d H:i:s', $dateInSeconds + ($offset * 3600));
    $post_date_gmt = gmdate('Y-m-d H:i:s', $dateInSeconds);
    DebugEcho("post date: $post_date / $post_date_gmt");

    return array($post_date, $post_date_gmt, $delay);
}

/**
 * This function takes the content of the message - looks for a subject at the begining surrounded by # and then removes that from the content
 */
function tag_Subject($content, $defaultTitle) {
    DebugEcho("tag_Subject: Looking for subject in email body");
    if (substr($content, 0, 1) != "#") {
        DebugEcho("tag_Subject: No subject found, using default [1]");
        return(array($defaultTitle, $content));
    }
    if (strtolower(substr($content, 1, 3)) != "img") {

        $subjectEndIndex = strpos($content, "#", 1);
        if (!$subjectEndIndex > 0) {
            DebugEcho("tag_Subject: No subject found, using default [2]");
            return(array($defaultTitle, $content));
        }
        $subject = substr($content, 1, $subjectEndIndex - 1);
        $content = substr($content, $subjectEndIndex + 1, strlen($content));
        DebugEcho("tag_Subject: Subject found in body: $subject");
        return array($subject, $content);
    } else {
        return(array($defaultTitle, $content));
    }
}

/**
 * This method sorts thru the mime parts of the message. It is looking for files labeled - "applefile" - current
 * this part of the file attachment is not supported
 * @param object
 */
function filter_AppleFile(&$mimeDecodedEmail) {
    $newParts = array();
    $found = false;
    for ($i = 0; $i < count($mimeDecodedEmail->parts); $i++) {
        if ($mimeDecodedEmail->parts[$i]->ctype_secondary == "applefile") {
            $found = true;
            LogInfo("Removing 'applefile'");
        } else {
            $newParts[] = $mimeDecodedEmail->parts[$i];
        }
    }
    if ($found && $newParts) {
        $mimeDecodedEmail->parts = $newParts; //This is now the filtered list of just the preferred type.
    }
}

function postie_media_handle_upload($part, $post_id, $poster, $generate_thubnails = true) {
    $post_data = array();
    $overrides = array('test_form' => false);

    $tmpFile = tempnam(get_temp_dir(), 'postie');

    $fp = fopen($tmpFile, 'w');
    if ($fp) {
        fwrite($fp, $part->body);
        fclose($fp);
    } else {
        EchoInfo("could not write to temp file: '$tmpFile' ");
    }

    //special case to deal with older png implementations
    $namecs = "";
    if (property_exists($part, "ctype_secondary")) {
        $namecs = strtolower($part->ctype_secondary);
        if ($namecs == 'x-png') {
            DebugEcho("postie_media_handle_upload: x-png found, renamed to png");
            $part->ctype_secondary = 'png';
        }
    }

    $name = 'postie-media.' . $namecs;
    if (property_exists($part, 'ctype_parameters') && is_array($part->ctype_parameters)) {
        if (array_key_exists('name', $part->ctype_parameters) && $part->ctype_parameters['name'] != '') {
            $name = $part->ctype_parameters['name'];
        }
        if (array_key_exists('filename', $part->ctype_parameters) && $part->ctype_parameters['filename'] != '') {
            $name = $part->ctype_parameters['filename'];
        }
    }
    if (property_exists($part, 'd_parameters') && is_array($part->d_parameters) && array_key_exists('filename', $part->d_parameters) && $part->d_parameters['filename'] != '') {
        $name = $part->d_parameters['filename'];
    }
    DebugEcho("pre-sanitize name: $name, size: " . filesize($tmpFile));
    $name = sanitize_file_name($name);
    DebugEcho("post sanitize name: $name");
    //DebugDump($part);

    $the_file = array('name' => $name,
        'tmp_name' => $tmpFile,
        'size' => filesize($tmpFile),
        'error' => ''
    );

    if (stristr('.zip', $name)) {
        DebugEcho("exploding zip file");
        $parts = explode('.', $name);
        $ext = $parts[count($parts) - 1];
        $type = $part->primary . '/' . $part->secondary;
        $the_file['ext'] = $ext;
        $the_file['type'] = $type;
        $overrides['test_type'] = false;
    }

    $time = current_time('mysql');
    $post = get_post($post_id);
    if (substr($post->post_date, 0, 4) > 0) {
        DebugEcho("using post date");
        $time = $post->post_date;
    }

    $file = postie_handle_upload($the_file, $overrides, $time);
    //unlink($tmpFile);

    if (isset($file['error'])) {
        DebugDump($file['error']);
        //throw new Exception($file['error']);
        return new WP_Error('upload_error', $file['error']);
    }

    $url = $file['url'];
    $type = $file['type'];
    $file = $file['file'];
    $title = preg_replace('/\.[^.]+$/', '', basename($file));
    $content = '';

    // use image exif/iptc data for title and caption defaults if possible
    if (file_exists(ABSPATH . '/wp-admin/includes/image.php')) {
        include_once(ABSPATH . '/wp-admin/includes/image.php');
        include_once(ABSPATH . '/wp-admin/includes/media.php');
        DebugEcho("reading metadata");
        if ($image_meta = @wp_read_image_metadata($file)) {
            if (trim($image_meta['title'])) {
                $title = $image_meta['title'];
                DebugEcho("Using metadata title: $title");
            }
            if (trim($image_meta['caption'])) {
                $content = $image_meta['caption'];
                DebugEcho("Using metadata caption: $content");
            }
        }
    }

    // Construct the attachment array
    $attachment = array_merge(array(
        'post_mime_type' => $type,
        'guid' => $url,
        'post_parent' => $post_id,
        'post_title' => $title,
        'post_excerpt' => $content,
        'post_content' => $content,
        'post_author' => $poster
            ), $post_data);

    // Save the data
    DebugEcho("before wp_insert_attachment");
    $id = wp_insert_attachment($attachment, $file, $post_id);
    DebugEcho("after wp_insert_attachment: attachement id: $id");

    if (!is_wp_error($id)) {
        if ($generate_thubnails) {
            $amd = wp_generate_attachment_metadata($id, $file);
            DebugEcho("wp_generate_attachment_metadata");
            //DebugDump($amd);
            wp_update_attachment_metadata($id, $amd);
            DebugEcho("wp_update_attachment_metadata complete");
        } else {
            DebugEcho("thumbnail generation disabled");
        }
    } else {
        EchoInfo("There was an error adding the attachement: " . $id->get_error_message());
    }

    return $id;
}

function postie_handle_upload(&$file, $overrides = false, $time = null) {
    // The default error handler.
    if (!function_exists('wp_handle_upload_error')) {

        function wp_handle_upload_error(&$file, $message) {
            return array('error' => $message);
        }

    }

    // A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
    $wp_filetype = wp_check_filetype($file['name']);
    DebugEcho("postie_handle_upload: detected file type for " . $file['name'] . " is " . $wp_filetype['type']);

    if (!isset($file['type'])) {
        DebugEcho("postie_handle_upload: adding type - " . $wp_filetype['type']);
        $file['type'] = $wp_filetype['type'];
    }
    $file = apply_filters('wp_handle_upload_prefilter', $file);

    // You may define your own function and pass the name in $overrides['upload_error_handler']
    $upload_error_handler = 'wp_handle_upload_error';

    // $_POST['action'] must be set and its value must equal $overrides['action'] or this:
    $action = 'wp_handle_upload';

    // Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
    $upload_error_strings = array(false,
        __("The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>."),
        __("The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form."),
        __("The uploaded file was only partially uploaded."),
        __("No file was uploaded."),
        '',
        __("Missing a temporary folder."),
        __("Failed to write file to disk."));

    // Install user overrides. Did we mention that this voids your warranty?
    if (is_array($overrides)) {
        extract($overrides, EXTR_OVERWRITE);
    }
    // A successful upload will pass this test. It makes no sense to override this one.
    if ($file['error'] > 0) {
        return $upload_error_handler($file, $upload_error_strings[$file['error']]);
    }
    // A non-empty file will pass this test.
    if (!($file['size'] > 0 )) {
        return $upload_error_handler($file, __('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.'));
    }
    // A properly uploaded file will pass this test. There should be no reason to override this one.
    if (!file_exists($file['tmp_name'])) {
        return $upload_error_handler($file, __('Specified file failed upload test.'));
    }

    extract($wp_filetype);

    if ((!$type || !$ext ) && !current_user_can('unfiltered_upload')) {
        return $upload_error_handler($file, __('File type does not meet security guidelines. Try another.'));
    }
    if (!$ext) {
        $ext = ltrim(strrchr($file['name'], '.'), '.');
    }
    if (!$type) {
        $type = $file['type'];
    }
    // A writable uploads dir will pass this test. Again, there's no point overriding this one.
    if (!( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] )) {
        return $upload_error_handler($file, $uploads['error']);
    }
    // fix filename (encode non-standard characters)
    $file['name'] = filename_fix($file['name']);
    $filename = wp_unique_filename($uploads['path'], $file['name']);
    DebugEcho("wp_unique_filename: $filename");

    // Move the file to the uploads dir
    $new_file = $uploads['path'] . "/$filename";

    //move_uploaded_file() will not work here
    if (false === rename($file['tmp_name'], $new_file)) {
        DebugEcho("upload: rename failed");
        DebugEcho("old file: " . $file['tmp_name']);
        DebugEcho("new file: $new_file");
        //DebugDump($file);
        //DebugDump($uploads);
        return $upload_error_handler($file, sprintf(__('The uploaded file could not be moved to %s.'), $uploads['path']));
    } else {
        DebugEcho("upload: rename to $new_file succeeded");
    }

    // Set correct file permissions
    $stat = stat(dirname($new_file));
    $perms = $stat['mode'] & 0000666;
    if (chmod($new_file, $perms)) {
        DebugEcho("upload: permissions changed");
    } else {
        DebugEcho("upload: permissions not changed $new_file");
    }

    // Compute the URL
    $url = $uploads['url'] . "/$filename";

    DebugEcho("upload: before apply_filters");
    $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type));
    DebugEcho("upload: after apply_filters");

    return $return;
}

function filename_fix($filename) {
    return str_replace('%', '', urlencode($filename));
}

/**
 * This method sorts thru the mime parts of the message. It is looking for a certain type of text attachment. If
 * that type is present it filters out all other text types. If it is not - then nothing is done
 * @param object
 */
function filter_PreferedText($mimeDecodedEmail, $preferTextType) {
    DebugEcho("filter_PreferedText: begin " . count($mimeDecodedEmail->parts));
    $newParts = array();

    for ($i = 0; $i < count($mimeDecodedEmail->parts); $i++) {
        if (!property_exists($mimeDecodedEmail->parts[$i], "ctype_primary")) {
            DebugEcho("filter_PreferedText: missing ctype_primary");
            //DebugDump($mimeDecodedEmail->parts[$i]);
        } else {
            DebugEcho("filter_PreferedText: part: $i " . $mimeDecodedEmail->parts[$i]->ctype_primary . "/" . $mimeDecodedEmail->parts[$i]->ctype_secondary);
        }
        if (array_key_exists('disposition', $mimeDecodedEmail->parts[$i]) && $mimeDecodedEmail->parts[$i]->disposition == 'attachment') {
            DebugEcho("filter_PreferedText: found disposition/attachment");
            $newParts[] = $mimeDecodedEmail->parts[$i];
        } else {
            if ($mimeDecodedEmail->parts[$i]->ctype_primary == "text") {
                $ctype = $mimeDecodedEmail->parts[$i]->ctype_secondary;
                if ($ctype == 'html' || $ctype == 'plain') {
                    DebugEcho("filter_PreferedText: checking prefered type");
                    if ($ctype == $preferTextType) {
                        DebugEcho("filter_PreferedText: keeping: $ctype");
                        DebugEcho(substr($mimeDecodedEmail->parts[$i]->body, 0, 500));
                        $newParts[] = $mimeDecodedEmail->parts[$i];
                    } else {
                        DebugEcho("filter_PreferedText: removing: $ctype");
                    }
                } else {
                    DebugEcho("filter_PreferedText: keeping: {$mimeDecodedEmail->parts[$i]->ctype_primary}");
                    $newParts[] = $mimeDecodedEmail->parts[$i];
                }
            } else {
                DebugEcho("filter_PreferedText: keeping: {$mimeDecodedEmail->parts[$i]->ctype_primary}");
                $newParts[] = $mimeDecodedEmail->parts[$i];
            }
        }
    }
    if ($newParts) {
        //This is now the filtered list of just the preferred type.
        DebugEcho(count($newParts) . " parts");
        $mimeDecodedEmail->parts = $newParts;
    }
    DebugEcho("filter_PreferedText: end");
}

/**
 * This function can be used to send confirmation or rejection emails
 * It accepts an object containing the entire message
 */
function MailToRecipients(&$mail_content, $testEmail = false, $recipients = array(), $returnToSender = false, $reject = true, $postid = null) {
    DebugEcho("MailToRecipients: send mail");
    if ($testEmail) {
        return false;
    }

    $myemailadd = get_option("admin_email");
    $blogname = get_option("blogname");
    $posturl = '';
    if ($postid != null) {
        $posturl = get_permalink($postid);
    }

    if (count($recipients) == 0) {
        DebugEcho("MailToRecipients: no recipients");
        return false;
    }

    $from = trim($mail_content->headers["from"]);
    $subject = $mail_content->headers['subject'];
    if ($returnToSender) {
        DebugEcho("MailToRecipients: return to sender $returnToSender");
        array_push($recipients, $from);
    }

    $headers = "From: Wordpress <" . $myemailadd . ">\r\n";
    foreach ($recipients as $recipient) {
        $recipient = trim($recipient);
        if (!empty($recipient)) {
            $headers .= "Cc: " . $recipient . "\r\n";
        }
    }
    DebugEcho($headers);
    // Set email subject
    if ($reject) {
        DebugEcho("MailToRecipients: sending reject mail");
        $alert_subject = $blogname . ": Unauthorized Post Attempt from $from";
        if (is_array($mail_content->ctype_parameters) && array_key_exists('boundary', $mail_content->ctype_parameters) && $mail_content->ctype_parameters['boundary']) {
            $boundary = $mail_content->ctype_parameters["boundary"];
        } else {
            $boundary = uniqid("B_");
        }
        // Set sender details
        $headers.="Content-Type:multipart/alternative; boundary=\"$boundary\"\r\n";
        $message = "An unauthorized message has been sent to $blogname.\n";
        $message .= "Sender: $from\n";
        $message .= "Subject: $subject\n";
        $message .= "\n\nIf you wish to allow posts from this address, please add " . $from . " to the registered users list and manually add the content of the email found below.";
        $message .= "\n\nOtherwise, the email has already been deleted from the server and you can ignore this message.";
        $message .= "\n\nIf you would like to prevent postie from forwarding mail in the future, please change the FORWARD_REJECTED_MAIL setting in the Postie settings panel";
        $message .= "\n\nThe original content of the email has been attached.\n\n";
        $mailtext = "--$boundary\r\n";
        $mailtext .= "Content-Type: text/plain;format=flowed;charset=\"iso-8859-1\";reply-type=original\n";
        $mailtext .= "Content-Transfer-Encoding: 7bit\n";
        $mailtext .= "\n";
        $mailtext .= "$message\n";
        if ($mail_content->parts) {
            $mailparts = $mail_content->parts;
        } else {
            $mailparts[] = $mail_content;
        }
        foreach ($mailparts as $part) {
            $mailtext .= "--$boundary\r\n";
            if (array_key_exists('content-type', $part->headers)) {
                $mailtext .= "Content-Type: " . $part->headers["content-type"] . "\n";
            }
            if (array_key_exists('content-transfer-encoding', $part->headers)) {
                $mailtext .= "Content-Transfer-Encoding: " . $part->headers["content-transfer-encoding"] . "\n";
            }
            if (array_key_exists('content-disposition', $part->headers)) {
                $mailtext .= "Content-Disposition: " . $part->headers["content-disposition"] . "\n";
            }
            $mailtext .= "\n";
            if (property_exists($part, 'body')) {
                $mailtext .= $part->body;
            }
        }
    } else {
        $alert_subject = "Successfully posted to $blogname";
        $mailtext = "Your post '$subject' has been successfully published to $blogname <$posturl>.\n";
        DebugEcho("MailToRecipients: $alert_subject\n$mailtext");
    }

    wp_mail($myemailadd, $alert_subject, $mailtext, $headers);

    return true;
}

/**
 * This function handles the basic mime decoding
 * @param string
 * @return array
 */
function DecodeMIMEMail($email) {
    $params = array();
    $params['include_bodies'] = true;
    $params['decode_bodies'] = false;
    $params['decode_headers'] = true;
    $params['input'] = $email;
    $md = new Mail_mimeDecode($email);
    $decoded = $md->decode($params);
    if (empty($decoded->parts)) {
        $decoded->parts = array(); // have an empty array at minimum, so that it is safe for "foreach"
    }
    return $decoded;
}

/**
 * This is used for debugging the mimeDecodedEmail of the mail
 */
function DisplayMIMEPartTypes($mimeDecodedEmail) {
    foreach ($mimeDecodedEmail->parts as $part) {
        DebugEcho($part->ctype_primary . " / " . $part->ctype_secondary . "/ " . $part->headers['content-transfer-encoding']);
    }
}

/**
 * This compares the current address to the list of authorized addresses
 * @param string - email address
 * @return boolean
 */
function isEmailAddressAuthorized($address, $authorized) {
    $r = false;
    if (is_array($authorized)) {
        $a = strtolower(trim($address));
        if (!empty($a)) {
            $r = in_array($a, array_map('strtolower', $authorized));
        }
    }
    return $r;
}

/**
 * This method works around a problem with email address with extra <> in the email address
 * @param string
 * @return string
 */
function RemoveExtraCharactersInEmailAddress($address) {
    $matches = array();
    if (preg_match('/^[^<>]+<([^<> ()]+)>$/', $address, $matches)) {
        $address = $matches[1];
        DebugEcho("RemoveExtraCharactersInEmailAddress: $address (1)");
        DebugDump($matches);
    } else if (preg_match('/<([^<> ()]+)>/', $address, $matches)) {
        $address = $matches[1];
        DebugEcho("RemoveExtraCharactersInEmailAddress: $address (2)");
    }

    return $address;
}

/**
 * This function gleans the name from the 'from:' header if available. If not
 * it just returns the username (everything before @)
 */
function GetNameFromEmail($address) {
    $name = "";
    $matches = array();
    if (preg_match('/^([^<>]+)<([^<> ()]+)>$/', $address, $matches)) {
        $name = $matches[1];
    } else if (preg_match('/<([^<>@ ()]+)>/', $address, $matches)) {
        $name = $matches[1];
    } else if (preg_match('/(.+?)@(.+)/', $address, $matches)) {
        $name = $matches[1];
    }

    return trim($name);
}

/**
 * Choose an appropriate file icon based on the extension and mime type of
 * the attachment
 */
function chooseAttachmentIcon($file, $primary, $secondary, $iconSet = 'silver', $size = '32') {
    if ($iconSet == 'none') {
        return('');
    }
    $fileName = basename($file);
    $parts = explode('.', $fileName);
    $ext = $parts[count($parts) - 1];
    $docExts = array('doc', 'docx');
    $docMimes = array('msword', 'vnd.ms-word', 'vnd.openxmlformats-officedocument.wordprocessingml.document');
    $pptExts = array('ppt', 'pptx');
    $pptMimes = array('mspowerpoint', 'vnd.ms-powerpoint', 'vnd.openxmlformats-officedocument.');
    $xlsExts = array('xls', 'xlsx');
    $xlsMimes = array('msexcel', 'vnd.ms-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $iWorkMimes = array('zip', 'octet-stream');
    $mpgExts = array('mpg', 'mpeg', 'mp2');
    $mpgMimes = array('mpg', 'mpeg', 'mp2');
    $mp3Exts = array('mp3');
    $mp3Mimes = array('mp3', 'mpeg3', 'mpeg');
    $mp4Exts = array('mp4', 'm4v');
    $mp4Mimes = array('mp4', 'mpeg4', 'octet-stream');
    $aacExts = array('m4a', 'aac');
    $aacMimes = array('m4a', 'aac', 'mp4');
    $aviExts = array('avi');
    $aviMimes = array('avi', 'x-msvideo');
    $movExts = array('mov');
    $movMimes = array('mov', 'quicktime');
    if ($ext == 'pdf' && $secondary == 'pdf') {
        $fileType = 'pdf';
    } else if ($ext == 'pages' && in_array($secondary, $iWorkMimes)) {
        $fileType = 'pages';
    } else if ($ext == 'numbers' && in_array($secondary, $iWorkMimes)) {
        $fileType = 'numbers';
    } else if ($ext == 'key' && in_array($secondary, $iWorkMimes)) {
        $fileType = 'key';
    } else if (in_array($ext, $docExts) && in_array($secondary, $docMimes)) {
        $fileType = 'doc';
    } else if (in_array($ext, $pptExts) && in_array($secondary, $pptMimes)) {
        $fileType = 'ppt';
    } else if (in_array($ext, $xlsExts) && in_array($secondary, $xlsMimes)) {
        $fileType = 'xls';
    } else if (in_array($ext, $mp4Exts) && in_array($secondary, $mp4Mimes)) {
        $fileType = 'mp4';
    } else if (in_array($ext, $movExts) && in_array($secondary, $movMimes)) {
        $fileType = 'mov';
    } else if (in_array($ext, $aviExts) && in_array($secondary, $aviMimes)) {
        $fileType = 'avi';
    } else if (in_array($ext, $mp3Exts) && in_array($secondary, $mp3Mimes)) {
        $fileType = 'mp3';
    } else if (in_array($ext, $mpgExts) && in_array($secondary, $mpgMimes)) {
        $fileType = 'mpg';
    } else if (in_array($ext, $aacExts) && in_array($secondary, $aacMimes)) {
        $fileType = 'aac';
    } else {
        $fileType = 'default';
    }
    $fileName = "/icons/$iconSet/$fileType-$size.png";
    if (!file_exists(POSTIE_ROOT . $fileName)) {
        $fileName = "/icons/$iconSet/default-$size.png";
    }
    $iconHtml = "<img src='" . POSTIE_URL . $fileName . "' alt='$fileType icon' />";
    DebugEcho("icon: $iconHtml");
    return $iconHtml;
}

function parseTemplate($id, $type, $template, $orig_filename, $icon = "") {
    DebugEcho("parseTemplate - before: $template");
    $size = 'medium';
    /* we check template for thumb, thumbnail, large, full and use that as
      size. If not found, we default to medium */
    if ($type == 'image') {
        $sizes = array('thumbnail', 'medium', 'large');
        $hwstrings = array();
        $widths = array();
        $heights = array();
        $img_src = array();

        DebugFiltersFor('image_downsize'); //possible overrides for image_downsize()

        for ($i = 0; $i < count($sizes); $i++) {
            list( $img_src[$i], $widths[$i], $heights[$i] ) = image_downsize($id, $sizes[$i]);
            $hwstrings[$i] = image_hwstring($widths[$i], $heights[$i]);
        }
        DebugEcho('Sources');
        DebugDump($img_src);
        DebugEcho('Heights');
        DebugDump($heights);
        DebugEcho('Widths');
        DebugDump($widths);
    }

    $attachment = get_post($id);
    $the_parent = get_post($attachment->post_parent);
    $uploadDir = wp_upload_dir();
    $fileName = basename($attachment->guid);
    $absFileName = $uploadDir['path'] . '/' . $fileName;
    $relFileName = str_replace(ABSPATH, '', $absFileName);
    $fileLink = wp_get_attachment_url($id);
    $pageLink = get_attachment_link($id);

    $template = str_replace('{TITLE}', $attachment->post_title, $template);
    $template = str_replace('{ID}', $id, $template);
    if ($type == 'image') {
        $template = str_replace('{THUMBNAIL}', $img_src[0], $template);
        $template = str_replace('{THUMB}', $img_src[0], $template);
        $template = str_replace('{MEDIUM}', $img_src[1], $template);
        $template = str_replace('{LARGE}', $img_src[2], $template);
        $template = str_replace('{THUMBWIDTH}', $widths[0] . 'px', $template);
        $template = str_replace('{THUMBHEIGHT}', $heights[0] . 'px', $template);
        $template = str_replace('{MEDIUMWIDTH}', $widths[1] . 'px', $template);
        $template = str_replace('{MEDIUMHEIGHT}', $heights[1] . 'px', $template);
        $template = str_replace('{LARGEWIDTH}', $widths[2] . 'px', $template);
        $template = str_replace('{LARGEHEIGHT}', $heights[2] . 'px', $template);
    }
    $template = str_replace('{FULL}', $fileLink, $template);
    $template = str_replace('{FILELINK}', $fileLink, $template);
    $template = str_replace('{PAGELINK}', $pageLink, $template);
    $template = str_replace('{FILENAME}', $orig_filename, $template);
    $template = str_replace('{IMAGE}', $fileLink, $template);
    $template = str_replace('{URL}', $fileLink, $template);
    $template = str_replace('{RELFILENAME}', $relFileName, $template);
    $template = str_replace('{ICON}', $icon, $template);

    DebugEcho("parseTemplate - after: $template");
    return $template . '<br />';
}

/**
 * When sending in HTML email the html refers to the content-id(CID) of the image - this replaces
 * the cid place holder with the actual url of the image sent in
 * @param string - text of post
 * @param array - array of HTML for images for post
 */
function filter_ReplaceImageCIDs(&$content, &$attachments, $config) {
    if ($config['prefer_text_type'] == "html" && count($attachments["cids"])) {
        DebugEcho("ReplaceImageCIDs");
        $used = array();
        foreach ($attachments["cids"] as $key => $info) {
            $key = str_replace('/', '\/', $key);
            $pattern = "/cid:$key/";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $info[0], $content);
                $used[] = $info[1]; //Index of html to ignore
            }
        }
        DebugEcho("# cid attachments: " . count($used));

        $html = array();
        $att = array_values($attachments["html"]); //make sure there are numeric indexes
        DebugEcho('$attachments');
        //DebugDump($attachments);
        DebugEcho('$used');
        //DebugDump($used);
        for ($i = 0; $i < count($attachments["html"]); $i++) {
            if (!in_array($i, $used)) {
                $html[] = $att[$i];
            }
        }

        foreach ($attachments['html'] as $key => $value) {
            if (!in_array($value, $used)) {
                $html[$key] = $value;
            }
        }

        $attachments["html"] = $html;
        //DebugDump($attachments);
    }
}

/**
 * This function handles replacing image place holder #img1# with the HTML for that image
 * @param string - text of post
 * @param array - array of HTML for images for post
 */
function filter_ReplaceImagePlaceHolders(&$content, $attachments, $config, $post_id, $image_pattern, $autoadd_images) {
    if (!$config['custom_image_field']) {
        $startIndex = $config['start_image_count_at_zero'] ? 0 : 1;

        $images = get_posts(array(
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_mime_type' => 'image',));
        DebugEcho("images in post: " . count($images));

        if ((count($images) > 0) && $config['auto_gallery']) {
            $imageTemplate = '[gallery]';
            if ($config['images_append']) {
                $content .= "\n$imageTemplate";
                DebugEcho("Auto gallery: append");
            } else {
                $content = "$imageTemplate\n" . $content;
                DebugEcho("Auto gallery: prepend");
            }
            DebugFiltersFor('post_gallery'); //list gallery handler

            return;
        } else {
            DebugEcho("Auto gallery: none");
        }

        $pics = "";
        $i = 0;
        foreach ($attachments as $attachementName => $imageTemplate) {
            // looks for ' #img1# ' etc... and replaces with image
            $img_placeholder_temp = rtrim(str_replace("%", intval($startIndex + $i), $image_pattern), '#');

            DebugEcho("img_placeholder_temp: $img_placeholder_temp");
            if (stristr($content, $img_placeholder_temp)) {
                // look for caption
                DebugEcho("Found $img_placeholder_temp");
                $caption = '';
                if (preg_match("/$img_placeholder_temp caption=(.*?)#/i", $content, $matches)) {
                    //DebugDump($matches);
                    $caption = trim($matches[1]);
                    if (strlen($caption) > 2 && ($caption[0] == "'" || $caption[0] == '"')) {
                        $caption = substr($caption, 1, strlen($caption) - 2);
                    }
                    DebugEcho("caption: $caption");

                    $img_placeholder_temp = substr($matches[0], 0, -1);
                    DebugEcho($img_placeholder_temp);
                } else {
                    DebugEcho("No caption found");
                }
                DebugEcho("parameterize templete: " . $imageTemplate);
                $imageTemplate = mb_str_replace('{CAPTION}', htmlspecialchars($caption, ENT_QUOTES), $imageTemplate);
                DebugEcho("populated template: " . $imageTemplate);

                $img_placeholder_temp.='#';

                $content = str_ireplace($img_placeholder_temp, $imageTemplate, $content);
                DebugEcho("post replace: $content");
            } else {
                DebugEcho("No $img_placeholder_temp found");
                $imageTemplate = str_replace('{CAPTION}', '', $imageTemplate);
                /* if using the gallery shortcode, don't add pictures at all */
                if (!preg_match("/\[gallery[^\[]*\]/", $content, $matches)) {
                    $pics .= $imageTemplate;
                } else {
                    DebugEcho("gallery detected, not inserting images");
                }
            }
            $i++;
        }
        if ($autoadd_images) {
            if ($config['images_append']) {
                DebugEcho("auto adding images to end");
                $content .= $pics;
            } else {
                DebugEcho("auto adding images to beginning");
                $content = $pics . $content;
            }
        } else {
            DebugEcho("Not auto adding images");
        }
    } else {
        DebugEcho("Custom image field, not adding images");
    }
}

/**
 * This function handles finding and setting the correct subject
 * @return array - (subject,content)
 */
function GetSubject(&$mimeDecodedEmail, &$content, $config) {
    extract($config);
    //assign the default title/subject
    if (!array_key_exists('subject', $mimeDecodedEmail->headers) || empty($mimeDecodedEmail->headers['subject'])) {
        DebugEcho("No subject in email");
        if ($allow_subject_in_mail) {
            list($subject, $content) = tag_Subject($content, $default_title);
        } else {
            DebugEcho("Using default subject");
            $subject = $default_title;
        }
        $mimeDecodedEmail->headers['subject'] = $subject;
    } else {
        $subject = $mimeDecodedEmail->headers['subject'];
        DebugEcho(("Predecoded subject: $subject"));

        if ($allow_subject_in_mail) {
            list($subject, $content) = tag_Subject($content, $subject);
        }
    }
    if (!$allow_html_in_subject) {
        DebugEcho("subject before htmlentities: $subject");
        $subject = htmlentities($subject, ENT_COMPAT, $message_encoding);
        DebugEcho("subject after htmlentities: $subject");
    }

    //This is for ISO-2022-JP - Can anyone confirm that this is still neeeded?
    // escape sequence is 'ESC $ B' == 1b 24 42 hex.
    if (strpos($subject, "\x1b\x24\x42") !== false) {
        // found iso-2022-jp escape sequence in subject... convert!
        DebugEcho("extra parsing for ISO-2022-JP");
        $subject = iconv("ISO-2022-JP", "UTF-8//TRANSLIT", $subject);
    }
    DebugEcho("Subject: $subject");
    return $subject;
}

/**
 * this function determines tags for the post
 *
 */
function tag_Tags(&$content, $defaultTags) {
    $post_tags = array();

    $html = LoadDOM($content);
    if ($html !== false) {
        DebugEcho("tag_Tags: html detected");
        foreach ($html->find('text') as $element) {
            $e = $element->innertext;
            $post_tags = array_merge($post_tags, tag_TagsWorker($e));
            $element->innertext = $e;
        }
        $content = $html->save();
    } else {
        $post_tags = tag_TagsWorker($content);
    }

    if (count($post_tags) == 0 && is_array($defaultTags)) {
        $post_tags = $defaultTags;
    }
    return $post_tags;
}

function tag_TagsWorker(&$content) {
    $post_tags = array();
    $matches = array();
    if (preg_match('/tags: ?(.*)$/im', $content, $matches)) {
        if (!empty($matches[1])) {
            DebugEcho("Found tags: $matches[1]");
            $content = str_replace($matches[0], "", $content);
            $post_tags = preg_split("/,\s*/", trim($matches[1]));
        }
    }
    return $post_tags;
}

function tag_Tags2(&$content, $defaultTags) {
    $post_tags = array();
    //try and determine tags
    $matches = array();
    if (preg_match('/tags: ?(.*)$/im', $content, $matches)) {
        if (!empty($matches[1])) {
            DebugEcho("Found tags: $matches[1]");
            $content = str_replace($matches[0], "", $content);
            $post_tags = preg_split("/,\s*/", trim($matches[1]));
            //DebugDump($post_tags);
        }
    }
    if (count($post_tags) == 0 && is_array($defaultTags)) {
        $post_tags = $defaultTags;
    }
    return $post_tags;
}

/**
 * this function determines excerpt for the post
 *
 */
function tag_Excerpt(&$content, $config) {
    $post_excerpt = '';
    $matches = array();
    if (preg_match('/:excerptstart ?(.*):excerptend/s', $content, $matches)) {
        $content = str_replace($matches[0], "", $content);
        $post_excerpt = $matches[1];
        DebugEcho("excerpt found: $post_excerpt");
        if ($config['filternewlines']) {
            DebugEcho("filtering newlines from excerpt");
            filter_Newlines($post_excerpt, $config);
        }
    }
    return $post_excerpt;
}

/**
 * This function determines the categories ids for the post
 * @return array
 */
function tag_Categories(&$subject, $defaultCategory, $category_match) {
    $original_subject = $subject;
    $post_categories = array();
    $matchtypes = array();
    $matches = array();

    if (preg_match_all('/\[(.[^\[]*)\]/', $subject, $matches)) { // [<category1>] [<category2>] <Subject>
        //preg_match("/]([^\[]*)$/", $subject, $subject_matches);
        //$subject = trim($subject_matches[1]);
        $matchtypes[] = $matches;
    }
    if (preg_match_all('/-(.[^-]*)-/', $subject, $matches)) { // -<category>- -<category2>- <Subject>
        //preg_match("/-(.[^-]*)$/", $subject, $subject_matches);
        //$subject = trim($subject_matches[1]);
        $matchtypes[] = $matches;
    }
    if (preg_match('/(.+): (.*)/', $subject, $matches)) { // <category>:<Subject>
        $category = lookup_category($matches[1], $category_match);
        if (!empty($category)) {
            DebugEcho("colon category: $category");
            $subject = trim($matches[2]);
            $post_categories[] = $category;
        }
    }

    DebugEcho("tag_Categories: found categories");
    DebugDump($matchtypes);
    foreach ($matchtypes as $matches) {
        if (count($matches)) {
            $i = 0;
            foreach ($matches[1] as $match) {
                $category = lookup_category($match, $category_match);
                if (!empty($category)) {
                    $subject = str_replace($matches[0][$i], '', $subject);
                    $post_categories[] = $category;
                }
                $i++;
            }
        }
    }
    if (!count($post_categories)) {
        $post_categories[] = $defaultCategory;
        $subject = $original_subject;
    }
    $subject = trim($subject);
    return $post_categories;
}

function lookup_category($trial_category, $category_match) {
    global $wpdb;
    $trial_category = trim($trial_category);
    $found_category = NULL;
    DebugEcho("lookup_category: $trial_category");

    $term = get_term_by('name', $trial_category, 'category');
    if (!empty($term)) {
        DebugEcho("category: found by name $trial_category");
        DebugDump($term);
        //then category is a named and found
        return $term->term_id;
    }

    if (is_numeric($trial_category)) {
        DebugEcho("category: looking for id '$trial_category'");
        $cat_id = intval($trial_category);
        $term = get_term_by('id', $cat_id, 'category');
        if (!empty($term) && $term->term_id == $trial_category) {
            DebugEcho("category: found by id '$cat_id'");
            DebugDump($term);
            //then cateogry was an ID and found
            return $term->term_id;
        }
    }

    if ($category_match) {
        DebugEcho("category wildcard lookup: $trial_category");
        $sql_sub_name = 'SELECT term_id FROM ' . $wpdb->terms . ' WHERE name LIKE \'' . addslashes($trial_category) . '%\' limit 1';
        $found_category = $wpdb->get_var($sql_sub_name);
        DebugEcho("category wildcard found: $found_category");
    }

    return intval($found_category); //force to integer
}

/**
 * This function just outputs a simple html report about what is being posted in
 */
function DisplayEmailPost($details) {
    //DebugDump($details);
    // Report
    DebugEcho('Post Author: ' . $details["post_author"]);
    DebugEcho('Date: ' . $details["post_date"]);
    foreach ($details["post_category"] as $category) {
        DebugEcho('Category: ' . $category);
    }
    DebugEcho('Ping Status: ' . $details["ping_status"]);
    DebugEcho('Comment Status: ' . $details["comment_status"]);
    DebugEcho('Subject: ' . $details["post_title"]);
    DebugEcho('Postname: ' . $details["post_name"]);
    DebugEcho('Post Id: ' . $details["ID"]);
    DebugEcho('Post Type: ' . $details["post_type"]); /* Added by Raam Dev <raam@raamdev.com> */
    //DebugEcho('Posted content: '.$details["post_content"]);
}

/**
 * Takes a value and builds a simple simple yes/no select box
 * @param string
 * @param string
 * @param string
 * @param string
 */
function BuildBooleanSelect($label, $id, $current_value, $recommendation = NULL, $options = null) {

    $html = "<tr>
	<th scope='row'>" . $label . ":";
    if (!empty($recommendation)) {
        $html.='<br /><span class = "recommendation">' . $recommendation . '</span>';
    }

    if (!(is_array($options) && count($options) == 2)) {
        $options = Array('Yes', 'No');
    }

    $html.="</th>
	<td><select name='$id' id='$id'>
            <option value='1'>" . __($options[0], 'postie') . "</option>
            <option value='0' " . (!$current_value ? "selected='selected'" : "") . ">" . __($options[1], 'postie') . '</option>
    </select>';
    $html.="</td>\n</tr>";

    return $html;
}

/**
 * This takes an array and display a text box for editing
 * @param string
 * @param string
 * @param array
 * @param string
 */
function BuildTextArea($label, $id, $current_value, $recommendation = NULL) {
    $html = "<tr> <th scope='row'>" . $label . ":";
    if ($recommendation) {
        $html.="<br /><span class='recommendation'>" . $recommendation . "</span>";
    }
    $html.="</th>";

    $html .="<td><br /><textarea cols=40 rows=3 name='$id' id='$id'>";
    $current_value = preg_split("/[\r\n]+/", esc_attr(trim($current_value)));
    if (is_array($current_value)) {
        foreach ($current_value as $item) {
            $html .= "$item\n";
        }
    }
    $html .= "</textarea></td></tr>";
    return $html;
}

/**
 * This function resets all the configuration options to the default
 */
function config_ResetToDefault() {
    $newconfig = config_GetDefaults();
    $config = get_option('postie-settings');
    $save_keys = array('mail_password', 'mail_server', 'mail_server_port', 'mail_userid', 'input_protocol');
    foreach ($save_keys as $key) {
        $newconfig[$key] = $config[$key];
    }
    update_option('postie-settings', $newconfig);
    config_Update($newconfig);
    return $newconfig;
}

/**
 * This function used to handle updating the configuration.
 * @return boolean
 */
function config_Update($data) {
    UpdatePostiePermissions($data["role_access"]);
    // We also update the cron settings
    postie_cron($data['interval']);
}

/**
 * return an array of the config defaults
 */
function config_GetDefaults() {
    include('templates/audio_templates.php');
    include('templates/image_templates.php');
    include('templates/video1_templates.php');
    include('templates/video2_templates.php');
    include 'templates/general_template.php';
    return array(
        'add_meta' => 'no',
        'admin_username' => 'admin',
        'allow_html_in_body' => true,
        'allow_html_in_subject' => true,
        'allow_subject_in_mail' => true,
        'audiotemplate' => $simple_link,
        'audiotypes' => array('m4a', 'mp3', 'ogg', 'wav', 'mpeg'),
        'authorized_addresses' => array(),
        'banned_files_list' => array(),
        'confirmation_email' => '',
        'convertnewline' => false,
        'converturls' => true,
        'custom_image_field' => false,
        'default_post_category' => NULL,
        'category_match' => true,
        'default_post_tags' => array(),
        'default_title' => "Live From The Field",
        'delete_mail_after_processing' => true,
        'drop_signature' => true,
        'filternewlines' => true,
        'forward_rejected_mail' => true,
        'icon_set' => 'silver',
        'icon_size' => 32,
        'auto_gallery' => false,
        'image_new_window' => false,
        'image_placeholder' => "#img%#",
        'images_append' => true,
        'imagetemplate' => $wordpress_default,
        'imagetemplates' => $imageTemplates,
        'input_protocol' => "pop3",
        'interval' => 'twiceperhour',
        'mail_server' => NULL,
        'mail_server_port' => 110,
        'mail_userid' => NULL,
        'mail_password' => NULL,
        'maxemails' => 0,
        'message_start' => ":start",
        'message_end' => ":end",
        'message_encoding' => "UTF-8",
        'message_dequote' => true,
        'post_status' => 'publish',
        'prefer_text_type' => "plain",
        'return_to_sender' => false,
        'role_access' => array(),
        'selected_audiotemplate' => 'simple_link',
        'selected_imagetemplate' => 'wordpress_default',
        'selected_video1template' => 'simple_link',
        'selected_video2template' => 'simple_link',
        'shortcode' => false,
        'sig_pattern_list' => array('--\s?[\r\n]?', '--\s', '--', '---'),
        'smtp' => array(),
        'start_image_count_at_zero' => false,
        'supported_file_types' => array('application'),
        'turn_authorization_off' => false,
        'time_offset' => get_option('gmt_offset'),
        'video1template' => $simple_link,
        'video1types' => array('mp4', 'mpeg4', '3gp', '3gpp', '3gpp2', '3gp2', 'mov', 'mpeg', 'quicktime'),
        'video2template' => $simple_link,
        'video2types' => array('x-flv'),
        'video1templates' => $video1Templates,
        'video2templates' => $video2Templates,
        'wrap_pre' => 'no',
        'featured_image' => false,
        'email_tls' => false,
        'post_format' => 'standard',
        'post_type' => 'post',
        'generaltemplates' => $generalTemplates,
        'generaltemplate' => $postie_default,
        'selected_generaltemplate' => 'postie_default',
        'generate_thumbnails' => true
    );
}

/**
 * =======================================================
 * the following functions are only used to retrieve the old (pre 1.4) config, to convert it
 * to the new format
 */
function config_GetListOfArrayConfig() {
    return(array('SUPPORTED_FILE_TYPES', 'AUTHORIZED_ADDRESSES',
        'SIG_PATTERN_LIST', 'BANNED_FILES_LIST', 'VIDEO1TYPES',
        'VIDEO2TYPES', 'AUDIOTYPES', 'SMTP'));
}

function config_Read() {
    $config = get_option('postie-settings');
    return config_ValidateSettings($config);
}

/**
 * This function retrieves the old-format config (pre 1.4) from the database
 * @return array
 */
function config_ReadOld() {
    $config = array();
    global $wpdb;
    $wpdb->query("SHOW TABLES LIKE '" . $GLOBALS["table_prefix"] . "postie_config'");
    if ($wpdb->num_rows > 0) {
        $data = $wpdb->get_results("SELECT label,value FROM " . $GLOBALS["table_prefix"] . "postie_config;");
        if (is_array($data)) {
            foreach ($data as $row) {
                if (in_array($row->label, config_GetListOfArrayConfig())) {
                    $config[$row->label] = unserialize($row->value);
                } else {
                    $config[$row->label] = $row->value;
                }
            }
        }
    }
    return $config;
}

/**
 * This function processes the old-format config (pre 1.4) for conversion to the 1.4 format
 * @return array
 * @access private
 */
function config_UpgradeOld() {
    $config = config_ReadOld();
    if (!isset($config["ADMIN_USERNAME"])) {
        $config["ADMIN_USERNAME"] = 'admin';
    }
    if (!isset($config["PREFER_TEXT_TYPE"])) {
        $config["PREFER_TEXT_TYPE"] = "plain";
    }
    if (!isset($config["DEFAULT_TITLE"])) {
        $config["DEFAULT_TITLE"] = "Live From The Field";
    }
    if (!isset($config["INPUT_PROTOCOL"])) {
        $config["INPUT_PROTOCOL"] = "pop3";
    }
    if (!isset($config["IMAGE_PLACEHOLDER"])) {
        $config["IMAGE_PLACEHOLDER"] = "#img%#";
    }
    if (!isset($config["IMAGES_APPEND"])) {
        $config["IMAGES_APPEND"] = true;
    }
    if (!isset($config["ALLOW_SUBJECT_IN_MAIL"])) {
        $config["ALLOW_SUBJECT_IN_MAIL"] = true;
    }
    if (!isset($config["DROP_SIGNATURE"])) {
        $config["DROP_SIGNATURE"] = true;
    }
    if (!isset($config["MESSAGE_START"])) {
        $config["MESSAGE_START"] = ":start";
    }
    if (!isset($config["MESSAGE_END"])) {
        $config["MESSAGE_END"] = ":end";
    }
    if (!isset($config["FORWARD_REJECTED_MAIL"])) {
        $config["FORWARD_REJECTED_MAIL"] = true;
    }
    if (!isset($config["RETURN_TO_SENDER"])) {
        $config["RETURN_TO_SENDER"] = false;
    }
    if (!isset($config["CONFIRMATION_EMAIL"])) {
        $config["CONFIRMATION_EMAIL"] = '';
    }
    if (!isset($config["ALLOW_HTML_IN_SUBJECT"])) {
        $config["ALLOW_HTML_IN_SUBJECT"] = true;
    }
    if (!isset($config["ALLOW_HTML_IN_BODY"])) {
        $config["ALLOW_HTML_IN_BODY"] = true;
    }
    if (!isset($config["START_IMAGE_COUNT_AT_ZERO"])) {
        $config["START_IMAGE_COUNT_AT_ZERO"] = false;
    }
    if (!isset($config["MESSAGE_ENCODING"])) {
        $config["MESSAGE_ENCODING"] = "UTF-8";
    }
    if (!isset($config["MESSAGE_DEQUOTE"])) {
        $config["MESSAGE_DEQUOTE"] = true;
    }
    if (!isset($config["TURN_AUTHORIZATION_OFF"])) {
        $config["TURN_AUTHORIZATION_OFF"] = false;
    }
    if (!isset($config["CUSTOM_IMAGE_FIELD"])) {
        $config["CUSTOM_IMAGE_FIELD"] = false;
    }
    if (!isset($config["CONVERTNEWLINE"])) {
        $config["CONVERTNEWLINE"] = false;
    }
    if (!isset($config["SIG_PATTERN_LIST"])) {
        $config["SIG_PATTERN_LIST"] = array('--', '---');
    }
    if (!isset($config["BANNED_FILES_LIST"])) {
        $config["BANNED_FILES_LIST"] = array();
    }
    if (!isset($config["SUPPORTED_FILE_TYPES"])) {
        $config["SUPPORTED_FILE_TYPES"] = array("application");
    }
    if (!isset($config["AUTHORIZED_ADDRESSES"])) {
        $config["AUTHORIZED_ADDRESSES"] = array();
    }
    if (!isset($config["MAIL_SERVER"])) {
        $config["MAIL_SERVER"] = NULL;
    }
    if (!isset($config["MAIL_SERVER_PORT"])) {
        $config["MAIL_SERVER_PORT"] = NULL;
    }
    if (!isset($config["MAIL_USERID"])) {
        $config["MAIL_USERID"] = NULL;
    }
    if (!isset($config["MAIL_PASSWORD"])) {
        $config["MAIL_PASSWORD"] = NULL;
    }
    if (!isset($config["DEFAULT_POST_CATEGORY"])) {
        $config["DEFAULT_POST_CATEGORY"] = NULL;
    }
    if (!isset($config["DEFAULT_POST_TAGS"])) {
        $config["DEFAULT_POST_TAGS"] = NULL;
    }
    if (!isset($config["TIME_OFFSET"])) {
        $config["TIME_OFFSET"] = get_option('gmt_offset');
    }
    if (!isset($config["WRAP_PRE"])) {
        $config["WRAP_PRE"] = 'no';
    }
    if (!isset($config["CONVERTURLS"])) {
        $config["CONVERTURLS"] = true;
    }
    if (!isset($config["SHORTCODE"])) {
        $config["SHORTCODE"] = false;
    }
    if (!isset($config["ADD_META"])) {
        $config["ADD_META"] = 'no';
    }
    $config['ICON_SETS'] = array('silver', 'black', 'white', 'custom', 'none');
    if (!isset($config["ICON_SET"])) {
        $config["ICON_SET"] = 'silver';
    }
    $config['ICON_SIZES'] = array(32, 48, 64);
    if (!isset($config["ICON_SIZE"])) {
        $config["ICON_SIZE"] = 32;
    }

    //audio
    include('templates/audio_templates.php');
    if (!isset($config["SELECTED_AUDIOTEMPLATE"])) {
        $config['SELECTED_AUDIOTEMPLATE'] = 'simple_link';
    }
    $config['AUDIOTEMPLATES'] = $audioTemplates;
    if (!isset($config["SELECTED_VIDEO1TEMPLATE"])) {
        $config['SELECTED_VIDEO1TEMPLATE'] = 'simple_link';
    }
    if (!isset($config["AUDIOTEMPLATE"])) {
        $config["AUDIOTEMPLATE"] = $simple_link;
    }

    //video1
    if (!isset($config["VIDEO1TYPES"])) {
        $config['VIDEO1TYPES'] = array('mp4', 'mpeg4', '3gp', '3gpp', '3gpp2', '3gp2', 'mov', 'mpeg', 'quicktime');
    }
    if (!isset($config["AUDIOTYPES"])) {
        $config['AUDIOTYPES'] = array('m4a', 'mp3', 'ogg', 'wav', 'mpeg');
    }
    if (!isset($config["SELECTED_VIDEO2TEMPLATE"])) {
        $config['SELECTED_VIDEO2TEMPLATE'] = 'simple_link';
    }
    include('templates/video1_templates.php');
    $config['VIDEO1TEMPLATES'] = $video1Templates;
    if (!isset($config["VIDEO1TEMPLATE"])) {
        $config["VIDEO1TEMPLATE"] = $simple_link;
    }

    //video2
    if (!isset($config["VIDEO2TYPES"])) {
        $config['VIDEO2TYPES'] = array('x-flv');
    }
    if (!isset($config["POST_STATUS"])) {
        $config["POST_STATUS"] = 'publish';
    }
    if (!isset($config["IMAGE_NEW_WINDOW"])) {
        $config["IMAGE_NEW_WINDOW"] = false;
    }
    if (!isset($config["FILTERNEWLINES"])) {
        $config["FILTERNEWLINES"] = true;
    }
    include('templates/video2_templates.php');
    $config['VIDEO2TEMPLATES'] = $video2Templates;
    if (!isset($config["VIDEO2TEMPLATE"])) {
        $config["VIDEO2TEMPLATE"] = $simple_link;
    }

    //image
    if (!isset($config["SELECTED_IMAGETEMPLATE"])) {
        $config['SELECTED_IMAGETEMPLATE'] = 'wordpress_default';
    }
    if (!isset($config["SMTP"])) {
        $config["SMTP"] = array();
    }
    include('templates/image_templates.php');
    if (!isset($config["IMAGETEMPLATE"])) {
        $config["IMAGETEMPLATE"] = $wordpress_default;
    }
    $config['IMAGETEMPLATES'] = $imageTemplates;

    //general
    include('templates/general_template.php');
    if (!isset($config["GENERALTEMPLATE"])) {
        $config["GENERALTEMPLATE"] = $postie_default;
    }

    return $config;
}

/**
 * This function returns the old
  -format config (pre 1.4)
 * @return array
 */
function config_GetOld() {
    $config = config_UpgradeOld();
//These should only be modified if you are testing
    $config["DELETE_MAIL_AFTER_PROCESSING"] = true;
    $config["POST_TO_DB"] = true;
    $config["TEST_EMAIL"] = false;
    $config["TEST_EMAIL_ACCOUNT"] = "blogtest";
    $config["TEST_EMAIL_PASSWORD"] = "yourpassword";
    if (file_exists(POSTIE_ROOT . '/postie_test_variables.php')) {
        include(POSTIE_ROOT . '/postie_test_variables.php');
    }
//include(POSTIE_ROOT . "/../postie-test.php");
// These are computed
    $config["TIME_OFFSET"] = get_option('gmt_offset');
    $config["POSTIE_ROOT"] = POSTIE_ROOT;
    for ($i = 0; $i < count($config["AUTHORIZED_ADDRESSES"]); $i++) {
        $config["AUTHORIZED_ADDRESSES"][$i] = strtolower($config["AUTHORIZED_ADDRESSES"][$i]);
    }
    return $config;
}

/**
 * end of functions u sed to retrieve the old (pre 1.4) config
 * =======================================================
 */

/**
 * Returns a list of config keys that should be arrays
 * @return array
 */
function config_ArrayedSettings() {
    return array(
        ', ' => array('audiotypes', 'video1types', 'video2types', 'default_post_tags'),
        "\n" => array('smtp', 'authorized_addresses', 'supported_file_types', 'banned_files_list', 'sig_pattern_list'));
}

/**
 * Detects if they can do IMAP
 * @return boolean
 */
function HasIMAPSupport($display = true) {
    $function_list = array("imap_open",
        "imap_delete",
        "imap_expunge",
        "imap_body",
        "imap_fetchheader");
    return(HasFunctions($function_list, $display));
}

function HasMbStringInstalled() {
    $function_list = array("mb_detect_encoding");
    return(HasFunctions($function_list));
}

function HasIconvInstalled($display = true) {
    $function_list = array("iconv");
    return(HasFunctions($function_list, $display));
}

/**
 * Handles verifing that a list of functions exists
 * @return boolean
 * @param array
 */
function HasFunctions($function_list, $display = true) {
    foreach ($function_list as $function) {
        if (!function_exists($function)) {
            if ($display) {
                EchoInfo("Missing $function");
            }
            return false;
        }
    }
    return true;
}

/**
 * This function tests to see if postie is its own directory
 */
function isPostieInCorrectDirectory() {
    $dir_parts = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
    $last_dir = array_pop($dir_parts);
    if ($last_dir != "postie") {
        return false;
    }
    return true;
}

/**
 * This function looks for markdown which causes problems with postie
 */
function isMarkdownInstalled() {
    if (in_array("markdown.php", get_option("active_plugins"))) {
        return true;
    }
    return false;
}

/**
 * validates the config form output, fills in any gaps by using the defaults,
 * and ensures that arrayed items are stored as such
 */
function config_ValidateSettings($in) {
    //DebugEcho("config_ValidateSettings");

    $out = array();

    //DebugDump($in);
    // use the default as a template:
    // if a field is present in the defaults, we want to store it; otherwise we discard it
    $allowed_keys = config_GetDefaults();
    foreach ($allowed_keys as $key => $default) {
        if (is_array($in)) {
            $out[$key] = array_key_exists($key, $in) ? $in[$key] : $default;
        } else {
            $out[$key] = $default;
        }
    }

    // some fields are always forced to lower case:
    $lowercase = array('authorized_addresses', 'smtp', 'supported_file_types', 'video1types', 'video2types', 'audiotypes');
    foreach ($lowercase as $field) {
        $out[$field] = ( is_array($out[$field]) ) ? array_map("strtolower", $out[$field]) : strtolower($out[$field]);
    }
    $arrays = config_ArrayedSettings();

    foreach ($arrays as $sep => $fields) {
        foreach ($fields as $field) {
            if (!is_array($out[$field])) {
                $out[$field] = explode($sep, trim($out[$field]));
            }
            foreach ($out[$field] as $key => $val) {
                $tst = trim($val);
                if
                (empty($tst)) {
                    unset($out[$field][$key]);
                } else {
                    $out[$field][$key] = $tst;
                }
            }
        }
    }

    config_Update($out);
    return $out;
}

/**
 * This function handles setting up the basic permissions
 */
function UpdatePostiePermissions($role_access) {
    global $wp_roles;
    if (is_object($wp_roles)) {
        $admin = $wp_roles->get_role("administrator");
        $admin->add_cap("config_postie");
        $admin->add_cap("post_via_postie");

        if (!is_array($role_access)) {
            $role_access = array();
        }
        foreach ($wp_roles->role_names as $roleId => $name) {
            $role = $wp_roles->get_role($roleId);
            if ($roleId != "administrator") {
                if (array_key_exists($roleId, $role_access)) {
                    $role->add_cap("post_via_postie");
                    //DebugEcho("added $roleId");
                } else {
                    $role->remove_cap("post_via_postie");
                    //DebugEcho("removed $roleId");
                }
            }
        }
    }
}

function IsDebugMode() {
    return (defined('POSTIE_DEBUG') && POSTIE_DEBUG == true);
}

function DebugEmailOutput($email, $mimeDecodedEmail) {
    if (IsDebugMode()) {
        //DebugDump($email);
        //DebugDump($mimeDecodedEmail);

        $dname = POSTIE_ROOT . DIRECTORY_SEPARATOR . "test_emails" . DIRECTORY_SEPARATOR;
        if (is_dir($dname)) {
            $fname = $dname . sanitize_file_name($mimeDecodedEmail->headers["message-id"]);
            $file = fopen($fname . ".txt ", "w");
            fwrite($file, $email);
            fclose($file);

            $file = fopen($fname . "-mime.txt ", "w");
            fwrite($file, print_r($mimeDecodedEmail, true));
            fclose($file);

            $file = fopen($fname . ".php ", "w");
            fwrite($file, serialize($email));
            fclose($file);
        }
    }
}

function tag_CustomImageField(&$content, &$attachments, $config) {
    $customImages = array();
    if ($config['custom_image_field']) {
        DebugEcho("Looking for custom images");
        //DebugDump($attachments["html"]);

        foreach ($attachments["html"] as $key => $value) {
            //DebugEcho("checking " . htmlentities($value));
            $matches = array();
            if (preg_match("/src\s*=\s*['\"]([^'\"]*)['\"]/i", $value, $matches)) {
                DebugEcho("found custom image: " . $matches[1]);
                array_push($customImages, $matches[1]);
            }
        }
    }
    return $customImages;
}

/**
 * Special Vodafone handler - their messages are mostly vendor trash - this strips them down.
 */
function filter_VodafoneHandler(&$content, &$attachments) {
    if (preg_match('/You have been sent a message from Vodafone mobile/', $content)) {
        DebugEcho("Vodafone message");
        $index = strpos($content, "TEXT:");
        if (strpos !== false) {
            $alt_content = substr($content, $index, strlen($content));
            $matches = array();
            if (preg_match("/<font face=\"verdana,helvetica,arial\" class=\"standard\" color=\"#999999\"><b>(.*)<\/b>/", $alt_content, $matches)) {
                //The content is now just the text of the message
                $content = $matches[1];
                //Now to clean up the attachments
                $vodafone_images = array("live.gif", "smiley.gif", "border_left_txt.gif", "border_top.gif", "border_bot.gif", "border_right.gif", "banner1.gif", "i_text.gif", "i_picture.gif",);
                while (list($key, $value) = each($attachments['cids'])) {
                    if (!in_array($key, $vodafone_images)) {
                        $content .= "<br/>" . $attachments['html'][$attachments['cids'][$key][1]];
                    }
                }
            }
        }
    }
}

function DebugFiltersFor($hook = '') {
    global $wp_filter;
    if (empty($hook) || !isset($wp_filter[$hook])) {
        DebugEcho("No registered filters for $hook");
        return;
    }
    DebugEcho("Registered filters for $hook");
    DebugDump($wp_filter[$hook]);
}

function postie_test_config() {
    $config = config_Read();
    extract($config);
    get_currentuserinfo();

    if (!current_user_can('manage_options')) {
        LogInfo("non-admin tried to set options");
        echo "<h2> Sorry only admin can run this file</h2>";
        exit();
    }
    ?>
    <div class="wrap">
        <h1>Postie Configuration Test</h1>
        <?php
        postie_environment();
        ?>

        <h2>Clock Tests</h2>
        <p>This shows what time it would be if you posted right now</p>
        <?php
        $content = "";
        $data = filter_Delay($content, null, $config['time_offset']);
        EchoInfo("Post time: $data[0]");
        ?>

        <h2>Connect to Mail Host</h2>

        <?php
        if (!$mail_server || !$mail_server_port || !$mail_userid) {
            EchoInfo("FAIL - server settings not complete");
        } else {
            DebugEcho("checking");
        }

        switch (strtolower($config["input_protocol"])) {
            case 'imap':
            case 'imap-ssl':
            case 'pop3-ssl':
                if (!HasIMAPSupport()) {
                    EchoInfo("Sorry - you do not have IMAP php module installed - it is required for this mail setting.");
                } else {
                    require_once("postieIMAP.php");
                    $mail_server = &PostieIMAP::Factory($config["input_protocol"]);
                    if ($email_tls) {
                        $mail_server->TLSOn();
                    }
                    if (!$mail_server->connect($config["mail_server"], $config["mail_server_port"], $config["mail_userid"], $config["mail_password"])) {
                        EchoInfo("Unable to connect. The server said:");
                        EchoInfo($mail_server->error());
                    } else {
                        EchoInfo("Successful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
                        EchoInfo("# of waiting messages: " . $mail_server->getNumberOfMessages());
                        $mail_server->disconnect();
                    }
                }
                break;
            case 'pop3':
            default:
                require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-pop3.php');
                $pop3 = new POP3();
                if (defined('POSTIE_DEBUG')) {
                    $pop3->DEBUG = POSTIE_DEBUG;
                }
                if (!$pop3->connect($config["mail_server"], $config["mail_server_port"])) {
                    EchoInfo("Unable to connect. The server said:" . $pop3->ERROR);
                } else {
                    EchoInfo("Sucessful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
                    $msgs = $pop3->login($config["mail_userid"], $config["mail_password"]);
                    if ($msgs === false) {
                        //workaround for bug reported here Apr 12, 2013
                        //https://sourceforge.net/tracker/?func=detail&atid=100311&aid=3610701&group_id=311
                        //originally repoted here:
                        //https://core.trac.wordpress.org/ticket/10587
                        if (empty($pop3->ERROR)) {
                            EchoInfo("No waiting messages");
                        } else {
                            EchoInfo("Unable to login. The server said:" . $pop3->ERROR);
                        }
                    } else {
                        EchoInfo("# of waiting messages: $msgs");
                    }
                    $pop3->quit();
                }
                break;
        }
        ?>
    </div>
    <?php
}

function postie_get_mail() {
    require_once (plugin_dir_path(__FILE__) . 'mimedecode.php');
    if (!function_exists('file_get_html')) {
        require_once (plugin_dir_path(__FILE__) . 'simple_html_dom.php');
    }

    EchoInfo("Starting mail fetch");
    postie_environment();
    $wp_content_path = dirname(dirname(dirname(__FILE__)));
    DebugEcho("wp_content_path: $wp_content_path");
    if (file_exists($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php")) {
        DebugEcho("found filterPostie.php in $wp_content_path");
        include_once ($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php");
    }

    if (has_filter('postie_post')) {
        echo "Postie: filter 'postie_post' is depricated in favor of 'postie_post_before'";
    }

    $test_email = null;
    $config = config_Read();
    //extract($config);
    if (!array_key_exists('maxemails', $config)) {
        $config['maxemails'] = 0;
    }

    $emails = FetchMail($config['mail_server'], $config['mail_server_port'], $config['mail_userid'], $config['mail_password'], $config['input_protocol'], $config['time_offset'], $test_email, $config['delete_mail_after_processing'], $config['maxemails'], $config['email_tls']);
    $message = 'Done.';

    EchoInfo(sprintf(__("There are %d messages to process", "postie"), count($emails)));

    if (function_exists('memory_get_usage')) {
        DebugEcho(__("memory at start of email processing:") . memory_get_usage());
    }

    DebugDump($config);

    //loop through messages
    $message_number = 0;
    foreach ($emails as $email) {
        $message_number++;
        DebugEcho("$message_number: ------------------------------------");
        //sanity check to see if there is any info in the message
        if ($email == NULL) {
            $message = __('Dang, message is empty!', 'postie');
            EchoInfo("$message_number: $message");
            continue;
        } else if ($email == 'already read') {
            $message = __("Message is already marked 'read'.", 'postie');
            EchoInfo("$message_number: $message");
            continue;
        }

        $mimeDecodedEmail = DecodeMIMEMail($email);

        DebugEmailOutput($email, $mimeDecodedEmail);

        //Check poster to see if a valid person
        $poster = ValidatePoster($mimeDecodedEmail, $config);
        $project = ValidateProject($mimeDecodedEmail, $config);
        if (!empty($poster)) {
            PostEmail($poster, $project, $mimeDecodedEmail, $config);
        } else {
            EchoInfo("Ignoring email - not authorized.");
        }
        flush();
    }
    EchoInfo("Mail fetch complete, $message_number emails");

    if (function_exists('memory_get_usage')) {
        DebugEcho("memory at end of email processing:" . memory_get_usage());
    }
}
