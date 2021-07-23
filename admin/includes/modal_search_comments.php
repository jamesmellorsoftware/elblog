<!-- Modal -->
<div id="searchModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="get" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__comment-id" placeholder="Comment ID"
                        value="<?php if (isset($_GET['filter__comment-id'])) echo $_GET['filter__comment-id']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__comment-author" placeholder="Comment Author"
                        value="<?php if (isset($_GET['filter__comment-author'])) echo $_GET['filter__comment-author']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__comment-post_id" placeholder="Post ID"
                        value="<?php if (isset($_GET['filter__comment-post_id'])) echo $_GET['filter__comment-post_id']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__comment-content" placeholder="Comment Content"
                        value="<?php if (isset($_GET['filter__comment-content'])) echo $_GET['filter__comment-content']; ?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="filter__comment-status">
                            <option value="">Select Comment Status</option>
                            <option value="approved"
                            <?php if (isset($_GET['filter__comment-status']) && $_GET['filter__comment-status'] === "approved") echo "selected"; ?>>
                                Approved
                            </option>
                            <option value="unapproved"
                            <?php if (isset($_GET['filter__comment-status']) && $_GET['filter__comment-status'] === "unapproved") echo "selected"; ?>>
                                Unapproved
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter__results_per_page">No. of Comments per Page</label>
                        <select class="form-control" name="filter__results_per_page">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="comments.php">Clear</a>
                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                </div>
            </form>
        </div>
    </div>
</div>