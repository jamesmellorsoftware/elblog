<?php
// Check if post selected
// If selected retrieve post data
// Redirect to main posts page if not
if (isset($_GET['post_id'])) {
    $post_id = trim(mysqli_real_escape_string($connection, $_GET['post_id']));

    $query = "SELECT * FROM posts WHERE post_id = {$post_id} ";
    $select_post = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($select_post);

    // Catch user data
    $post_author_id     = $row['post_author_id'];
    $post_title         = $row['post_title'];
    $post_category_id   = $row['post_category_id'];
    $post_status        = $row['post_status'];
    $post_tags          = $row['post_tags'];
    $post_date          = $row['post_date'];
    $post_content       = $row['post_content'];
} else {
    header("Location: posts.php");
}

// Initialise errors
$edit_post_errors = [
    "post_title"       => "",
    "post_category_id" => "",
    "post_tags"        => "",
    "post_content"     => ""
];

$post_edited_successfully = false;

// If post edited, catch and validate new properties and update posts table with them
if (isset($_POST['update_post'])) {
    // Catch and sanitise user inputs
    $post_title       = trim(mysqli_real_escape_string($connection, $_POST['post_title']));
    $post_category_id = trim(mysqli_real_escape_string($connection, $_POST['post_category_id']));
    $post_status      = trim(mysqli_real_escape_string($connection, $_POST['post_status']));
    $post_tags        = trim(mysqli_real_escape_string($connection, $_POST['post_tags']));
    $post_content     = trim(mysqli_real_escape_string($connection, $_POST['post_content']));
    $post_date        = date('d-m-y');

    // Fields empty?
    if (empty($post_title))       $edit_post_errors['post_title']       = "Please give your post a title.";
    if (empty($post_category_id)) $edit_post_errors['post_category_id'] = "Please select a category.";
    if (empty($post_tags))        $edit_post_errors['post_tags']        = "Please give your post some tags.";
    if (empty($post_content))     $edit_post_errors['post_content']     = "Please write some content.";

    // Errors exist in form?
    $edit_post_errors_exist = false;
    foreach ($edit_post_errors as $edit_post_error) {
        if (!empty($edit_post_error)) $edit_post_errors_exist = true;
    }

    // If no errors exist, update post
    if (!$edit_post_errors_exist) {
        $query = "UPDATE posts SET ";
        $query.= "post_title = '{$post_title}', ";
        $query.= "post_category_id = {$post_category_id}, ";
        $query.= "post_date = now(), ";
        $query.= "post_author_id = '{$post_author_id}', ";
        $query.= "post_status = '{$post_status}', ";
        $query.= "post_tags = '{$post_tags}', ";
        $query.= "post_content = '{$post_content}' ";
        $query.= "WHERE post_id = {$post_id} ";

        $update_post_query = mysqli_query($connection, $query);

        checkQuery($update_post_query);

        $post_edited_successfully = true;

        $edit_post_success_msgs = [
            "post_edited" => "Post edited succesfully."
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

if ($post_edited_successfully) {
    echo generateDismissableAlert($edit_post_success_msgs, "success");
} else {
    
?>

<form action="" method="post" enctype="multipart/form-data">
    <?php echo generateDismissableAlert($edit_post_errors); ?>
    <div class="form-group">
        <input type="text" name="post_title"
        class="form-control <?php if (!empty($edit_post_errors['post_title'])) echo "is-invalid"; ?>"
        placeholder="Title"
        value="<?php if (isset($post_title)) echo $post_title; ?>">
    </div>

    <div class="form-group">
        <select name="post_category_id" id="post_category"
        class="form-control <?php if (!empty($edit_post_errors['post_category_id'])) echo "is-invalid"; ?>">
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
            <option value="draft"
            <?php if (isset($post_status) && $post_status === "draft") echo "selected" ?>>Draft</option>
            <option value="published"
            <?php if (isset($post_status) && $post_status === "published") echo "selected" ?>>Published</option>
        </select>
    </div>

    <div class="form-group">
        <input type="text" name="post_tags"
        class="form-control <?php if (!empty($edit_post_errors['post_tags'])) echo "is-invalid"; ?>"
        placeholder="Post Tags (separate with commas , )"
        value="<?php if (isset($post_tags)) echo $post_tags; ?>">
    </div>

    <div class="form-group">
        <textarea id="text_body" name="post_content"
        class="form-control <?php if (!empty($edit_post_errors['post_content'])) echo "is-invalid"; ?>"
        cols="30" rows="10"
        placeholder="Post Content"><?php if (isset($post_content)) echo $post_content; ?></textarea>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="update_post" value="Update">
    </div>
</form>
<?php } ?>