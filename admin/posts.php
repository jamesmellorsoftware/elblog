<?php include "includes/head.php"; ?>

<div class="col-lg-12">
    <?php
        // Mini display controller
        // Load different pages based on GET
        (isset($_GET['source'])) ? $source = $_GET['source'] : $source = '';
        // Writers should not have access to edit or view posts
        // Default them to the create page
        if (userIsWriter()) $source = "create_post";
        switch ($source) {
            case "create_post":
                include "subpages/create_post.php";
                break;
            case "edit_post":
                include "subpages/edit_post.php";
                break;
            default:
                include "subpages/view_all_posts.php";
                break;
        }
    ?>
</div>

<?php include "includes/footer.php"; ?>