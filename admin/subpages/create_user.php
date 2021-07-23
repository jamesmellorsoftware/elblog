<?php

// Initialise errors
$create_user_errors = [
    "user_firstname" => "",
    "user_lastname"  => "",
    "user_username"  => "",
    "user_password"  => "",
    "user_email"     => ""
];

$user_created_successfully = false;

// If form filled, catch and validate new properties and insert into users table
if (isset($_POST['create_user'])) {
    // Catch & sanitise inputs
    $user_firstname = trim(mysqli_real_escape_string($connection, $_POST['user_firstname']));
    $user_lastname  = trim(mysqli_real_escape_string($connection, $_POST['user_lastname']));
    $user_username  = trim(mysqli_real_escape_string($connection, $_POST['user_username']));
    $user_password  = trim(mysqli_real_escape_string($connection, $_POST['user_password']));
    $user_email     = trim(mysqli_real_escape_string($connection, $_POST['user_email']));
    $user_role      = trim(mysqli_real_escape_string($connection, $_POST['user_role']));

    // Fields too long or too short?
    if (strlen($user_username) < 4)  $create_user_errors['user_username'] = 'Username needs to be longer than 4 characters.';
    if (strlen($user_password) < 2)  $create_user_errors['user_password'] = 'Password needs to be longer than 2 characters.';
    if (strlen($user_username) > 20) $create_user_errors['user_username'] = 'Username needs to be shorter than 20 characters.';
    if (strlen($user_password) > 20) $create_user_errors['user_password'] = 'Password needs to be shorter than 20 characters.';

    // Fields empty?
    if (empty($user_firstname)) $create_user_errors['user_firstname'] = "Please enter a first name.";
    if (empty($user_lastname))  $create_user_errors['user_lastname']  = "Please enter a last name.";
    if (empty($user_username))  $create_user_errors['user_username']  = "Please enter a username.";
    if (empty($user_password))  $create_user_errors['user_password']  = "Please enter a password.";
    if (empty($user_email))     $create_user_errors['user_email']     = "Please enter an email.";

    // Username or email already in use?
    if (usernameExists($user_username)) $create_user_errors['user_username'] = "Username already exists.";
    if (emailInUse($user_email))        $create_user_errors['user_email']    = "Email already in use.";

    // Errors exist in form?
    $create_user_errors_exist = false;
    foreach ($create_user_errors as $create_user_error) {
        if (!empty($create_user_error)) $create_user_errors_exist = true;
    }

    // If no errors in form, encrypt password and create user
    if (!$create_user_errors_exist) {
        // Encrypt password
        $user_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 12) );

        $query = "INSERT INTO users(user_username, user_password, user_firstname, user_lastname,
                user_role, user_email) ";
        $query.= "VALUES('{$user_username}', '{$user_password}', '{$user_firstname}', '{$user_lastname}',
                '{$user_role}', '{$user_email}' ) ";
        $create_user_query = mysqli_query($connection, $query);
        checkQuery($create_user_query);

        $user_created_successfully = true;

        $edit_user_success_msgs = [
            "user_created" => "User created succesfully."
        ];
    }
}

?>

<h1 class="page-title">Create User</h1>

<form action="" method="post" enctype="multipart/form-data">
    <?php
        echo generateDismissableAlert($create_user_errors);

        if ($user_created_successfully) {
            echo generateDismissableAlert($edit_user_success_msgs, "success");
            $create_user_errors = [];
            $user_firstname = "";
            $user_lastname  = "";
            $user_username  = "";
            $user_password  = "";
            $user_email     = "";
            $user_role      = "";
        }
    ?>

    <div class="form-group">
        <input type="text" name="user_username"
        class="form-control <?php if (!empty($create_user_errors['user_username'])) echo "is-invalid"; ?>"
        placeholder="Username"
        value="<?php if (isset($user_username)) echo $user_username; ?>">
    </div>

    <div class="form-group">
        <input type="text" name="user_firstname"
        class="form-control <?php if (!empty($create_user_errors['user_firstname'])) echo "is-invalid"; ?>"
        placeholder="First Name"
        value="<?php if (isset($user_firstname)) echo $user_firstname; ?>">
    </div>

    <div class="form-group">
        <input type="text" name="user_lastname"
        class="form-control <?php if (!empty($create_user_errors['user_lastname'])) echo "is-invalid"; ?>"
        placeholder="Last Name"
        value="<?php if (isset($user_lastname)) echo $user_lastname; ?>">
    </div>

    <div class="form-group">
        <input type="password" name="user_password"
        class="form-control <?php if (!empty($create_user_errors['user_password'])) echo "is-invalid"; ?>"
        autofill="off" placeholder="Password">
    </div>

    <div class="form-group">
        <input type="text" name="user_email"
        class="form-control <?php if (!empty($create_user_errors['user_email'])) echo "is-invalid"; ?>"
        placeholder="Email Address"
        value="<?php if (isset($user_email)) echo $user_email; ?>">
    </div>

    <div class="form-group">
        <select name="user_role" id="user_role" class="form-control">
            <option value="subscriber"
            <?php if (isset($user_role) && $user_role === "admin") echo "selected"; ?>>Subscriber</option>
            <option value="writer"
            <?php if (isset($user_role) && $user_role === "writer") echo "selected"; ?>>Writer</option>
            <option value="admin"
            <?php if (isset($user_role) && $user_role === "admin") echo "selected"; ?>>Admin</option>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="create_user" value="Create User">
    </div>
</form>