<?php
$fbid = $_GET['fbid'];
$post_id = $_GET['post_id'];


// username from userid
function username_sa_fb($userid) {
    $file = file_get_contents("http://graph.facebook.com/$userid", true);
    $obj = json_decode($file);
    $username = $obj->username;
    return $username;
}

// check for duplicate comments
function double_comment_check($cid, $mess) {
    global $wpdb;
    $broj = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '" . $cid . "' AND comment_content = '" . $mess . "'");
    return $broj;
}

// check for duplicates
function double_commentmeta_check($cid, $mv) {
    global $wpdb;
    $broj = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."commentmeta WHERE comment_id = '" . $cid . "' AND meta_value = '" . $mv . "'");
    return $broj;
}

// comments import
function get_comments($id, $post_id) {
    $id = trim($id);
    $file = file_get_contents("http://graph.facebook.com/$id/comments?limit=5000", true);
    $obj = json_decode($file);
    $newar = array_reverse($obj->data);

    foreach ($newar as $element) {
        $id = $element->id;
        $message = $element->message;
        $created_time = $element->created_time;
        $name = $element->from->name;
        $web = $element->from->id;

        $username = username_sa_fb($web); // get username
        // check author username
        if ($username != "") {
            $prolink = "http://www.facebook.com/$username";
        }
        else{
            $prolink = "";
        }
        
        // replace strings
        $message = str_replace("'", '´', $message);
        $name = str_replace("'", '´', $name);

        // create time
        $date = explode("T", $created_time);
        $d = $date[0];
        $t = explode("+", $date[1]);
        $t = $t[0];
        $dt = "$d $t";
        
        // agent meta
        $ag = "facebook-comment-importer plugin";
        // is double?
        $jel_duplikat = double_comment_check($post_id, $message);
        
        if ($jel_duplikat == 0) {
            // show comment
            echo "<b>$name:</b> $message <br>$created_time<hr>";
            
            // save comment
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'comments', array(
                    'comment_post_ID' => $post_id,
                    'comment_author' => $name,
                    'comment_author_url' => $prolink,
                    'comment_content' => $message,
                    'comment_agent' => $ag,
                    'comment_date' => $dt,
                    'comment_date_gmt' => $dt ), array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
                );

            $lastid = mysql_insert_id();
            $metavalue = "288180548443_$id";
            wp_update_comment_count($post_id);
            
            // check double commentmeta
            $n = double_commentmeta_check($lastid, $metavalue);
            if ($n == 0) {
                // save commentmeta
                global $wpdb;
                $wpdb->insert($wpdb->prefix . 'commentmeta', array(
                        'meta_key' => 'fbci_comment_id',
                        'meta_value' => $metavalue,
                        'comment_id' => $lastid ), array('%d', '%s', '%s')
                    );
            }
        } else {
            echo "Skiped! Already Exist!<br>";
            //break;
        }
        
    }
}

get_comments($fbid,$post_id);
?><meta http-equiv="REFRESH" content="0;url=?page=fbsync_comments_free"><?
?>
