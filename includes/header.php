<?php
// Modify titles and header size based on page selection
// Catch page name and initialise defaults
$pageName = basename($_SERVER['PHP_SELF']);
if (isset($_GET['post_author'])) $pageName = "index.php?post_author";
$pageTitle = "";
$pageSubhead = "";
$extraStyles = "";
$showHeader = true;

// Adjust header text and size based on page name
switch ($pageName) {
    case "about.php":
        $pageTitle = "About Me";
        $pageSubhead = "This is what I do.";
        break;
    case "contact.php":
        $pageTitle = "Contact Me";
        $pageSubhead = "Have questions? I have answers.";
        break;
    case "index.php?post_author":
    case "login.php":
    case "post.php":
    case "register.php":
        $extraStyles = "height: 65px; ";
        $showHeader = false;
        break;
    case "index.php":
    default:
        $pageTitle = "El Blog";
        $pageSubhead = "A blog by James Mellor.";
        break;
}

?>

<header class="masthead" style="background-image: url('img/home-bg.jpg'); <?php echo $extraStyles; ?>">
    <div class="overlay"></div>
    <?php if ($showHeader) { ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="site-heading">
                        <h1><?php echo $pageTitle; ?></h1>
                        <span class="subheading"><?php echo $pageSubhead; ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</header>