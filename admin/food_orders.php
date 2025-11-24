<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("location:login.php");
    exit;
}
include_once 'Database.php';
include_once("./templates/top.php");
include_once("./templates/navbar.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Combo Page</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .resize { width: 50px; height: auto; }
</style>
</head>

<body>
<div class="container-fluid">
  <div class="row">

    <?php include "./templates/sidebar.php"; ?>

    <div class="col-10">

      <div class="d-flex justify-content-between align-items-center my-3">
        <h2>Manage Combos</h2>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_combo_modal">Add Combo</button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Combo Name</th>
              <th>Price</th>
              <th>Description</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
          <?php
          $query = mysqli_query($conn, "SELECT * FROM combos ORDER BY id DESC");
          while ($row = mysqli_fetch_assoc($query)) {
              $id = $row['id'];
          ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td>
                <?php if ($row['image'] != "") { ?>
                    <img src="image/<?= $row['image']; ?>" class="resize">
                <?php } ?>
              </td>
              <td><?= $row['combo_name']; ?></td>
              <td><?= number_format($row['price']); ?> đ</td>
              <td><?= $row['description']; ?></td>
              <td><?= $row['status'] ? "Active" : "Hidden"; ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit_combo_<?= $id; ?>">Edit</button>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete_combo_<?= $id; ?>">Delete</button>
              </td>
            </tr>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="edit_combo_<?= $id; ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Combo</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <div class="modal-body">
                    <form method="POST" action="insert_data.php" enctype="multipart/form-data">

                        <input type="hidden" name="update_combo" value="1">
                        <input type="hidden" name="combo_id" value="<?= $id ?>">
                        <input type="hidden" name="old_combo_image" value="<?= $row['image']; ?>">

                        <div class="form-group">
                            <label>Combo Name</label>
                            <input name="edit_combo_name" class="form-control" value="<?= $row['combo_name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input name="edit_combo_price" class="form-control" type="number" value="<?= $row['price']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="edit_combo_description" class="form-control"><?= $row['description']; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="edit_combo_is_active" class="form-control">
                                <option value="1" <?= $row['status']==1?"selected":"" ?>>Active</option>
                                <option value="0" <?= $row['status']==0?"selected":"" ?>>Hidden</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Image</label><br>
                            <?php if($row['image']!=""){ ?>
                                <img src="image/<?= $row['image']; ?>" width="60"><br>
                            <?php } ?>
                            <input type="file" name="edit_combo_img">
                        </div>

                        <button class="btn btn-primary">Update Combo</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

            <!-- DELETE MODAL -->
            <div class="modal fade" id="delete_combo_<?= $id; ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  
                  <div class="modal-header">
                    <h5 class="modal-title">Delete Combo</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <div class="modal-body">
                    <form method="POST" action="insert_data.php">
                        <input type="hidden" name="delete_combo" value="1">
                        <input type="hidden" name="id" value="<?= $id; ?>">

                        <p>Bạn chắc chắn muốn xóa combo <b><?= $row['combo_name']; ?></b>?</p>

                        <button class="btn btn-danger">Delete</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ADD COMBO MODAL -->
<div class="modal fade" id="add_combo_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add Combo</h5>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <form method="POST" action="insert_data.php" enctype="multipart/form-data">

            <input type="hidden" name="add_combo" value="1">

            <div class="form-group">
                <label>Combo Name</label>
                <input name="combo_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Price</label>
                <input name="combo_price" class="form-control" type="number" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="combo_description" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="combo_is_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Hidden</option>
                </select>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input name="combo_img" type="file" class="form-control">
            </div>

            <button class="btn btn-primary">Add Combo</button>

        </form>
      </div>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include_once("./templates/footer.php"); ?>
