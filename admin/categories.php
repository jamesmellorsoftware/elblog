<?php
include "includes/head.php";

redirectWriter(); // Writer should not be able to edit categories

// Check if page number set
// If not set set to default 1
// Calculate offset and limit for pagination
$page = (isset($_GET['page'])) ? $page = trim(mysqli_real_escape_string($connection, $_GET['page'])) : $page = 1;
$results_per_page = (isset($_GET['filter__results_per_page'])) ? trim(mysqli_real_escape_string($connection, $_GET['filter__results_per_page'])) : 20;
$page_offset = ($page === 1) ? 0 : ($page * $results_per_page) - $results_per_page;

// Initialise filters
// Add filters if set
$search_filters = [];
if (isset($_POST['search'])) {
    $search_filters = [
        "category_id" => [
            "type" => "int", "key" => "categories.cat_id", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__category-id']))
        ],
        "category_name" => [
            "type" => "text", "key" => "categories.cat_name", "value" => trim(mysqli_real_escape_string($connection, $_GET['filter__category-name']))
        ]
    ];
}

// Initialise bulk options
$bulk_options = [
    ["value" => "delete", "name"  => "Delete"]
];

// Apply bulk option to every selected category
if (isset($_POST['checkboxArray'])) {
    foreach($_POST['checkboxArray'] as $cat_id) {

        $cat_id = trim(mysqli_real_escape_string($connection, $cat_id));

        $bulk_option = trim(mysqli_real_escape_string($connection, $_POST['bulk_option']));

        switch ($bulk_option) {
            case "delete":
                $query = "DELETE FROM categories WHERE cat_id = {$cat_id} ";
                break;
        }

        $bulk_option_query = mysqli_query($connection, $query);
        checkQuery($bulk_option_query);
    }
}

// Add category
if (isset($_POST['add_category'])) {
    // Catch and sanitise new category name
    $new_cat_name = trim(mysqli_escape_string($connection, $_POST['cat_name']));

    // Category empty?
    if ($new_cat_name == "" || empty($new_cat_name)) {
        $category_error = "Field cannot be empty.";
    } else {
        // Insert category
        $stmt = mysqli_prepare($connection, "INSERT INTO categories(cat_name) VALUES(?) ");
        mysqli_stmt_bind_param($stmt, "s", $new_cat_name);
        mysqli_stmt_execute($stmt);

        if (!$stmt) die("QUERY FAILED " . mysqli_error($connection));

        mysqli_stmt_close($stmt);
    }
}

// Edit category
if (isset($_POST['edit'])) {
    // Catch category properties
    $cat_id_edit = trim(mysqli_escape_string($connection, $_POST['cat_id_edit']));
    $cat_name = trim(mysqli_escape_string($connection, $_POST['cat_name']));
    
    // Update category
    $query = "UPDATE categories SET cat_name = '{$cat_name}' WHERE cat_id = {$cat_id_edit} ";
    $edit_query = mysqli_query($connection, $query);
    checkQuery($edit_query);
    header("Location: categories.php");
}

// Retrieve categories
$query = "SELECT * FROM categories ";
$query.= applySearchFilters($search_filters);
$query.= applyOrderBy();
$query.= "LIMIT {$page_offset}, {$results_per_page} "; // Set limit for pagination
$select_all_categories = mysqli_query($connection, $query);

// Catch categories in array
$i = 0;
$categories = [];
while ($row = mysqli_fetch_assoc($select_all_categories)) {
    $categories[$i]['cat_id']   = $row['cat_id'];
    $categories[$i]['cat_name'] = $row['cat_name'];
    $i++;
}

// Include search modal
include "includes/modal_search_categories.php";

?>

<div class="page-header-search">
    <h1 class="page-title">Categories</h1>
    <i aria-hidden="true" id="" data-toggle="modal" data-target="#searchModal"
    class="fa fa-search fa-2x text-right <?php if (isset($_GET['search'])) echo "text-info"; ?>"></i>
</div>

<div class="col-xs-12">
    <!-- Add new category section -->
    <form action="" method="post">
        <div class="input-group">
            <input id="cat_name" type="text" class="form-control" name="cat_name">
            <span class="input-group-btn">
                <input type="hidden" id="cat_id_edit" name="cat_id_edit" value="">
                <input id="addEditCategory" class="btn btn-primary" type="submit" name="add_category" value="Add">
            </span>
        </div>
    </form>
</div>
<br><br><!-- remove later!!! -->
<!-- display categories -->
<div class="col-xs-12">
    <form action="" method="post">
        <table class="table table-bordered table-hover">
            <?php generateAdminBulkOptions($bulk_options); ?>
            <thead>
                <tr class="table__headers">
                    <th class="clickable_th checkboxTableRow"><input type="checkbox" id="selectAllCheckboxes"></th>
                    <th rel="cat_id" class="<?php echo displayColOrderClass("cat_id"); ?>">
                        ID <?php echo adminTableHeaderArrow("cat_id"); ?>
                    </th>
                    <th rel="cat_name" class="<?php echo displayColOrderClass("cat_name"); ?>">
                        Category Title <?php echo adminTableHeaderArrow("cat_name"); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($categories as $category) {
                    echo "<tr>";
                    echo "<td><input class='selectCheckbox' type='checkbox' name='checkboxArray[]' value='{$category['cat_id']}'></td>";
                    echo "<td>{$category['cat_id']}</td>";
                    echo "<td id='category{$category['cat_id']}'>{$category['cat_name']}</td>";
                    echo "<td><a href='#' rel='{$category['cat_id']}' class='edit_category'>Edit</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <?php include "../includes/pagination.php"; ?>

    <br /> <!-- remove later! -->

    </form>
</div>

<script>
    $(document).ready(function(){
        // Editing a category
        $(".edit_category").on('click', function(){
            // Get category ID and name
            var cat_id = $(this).attr('rel');
            var cat_name = $("#category"+cat_id).html();

            // Change add category bar to edit category
            $("#cat_id_edit").attr("value", cat_id);
            $("#cat_name").attr("value", cat_name);
            $("#addEditCategory").attr("name", "edit");
            $("#addEditCategory").attr("value", "Edit");
        });
    });
</script>
                
<?php include "includes/footer.php"; ?>