<?php
// Get page name so active page can be selected
$pagename = basename($_SERVER['PHP_SELF']);
?>

<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav">
                <li>
                    <a href="#submenu__posts" data-toggle="collapse"
                    class="<?php echo ($pagename == "posts.php") ? "active" : "collapsed"; ?>">
                        <i class="lnr lnr-pencil"></i>
                        <span>Posts</span>
                        <i class="icon-submenu lnr lnr-chevron-left"></i>
                    </a>
                    <div id="submenu__posts" class="<?php echo ($pagename == "posts.php") ? "active" : "collapse"; ?>">
                        <ul class="nav">
                            <?php if (userIsAdmin()) { ?>
                                <li>
                                    <a href="posts.php"
                                    class="<?php if ($pagename === "posts.php" && !isset($_GET['source'])) echo "active"; ?>">
                                        All Posts
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="posts.php?source=create_post"
                                class="<?php if ($pagename === "posts.php" && isset($_GET['source']) && $_GET['source'] === "create_post") echo "active"; ?>">
                                    Create Post
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <?php if (userIsAdmin()) { ?>
                    <li>
                        <a href="#submenu__users" data-toggle="collapse"
                        class="<?php echo ($pagename == "users.php") ? "active" : "collapsed"; ?>">
                            <i class="lnr lnr-user"></i>
                            <span>Users</span>
                            <i class="icon-submenu lnr lnr-chevron-left"></i>
                        </a>
                        <div id="submenu__users" class="<?php echo ($pagename == "users.php") ? "active" : "collapse"; ?>">
                            <ul class="nav">
                                <li>
                                    <a href="users.php"
                                    class="<?php if ($pagename === "users.php" && !isset($_GET['source'])) echo "active"; ?>">
                                        All Users
                                    </a>
                                </li>
                                <li>
                                    <a href="users.php?source=create_user"
                                    class="<?php if ($pagename === "users.php" && isset($_GET['source']) && $_GET['source'] === "create_user") echo "active"; ?>">
                                        Create User
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="comments.php" 
                        class="<?php if ($pagename === "comments.php") echo "active"; ?>">
                            <i class="fa fa-paper-plane-o"></i> <span>Comments</span>
                        </a>
                    </li>
                    <li>
                        <a href="categories.php"  class="<?php if ($pagename == "categories.php") echo "active"; ?>">
                            <i class="lnr lnr-bookmark"></i> <span>Categories</span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>