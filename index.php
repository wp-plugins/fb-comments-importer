<?php
/*
Plugin Name: FB Comments Importer
Plugin URI: http://projects.geekydump.com/
Description: Imports Facebook comments to your Wordpress site and gives it a SEO boost.
Version: 1.3.3
Author: Ivan M & steelmaiden
*/

require_once 'FBComments.class.inc';
/*
 * admin page
 */
add_action( 'admin_menu', 'fbsync_comments_free_plugin_menu' );


// add avatar from FB

add_filter('get_avatar', 'comimp_get_avatar_free', 10, 5);
function comimp_get_avatar_free($avatar, $id_or_email, $size = '50') {
    $FBCommentsFree = new FBCommentsFree();
    $avatar = $FBCommentsFree->GenerateAvatar($avatar, $id_or_email, $size);
    return $avatar;
}


function fbsync_comments_free_plugin_menu() {
    add_menu_page(__('FB Comments Importer', 'fbsync_comments_options_f'), __('FB Comments Importer', 'fbsync_comments_options_f'), 'manage_options', 'fbsync_comments_free', 'fbsync_comments_plugin_options_f');
    wp_register_script( 'FBScriptReadyFree', plugins_url('js/script.js?v=2', __FILE__) );
    wp_enqueue_script( 'FBScriptReadyFree' );
    wp_register_style( 'FBmyPluginStylesheet', plugins_url('css/css.css?v=2', __FILE__) );
    wp_enqueue_style( 'FBmyPluginStylesheet' );
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
            $commentsStatus = $_POST['comments_status'];
            
            update_option('fbsync_comments_pageID', $pageID);
            update_option('fbsync_comments_appID', $appID);
            update_option('fbsync_comments_appSecret', $appSecret);
            update_option('commentes_importer_comments_status', $commentsStatus);
            
            echo "Settings are saved!";
            ?><meta http-equiv="REFRESH" content="2;url=?page=fbsync_comments_free"><?php
        }
        // on uvezi click
        else if($_GET['action']=="import"){
            include("manual_import.php");
        }
        // Show comments
        else {
            // update settings form template
            $pageID = get_option('fbsync_comments_pageID');
            $appID = get_option('fbsync_comments_appID');
            $appSecret = get_option('fbsync_comments_appSecret');
            $comments_status_value = get_option('commentes_importer_comments_status');
            $wp_site_url = get_site_url();
            
            // show update form, and buy now message
            include("update_form.php");
            
            // new FB comments object, and generate access token
            $FBCommentsFree = new FBCommentsFree();
            $token = $FBCommentsFree->GenerateAccessToken();
            
            // get limit from post
            $limit = $_POST['limit'];
            if(!$limit){
                $limit = "30";
            }
            
            // get items
            $FBObject = $FBCommentsFree->GetListOfFBPosts($limit, $token);
            // show template
            include("templates/home.php");
             
        }
        
        
}

/*
 * create database on plugin activation
 */

function my_fb_commentes_sync_activation_f() {
    
    $my_fb_plugin_version = "1.3";
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