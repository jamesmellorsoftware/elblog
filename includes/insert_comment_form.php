<!-- Comments -->
<!-- Comments Form -->
<div class="col-lg-8 col-md-10 mx-auto">
    <?php if (!userLoggedIn()) { ?>
        <h4>
            <a href="login.php">Log in to comment.</a>
        </h4>
    <?php } else { ?>
        <h4>Leave a Comment:</h4>
        <form action="" method="post" role="form">
            <?php
                echo generateDismissableAlert($comment_errors);
                if ($comment_sent) echo generateDismissableAlert($comment_success_msgs, "success");
            ?>
            <div class="form-group">
                <textarea class="form-control <?php if (!empty($comment_errors['comment_content'])) echo "is-invalid"; ?>"
                rows="3"name="comment_content"
                placeholder="Your Comment"><?php if (isset($comment_content)) echo $comment_content; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="create_comment">Create Comment</button>
        </form>
    <?php } ?>
</div>