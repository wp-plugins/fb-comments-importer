
<div class="infodiv">
    <form action="#" method="POST">
        Limit: <input type="text" name="limit" value="<?php echo $limit;?>"> <input type="submit" value="Show">
    </form>
</div>

<h2><?php echo $wp_site_url;?></h2>

<h3>Latest Posts:</h3>
<a class="button button-primary button" href="javascript:void(0);" onclick="ShowHideRows();">Show / Hide unavailable posts</a>
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

    if($FBObject){

        foreach ($FBObject as $element) {
            
            if($element['wp_post_id'] == "-"){
                $myclass = "class='hideme'";
            }
            else {
                $myclass = "";
            }
            
            ?>
            <tr <?php echo $myclass;?>>
                <td><b>(<?php echo $element['type'];?>)</b> <?php echo $element['name'];?></td>
                <td><a href="<?php echo $element['link'];?>" target="_blank"><?php echo substr($element['link'], 0, 30);?><?php if(strlen($element['link'])>30){echo "...";}?></a></td>
                <td><?php echo $element['type'];?></td>
                <td>
                    <?php
                    if($element['wp_post_id'] !="-"){
                    ?>
                    <a href="javascript:void(0);" onclick="CheckComNumFree('<?php echo $element['id'];?>');">Check</a> <span id="countcomm_<?php echo $element['id'];?>"> </span>
                    <?php
                    }
                    else{
                    ?>
                    <a href="javascript:void(0);" onclick="CheckComNumFree('<?php echo $element['id'];?>');">Check</a> <span id="countcomm_<?php echo $element['id'];?>"> </span>
                    <?php 
                    }
                    ?>
                </td>
                <td><?php echo $element['total_comments'];?></td>
                <td>
                    <?php
                    if($element['wp_post_id'] !="-"){
                    ?>
                    <a href="?page=fbsync_comments_free&action=import&fbid=<?php echo $element['id'];?>&post_id=<?php echo $element['wp_post_id'];?>">Import Now!</a>
                    <?php
                    }
                    else {
                        echo "<font color='red'><a href='http://projects.geekydump.com/fb-comments-importer/' target='_blank'>-- PRO Feature --</a></font>";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($element['wp_post_id'] !="-"){
                        echo "Yes";
                    }
                    else {
                        echo "<font color='red'>Not avaliable</font>";
                    }
                    ?>
                </td>
            </tr>
    <?php
        }
    }
    ?>
    </tbody>
</table>