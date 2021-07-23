<?php

// Check if page number set
// If not set set to default 1
// Calculate offset and limit for pagination
$page = (isset($_GET['page'])) ? $page = trim(mysqli_real_escape_string($connection, $_GET['page'])) : $page = 1;
$results_per_page = (isset($_GET['filter__results_per_page'])) ? trim(mysqli_real_escape_string($connection, $_GET['filter__results_per_page'])) : 20;
$page_offset = ($page === 1) ? 0 : ($page * $results_per_page) - $results_per_page;

// Initialise search filters
// Check if user searched, apply filters
$search_filters = [];
if (isset($_GET['search'])) {
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

// Initialise bulk options
$bulk_options = [
    ["value" => "published",      "name"  => "Publish"],
    ["value" => "draft",          "name"  => "Draft"],
    ["value" => "delete",         "name"  => "Delete"],
    ["value" => "reset_likes",    "name"  => "Reset Likes"],
    ["value" => "reset_views",    "name"  => "Reset Views"],
    ["value" => "purge_comments", "name"  => "Purge Comments"]
];

// Apply bulk option to every user checked
if (isset($_POST['checkboxArray'])) {
    foreach($_POST['checkboxArray'] as $post_id) {

        $post_id = trim(mysqli_real_escape_string($connection, $post_id));

        $bulk_option = trim(mysqli_real_escape_string($connection, $_POST['bulk_option']));

        $queries = [];

        switch ($bulk_option) {
            case "published":
            case "draft":
                $queries[0] = "UPDATE posts SET post_status = '{$bulk_option}' WHERE post_id = {$post_id} ";
                break;
            case "delete":
                $queries[0] = "DELETE FROM posts WHERE post_id = {$post_id} ";
                $queries[1] = "DELETE FROM likes WHERE like_post_id = {$post_id} ";
                break;
            case "reset_likes":
                $queries[0] = "DELETE FROM likes WHERE like_post_id = {$post_id} ";
                break;
            case "reset_views":
                $queries[0] = "UPDATE posts SET post_views = 0 WHERE post_id = {$post_id} ";
                break;
            case "purge_comments":
                $queries[0] = "DELETE FROM comments WHERE comment_post_id = {$comment_post_id} ";
                break;
        }

        foreach ($queries as $query) {
            $bulk_option_query = mysqli_query($connection, $query);
            checkQuery($bulk_option_query);
        }
    }
}

// Retrieve posts
// Select all fields from posts
// Join with users on post author ID = user ID to get username
// Join with comments to get comment count on comment post id = post id, group by count
$query = "SELECT ";
$query.= "posts.post_id, posts.post_author_id, posts.post_title, posts.post_category_id, ";
$query.= "posts.post_status, posts.post_tags, posts.post_date, posts.post_views_count, ";
$query.= "users.user_username, ";
$query.= "categories.cat_name, ";
$query.= "COUNT(comments.comment_post_id) AS comment_count, ";
$query.= "COUNT(likes.like_post_id) AS post_likes ";
$query.= "FROM posts ";
$query.= "LEFT JOIN users ON posts.post_author_id = users.user_id ";
$query.= "LEFT JOIN comments ON comments.comment_post_id = posts.post_id ";
$query.= "LEFT JOIN likes ON likes.like_post_id = posts.post_id ";
$query.= "LEFT JOIN categories ON categories.cat_id = posts.post_category_id ";
// Apply filters
$query.= applySearchFilters($search_filters);
$query.= "GROUP BY posts.post_id "; // Group by to avoid duplicate rows
$query.= applyOrderBy();
$query.= "LIMIT {$page_offset}, {$results_per_page} "; // Apply page limit for pagination
$select_all_posts = mysqli_query($connection, $query);

// Catch posts in array
$i = 0;
$posts = [];
while ($row = mysqli_fetch_assoc($select_all_posts)) {
    $posts[$i]['post_id']            = $row['post_id'];
    $posts[$i]['post_author_id']     = $row['post_author_id'];
    $posts[$i]['post_author']        = $row['user_username'];
    $posts[$i]['post_title']         = $row['post_title'];
    $posts[$i]['post_category']      = $row['cat_name'];
    $posts[$i]['post_status']        = $row['post_status'];
    $posts[$i]['post_tags']          = $row['post_tags'];
    $posts[$i]['post_comment_count'] = $row['comment_count'];
    $posts[$i]['post_date']          = date("d/m/Y", strtotime($row['post_date']));
    $posts[$i]['post_views_count']   = $row['post_views_count'];
    $posts[$i]['post_likes']         = $row['post_likes'];
    $i++;
}

// Include search modal
include "includes/modal_search_posts.php";

?>

<div class="page-header-search">
    <h1 class="page-title">All Posts</h1>
    <i aria-hidden="true" id="" data-toggle="modal" data-target="#searchModal"
    class="fa fa-search fa-2x text-right <?php if (isset($_GET['search'])) echo "text-info"; ?>"></i>
</div>

<form action="" method="post">
    <?php generateAdminBulkOptions($bulk_options); ?>
    <table id="table-admin" class="table table-bordered table-hover">
        <thead>
            <tr class="table__headers">
                <th class="clickable_th checkboxTableRow"><input type="checkbox" id="selectAllCheckboxes"></th>
                <th rel="post_author_id" class="<?php echo displayColOrderClass("post_author_id"); ?>">
                    Author <?php echo adminTableHeaderArrow("post_author_id"); ?>
                </th>
                <th rel="post_title" class="<?php echo displayColOrderClass("post_title"); ?>">
                    Title <?php echo adminTableHeaderArrow("post_title"); ?>
                </th>
                <th rel="post_category_id" class="<?php echo displayColOrderClass("post_category_id"); ?>">
                    Category <?php echo adminTableHeaderArrow("post_category_id"); ?>
                </th>
                <th rel="post_status" class="<?php echo displayColOrderClass("post_status"); ?>">
                    Status <?php echo adminTableHeaderArrow("post_status"); ?>
                </th>
                <th rel="post_tags" class="<?php echo displayColOrderClass("post_tags"); ?>">
                    Tags <?php echo adminTableHeaderArrow("post_tags"); ?>
                </th>
                <th rel="post_comment_count" class="<?php echo displayColOrderClass("post_comment_count"); ?>">
                    Comments <?php echo adminTableHeaderArrow("post_comment_count"); ?>
                </th>
                <th rel="post_likes" class="<?php echo displayColOrderClass("post_likes"); ?>">
                    Likes <?php echo adminTableHeaderArrow("post_likes"); ?>
                </th>
                <th rel="post_views_count" class="<?php echo displayColOrderClass("post_views_count"); ?>">
                    Views <?php echo adminTableHeaderArrow("post_views_count"); ?>
                </th>
                <th rel="post_date" class="<?php echo displayColOrderClass("post_date"); ?>">
                    Date <?php echo adminTableHeaderArrow("post_date"); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($posts as $post) {
                    echo "<tr class='admin_table_row' rel='../post.php?post_id={$post['post_id']}'>";
                    echo "<td class='clickable_td'><input class='selectCheckbox' type='checkbox' name='checkboxArray[]' value='{$post['post_id']}'></td>";
                    echo "<td>{$post['post_author']}</td>";
                    echo "<td>{$post['post_title']}</td>";
                    echo "<td>{$post['post_category']}</td>";
                    echo "<td>{$post['post_status']}</td>";
                    echo "<td>{$post['post_tags']}</td>";
                    echo "<td><a href='comments.php?p_id={$post['post_id']}'>{$post['post_comment_count']}</a></td>";
                    echo "<td>{$post['post_likes']}</td>";
                    echo "<td>{$post['post_views_count']}</td>";
                    echo "<td>{$post['post_date']}</td>";
                    echo "<td class='clickable_td'><a href='posts.php?source=edit_post&post_id={$post['post_id']}'>Edit</a></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>

    <?php include "../includes/pagination.php"; ?>

    <br /> <!-- remove later! -->

</form>

<script>
    $(document).ready(function(){
        // Clicking row goes to post
        $('.admin_table_row td').not(".clickable_td").click(function() {
            var href = $(this).parent().attr("rel");
            if (href) window.location = href;
        });
    });
</script>