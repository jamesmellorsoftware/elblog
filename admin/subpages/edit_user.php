<?php
// Check if user selected
// If selected retrieve user data
// Redirect to main users page if not
if (isset($_GET['edit'])) {
    $user_id = trim(mysqli_escape_string($connection, $_GET['edit']));

    $query = "SELECT * FROM users WHERE user_id = {$user_id} ";
    $select_user = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($select_user);

    // Catch user data
    $user_firstname  = $row['user_firstname'];
    $user_lastname   = $row['user_lastname'];
    $user_username   = $row['user_username'];
    $user_password   = $row['user_password'];
    $user_email      = $row['user_email'];
    $user_role       = $row['user_role'];
} else {
    header("Location: users.php");
}

// Initialise errors
$edit_user_errors = [
    "user_firstname" => "",
    "user_lastname"  => "",
    "user_username"  => "",
    "user_password"  => "",
    "user_email"     => ""
];

$user_edited_successfully = false;

// If user edited, catch and validate new properties and update users table with them
if (isset($_POST['edit_user'])) {
    // Catch and sanitise user inputs
    $user_firstname = trim(mysqli_real_escape_string($connection, $_POST['user_firstname']));
    $user_lastname  = trim(mysqli_real_escape_string($connection, $_POST['user_lastname']));
    $user_username1 = trim(mysqli_real_escape_string($connection, $_POST['user_username']));
    $user_password  = trim(mysqli_real_escape_string($connection, $_POST['user_password']));
    $user_email1    = trim(mysqli_real_escape_string($connection, $_POST['user_email']));
    $user_role      = trim(mysqli_real_escape_string($connection, $_POST['user_role']));

    // Username or password too long / short?
    if (strlen($user_username1) < 4)  $edit_user_errors['user_username'] = 'Username needs to be longer than 4 characters.';
    if (strlen($user_password)  < 2)  $edit_user_errors['user_password'] = 'Password needs to be longer than 2 characters.';
    if (strlen($user_username)  > 20) $edit_user_errors['user_username'] = 'Username needs to be shorter than 20 characters.';
    if (strlen($user_password)  > 20) $edit_user_errors['user_password'] = 'Password needs to be shorter than 20 characters.';
    
    // Fields empty?
    if (empty($user_firstname)) $edit_user_errors['user_firstname'] = "Please enter a first name.";
    if (empty($user_lastname))  $edit_user_errors['user_lastname']  = "Please enter a last name.";
    if (empty($user_username1)) $edit_user_errors['user_username']  = "Please enter a username.";
    if (empty($user_password))  $edit_user_errors['user_password']  = "Please enter a password.";
    if (empty($user_email1))    $edit_user_errors['user_email']     = "Please enter an email.";

    // Username exists or email already in use?
    if ($user_username1 !== $user_username && usernameExists($user_username)) $edit_user_errors['user_username'] = "Username already exists.";
    if ($user_email1 !== $user_email && emailInUse($user_email1))             $edit_user_errors['user_email']    = "Email already in use.";

    // Errors exist in form?
    $edit_user_errors_exist = false;
    foreach ($edit_user_errors as $edit_user_error) {
        if (!empty($edit_user_error)) $edit_user_errors_exist = true;
    }

    // If no errors exist, update user
    if (!$edit_user_errors_exist) {
        // Encrypt password
        $user_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10) );

        // Update user info
        $query = "UPDATE users SET ";
        $query.= "user_username = '{$user_username}', ";
        $query.= "user_password = '{$user_password}', ";
        $query.= "user_firstname = '{$user_firstname}', ";
        $query.= "user_lastname = '{$user_lastname}', ";
        $query.= "user_role = '{$user_role}', ";
        $query.= "user_email = '{$user_email1}' ";
        $query.= "WHERE user_id = {$user_id} ";
        
        $edit_user_query = mysqli_query($connection, $query);

        checkQuery($edit_user_query);

        $user_edited_successfully = true;

        $edit_user_success_msgs = [
            "user_edited" => "User edited succesfully."
        ];
    }
}

if ($user_edited_successfully) {
    echo generateDismissableAlert($edit_user_success_msgs, "success");
} else { ?>

<form action="" method="post" enctype="multipart/form-data">
    <?php echo generateDismissableAlert($edit_user_errors); ?>
    <div class="form-group">
        <input type="text" name="user_username"
        class="form-control <?php if (!empty($edit_user_errors['user_username'])) echo "is-invalid"; ?>"
        placeholder="Username"
        value="<?php if (isset($user_username)) echo $user_username; ?>">
    </div>

    <div class="form-group">
        <input type="text" name="user_firstname"
        class="form-control <?php if (!empty($edit_user_errors['user_firstname'])) echo "is-invalid"; ?>"
        placeholder="First Name"
        value="<?php if (isset($user_firstname)) echo $user_firstname; ?>">
    </div>

    <div class="form-group">
        <input type="text" name="user_lastname"
        class="form-control <?php if (!empty($edit_user_errors['user_lastname'])) echo "is-invalid"; ?>"
        placeholder="Last Name"
        value="<?php if (isset($user_lastname)) echo $user_lastname; ?>">
    </div>

    <div class="form-group">
        <input type="password" name="user_password"
        class="form-control <?php if (!empty($edit_user_errors['user_password'])) echo "is-invalid"; ?>"
        autofill="off" placeholder="Password">
    </div>

    <div class="form-group">
        <input type="text" name="user_email"
        class="form-control <?php if (!empty($edit_user_errors['user_email'])) echo "is-invalid"; ?>"
        placeholder="Email Address"
        value="<?php if (isset($user_email)) echo $user_email; ?>">
    </div>

    <div class="form-group">
        <select name="user_role" id="user_role" class="form-control">
            <option value="subscriber"
            <?php if (isset($user_role) && $user_role === "admin") echo "selected"; ?>>Subscriber</option>
            <option value="admin"
            <?php if (isset($user_role) && $user_role === "admin") echo "selected"; ?>>Admin</option>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="edit_user" value="Edit User">
    </div>
</form>
<?php } ?>