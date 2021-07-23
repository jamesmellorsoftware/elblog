<div class="col-lg-8 col-md-10 mx-auto" style="margin-top: 30px;">
<!-- Display comments -->
<?php
    // Retrieve approved comments for this post
    // Order by comment ID
    $query = "SELECT comments.comment_date, comments.comment_user_id, ";
    $query.= "comments.comment_content, comments.comment_id, ";
    $query.= "users.user_username, ";
    $query.= "COUNT(comment_likes.comment_id) AS comment_likes ";
    $query.= "FROM comments ";
    $query.= "LEFT JOIN users ON comments.comment_user_id = users.user_id ";
    $query.= "LEFT JOIN comment_likes ON comment_likes.comment_id = comments.comment_id ";
    $query.= "WHERE comment_post_id = {$post_id} ";
    $query.= "AND comment_status = 'approved' ";
    $query.= "GROUP BY comments.comment_id "; // Group by comment ID to avoid duplicate rows
    $query.= "ORDER BY comment_id DESC";
    $select_comments_query = mysqli_query($connection, $query);
    checkQuery($select_comments_query);

    // Catch variables, display comments
    while ($row = mysqli_fetch_array($select_comments_query)) {
        // Catch variables
        $comment_date    = $row['comment_date'];
        $comment_user_id = $row['comment_user_id'];
        $comment_author  = $row['user_username'];
        $comment_content = $row['comment_content'];
        $comment_id      = $row['comment_id'];
        $comment_likes   = $row['comment_likes'];

        // User liked this comment?
        $query = "SELECT * FROM comment_likes WHERE comment_id = {$comment_id} AND comment_like_user_id = {$user_id} ";
        $select_comment_likes_query = mysqli_query($connection, $query);
        $userLiked = (mysqli_num_rows($select_comment_likes_query) > 0) ? true : false;
        ?>

        <div class="">
            <h4 class="media-heading">
                <?php echo $comment_author; ?>
                <small><?php echo date("d/m/Y", strtotime($comment_date)); ?></small>
            </h4>
            <div><?php echo $comment_content; ?></div>
            <div>
                <span class="comment_likes"><?php echo $comment_likes; ?></span>
                <i aria-hidden="true" rel="<?php echo $comment_id; ?>"
                class="commentLikeThumb comment fa fa-thumbs-up fa-1x <?php if ($userLiked) echo "liked"; ?>"></i>
            </div>
        </div>
        <br> <?php // remove this later!!! ?>
<?php } ?>
</div>