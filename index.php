<?php
/*
Plugin Name: FB Comments Importer - Free
Plugin URI: http://projects.geekydump.com/
Description: Import facebook comments to your wordpress site
Version: 1.0.1
Author: Ivan M & steelmaiden
*/

/*
 * admin page
 */
add_action( 'admin_menu', 'fbsync_comments_free_plugin_menu' );


// add avatar from FB

add_filter('get_avatar', 'comimp_get_avatar_free', 10, 5);
function comimp_get_avatar_free($avatar, $id_or_email, $size='50') {
	if (!is_object($id_or_email)) { 
		$id_or_email = get_comment($id_or_email);
    }

    if (is_object($id_or_email)) {
        if ($id_or_email->comment_agent=='facebook-comment-importer plugin'){
			$alt = '';
            $fb_url = $id_or_email->comment_author_url;
            $fb_array = split("/", $fb_url);
            $fb_id = $fb_array[count($fb_array)-1];
            if (strlen($fb_id)>1) {
                $img = "http://graph.facebook.com/".$fb_id."/picture";
                return "<img alt='{$alt}' src='{$img}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
            }
        }
    }
    return $avatar;
}


function fbsync_comments_free_plugin_menu() {
        add_menu_page(__('FB Comments Importer','fbsync_comments_options_f'), __('FB Comments Importer','fbsync_comments_options_f'), 'manage_options', 'fbsync_comments_free', 'fbsync_comments_plugin_options_f' );
}

// curl get content
function gethttps_data_f($fullurl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_URL, $fullurl);
    $returned = curl_exec($ch);

    return $returned;
}

// get post id from post name
function post_id_from_name($post_title,$fid){
    global $wpdb;
    $post_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_name = '".$post_title."'" );
    $post_id = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = '".$post_title."'");

    if($post_count == 1){
        return $post_id;
    }
    else {
        global $wpdb;
        $post_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."fb_comments_image_data WHERE imgid = '".$fid."'" );
        if($post_count >=1){
            global $wpdb;
            $post_id = $wpdb->get_var("SELECT postid FROM ".$wpdb->prefix."fb_comments_image_data WHERE imgid = '".$fid."'");
            return $post_id;
        }
        else {
            return "-";
        }
    }
}

function total_comments_f($post_id){
    if($post_id == "-"){
        return 0;
    }
    else {
    global $wpdb;
        $commentes_num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '".$post_id."'" );
         //$commentes_num = get_comments_number( $post_id );
        return $commentes_num;
    }
}

// admin page
function fbsync_comments_plugin_options_f() {
        ?>
        <div class="wrap">
            <div id="icon-edit" class="icon32"><br></div><h2>Import Facebook Comments</h2><br><br>
        <?php
        // check permissions
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'Access denied.' ) );
	}
        // on save data click
        if($_GET['action']=="save_data"){
            
            $pageID = $_POST['pageID'];
            $appID = $_POST['appID'];
            $appSecret = $_POST['appSecret'];
            
            update_option('fbsync_comments_pageID', $pageID);
            update_option('fbsync_comments_appID', $appID);
            update_option('fbsync_comments_appSecret', $appSecret);
            
            echo "Settings are saved!";
            ?><meta http-equiv="REFRESH" content="2;url=?page=fbsync_comments_free"><?php
        }
        // on uvezi click
        else if($_GET['action']=="import"){
            include("manual_import.php");
        }
        // Show comments
        else {
            
            // get data from dataase
            $pageID = get_option('fbsync_comments_pageID');
            $appID = get_option('fbsync_comments_appID');
            $appSecret = get_option('fbsync_comments_appSecret');
            
            // update settings form template
            include("update_form.php");
            
            $fullurl = "https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=$appID&client_secret=$appSecret";
            $ret = gethttps_data_f($fullurl);
            // get limit
            $limit = $_POST[limit];
            if(!$limit){
                $limit = "25";
            }
            
            $data = gethttps_data_f("https://graph.facebook.com/$pageID/posts/?$ret&limit=30");
            $obj = json_decode($data);
            
            // parse object
            $wp_site_url = get_site_url();
            
            ?>
            <h2><?=$wp_site_url;?></h2>
            <h3>Latest Posts:</h3>
            <table class="widefat" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th width="250">Title</th>
                        <th width="200">URL</th>
                        <th>Type</th>
                        <th>FB Comments</th>
                        <th>WP Comments</th>
                        <th>Import</th>
                        <th>Connected</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($obj->data as $element) {
                // get data from facebook api pbject
                $link = $element->link;
                $name = $element->name;
                $picture = $element->picture;
                $message = $element->message;
                $type = $element->type;
                $comments_count = $element->comments->count;
                $id = $element->id;
                 
                
                if($name){
                    
                    $wp_post_id = url_to_postid($link);
                    
                    $pos = strpos($link, $wp_site_url);
                    if ($pos === false) {
                        $wp_post_id = "-";
                    }

                    if($wp_post_id!="-"){
                        
                        $ukupno_komentara = total_comments_f($wp_post_id);
                        ?>
                        <tr>
                            <td><b>(<?=$type;?>)</b> <?=$name;?></td>
                            <td><a href="<?=$link;?>" target="_blank"><?php echo substr($link, 0, 50);?></a></td>
                            <td>Article</td>
                            <td><?=$comments_count;?></td>
                            <td><?=$ukupno_komentara;?></td>
                            <td><a href="?page=fbsync_comments_free&action=import&fbid=<?=$id;?>&post_id=<?=$wp_post_id;?>">Import Now!</a></td>
                            <td>Yes</td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
                </tbody>
            </table>  
            <?php
             
        }
        
        
}

/*
 * create database on plugin activation
 */

function my_fb_commentes_sync_activation_f() {
    
    $my_fb_plugin_version = "1.0";
    global $wpdb;

    // Check if installed
    if (get_option('my_fb_commentes_sync_version') < $my_fb_plugin_version) {
        $my_fb_comments_image_data_table = $wpdb->get_col("SHOW COLUMNS FROM " . $wpdb->prefix."fb_comments_image_data");
        
        if (!$my_fb_comments_image_data_table) {
            
             $table_name = $wpdb->prefix.'fb_comments_image_data';
            
             $sql = "CREATE TABLE $table_name (
                id int(9) NOT NULL AUTO_INCREMENT,
                imgid varchar(100) NOT NULL,
                postid int(20) NOT NULL,
                UNIQUE KEY id (id)
              );";

             require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
             dbDelta($sql);
            
            
        } else {
            // nothing for now
        }

        update_option('my_fb_commentes_sync_version', $my_fb_plugin_version);
    }
}

register_activation_hook(__FILE__, 'my_fb_commentes_sync_activation_f');
?>