<table width="100%">
    <tr>
        <td width="50%">
            <form action ="?page=fbsync_comments_free&action=save_data" method="POST">
                <table>
                    <tr>
                        <td>Facebook Fan Page ID :</td>
                        <td><input name="pageID" type="text" value="<?= $pageID; ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <td><a href="https://developers.facebook.com/apps" target="_blank">APP ID:</a></td>
                        <td><input name="appID" type="text" value="<?= $appID; ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <td><a href="https://developers.facebook.com/apps" target="_blank">APP Secret Code:</a></td>
                        <td><input name="appSecret" type="text" value="<?= $appSecret; ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value="Save"></td>
                    </tr>
                </table>
            </form>
        </td>
        <td valign="top">
            <div style="border: 1px solid #000000; padding: 5px; background: #DBFFE9; height: 200px; border-radius: 15px;">
                <center>
                <h2><a href="http://projects.geekydump.com" target="_blank">Get Premium version now!</a></h2>
                </center>
                <table width="100%">
                    <tr>
                        <td>
                            <b>Premium Features: </b><br>
                            <ul>
                                <li>- Import comments from images</li>
                                <li>- Show unlimited posts</li>
                                <li>- Manually connect post with comments</li>
                                <li>- Import comments automatically (with WP cron)</li>
                                <li>- Faster and more efficient support</li>
                                <li>... and more...</li>
                            </ul>
                        </td>
                        <td align="center" valign="top">
                            <h1>Only $24.99</h1>
                            <h3><a href="http://projects.geekydump.com" target="_blank">Order now!</a></h3>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
