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
        "user_id" => [
            "type" => "int", "key" => "users.user_id", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-id']))
        ],
        "user_username" => [
            "type" => "text", "key" => "users.user_username", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-username']))
        ],
        "user_firstname" => [
            "type" => "text", "key" => "users.user_firstname", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-firstname']))
        ],
        "user_lastname" => [
            "type" => "text", "key" => "users.user_lastname", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-lastname']))
        ],
        "user_email" => [
            "type" => "text", "key" => "users.user_email", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-email']))
        ],
        "user_role" => [
            "type" => "text", "key" => "users.user_role", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__user-role']))
        ]
    ];
}

// Initialise bulk options
$bulk_options = [
    ["value" => "delete",     "name"  => "Delete"],
    ["value" => "admin",      "name"  => "Change to Admin"],
    ["value" => "writer",     "name"  => "Change to Writer"],
    ["value" => "subscriber", "name"  => "Change to Subscriber"]
];

// Apply bulk option to every user checked
if (isset($_POST['checkboxArray'])) {
    foreach($_POST['checkboxArray'] as $user_id) {

        $user_id = trim(mysqli_real_escape_string($connection, $user_id));

        $bulk_option = trim(mysqli_real_escape_string($connection, $_POST['bulk_option']));

        switch ($bulk_option) {
            case "writer":
            case "subscriber":
            case "admin":
                $query = "UPDATE users SET user_role = '{$bulk_option}' WHERE user_id = {$user_id} ";
                break;
            case "delete":
                $query = "DELETE FROM users WHERE user_id = {$user_id} ";
                break;
        }

        $bulk_option_query = mysqli_query($connection, $query);
        checkQuery($bulk_option_query);
    }
}

// To edit user, redirect to edit page
if (isset($_GET['edit'])) {
    $user_id_delete = $_GET['edit'];
    header("Location: users.php");
}

// Retrieve users
$query = "SELECT * FROM users ";
$query.= applySearchFilters($search_filters);
$query.= applyOrderBy();
$query.= "LIMIT {$page_offset}, {$results_per_page} "; // Limit for pagination
$select_all_users = mysqli_query($connection, $query);

// Catch users in array
$i = 0;
$users = [];
while ($row = mysqli_fetch_assoc($select_all_users)) {
    $users[$i]['user_id']        = $row['user_id'];
    $users[$i]['user_username']  = $row['user_username'];
    $users[$i]['user_firstname'] = $row['user_firstname'];
    $users[$i]['user_lastname']  = $row['user_lastname'];
    $users[$i]['user_role']      = $row['user_role'];
    $users[$i]['user_email']     = $row['user_email'];
    $i++;
}

// Include search modal
include "includes/modal_search_users.php";

?>

<div class="page-header-search">
    <h1 class="page-title">All Users</h1>
    <i aria-hidden="true" id="" data-toggle="modal" data-target="#searchModal"
    class="fa fa-search fa-2x text-right <?php if (isset($_GET['search'])) echo "text-info"; ?>"></i>
</div>

<form action="" method="post">
    <?php generateAdminBulkOptions($bulk_options); ?>
    <table class="table table-bordered table-hover">
        <thead>
            <tr class="table__headers">
                <th class="clickable_th checkboxTableRow"><input type="checkbox" id="selectAllCheckboxes"></th>
                <th rel="user_id" class="<?php echo displayColOrderClass("user_id"); ?>">
                    ID <?php echo adminTableHeaderArrow("user_id"); ?>
                </th>
                <th rel="user_username" class="<?php echo displayColOrderClass("user_username"); ?>">
                    Username <?php echo adminTableHeaderArrow("user_username"); ?>
                </th>
                <th rel="user_firstname" class="<?php echo displayColOrderClass("user_firstname"); ?>">
                    First Name <?php echo adminTableHeaderArrow("user_firstname"); ?>
                </th>
                <th rel="user_lastname" class="<?php echo displayColOrderClass("user_lastname"); ?>">
                    Last Name <?php echo adminTableHeaderArrow("user_lastname"); ?>
                </th>
                <th rel="user_role" class="<?php echo displayColOrderClass("user_role"); ?>">
                    Role <?php echo adminTableHeaderArrow("user_role"); ?>
                </th>
                <th rel="user_email" class="<?php echo displayColOrderClass("user_email"); ?>">
                    Email <?php echo adminTableHeaderArrow("user_email"); ?>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td><input class='selectCheckbox' type='checkbox' name='checkboxArray[]' value='{$user['user_id']}'></td>";
                    echo "<td>{$user['user_id']}</td>";
                    echo "<td>{$user['user_username']}</td>";
                    echo "<td>{$user['user_firstname']}</td>";
                    echo "<td>{$user['user_lastname']}</td>";
                    echo "<td>{$user['user_role']}</td>";
                    echo "<td>{$user['user_email']}</td>";
                    echo "<td><a href='?source=edit_user&edit={$user['user_id']}'>Edit</a></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>

    <?php include "../includes/pagination.php"; ?>

    <br /> <!-- remove later! -->

</form>