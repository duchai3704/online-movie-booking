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
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Movies Page</title>
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
        <h2>Add movie</h2>
        <button data-toggle="modal" data-target="#add_movie_modal" class="btn btn-primary btn-sm">Add Movie</button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>id</th>
              <th>Movie name</th>
              <th>Directer</th>
              <th>Category</th>
              <th>Language</th>
              <th>Show</th>
              <th>Image</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $result = mysqli_query($conn,"SELECT * FROM add_movie");
            while($row = mysqli_fetch_assoc($result)){
                $id = $row['id'];
                $shows = explode(',', $row['show']);
            ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= $row['movie_name']; ?></td>
              <td><?= $row['directer']; ?></td>
              <td><?= $row['categroy']; ?></td>
              <td><?= $row['language']; ?></td>
              <td><?= $row['show']; ?></td>
              <td><img src="image/<?= $row['image']; ?>" class="resize"></td>
              <td>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_movie_modal_<?= $id; ?>">Edit</button>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete_movie_modal_<?= $id; ?>">Delete</button>
              </td>
            </tr>

            <!-- Edit Movie Modal -->
            <div class="modal fade" id="edit_movie_modal_<?= $id; ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Movie</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <form id="edit_movie_form_<?= $id; ?>" method="post" action="insert_data.php" enctype="multipart/form-data">
                        <input type="hidden" name="updatemovie" value="1">
                        <input type="hidden" name="e_id" value="<?= $id; ?>">

                        <div class="form-group">
                            <label>Movie Name</label>
                            <input class="form-control" name="edit_movie_name" value="<?= $row['movie_name']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Directer Name</label>
                            <input class="form-control" name="edit_directer_name" value="<?= $row['directer']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <input class="form-control" name="edit_category" value="<?= $row['categroy']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Language</label>
                            <input class="form-control" name="edit_language" value="<?= $row['language']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Show</label><br>
                            <?php
                            $allShows = mysqli_query($conn,"SELECT * FROM theater_show");
                            while($s = mysqli_fetch_assoc($allShows)){
                                $checked = in_array($s['show'], $shows) ? "checked" : "";
                                echo "<label><input type='checkbox' name='show[]' value='{$s['show']}' $checked> {$s['show']}</label> ";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label>Trailer</label>
                            <input class="form-control" name="edit_tailer" value="<?= $row['you_tube_link']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Action</label>
                            <select class="form-control" name="edit_action">
                                <option value="<?= $row['action']; ?>"><?= $row['action']; ?></option>
                                <option value="upcoming">upcoming</option>
                                <option value="running">running</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="decription"><?= $row['decription']; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Image</label><br>
                            <img src="image/<?= $row['image']; ?>" width="50">
                            <input type="file" name="edit_img">
                            <input type="hidden" name="old_image" value="<?= $row['image']; ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delete Movie Modal -->
            <div class="modal fade" id="delete_movie_modal_<?= $id; ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Delete Movie</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <form class="delete_movie_form">
                      <input type="hidden" name="deletemovie" value="1">
                      <input type="hidden" name="id" value="<?= $id; ?>">
                      <p>Are you sure to delete movie ID <?= $id; ?>?</p>
                      <button type="submit" class="btn btn-danger">Delete</button>
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

<!-- Add Movie Modal -->
<div class="modal fade" id="add_movie_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Movie</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="add_movie_form" method="post" action="insert_data.php" enctype="multipart/form-data">
          <input type="hidden" name="submit" value="1">

          <div class="form-group">
            <label>Movie Name</label>
            <input class="form-control" name="movie_name" required>
          </div>

          <div class="form-group">
            <label>Directer Name</label>
            <input class="form-control" name="directer_name" required>
          </div>

          <div class="form-group">
            <label>Release Date</label>
            <input class="form-control" type="date" name="release_date" required>
          </div>

          <div class="form-group">
            <label>Category</label>
            <input class="form-control" name="category" required>
          </div>

          <div class="form-group">
            <label>Language</label>
            <input class="form-control" name="language" required>
          </div>

          <div class="form-group">
            <label>Show</label><br>
            <?php
            $allShows = mysqli_query($conn,"SELECT * FROM theater_show");
            while($s = mysqli_fetch_assoc($allShows)){
                echo "<label><input type='checkbox' name='show[]' value='{$s['show']}'> {$s['show']}</label> ";
            }
            ?>
          </div>

          <div class="form-group">
            <label>Trailer</label>
            <input class="form-control" name="tailer">
          </div>

          <div class="form-group">
            <label>Action</label>
            <select class="form-control" name="action" required>
              <option value="">Select</option>
              <option value="upcoming">upcoming</option>
              <option value="running">running</option>
            </select>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="decription" required></textarea>
          </div>

          <div class="form-group">
            <label>Image</label>
            <input type="file" name="img" required>
          </div>

          <button type="submit" class="btn btn-primary">Add Movie</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="admin/js/movie.js"></script>

</body>
</html>
<?php include_once("./templates/footer.php"); ?>
