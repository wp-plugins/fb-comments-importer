<?php
// get data
$fbid = $_GET['fbid'];
$post_id = $_GET['post_id'];

// prepare and import comments
$FBCAPI = new FBCommentsFree();
$GetComments = $FBCAPI->GetFBComments($fbid,$post_id);

$SaveComments = $FBCAPI->SaveCommentsToDatabase($GetComments, $post_id);

echo "Import Done. Number of imported comments: <b>".$SaveComments."</b>";
echo '<br><a href="?page=fbsync_comments_free">Click here</a> to go back';

