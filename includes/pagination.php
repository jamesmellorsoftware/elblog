<?php
// Catch page name if it got missed by the header
if (!isset($pagename)) $pagename = basename($_SERVER['PHP_SELF']);

// Load extra get requests to add to button href later
$get_requests = "";
foreach ($_GET as $key => $get_request) $get_requests.= "{$key}={$get_request}&";

// Get number of results returned from arrays caught from db
// Default zero so pagination buttons don't show
$results_returned = 0;
switch ($pagename) {
    case "categories.php":
        $results_returned = count($categories);
        break;
    case "comments.php":
        $results_returned = count($comments);
        break;
    case "index.php":
    case "posts.php":
        $results_returned = count($posts);
        break;
    case "users.php":
        $results_returned = count($users);
        break;
}

?>

<div class="buttons__container buttons__container--pagination">
    <a
    class="btn btn-primary <?php if ($page == 1) echo "invisible"; ?>"
    href="<?php echo $pagename; ?>?<?php echo $get_requests; ?>page=<?php echo $page - 1; ?>">
        &larr; Prev Page
    </a>
    <a
    class="btn btn-primary <?php if ($results_returned < $results_per_page) echo "invisible"; ?>"
    style="align-self: flex-end;"
    href="<?php echo $pagename; ?>?<?php echo $get_requests; ?>page=<?php echo $page+1; ?>">
        Next Page &rarr;
    </a>
</div>