<?php
/*
Plugin Name: Facebook Comments Importer
Plugin URI: http://wp-resources.com/
Description: Imports Facebook comments to your Wordpress site and gives it a SEO boost.
Version: 1.7.9
Author: Ivan M
*/

require_once 'FBComments.class.inc';
/*
 * admin page
 */
add_action( 'admin_menu', 'fbsync_comments_free_plugin_menu' );


// add avatar from FB

add_filter('get_avatar', 'comimp_get_avatar_free', 2, 5);
function comimp_get_avatar_free($avatar, $id_or_email, $size = '50') {
    $FBCommentsFree = new FBCommentsFree();
    $avatar = $FBCommentsFree->GenerateAvatar($avatar, $id_or_email, $size);
    return $avatar;
}


function fbsync_comments_free_plugin_menu() {
    add_menu_page(__('FB Comments Importer', 'fbsync_comments_options_f'), __('FB Comments Importer', 'fbsync_comments_options_f'), 'manage_options', 'fbsync_comments_free', 'fbsync_comments_plugin_options_f');
    add_submenu_page("fbsync_comments_free", "Pro Version", "Pro Version", 'manage_options', "fbsync_comments_about_pro", "fbsync_comments_about_pro_function");

    wp_register_script( 'FBScriptReadyFree', plugins_url('js/script.js?v=2', __FILE__) );
    wp_enqueue_script( 'FBScriptReadyFree' );
    wp_register_style( 'FBmyPluginStylesheet', plugins_url('css/css.css?v=3', __FILE__) );
    wp_enqueue_style( 'FBmyPluginStylesheet' );
}

// about pro version page
function fbsync_comments_about_pro_function(){
    include("templates/about_pro.php");
}


// admin page
function fbsync_comments_plugin_options_f() {
        ?>
        <div class="wrap">
            <div id="icon-edit" class="icon32"><br></div><h2>Facebook comments importer</h2>
        <?php
        // check permissions
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'Access denied.' ) );
	}
        // on save data click
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        if($action == "save_data"){
            
            $pageID = filter_input(INPUT_POST, 'pageID',FILTER_SANITIZE_SPECIAL_CHARS);
            $appID = filter_input(INPUT_POST, 'appID',FILTER_SANITIZE_SPECIAL_CHARS);
            $appSecret = filter_input(INPUT_POST, 'appSecret',FILTER_SANITIZE_SPECIAL_CHARS);
            $commentsStatus = filter_input(INPUT_POST, 'comments_status',FILTER_SANITIZE_SPECIAL_CHARS);
            $followRedirects = filter_input(INPUT_POST, 'follow_redirects',FILTER_SANITIZE_SPECIAL_CHARS);
            $disable_images = filter_input(INPUT_POST, 'disable_images',FILTER_SANITIZE_SPECIAL_CHARS);
            $WSBaseURL = filter_input(INPUT_POST,'ws_base_url');
            
            update_option('fbsync_comments_pageID', $pageID);
            update_option('fbsync_comments_appID', $appID);
            update_option('fbsync_comments_appSecret', $appSecret);
            update_option('commentes_importer_follow_redirects', $followRedirects);
            update_option('commentes_importer_disable_images', $disable_images);
            update_option('commentes_importer_comments_status', $commentsStatus);
            update_option('commentes_importer_website_base_url', $WSBaseURL);
            
            echo "Settings are saved!";
            ?><meta http-equiv="REFRESH" content="2;url=?page=fbsync_comments_free"><?php
        }
        // on uvezi click
        else if($action == "import"){
            include("manual_import.php");
        }
        else if(filter_input(INPUT_GET, 'action') == "import_test"){
            
            $fbid = filter_input(INPUT_GET, 'fbid');
            $post_id = filter_input(INPUT_GET, 'post_id');

            // import comments from fb page, token not required here
            $FBCAPI = new FBCommentsFree();
            $GetComments = $FBCAPI->GetFBComments($fbid,$post_id);
            echo "<pre>";
            print_r($GetComments);
            echo "</pre>";

        }
	else if(filter_input(INPUT_GET, 'action') == "test_filters"){
		$filter = filter_input(INPUT_GET, 'filter');
		print_filters_for( $filter );
	}
        // Show comments
        else {
            // update settings form template
            $pageID = get_option('fbsync_comments_pageID');
            $appID = get_option('fbsync_comments_appID');
            $appSecret = get_option('fbsync_comments_appSecret');
            $comments_status_value = get_option('commentes_importer_comments_status');
            $follow_redirects = get_option('commentes_importer_follow_redirects');
            $disable_images = get_option('commentes_importer_disable_images');
            $website_base_url = get_option('commentes_importer_website_base_url');
            
            
            if(!$website_base_url){
            $wp_site_url = get_site_url();
                update_option('commentes_importer_website_base_url', $wp_site_url);
            }
            
            // show update form, and buy now message
            include("update_form.php");
            
            // new FB comments object, and generate access token
            $FBCommentsFree = new FBCommentsFree();
            $token = $FBCommentsFree->GenerateAccessToken();
            
            // get limit from post
            $limit = filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_SPECIAL_CHARS);
            if(!$limit){
                $limit = "30";
            }
            
            // get items
            $FBObject = $FBCommentsFree->GetListOfFBPosts($limit, $token);
            if(isset($FBObject['status']) && $FBObject['status']===false){
                echo "<b>Error Message:</b> ".$FBObject['msg'];
                if(isset($FBObject['error_code']) && $FBObject['error_code'] === 803){
                    echo "<br>Advice: Please check your facebook page ID. Did you enter correct ID?";
                }
            } else {
                // show template
                include("templates/home.php");
            }
        }
        
        
}

/*
 * create database on plugin activation
 */

function my_fb_commentes_sync_activation_f() {
    
    $my_fb_plugin_version = "1.5";
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

// follow short urls filter
function fbcomments_importer_filter_shortner($url) {
    
    $follow_redirects = get_option('commentes_importer_follow_redirects');
    
    
    if (function_exists('curl_init') && $follow_redirects) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE); // We'll parse redirect url from header.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE); // We want to just get redirect url but not to follow it.
        $response = curl_exec($ch);
        preg_match_all('/^Location:(.*)$/mi', $response, $matches);
        curl_close($ch);
        if (!empty($matches[1])) { // if there's a location redirect use this
            return trim($matches[1][0]);
        } else {
            return $url; // otherwise use normal url
        }
    } else {
        return $url;  // no curl? use normal url.
    }
    
}
add_filter('url_to_postid', 'fbcomments_importer_filter_shortner', 0);

if (!function_exists('fb_comments_importer_pro_preprocess_comment')) {
    // add images to comments
    function fb_comments_importer_preprocess_comment($commentdata) {
        // get disable images option
        $disable_images = get_option('commentes_importer_disable_images');
        if($disable_images != 1){
            foreach ($commentdata as $key => $one_comment) {

                $meta_values = get_comment_meta($one_comment->comment_ID, 'fb_comments_importer_comment_image', true);
                $meta_values = unserialize($meta_values);
                if($meta_values){
                    $commentdata[$key]->comment_content .= '<br><a target="_blank" href="'.$meta_values['url'].'"><img src="'.$meta_values['image'].'"></a>';
                }
            }
        }
        return $commentdata;
    }
    add_filter( 'comments_array' , 'fb_comments_importer_preprocess_comment'); 
}