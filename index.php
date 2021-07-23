<?php
// Include head, db, functions
include "includes/head.php";

// Set page number from GET or set to default page 1
$page = (isset($_GET['page'])) ? $page = $_GET['page'] : $page = 1;

// Set posts per page from POST filter or to default number (hardcoded for now)
$results_per_page = (isset($_GET['filter__results_per_page'])) ? trim(mysqli_real_escape_string($connection, $_GET['filter__results_per_page'])) : 5;
$page_offset = ($page === 1) ? 0 : ($page * $results_per_page) - $results_per_page;

// Check if user searched, initialise search filters, apply search filters
$search_active = (isset($_GET['search'])) ? true : false;
$search_filters = [];
if ($search_active) {
    $search_filters = [
        "post_title" => [
            "type" => "text", "key" => "posts.post_title", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-title']))
        ],
        "post_author" => [
            "type" => "text", "key" => "users.user_username", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-author']))
        ],
        "post_tags" => [
            "type" => "text", "key" => "posts.post_tags", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-tags']))
        ],
        "post_date_from" => [
            "type" => "date", "key" => "posts.post_date", "toOrFrom" => "from", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-date_from']))
        ],
        "post_date_to"   => [
            "type" => "date", "key" => "posts.post_date", "toOrFrom" => "to", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-date_to']))
        ],
        "post_category_id" => [
            "type" => "int", "key" => "posts.post_category_id", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__post-category']))
        ]
    ];
}

// Check for order by from filter to add to the post retrieval query later
$order_by = "posts.post_date DESC ";
if (isset($_GET['filter__post--order_by'])) {
    switch($_GET['filter__post--order_by']) {
        case "likes":
            $order_by = "like_count DESC ";
            break;
        case "oldest":
            $order_by = "posts.post_date ASC ";
            break;
        case "newest":
        default:
            $order_by = "posts.post_date DESC ";
            break;
    }
}

// Retrieve published posts
// If user clicks a user, only show posts from that user
// Limit number of posts per page for pagination
// Join posts with users to get username
// Join posts with likes to get like count
$query = "SELECT ";
$query.= "posts.post_id, posts.post_title, posts.post_author_id, posts.post_date, posts.post_tags, ";
$query.= "posts.post_content, posts.post_status, posts.post_category_id, posts.post_views_count, ";
$query.= "users.user_username, ";
$query.= "COUNT(likes.like_post_id) AS like_count ";
$query.= "FROM posts ";
$query.= "LEFT JOIN users ON posts.post_author_id = users.user_id ";
$query.= "LEFT JOIN likes ON likes.like_post_id = posts.post_id ";
$query.= "WHERE post_status = 'published' ";
if (isset($_GET['post_author'])) {
    // If post author selected
    $post_author = getIdFromUsername(trim(mysqli_real_escape_string($connection, $_GET['post_author'])));
    $query.= "AND post_author_id = {$post_author} ";
}
$query.= applySearchFilters($search_filters);
$query.= "GROUP BY posts.post_id "; // Stop duplicate rows by grouping by unique post ID
$query.= "ORDER BY {$order_by} "; // Apply order by
$query.= "LIMIT {$page_offset}, {$results_per_page} "; // Apply pagination limit and offset
$select_all_posts_query = mysqli_query($connection, $query);

// Catch posts in array
$i = 0;
$posts = [];
while ($row = mysqli_fetch_assoc($select_all_posts_query)) {
    $posts[$i]['post_id']      = $row['post_id'];
    $posts[$i]['post_title']   = $row['post_title'];
    $posts[$i]['post_author']  = $row['user_username'];
    $posts[$i]['post_date']    = date("d/m/Y", strtotime($row['post_date']));
    $posts[$i]['post_content'] = substr($row['post_content'], 0, 100);
    $posts[$i]['post_tags']    = $row['post_tags'];
    $posts[$i]['post_status']  = $row['post_status'];
    $posts[$i]['post_likes']   = $row['like_count'];
    $i++;
}

// Include search modal
include "includes/modal_search.php";

?>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <?php if (isset($_GET['post_author'])) { ?>
                <h1 class="text-center">Posts by: <?php echo $_GET['post_author']; ?></h1>
            <?php } ?>
            <p class="text-center">
                <i aria-hidden="true" id="" data-toggle="modal" data-target="#searchModal"
                class="fa fa-search fa-2x text-right <?php if ($search_active) echo "text-info"; ?>"></i>
            </p>
            <div id='postbox'>
                <?php if (empty($select_all_posts_query) || empty($posts)) { ?>
                    <h2 class='post-title text-center'>No posts.</h2>
                <?php } else {
                    foreach ($posts as $post) { ?>
                        <div class="post-preview">
                            <a href="post.php?post_id=<?php echo $post['post_id']; ?>">
                                <h2 class="post-title"><?php echo $post['post_title']; ?></h2>
                                <h3 class="post-subtitle"><?php echo $post['post_content']; ?>...</h3>
                            </a>
                            <p class="post-meta">Posted by
                                <a href="index.php?post_author=<?php echo $post['post_author']; ?>">
                                    <?php echo $post['post_author']; ?>
                                </a>
                                on <?php echo $post['post_date']; ?>
                                <i aria-hidden="true" class="fa fa-thumbs-up fa-1x"></i>
                                <?php echo $post['post_likes']; ?>
                            </p>
                        </div>
                        <hr>
                    <?php }
                    include "includes/pagination.php";
                } ?>
            </div>
        </div>
    </div>
</div>

<hr>

<?php include "includes/footer.php"; ?>