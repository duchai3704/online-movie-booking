<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Theater and Show Page</title>
    <?php
    // nếu templates/top.php đã include CSS/JS (bootstrap, jquery) thì ko cần lặp lại.
    include_once("./templates/top.php");
    ?>
    <style>
      /* nhỏ gọn */
      .modal .form-group label { font-weight:600; }
    </style>
  </head>
<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("location:login.php");
  exit;
}
?>
<body>
<?php include_once("./templates/navbar.php"); ?>

<div class="container-fluid">
  <div class="row">
    <?php include "./templates/sidebar.php"; ?>
    <main class="col ml-sm-auto px-4">
      <div class="row mt-3 mb-2">
        <div class="col-10">
          <h2>Theater & Show</h2>
        </div>
        <div class="col-2 text-right">
          <button data-toggle="modal" data-target="#add_show" class="btn btn-primary btn-sm">Add Show</button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm" id="shows_table">
          <thead>
            <tr>
              <th>id</th>
              <th>Show</th>
              <th>Theater</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="product_list">
           <?php
           include_once 'Database.php';
           $result = mysqli_query($conn,"SELECT * FROM theater_show ORDER BY id ASC");
           if ($result && mysqli_num_rows($result) > 0) {
             while($row = mysqli_fetch_assoc($result)) {
               $id = (int)$row['id'];
               $show_time = htmlspecialchars($row['show']);
               $theater = htmlspecialchars($row['theater']);
           ?>
            <tr id="row_<?php echo $id;?>">
              <td><?php echo $id;?></td>
              <td><?php echo $show_time;?></td>
              <td><?php echo $theater;?></td>
              <td>
                <button data-toggle="modal" data-target="#update_show<?php echo $id;?>" class="btn btn-primary btn-sm">Edit</button>
                <button data-toggle="modal" data-target="#delete_show<?php echo $id;?>" class="btn btn-danger btn-sm">Delete</button>
              </td>
            </tr>

            <!-- EDIT Modal -->
            <div class="modal fade" id="update_show<?php echo $id;?>" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <form id="form_update_show_<?php echo $id;?>" class="form_update_show" action="insert_data.php" method="post">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Show #<?php echo $id;?></h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="e_id" value="<?php echo $id;?>">
                      <div class="form-group">
                        <label>Screen (theater)</label>
                        <select class="form-control" name="edit_screen" required>
                          <option value="<?php echo $theater;?>"><?php echo $theater;?></option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <!-- nếu em có bảng theater, thay bằng load từ DB -->
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Show (time)</label>
                        <input type="time" class="form-control" name="edit_time" value="<?php echo $show_time;?>" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <input type="submit" name="updatetime" class="btn btn-primary" value="Update">
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- DELETE Modal -->
            <div class="modal fade" id="delete_show<?php echo $id;?>" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <form id="form_delete_show_<?php echo $id;?>" class="form_delete_show" action="insert_data.php" method="post">
                    <div class="modal-header">
                      <h5 class="modal-title">Delete Show #<?php echo $id;?></h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to delete show <strong><?php echo $show_time;?></strong> (theater <?php echo $theater;?>)?</p>
                      <input type="hidden" name="id" value="<?php echo $id;?>">
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <input type="submit" name="deletetime" class="btn btn-danger" value="Delete">
                    </div>
                  </form>
                </div>
              </div>
            </div>

           <?php
             } // while
           } else {
             echo '<tr><td colspan="4">No shows found</td></tr>';
           }
           ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- ADD Modal -->
<div class="modal fade" id="add_show" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form_add_show" action="insert_data.php" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Add Show</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Theater name</label>
            <select class="form-control" name="theater_name" id="theater_name" required>
              <option value="">Select theater</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <!-- thay bằng load từ bảng theater nếu có -->
            </select>
          </div>
          <div class="form-group">
            <label>Show (time)</label>
            <input type="time" name="show" id="show" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="submit" name="addshow" class="btn btn-primary" value="Add Show">
        </div>
      </form>
    </div>
  </div>
</div>
<script src="js/theater_show.js"></script>
<?php include_once("./templates/footer.php"); ?>


</body>
</html>
