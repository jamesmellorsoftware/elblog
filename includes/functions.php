<?php

function logOutUser() {
    // Destroys session variables and logs user out
    // Redirects user to the blog index
    $_SESSION['user_username']   = null;
    $_SESSION['user_first_name'] = null;
    $_SESSION['user_last_name']  = null;
    $_SESSION['user_role']       = null;

    header("Location: ../index.php");
}

function usernameExists($username = "") {
    // Query database to see if user exists
    // Returns true or false
    global $connection;

    // Sanitise username
    trim(mysqli_real_escape_string($connection, $username));

    $query = "SELECT user_username FROM users WHERE user_username = '$username' ";
    $result = mysqli_query($connection, $query);
    checkQuery($result);

    return (mysqli_num_rows($result) > 0) ? true : false;
}


function emailInUse($email = "") {
    // Query database to see if email is in use
    // Returns true or false
    global $connection;

    // Sanitise email
    trim(mysqli_real_escape_string($connection, $email));

    $query = "SELECT user_email FROM users WHERE user_email = '$email' ";
    $result = mysqli_query($connection, $query);
    checkQuery($result);

    return (mysqli_num_rows($result) > 0) ? true : false;
}


function checkQuery($query){
    // Checks any query's success
    // Dies and provides error message on failure
    global $connection;
    if (!$query) die("QUERY FAILED: " . mysqli_error($connection));
}


function userLoggedIn() {
    // Checks if the user ID & role session variables are set
    // Returns true or false
    return (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) ? true : false;
}


function userIsAdmin() {
    // Checks if the user role session variable is set and equal to admin
    // Returns true or false
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == "admin") ? true : false;
}


function userIsWriter() {
    // Checks if the user role session variable is set and equal to writer
    // Returns true or false
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == "writer") ? true : false;
}


function redirectWriter() {
    // Checks if user is a writer
    // Redirects them to create post page
    if (userIsWriter()) header("Location: posts.php?source=create_post");
}


function getIdFromUsername($user_username = "") {
    // Takes in a username, queries database, returns the user ID
    global $connection;
    $user_id = "";

    // Sanitise username
    trim(mysqli_real_escape_string($connection, $user_username));

    if (!empty($user_username)) {
        $query = "SELECT user_id FROM users WHERE user_username = '$user_username' ";
        $result = mysqli_query($connection, $query);
        checkQuery($result);

        $row = mysqli_fetch_array($result);
        if (!empty($row)) $user_id = $row['user_id'];
    }

    return $user_id;
}


function generateAdminBulkOptions($bulk_options){
    // Generate bulk options selector for pages in admin section
    // Accepts a 2D array options with "value" and "name" keys
    // "value" is the select option value and "name" is the inner HTML
    echo "";
    echo "<div id='bulkOptionsContainer' class='input-group'>";
    echo "<select class='form-control' name='bulk_option'>";
    echo "<option value=''>Select Option</option>";
    foreach ($bulk_options as $bulk_option) {
        $bulk_option_value = $bulk_option['value'];
        $bulk_option_name  = $bulk_option['name'];
        echo "<option value='{$bulk_option_value}'>{$bulk_option_name}</option>";
    }
    echo "</select>";
    echo "<span class='input-group-btn'>";
    echo "<input type='submit' class='btn btn-success' value='Apply'>";
    echo "</span>";
    echo "</div>";
}


function adminTableHeaderArrow($orderby = ""){
    // Returns arrow direction for the headers of a table in the admin section
    // Based off the GET request
    $arrow = "";

    if (isset($_GET['orderby']) && $_GET['orderby'] === $orderby) {
        if (isset($_GET['orderdirection']) && $_GET['orderdirection'] === "ASC") $arrow = "&uarr;";
        if (isset($_GET['orderdirection']) && $_GET['orderdirection'] === "DESC") $arrow = "&darr;";
    }
    
    return $arrow;
}


function displayColOrderClass($colRel = ""){
    // Returns the order direction of a column in an admin table
    if (isset($_GET['orderby']) && isset($_GET['orderdirection']) && $_GET['orderby'] === $colRel) {
        return $_GET['orderdirection'];
    }
    return "";
}


function generateDismissableAlert($errors = [], $alert = "danger") {
    // Generates a dismissable alert for forms etc
    // Default state danger
    // Returns HTML to be echo'd
    $html = "";

    foreach ($errors as $error) {
        if (!empty($error)) {
            $html.= "<div class='form-group'>";
            $html.= "<div class='alert alert-{$alert} alert-dismissible' role='alert'>";
            $html.= "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
            $html.= "<span aria-hidden='true'>×</span>";
            $html.= "</button> ";
            $html.= $error;
            $html.= "</div>";
            $html.= "</div>";
        }
    }

    return $html;
}


function checkFormForErrors($errors) {
    // Accepts array
    // Checks error array if empty
    // Returns true or false

    $errorsInForm = false;
    foreach ($errors as $error) {
        if (!empty($error)) {
            $errorsInForm = true;
            break;
        }
    }
    return $errorsInForm;
}


function applySearchFilters($search_filters = []) {
    // Applies AND sections of queries after search filter applied
    // Returns string $query
    $query = "";
    
    $andRequired = false;
    foreach ($search_filters as $key => $value) {
        if (!empty($value['value'])) {
            $query.= ($andRequired) ? "AND " : "WHERE ";

            switch ($value['type']) {
                case "date":
                    $query.= "{$value['key']} ";
                    $query.= ($value['toOrFrom'] === "from") ? ">= " : "<= ";
                    $query.= "'{$value['value']}' ";
                    break;
                case "int":
                    $query.= "{$value['key']} = {$value['value']} ";
                    break;
                case "text":
                    $query.= "LOWER({$value['key']}) LIKE '%{$value['value']}%' ";
                    break;
                default:
                    break;
            }

            $andRequired = true;
        }
    }

    return $query;
}


function applyOrderBy(){
    // Returns order by clause customised by user in search feature
    // Returns string $order_by

    global $connection;
    
    $order_by = "";

    if (isset($_GET['orderby']) && isset($_GET['orderdirection'])) {
        $order_by_col = trim(mysqli_real_escape_string($connection, $_GET['orderby']));
        $order_direction = trim(mysqli_real_escape_string($connection, $_GET['orderdirection']));
        $order_by = "ORDER BY {$order_by_col} {$order_direction} ";
    }

    return $order_by;
}

?>