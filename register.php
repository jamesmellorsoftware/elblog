<?php
// Include head, database, and functions
include "includes/head.php";

// If user logged in, redirect to admin page
if (userLoggedIn()) header("Location: admin/index.php");

// Initialise errors array
$registration_errors = [
    "username"   => "",
    "password"   => "",
    "email"      => "",
    "firstname" => "",
    "lastname"  => ""
];

// User clicked register
// Validate username and password
// If username and password set, log user in, else display errors
if (isset($_POST['register'])) {
    // Catch & sanitise user input
    $entered_username  = trim(mysqli_real_escape_string($connection, $_POST['username']));
    $entered_password  = trim(mysqli_real_escape_string($connection, $_POST['password']));
    $entered_firstname = trim(mysqli_real_escape_string($connection, $_POST['firstname']));
    $entered_lastname  = trim(mysqli_real_escape_string($connection, $_POST['lastname']));
    $entered_email     = trim(mysqli_real_escape_string($connection, $_POST['email']));

    // Inputs empty?
    if (empty($entered_username))  $registration_errors['username']  = "Username cannot be empty.";
    if (empty($entered_password))  $registration_errors['password']  = "Password cannot be empty.";
    if (empty($entered_firstname)) $registration_errors['firstname'] = "First Name cannot be empty.";
    if (empty($entered_lastname))  $registration_errors['lastname']  = "Last Name cannot be empty.";
    if (empty($entered_email))     $registration_errors['email']     = "Email cannot be empty.";

    // Username or email in use?
    if (usernameExists($entered_username)) $registration_errors['username'] = "Username already exists.";
    if (emailInUse($entered_email))        $registration_errors['email']    = "Email already in use.";

    // Inputs too long / short?
    if (strlen($entered_username) < 4)  $registration_errors['username'] = 'Username needs to be longer than 4 characters.';
    if (strlen($entered_password) < 2)  $registration_errors['password'] = 'Password needs to be longer than 2 characters.';
    if (strlen($entered_username) > 20) $registration_errors['username'] = 'Username needs to be shorter than 20 characters.';
    if (strlen($entered_password) > 20) $registration_errors['password'] = 'Password needs to be shorter than 20 characters.';

    // Check for errors
    $errorsInForm = checkFormForErrors($registration_errors);
    
    // If no errors, register user and log them in
    if (!$errorsInForm) {
        // Encrypt entered password
        $entered_password = password_hash($entered_password, PASSWORD_BCRYPT, array('cost' => 12) );

        // Register user, insert details into db
        $query = "INSERT INTO users (user_username, user_password, user_firstname, user_lastname, user_role, user_email) ";
        $query.= "VALUES ('{$entered_username}', '{$entered_password}', '{$entered_firstname}', '{$entered_lastname}', 'subscriber', '{$entered_email}') ";
        $register_user_query = mysqli_query($connection, $query);
        checkQuery($register_user_query);

        // Retrieve new user ID
        $query = "SELECT user_id FROM users WHERE user_username = '{$entered_username}' ";
        $retrieve_new_user_id = mysqli_query($connection, $query);
        checkQuery($retrieve_new_user_id);

        // Catch db data in variables
        $user = mysqli_fetch_array($retrieve_new_user_id);
        $user_id = $user['user_id'];

        // Set session variables (log user in), redirect to admin index
        $_SESSION['user_id']         = $user_id;
        $_SESSION['user_username']   = $entered_username;
        $_SESSION['user_first_name'] = $entered_firstname;
        $_SESSION['user_last_name']  = $entered_lastname;
        $_SESSION['user_role']       = "subscriber";
        header("Location: admin/index.php");
    }
}
?>

<!-- Main Content -->
<div class="container">
    <div class="row justify-content-center">
        <form id="registration-form" role="form" autocomplete="off" class="form" method="post">
            <?php echo generateDismissableAlert($registration_errors); ?>
            <div class="form-group">
                <div class="input-group">
                    <input name="username"
                    type="text"
                    class="form-control <?php if (!empty($registration_errors['username'])) echo "is-invalid"; ?>"
                    placeholder="Enter Username"
                    value="<?php if (isset($entered_username)) echo $entered_username; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input name="password"
                    type="password"
                    class="form-control <?php if (!empty($registration_errors['password'])) echo "is-invalid"; ?>"
                    placeholder="Enter Password">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input name="email"
                    type="email"
                    class="form-control <?php if (!empty($registration_errors['email'])) echo "is-invalid"; ?>"
                    placeholder="Enter Email Address"
                    value="<?php if (isset($entered_email)) echo $entered_email; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input name="firstname"
                    type="text"
                    class="form-control <?php if (!empty($registration_errors['firstname'])) echo "is-invalid"; ?>"
                    placeholder="Enter First Name"
                    value="<?php if (isset($entered_firstname)) echo $entered_firstname; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input name="lastname"
                    type="text"
                    class="form-control <?php if (!empty($registration_errors['lastname'])) echo "is-invalid"; ?>"
                    placeholder="Enter Last Name"
                    value="<?php if (isset($entered_lastname)) echo $entered_lastname; ?>">
                </div>
            </div>
            <div class="form-group">
                <input name="register" class="btn btn-primary btn-block" value="Register" type="submit">
            </div>
            <div class="form-group"><a href="login.php">Already have an account? Login</a></div>
        </form>
    </div>
</div>

<hr>

<?php include "includes/footer.php"; ?>