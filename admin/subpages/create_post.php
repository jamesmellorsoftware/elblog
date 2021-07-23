<?php

// Initialise errors
$create_post_errors = [
    "post_title"       => "",
    "post_category_id" => "",
    "post_tags"        => "",
    "post_content"     => ""
];

$post_created_successfully = false;

// If form filled, catch and validate new properties and insert into posts table
if (isset($_POST['create_post'])) {
    // Catch & sanitise inputs
    $post_title       = trim(mysqli_real_escape_string($connection, $_POST['post_title']));
    $post_author_id   = $_SESSION['user_id'];
    $post_category_id = trim(mysqli_real_escape_string($connection, $_POST['post_category_id']));
    $post_status      = trim(mysqli_real_escape_string($connection, $_POST['post_status']));
    $post_tags        = trim(mysqli_real_escape_string($connection, $_POST['post_tags']));
    $post_content     = trim(mysqli_real_escape_string($connection, $_POST['post_content']));
    $post_date        = date('d-m-y');

    // Fields empty?
    if (empty($post_title))       $create_post_errors['post_title']       = "Please give your post a title.";
    if (empty($post_category_id)) $create_post_errors['post_category_id'] = "Please select a category.";
    if (empty($post_tags))        $create_post_errors['post_tags']        = "Please give your post some tags.";
    if (empty($post_content))     $create_post_errors['post_content']     = "Please write some content.";

    // Errors in form?
    $create_post_errors_exist = false;
    foreach ($create_post_errors as $create_post_error) {
        if (!empty($create_post_error)) $create_post_errors_exist = true;
    }

    // If no errors in form, create post
    if (!$create_post_errors_exist) {
        $query = "INSERT INTO posts(post_title, post_author_id, post_date, post_content, post_status,
        post_category_id, post_tags) ";
        $query.= "VALUES('{$post_title}', '{$post_author_id}', now(), '{$post_content}', 
                '{$post_status}', '{$post_category_id}', '{$post_tags}' ) ";
        $create_post_query = mysqli_query($connection, $query);
        checkQuery($create_post_query);

        $post_id = mysqli_insert_id($connection);

        $post_created_successfully = true;

        $create_post_success_msgs = [
            "post_created" => "Post created succesfully."
        ];
    }
}

// Retrieve categories for use in select box
$query = "SELECT * FROM categories ";
$categories_query = mysqli_query($connection, $query);
checkQuery($categories_query);
$categories = [];
$i = 0;
while ($row = mysqli_fetch_assoc($categories_query)) {
    $categories[$i]['cat_id']   = $row['cat_id'];
    $categories[$i]['cat_name'] = $row['cat_name'];
    $i++;
}

?>

<h1 class="page-title">Create Post</h1>

<form action="" method="post" enctype="multipart/form-data">
    <?php
        echo generateDismissableAlert($create_post_errors);

        if ($post_created_successfully) {
            echo generateDismissableAlert($edit_post_success_msgs, "success");
            $create_post_errors = [];
            $post_title       = "";
            $post_author_id   = "";
            $post_category_id = "";
            $post_status      = "";
            $post_tags        = "";
            $post_content     = "";
            $post_date        = "";
        }
    ?>

    <div class="form-group">
        <input type="text" name="post_title"
        class="form-control <?php if (!empty($create_post_errors['post_title'])) echo "is-invalid"; ?>"
        placeholder="Title"
        value="<?php if (isset($post_title)) echo $post_title; ?>">
    </div>

    <div class="form-group">
        <select name="post_category_id" id="post_category"
        class="form-control <?php if (!empty($create_post_errors['post_category_id'])) echo "is-invalid"; ?>">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $category['cat_id']; ?>"
                <?php if (isset($post_category_id) && $post_category_id === $category['cat_id']) echo "selected "; ?>>
                    <?php echo $category['cat_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <select name="post_status" class="form-control">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </div>

    <div class="form-group">
        <input type="text" name="post_tags"
        class="form-control <?php if (!empty($create_post_errors['post_tags'])) echo "is-invalid"; ?>"
        placeholder="Post Tags (separate with commas , )"
        value="<?php if (isset($post_tags)) echo $post_tags; ?>">
    </div>

    <div class="form-group">
        <textarea id="text_body" name="post_content"
        class="form-control <?php if (!empty($create_post_errors['post_content'])) echo "is-invalid"; ?>"
        cols="30" rows="10"
        placeholder="Post Content"><?php if (isset($post_content)) echo $post_content; ?></textarea>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="create_post" value="Publish">
    </div>
</form>