<?php  
if(!function_exists('curl_version')){
    echo '<div style="border: 1px solid #000000; padding: 5px; background: #FF0D00; border-radius: 5px; color: #fff">';
    echo "<b>ERROR: cURL is NOT installed on this server. Please enable curl to use this plugin.</b>";
    echo '</div>';
} 
?>
<div class="infodiv">
<table width="100%">
    <tr>
        <td width="50%" valign="top">
            <h2>Configuration:</h2>
            <form action ="?page=fbsync_comments_free&action=save_data" method="POST">
                <table>
                    <tr>
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
                        <td>Comments status</td>
                        <td>
                            <input type="radio" <?php if($comments_status_value==1){echo "checked";}?> name="comments_status" value="1"> Approved 
                            <input type="radio" <?php if($comments_status_value==0){echo "checked";}?> name="comments_status" value="0"> Not approved
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
            <center>
            <h2><a href="http://projects.geekydump.com/fb-comments-importer/" target="_blank">Get PRO version now!</a></h2>
            </center>
            <table width="100%">
                <tr>
                    <td>
                        <b>PRO Features: </b><br>
                        <ul>
                            <li>- Import comments from images, statuses, pages, groups and profiles</li>
                            <li>- Manually connect post with comments</li>
                            <li>- Import from older posts</li>
                            <li>- Import comments automatically (with WP cron)</li>
                            <li>- Faster and more efficient support</li>
                            <li>... and more...</li>
                        </ul>
                    </td>
                    <td align="center" valign="top" width="200">
                        <h1>Only $24.99</h1>
                        <h3><a href="http://projects.geekydump.com/fb-comments-importer/" target="_blank">Order now!</a></h3>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>