<?php
include "includes/head.php";

// If user logged in, redirect to admin page
if (userLoggedIn()) header("Location: admin/index.php");

// Initialise errors
$login_errors = [
    "username" => "",
    "password" => ""
];

// If username and password set and correct, log user in
if (isset($_POST['login'])) {
    // Catch and sanitise user input
    $entered_username = trim(mysqli_real_escape_string($connection, $_POST['username']));
    $entered_password = trim(mysqli_real_escape_string($connection, $_POST['password']));

    // Fields empty? User doesn't exist?
    if (empty($entered_username))               $login_errors['username'] = "Username cannot be empty.";
    elseif (!usernameExists($entered_username)) $login_errors['username'] = "User does not exist.";
    elseif (empty($entered_password))           $login_errors['password'] = "Password cannot be empty.";
    
    // If no errors, check password
    if (empty($login_errors['username']) && empty($login_errors['password'])) {
        // Retrieve user data from entered username
        $query = "SELECT * FROM users WHERE user_username = '{$entered_username}' ";
        $select_user_query = mysqli_query($connection, $query);
        checkQuery($select_user_query);

        // Catch variables
        $user = mysqli_fetch_array($select_user_query);
        $user_id        = $user['user_id'];
        $user_username  = $user['user_username'];
        $user_password  = $user['user_password'];
        $user_firstname = $user['user_firstname'];
        $user_lastname  = $user['user_lastname'];
        $user_role      = $user['user_role'];
        $user_email     = $user['user_email'];

        // If entered password matches db password, set session vars to log in and send to admin
        // else notify user
        if (password_verify($entered_password, $user_password)) {
            // Login successful, send to admin
            $_SESSION['user_id']         = $user_id;
            $_SESSION['user_username']   = $user_username;
            $_SESSION['user_first_name'] = $user_firstname;
            $_SESSION['user_last_name']  = $user_lastname;
            $_SESSION['user_role']       = $user_role;
            $_SESSION['user_email']      = $user_email;
            header("Location: admin/index.php");
        } else {
            // Wrong password entered, notify user
            $login_errors['password'] = "Password incorrect.";
        } 
    }
}
?>

<!-- Main Content -->
<div class="container">
    <div class="row justify-content-center">
        <form id="login-form" role="form" autocomplete="off" class="form" method="post">
            <?php echo generateDismissableAlert($login_errors); ?>
            <div class="form-group">
                <div class="input-group">
                    <input name="username"
                    type="text"
                    class="form-control <?php if (!empty($login_errors['username'])) echo "is-invalid"; ?>"
                    placeholder="Enter Username"
                    value="<?php if (isset($entered_username)) echo $entered_username; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input name="password"
                    type="password"
                    class="form-control <?php if (!empty($login_errors['password'])) echo "is-invalid"; ?>"
                    placeholder="Enter Password">
                </div>
            </div>
            <div class="form-group">
                <input name="login" class="btn btn-primary btn-block" value="Login" type="submit">
            </div>
            <div class="form-group"><a href="forgot.php">Forgot Password</a></div>
            <div class="form-group"><a href="register.php">Don't have an account? Register</a></div>
        </form>
    </div>
</div>

<hr>

<?php include "includes/footer.php"; ?>