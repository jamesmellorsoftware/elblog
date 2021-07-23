<?php
include "includes/head.php";

redirectWriter(); // Writer should not have access to edit comments

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
        "comment_id" => [
            "type" => "int", "key" => "comments.comment_id", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__comment-id']))
        ],
        "comment_author" => [
            "type" => "text", "key" => "users.user_username", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__comment-author']))
        ],
        "comment_post_id" => [
            "type" => "int", "key" => "comments.comment_post_id", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__comment-post_id']))
        ],
        "comment_content" => [
            "type" => "text", "key" => "comments.comment_content", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__comment-content']))
        ],
        "comment_status" => [
            "type" => "text", "key" => "comments.comment_status", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__comment-status']))
        ]
    ];
}

// Initialise bulk options
$bulk_options = [
    ["value" => "approved",    "name"  => "Approve"],
    ["value" => "unapproved",  "name"  => "Unapprove"],
    ["value" => "delete",      "name"  => "Delete"],
    ["value" => "reset_likes", "name"  => "Reset Likes"]
];

// Apply bulk option to every comment checked
if (isset($_POST['checkboxArray'])) {
    foreach($_POST['checkboxArray'] as $comment_id) {

        $comment_id = trim(mysqli_real_escape_string($connection, $comment_id));

        $bulk_option = trim(mysqli_real_escape_string($connection, $_POST['bulk_option']));
        $queries = [];

        // Build query based on option selected
        switch ($bulk_option) {
            case "approved":
            case "unapproved":
                $queries[1] = "UPDATE comments SET comment_status = '{$bulk_option}' WHERE comment_id = {$comment_id} ";
                break;
            case "delete":
                $queries[1] = "DELETE FROM comments WHERE comment_id = {$comment_id} ";
                $queries[2] = "DELETE FROM comment_likes WHERE comment_id = {$comment_id} ";
                break;
            case "reset_likes":
                $queries[1] = "DELETE FROM comment_likes WHERE comment_id = {$comment_id} ";
                break;
        }

        foreach ($queries as $query) {
            $bulk_option_query = mysqli_query($connection, $query);
            checkQuery($bulk_option_query);
        }
    }
}

// Retrieve comments
$query = "SELECT ";
$query.= "comments.comment_id, comments.comment_user_id, comments.comment_content, ";
$query.= "comments.comment_post_id, comments.comment_status, comments.comment_date, ";
$query.= "users.user_username, ";
$query.= "COUNT(comment_likes.comment_id) AS comment_likes ";
$query.= "FROM comments ";
$query.= "LEFT JOIN users ON users.user_id = comments.comment_user_id ";
$query.= "LEFT JOIN comment_likes ON comment_likes.comment_id = comments.comment_id ";
$query.= applySearchFilters($search_filters);
$query.= "GROUP BY comments.comment_id "; // Group by comment ID to avoid duplicate rows
$query.= applyOrderBy();
$query.= "LIMIT {$page_offset}, {$results_per_page} "; // Apply limit for pagination
$select_all_comments = mysqli_query($connection, $query);

// Catch comments in array
$i = 0;
$comments = [];
if ($select_all_comments) {
    while ($row = mysqli_fetch_assoc($select_all_comments)) {
        $comments[$i]['comment_id']      = $row['comment_id'];
        $comments[$i]['comment_author']  = $row['user_username'];
        $comments[$i]['comment_content'] = $row['comment_content'];
        $comments[$i]['comment_post_id'] = $row['comment_post_id'];
        $comments[$i]['comment_likes']   = $row['comment_likes'];
        $comments[$i]['comment_status']  = $row['comment_status'];
        $comments[$i]['comment_date']    = $row['comment_date'];
        $i++;
    }
}

include "includes/modal_search_comments.php";

?>

<div class="page-header-search">
    <h1 class="page-title">Comments</h1>
    <i aria-hidden="true" id="" data-toggle="modal" data-target="#searchModal"
    class="fa fa-search fa-2x text-right <?php if (isset($_GET['search'])) echo "text-info"; ?>"></i>
</div>

<form action="" method="post">
    <table class="table table-bordered">
        <?php generateAdminBulkOptions($bulk_options); ?>
        <thead>
            <tr class="table__headers">
                <th class="clickable_th checkboxTableRow"><input type="checkbox" id="selectAllCheckboxes"></th>
                <th rel="comment_user_id" class="<?php echo displayColOrderClass("comment_user_id"); ?>">
                    Author <?php echo adminTableHeaderArrow("comment_user_id"); ?>
                </th>
                <th rel="comment_post_id" class="<?php echo displayColOrderClass("comment_post_id"); ?>">
                    Post <?php echo adminTableHeaderArrow("comment_post_id"); ?>
                </th>
                <th rel="comment_status" class="<?php echo displayColOrderClass("comment_status"); ?>">
                    Status <?php echo adminTableHeaderArrow("comment_status"); ?>
                </th>
                <th rel="comment_likes" class="<?php echo displayColOrderClass("comment_likes"); ?>">
                    Likes <?php echo adminTableHeaderArrow("comment_likes"); ?>
                </th>
                <th rel="comment_content" class="<?php echo displayColOrderClass("comment_content"); ?>">
                    Content <?php echo adminTableHeaderArrow("comment_content"); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($comments as $comment) {
                    echo "<tr>";
                    echo "<td><input class='selectCheckbox' type='checkbox' name='checkboxArray[]' value='{$comment['comment_id']}'></td>";
                    echo "<td>{$comment['comment_author']}</td>";
                    echo "<td class='comment_post_id' rel='../post.php?post_id={$comment['comment_post_id']}'>{$comment['comment_post_id']}</td>";
                    echo "<td>{$comment['comment_status']}</td>";
                    echo "<td>{$comment['comment_likes']}</td>";
                    echo "<td>{$comment['comment_content']}</td>";
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
        // Clicking post cell goes to post
        $('.comment_post_id').click(function() {
            var href = $(this).attr("rel");
            if (href) window.location = href;
        });
    });
</script>

<?php include "includes/footer.php"; ?>