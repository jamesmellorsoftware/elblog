<!-- Modal -->
<div id="searchModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="get" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__user-id" placeholder="User ID"
                        value="<?php if (isset($_GET['filter__user-id'])) echo $_GET['filter__user-id']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__user-username" placeholder="Username"
                        value="<?php if (isset($_GET['filter__user-username'])) echo $_GET['filter__user-username']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__user-firstname" placeholder="First Name"
                        value="<?php if (isset($_GET['filter__user-firstname'])) echo $_GET['filter__user-firstname']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__user-lastname" placeholder="Last Name"
                        value="<?php if (isset($_GET['filter__user-lastname'])) echo $_GET['filter__user-lastname']; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control"
                        name="filter__user-email" placeholder="Email"
                        value="<?php if (isset($_GET['filter__user-email'])) echo $_GET['filter__user-email']; ?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="filter__user-role">
                            <option value="">Select Role</option>
                            <option value="admin"
                            <?php if (isset($_GET['filter__user-role']) && $_GET['filter__user-role'] === "admin") echo "selected"; ?>>
                                Admin
                            </option>
                            <option value="writer"
                            <?php if (isset($_GET['filter__user-role']) && $_GET['filter__user-role'] === "writer") echo "selected"; ?>>
                                Writer
                            </option>
                            <option value="subscriber"
                            <?php if (isset($_GET['filter__user-role']) && $_GET['filter__user-role'] === "subscriber") echo "selected"; ?>>
                                Subscriber
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter__results_per_page">No. of Users per Page</label>
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
                    <a class="btn btn-danger" href="users.php">Clear</a>
                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                </div>
            </form>
        </div>
    </div>
</div>