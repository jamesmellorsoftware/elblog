<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">El Blog</a>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <?php if (userLoggedIn()) { ?>
                    <?php if (userIsAdmin() || userIsWriter()) { ?><li class="nav-item"><a class="nav-link" href="admin/">Admin</a></li><?php } ?>
                    <li class="nav-item"><a class="nav-link" href="includes/logout.php">Log out <?php echo $_SESSION['user_username']; ?></a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>