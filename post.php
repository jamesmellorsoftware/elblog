<?php
// If no post ID set, redirect user to index to avoid errors
if (!isset($_GET['post_id'])) header("Location: index.php");

// Include head to include db and functions
include "includes/head.php";

// Catch user ID
$user_id = (userLoggedIn()) ? $_SESSION['user_id'] : 0;

// Catch post ID, sanitise
$post_id = trim(mysqli_real_escape_string($connection, $_GET['post_id']));

// Retrieve post from db
$query = "SELECT ";
$query.= "posts.post_id, posts.post_title, posts.post_author_id, posts.post_date, posts.post_tags, ";
$query.= "posts.post_content, posts.post_status, posts.post_category_id, posts.post_views_count, ";
$query.= "users.user_username, ";
$query.= "COUNT(likes.like_post_id) AS like_count ";
$query.= "FROM posts ";
$query.= "LEFT JOIN users ON posts.post_author_id = users.user_id ";
$query.= "LEFT JOIN likes ON likes.like_post_id = posts.post_id ";
$query.= "WHERE post_id = ? ";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $post_id, $post_title, $post_author_id, $post_date, $post_tags,
$post_content, $post_status, $post_category_id, $post_views_count, $post_author, $post_likes);
$row = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($row)) header("Location: index.php"); // Post doesn't exist, redirect user to index

// User liked the post?
$userLiked = false;
if (userLoggedIn()) {
    $query = "SELECT * FROM likes WHERE like_post_id = {$post_id} AND like_user_id = {$user_id} ";
    $select_likes_query = mysqli_query($connection, $query);
    $userLiked = (mysqli_num_rows($select_likes_query) > 0) ? true : false;
}

// Post liked, insert like
if (isset($_POST['post_action']) && $_POST['post_action'] === "liked") {
    mysqli_query($connection, "INSERT INTO likes(like_user_id, like_post_id) VALUES ($user_id, $post_id) ");
    $post_likes++;
}

// Post unliked, delete like
if (isset($_POST['post_action']) && $_POST['post_action'] === "unliked") {
    mysqli_query($connection, "DELETE FROM likes WHERE like_user_id = $user_id AND like_post_id = $post_id ");
    $post_likes--;
}

// Comment liked, insert comment like
if (isset($_POST['post_action']) && $_POST['post_action'] === "comment_liked" && isset($_POST['liked_comment_id'])) {
    $liked_comment_id = $_POST['liked_comment_id'];
    mysqli_query($connection, "INSERT INTO comment_likes(comment_like_user_id, comment_id) VALUES ($user_id, $liked_comment_id) ");
}

// Comment unliked, delete like
if (isset($_POST['post_action']) && $_POST['post_action'] === "comment_unliked" && isset($_POST['liked_comment_id'])) {
    $liked_comment_id = $_POST['liked_comment_id'];
    mysqli_query($connection, "DELETE FROM comment_likes WHERE comment_like_user_id = {$user_id} AND comment_id = {$liked_comment_id} ");
}

// Initialise comment errors
$comment_errors = [
    "comment_content" => ""
];

// Creating comments
// Check all inputs for errors, if no errors then insert comment into db
$comment_sent = false;
if (isset($_POST['create_comment'])) {
    // User logged in? Only logged in users can comment
    if ($user_id === 0) $comment_errors['comment_content'] = "You must be logged in to write a comment.";

    // Catch comment user and content
    $comment_name = $_SESSION['user_username'];
    $comment_content = trim(mysqli_real_escape_string($connection, $_POST['comment_content']));

    // Comment empty?
    if (empty($comment_content)) $comment_errors['comment_content'] = "Please write a comment.";

    // Check for errors, if no errors, insert comment into db
    $errorsInForm = checkFormForErrors($comment_errors);    
    if (!$errorsInForm) {
        $query = "INSERT INTO comments (comment_user_id, comment_content, comment_post_id,
                    comment_status, comment_date) ";
        $query.= "VALUES ({$user_id}, '{$comment_content}', '{$post_id}',
                    'unapproved', now()) ";

        $insert_comment_query = mysqli_query($connection, $query);
        checkQuery($insert_comment_query);

        $comment_sent = true;
        $comment_content = "";

        $comment_success_msgs = [
            "comment_sent" => "Your comment was sent successfully and is pending admin approval."
        ];
    }
}
?>

<!-- Post Content -->
<article>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="post-heading">
                    <h1><?php echo $post_title; ?></h1>
                    <h1>
                        <span id="post_likes"><?php echo $post_likes; ?></span>
                        <i aria-hidden="true" id="likeThumb"
                        class="likeThumb fa fa-thumbs-up fa-1x <?php if ($userLiked) echo "liked"; ?>"></i>
                    </h1>
                </div>
                <div class="post-date">
                    <span class="meta">Posted by
                    <a href="index.php?post_author=<?php echo $post_author; ?>"><?php echo $post_author; ?></a>
                    on <?php echo $post_date; ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <p><?php echo $post_content; ?></p>
            </div>
            <?php include "includes/insert_comment_form.php"; ?>
            <?php include "includes/display_comments.php"; ?>
        </div>
    </div>
</article>

<hr>

<script>
    $(document).ready(function(){
        var post_id = <?php echo $post_id; ?>;
        var num_likes = <?php echo $post_likes; ?>;
        var isLoggedIn = <?php echo (userLoggedIn()) ? "true" : "false"; ?>;

        // Post liked
        // If not logged in, redirect to login page
        // Else increment or decrement likes
        // Send request via POST AJAX and change thumb properties when complete
        $("#likeThumb").click(function(){
            if (!isLoggedIn) {
                window.location = "login.php";
                return;
            }

            if ($(this).hasClass("liked")) {
                var post_action = "unliked";
                num_likes--;
            } else {
                var post_action = "liked";
                num_likes++;
            }

            $.ajax({
                url: "post.php?post_id=" + post_id,
                type: "post",
                data: { "post_action": post_action }
            }).done(function(){
                $("#likeThumb").toggleClass("liked");
                $("#post_likes").html(num_likes);
            });

        });

        // Comment liked
        // If not logged in, redirect to login page
        // Else increment or decrement likes
        // Send request via POST AJAX and change thumb properties when complete
        $(".commentLikeThumb").click(function(){
            if (!isLoggedIn) {
                window.location = "login.php";
                return;
            }

            var post_id = <?php echo $post_id; ?>;
            var clicked_comment_thumb = $(this);
            var liked_comment_id = $(clicked_comment_thumb).attr('rel');
            var num_likes = $(clicked_comment_thumb).siblings(".comment_likes").html();

            if ($(clicked_comment_thumb).hasClass("liked")) {
                var post_action = "comment_unliked";
                num_likes--;
            } else {
                var post_action = "comment_liked";
                num_likes++;
            }

            $.ajax({
                url: "post.php?post_id=" + post_id,
                type: "post",
                data: {
                    "post_action": post_action,
                    "liked_comment_id": liked_comment_id,
                    "num_likes": num_likes
                }
            }).done(function(){
                $(clicked_comment_thumb).toggleClass("liked");
                $(clicked_comment_thumb).siblings(".comment_likes").html(num_likes);
            });

        });
    });
</script>

<?php include "includes/footer.php"; ?>