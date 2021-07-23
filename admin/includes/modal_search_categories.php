<!-- Modal -->
<div id="searchModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="get" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__category-id" placeholder="Category ID"
                        value="<?php if (isset($_GET['filter__category-id'])) echo $_GET['filter__category-id']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__category-name" placeholder="Category Name"
                        value="<?php if (isset($_GET['filter__category-name'])) echo $_GET['filter__category-name']; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="categories.php">Clear</a>
                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                </div>
            </form>
        </div>
    </div>
</div>