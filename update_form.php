<?php  
if(!function_exists('curl_version')){
    echo '<div style="border: 1px solid #000000; padding: 5px; background: #FF0D00; border-radius: 5px; color: #fff">';
    echo "<b>ERROR: cURL is NOT installed on this server. Please enable curl to use this plugin.</b>";
    echo '</div>';
} 
?>
<div class="postbox">
    <div class="inside">
        <table width="100%">
            <tr>
                <td width="50%" valign="top">
                    <h2>Configuration:</h2>
                    <form action ="?page=fbsync_comments_free&action=save_data" method="POST">
                        <table>
                            <tr width="150">
                                <td>Facebook Fan Page ID :</td>
                                <td><input name="pageID" type="text" value="<?php echo $pageID; ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <td><a href="https://developers.facebook.com/apps" target="_blank">APP ID:</a></td>
                                <td><input name="appID" type="text" value="<?php echo $appID; ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <td><a href="https://developers.facebook.com/apps" target="_blank">APP Secret Code:</a></td>
                                <td><input name="appSecret" type="text" value="<?php echo $appSecret; ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <td>Website base URL:</td>
                                <td>
                                    <input name="ws_base_url" type="text" value="<?php echo $website_base_url;?>" class="regular-text"><br>
                                    <small>Please do not change this option if you are not sure what you are doing.</small>
                                </td>
                            </tr>
                            <tr>
                                <td>Comments status</td>
                                <td>
                                    <input type="radio" <?php if($comments_status_value==1){echo "checked";}?> name="comments_status" value="1"> Approved 
                                    <input type="radio" <?php if($comments_status_value==0){echo "checked";}?> name="comments_status" value="0"> Not approved
                                </td>
                            </tr>
                            <tr>
                                <td>follow url shortener redirects</td>
                                <td>
                                    <input type="radio" <?php if($follow_redirects==1){echo "checked";}?> name="follow_redirects" value="1"> Yes 
                                    <input type="radio" <?php if($follow_redirects==0){echo "checked";}?> name="follow_redirects" value="0"> No
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" name="submit" value="Save"></td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td valign="top">
                    <a href="http://wp-resources.com/facebook-comments-importer/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ );?>advert.png"></a>
                </td>
            </tr>
        </table>
    </div>
</div>