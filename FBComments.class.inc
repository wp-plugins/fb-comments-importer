<?php
Class FBCommentsFree{
    
    public function __construct(){
        $this->appID = get_option('fbsync_comments_appID');
        $this->pageID = get_option('fbsync_comments_pageID');
        $this->appSecret = get_option('fbsync_comments_appSecret');
    }
    
    
    /**
     * Generate avatar image
     * @param type $avatar
     * @param type $id_or_email
     * @param type $size
     * @return type
     */
    public function GenerateAvatar($avatar, $id_or_email, $size = '50'){
        if (!is_object($id_or_email)) {
            $id_or_email = get_comment($id_or_email);
        }

        if (is_object($id_or_email)) {
            if ($id_or_email->comment_agent == 'facebook-comment-importer plugin') {
                $alt = '';
                $fb_url = $id_or_email->comment_author_url;
                $fb_array = split("/", $fb_url);
                $fb_id = $fb_array[count($fb_array) - 1];
                if (strlen($fb_id) > 1) {
                    $img = "http://graph.facebook.com/" . $fb_id . "/picture";
                    return "<img alt='{$alt}' src='{$img}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
                }
            }
        }
        return $avatar;
    }
    
    /**
     * Curl get content - https enabled
     * @param type $fullurl
     * @return type
     */
    public function GetHTTPSData($fullurl) {
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
    
    /**
     * Get post ID from post title
     * @global type $wpdb
     * @global type $wpdb
     * @global type $wpdb
     * @param type $post_title
     * @param type $fid
     * @return string
     */
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
    
    
    /**
     * Return total number of comments
     * @global type $wpdb
     * @param type $post_id
     * @return int
     */
    function total_comments_f($post_id){
        if($post_id == "-"){
            return 0;
        }
        else {
            global $wpdb;
            $commentes_num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '".$post_id."'" );
            return $commentes_num;
        }
    }
    
    
    
    /////////// WORKING WITH FB API ////////////////////////////////////////////
    
    
    
    /**
     * Genrate token
     * @return type
     */
    public function GenerateAccessToken(){
        $fullurl = "https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=$this->appID&client_secret=$this->appSecret";
        $ret = $this->GetHTTPSData($fullurl);
        return $ret;
    }
    
    /**
     * Get items from fb page
     * @param int $limit
     * @param string $token
     * @param string $fields
     * @return type
     */
    public function GetListOfFBPosts($limit,$token,$fields = 'id,message,created_time,picture,type,name,source,link'){
        
        
        $data = $this->GetHTTPSData("https://graph.facebook.com/$this->pageID/posts/?$token&limit=$limit&fields=$fields");
        $obj = json_decode($data);
        $wp_site_url = get_site_url();
        
        
        // parse and prepare data from api
        if($obj->data){
            
            $i=0;
            // genrate array
            foreach ($obj->data as $element) {


                if($element->name){
                    
                    // check post id
                    $wp_post_id = url_to_postid($element->link);
                    $pos = strpos($element->link, $wp_site_url);
                    if ($pos === false) {
                        $wp_post_id = "-";
                    }
                    
                    // if id does not exist skip this
                    
                    $mydata['id'] = $element->id;
                    $mydata['name'] = $element->name;
                    $mydata['link'] = $element->link;
                    $mydata['type'] = $element->type;
                    $mydata['picture'] = $element->picture;
                    $mydata['message'] = $element->message;
                    // get only first 10
                    if($i <= 50){
                        $mydata['wp_post_id'] = $wp_post_id;
                    }
                    else {
                        $mydata['wp_post_id'] = '-';
                    }
                    //$mydata['comments_count'] = $this->CountCommentsForSingleItem($element->id,$token);
                    $mydata['total_comments'] = $this->total_comments_f($wp_post_id);
                    $FBPosts[] = $mydata;

                    $i++;
                }
                else {
                    $mydata['id'] = $element->id;
                    $mydata['name'] = $element->name;
                    $mydata['link'] = $element->link;
                    $mydata['type'] = $element->type;
                    $mydata['picture'] = $element->picture;
                    $mydata['message'] = $element->message;
                    $mydata['wp_post_id'] = "-";
                    //$mydata['comments_count'] = $this->CountCommentsForSingleItem($element->id,$token);
                    $mydata['total_comments'] = "0";
                    $FBPosts[] = $mydata;
                }
                
            }


            return $FBPosts;
        }
        else return false;
    }
    
    
    
    /**
     * Get number of comments for specific item
     * @param num $item_id
     * @param string $token
     * @return int
     */
    public function CountCommentsForSingleItem($item_id,$token){
        if($item_id && $token){
            $data = $this->GetHTTPSData("https://graph.facebook.com/$item_id/comments?$token&limit=$limit&fields=id");
            $obj = json_decode($data);
            $num = count($obj->data);
            return $num;
        }
        else return 0;
    }
    
    
    ////////// WORKING WITH WP Comments ////////////////////////////////////////
    
    /**
     * Get username from facebook
     * @param type $userid
     * @return type
     */
    public function FBUsername($userid){
        $file = file_get_contents("http://graph.facebook.com/$userid", true);
        $obj = json_decode($file);
        $username = $obj->username;
        return $username;
    }
    
    /**
     * Check is this double comment
     * @global type $wpdb
     * @param type $cid
     * @param type $mess
     * @return type
     */
    public function IsDoubleComment($cid, $mess){
        global $wpdb;
        $num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '" . $cid . "' AND comment_content = '" . $mess . "'");
        return $num;
    }
    /**
     * Check is this double comment meta
     * @global type $wpdb
     * @param type $cid
     * @param type $mv
     * @return type
     */
    public function IsDoubleCommentmeta($cid, $mv){
        global $wpdb;
        $num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."commentmeta WHERE comment_id = '" . $cid . "' AND meta_value = '" . $mv . "'");
        return $num;
    }
    
    
    
    /**
     * get comments and replies for single post
     * @param type $id
     * @param type $post_id
     * @return type
     */
    public function GetFBComments($id,$post_id){
        $id = trim($id);
        //$token = $this->GenerateAccessToken();        
        
        $file = $this->GetHTTPSData('https://graph.facebook.com/'.$id.'/comments?'.$token.'limit=1000&fields=id,from,message,created_time');
        $FBobject = json_decode($file);
        $FBCommentsArray = array_reverse($FBobject->data);

        // parse FB object, and create array for db insert
        if($FBCommentsArray){
            foreach($FBCommentsArray as $SingleCommentData){
                
                $data['comment_id'] = $SingleCommentData->id;
                $data['from_name'] = $SingleCommentData->from->name;
                $data['from_id'] = $SingleCommentData->from->id;
                $data['message'] = $SingleCommentData->message;
                $data['created_time'] = $SingleCommentData->created_time;
                $data['timestamp'] = strtotime($SingleCommentData->created_time);
                $data['wp_date'] = $this->GenerateTimeFromString($SingleCommentData->created_time);
                $data['fb_username'] = $this->FBUsername($SingleCommentData->from->id);
                $data['profile_link'] = 'http://www.facebook.com/'.$data['fb_username'];
                $data['double_comment_check'] = $this->IsDoubleComment($post_id, $SingleCommentData->message);
                $data['replies'] = $this->GetFBComments($SingleCommentData->id,$post_id);
                $Comments[] = $data;
                unset($SingleCommentData);
            }
        }
        
        
        // get comments from next page (max limit is 2000)
        if($FBobject->paging->next){
            
            $file_next = $this->GetHTTPSData($FBobject->paging->next);
            $FBobject_next = json_decode($file_next);
            $FBCommentsArray_next = array_reverse($FBobject_next->data);
            
            if($FBCommentsArray_next){
                foreach($FBCommentsArray_next as $SingleCommentData){

                    $data['comment_id'] = $SingleCommentData->id;
                    $data['from_name'] = $SingleCommentData->from->name;
                    $data['from_id'] = $SingleCommentData->from->id;
                    $data['message'] = $SingleCommentData->message;
                    $data['created_time'] = $SingleCommentData->created_time;
                    $data['timestamp'] = strtotime($SingleCommentData->created_time);
                    $data['wp_date'] = $this->GenerateTimeFromString($SingleCommentData->created_time);
                    $data['fb_username'] = $this->FBUsername($SingleCommentData->from->id);
                    $data['profile_link'] = 'http://www.facebook.com/'.$data['fb_username'];
                    $data['double_comment_check'] = $this->IsDoubleComment($post_id, $SingleCommentData->message);
                    $data['replies'] = $this->GetFBComments($SingleCommentData->id,$post_id);
                    $Comments[] = $data;
                    unset($SingleCommentData);
                }
            }
            
        }
        
        // return complete array with replays
        return $Comments;
    }
    
    /**
     * Generate time from wp string
     * @param type $created_time
     * @return type
     */
    private function GenerateTimeFromString($created_time){
        $date = explode("T", $created_time);
        $d = $date[0];
        $t = explode("+", $date[1]);
        $t = $t[0];
        $dt = "$d $t";
        return $dt;
    }
    
    
    
    /**
     * Save comments to database
     * @param type $comments
     * @param type $post_id
     */
    public function SaveCommentsToDatabase($comments,$post_id){
        
        $ag = "facebook-comment-importer plugin"; // set comment agent
        
        $i=0;
        // parse comments
        foreach($comments as $comment){
            
            // check is this double comment, if yes skip it
            if(!$comment['double_comment_check']){
            
                $data = array(
                    'comment_post_ID' => $post_id,
                    'comment_author' => $comment['from_name'],
                    'comment_author_url' => $comment['profile_link'],
                    'comment_content' => $comment['message'],
                    'comment_type' => 'comment',
                    'comment_agent' => $ag,
                    'comment_date' => $comment['wp_date'],
                    'comment_date_gmt' =>$comment['wp_date'], 
                    'comment_approved' => 1,
                );
                $parent_comment = wp_insert_comment($data);


                // insert replies if exist
                if($comment['replies']){
                    // parse replies
                    foreach($comment['replies'] as $replay){
                        $data = array(
                            'comment_post_ID' => $post_id,
                            'comment_author' => $replay['from_name'],
                            'comment_author_url' => $replay['profile_link'],
                            'comment_content' => $replay['message'],
                            'comment_type' => 'comment',
                            'comment_parent'=> $parent_comment,
                            'comment_agent' => $ag,
                            'comment_date' => $replay['wp_date'],
                            'comment_date_gmt' => $replay['wp_date'], 
                            'comment_approved' => 1,
                        );
                        wp_insert_comment($data);
                    }

                }
                $i++;
            }
            
            
        }
        // update comment count
        wp_update_comment_count($post_id);
        
        return $i;
    }
    
    
    
    
}
?>
