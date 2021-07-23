<?php
include "includes/head.php";
redirectWriter(); // Writers should not have access to user options
?>

<div class="col-lg-12">
    <?php
        // Mini display controller
        // Load different pages based on GET
        (isset($_GET['source'])) ? $source = $_GET['source'] : $source = '';
        switch ($source) {
            case "create_user":
                include "subpages/create_user.php";
                break;
            case "edit_user":
                include "subpages/edit_user.php";
                break;
            default:
                include "subpages/view_all_users.php";
                break;
        }
    ?>
</div>

<?php include "includes/footer.php"; ?>