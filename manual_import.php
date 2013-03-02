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

// provjera duplikata za komentare
function dupli_komentari_provjera($cid, $mess) {
    global $wpdb;
    $broj = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '" . $cid . "' AND comment_content = '" . $mess . "'");
    return $broj;
}

// provjera duplikata
function dupli_commentmeta_provjera($cid, $mv) {
    global $wpdb;
    $broj = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."commentmeta WHERE comment_id = '" . $cid . "' AND meta_value = '" . $mv . "'");
    return $broj;
}

// import komentara
function pokupi_komentare($id, $post_id) {
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

        $username = username_sa_fb($web); // dohvati username
        // provjeri autorov username
        if ($username != "") {
            $prolink = "http://www.facebook.com/$username";
        }
        else{
            $prolink = "";
        }
        
        // izaci nedozvoljene znakove
        $message = str_replace("'", '´', $message);
        $name = str_replace("'", '´', $name);

        // sastavi vrijeme
        $date = explode("T", $created_time);
        $d = $date[0];
        $t = explode("+", $date[1]);
        $t = $t[0];
        $dt = "$d $t";
        
        // agent
        $ag = "facebook-comment-importer plugin";
        // provjeri jel duplikat
        $jel_duplikat = dupli_komentari_provjera($post_id, $message);
        
        if ($jel_duplikat == 0) {
            // prikazi komentar
            echo "<b>$name:</b> $message <br>$created_time ($jel_duplikat)<br><hr>";
            
            // upisi komentar u bazu
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
            
            // provjera duplikata u commentmeta
            $n = dupli_commentmeta_provjera($lastid, $metavalue);
            if ($n == 0) {
                // upisi commentmeta u bazu
                global $wpdb;
                $wpdb->insert($wpdb->prefix . 'commentmeta', array(
                        'meta_key' => 'fbci_comment_id',
                        'meta_value' => $metavalue,
                        'comment_id' => $lastid ), array('%d', '%s', '%s')
                    );
            }
        } else {
            echo "Already exist - import done<br>";
            //break;
        }
        
    }
}

pokupi_komentare($fbid,$post_id);
?>
