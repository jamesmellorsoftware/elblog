<!-- Modal -->
<div id="searchModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="get" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__post-title" placeholder="Post Title"
                        value="<?php if (isset($_GET['filter__post-title'])) echo $_GET['filter__post-title']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__post-author" placeholder="Post Author"
                        value="<?php if (isset($_GET['filter__post-author'])) echo $_GET['filter__post-author']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__post-tags" placeholder="Post Tags"
                        value="<?php if (isset($_GET['filter__post-tags'])) echo $_GET['filter__post-tags']; ?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="filter__post-category">
                            <option value="">Select Category</option>
                            <?php
                                $query = "SELECT cat_id, cat_name FROM categories ";
                                $select_all_categories = mysqli_query($connection, $query);
                                while ($row = mysqli_fetch_assoc($select_all_categories)) { ?>
                                    <option value="<?php echo $row['cat_id']; ?>"
                                    <?php if (isset($_GET['filter__post-category']) && $_GET['filter__post-category'] === $row['cat_id']) echo "selected"; ?>>
                                        <?php echo $row['cat_name']; ?>
                                    </option>
                                <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter__post-date_from">From</label>
                        <input type="date" class="form-control"
                        name="filter__post-date_from" placeholder="Posted From"
                        value="<?php if (isset($_GET['filter__post-date_from'])) echo $_GET['filter__post-date_from']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="filter__post-date_to">To</label>
                        <input type="date" class="form-control"
                        name="filter__post-date_to" placeholder="To"
                        value="<?php if (isset($_GET['filter__post-date_to'])) echo $_GET['filter__post-date_to']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="filter__results_per_page">No. of Posts per Page</label>
                        <select class="form-control" name="filter__results_per_page">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter__post--order_by">Order by</label>
                        <select class="form-control" name="filter__post--order_by">
                            <option value="newest"
                            <?php if (isset($_GET['filter__post--order_by']) && $_GET['filter__post--order_by'] === "newest") echo "selected"; ?>>
                                Date, Newest First
                            </option>
                            <option value="oldest"
                            <?php if (isset($_GET['filter__post--order_by']) && $_GET['filter__post--order_by'] === "oldest") echo "selected"; ?>>
                                Date, Oldest First
                            </option>
                            <option value="likes"
                            <?php if (isset($_GET['filter__post--order_by']) && $_GET['filter__post--order_by'] === "likes") echo "selected"; ?>>
                                Likes
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="index.php">Clear</a>
                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                </div>
            </form>
        </div>
    </div>
</div>