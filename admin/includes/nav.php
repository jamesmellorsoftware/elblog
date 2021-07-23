<nav class="navbar navbar-default navbar-fixed-top">
    <div class="brand">
        <a href="index.php ">El Blog: Admin</a>
    </div>
    <div class="container-fluid">
        <?php if (userIsAdmin()) { ?>
            <div class="navbar-btn">
                <button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
            </div>
        <?php } ?>
        <div id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><?php echo $_SESSION['user_username']; ?></span>
                        <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="../index.php"><i class="lnr lnr-home"></i> <span>Back to Blog</span></a></li>
                        <?php if (userIsAdmin()) { ?>
                            <li><a href='users.php?source=edit_user&edit=<?php echo $_SESSION['user_id'] ?>'><i class="lnr lnr-user"></i> <span>Edit Profile</span></a></li>
                        <?php } ?>
                            <li><a href="../includes/logout.php"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>