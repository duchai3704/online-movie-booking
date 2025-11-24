<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    
<title>Dashboard Page</title>

<link rel="stylesheet" href="css/all.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/dashboard.css" type="text/css">
<?php 
session_start();  
if (!isset($_SESSION['admin'])) {
  header("location:login.php");
}

include "../admin/templates/top.php"; 
include "../admin/templates/navbar.php"; 
include_once 'Database.php';
?>

<div class="container-fluid">
  <div class="row">
    <?php include "../admin/templates/sidebar.php"; ?>

    <div class="col-10">
      <h2>Total Admins</h2>
      <div class="mb-2 text-right">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_admin_modal">Add Admin</button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm" id="adminTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $result = mysqli_query($conn,"SELECT * FROM admin");
          if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)) {
              $id = $row['id'];
              echo "<tr id='row_$id'>";
              echo "<td>{$row['id']}</td>";
              echo "<td>{$row['name']}</td>";
              echo "<td>{$row['email']}</td>";
              echo "<td>".($row['is_active'] ? 'Active' : 'Inactive')."</td>";
              echo "<td>
                      <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#edit_admin_modal$id'>Edit</button>
                      <button class='btn btn-danger btn-sm deleteAdminBtn' data-id='$id'>Delete</button>
                    </td>";
              echo "</tr>";
            }
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="add_admin_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Admin</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="addAdminForm">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="text" name="password" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Add Admin</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
// Edit Admin Modals
$result = mysqli_query($conn,"SELECT * FROM admin");
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    $id = $row['id'];
?>
<div class="modal fade" id="edit_admin_modal<?php echo $id; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Admin</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form class="editAdminForm" data-id="<?php echo $id; ?>">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="edit_name" class="form-control" value="<?php echo $row['name']; ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="edit_email" class="form-control" value="<?php echo $row['email']; ?>" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="text" name="edit_password" class="form-control" value="<?php echo $row['password']; ?>" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="edit_is_active" class="form-control">
              <option value="1" <?php if($row['is_active']==1) echo "selected"; ?>>Active</option>
              <option value="0" <?php if($row['is_active']==0) echo "selected"; ?>>Inactive</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Update Admin</button>
        </form>
      </div>
    </div>
  </div>
</div>


<?php
  }
}
?>

<?php include "../admin/templates/footer.php"; ?>
<script>
 $(document).ready(function(){

// Thêm Admin
$("#addAdminForm").submit(function(e){
    e.preventDefault();
    var form = $(this);
    $.post("insert_data.php", form.serialize(), function(data){
        data = data.trim();
        if(data === "success"){
            // Quay về trang index
            window.location.href = "index.php";
        } else {
            console.log("Error: " + data); // chỉ log ra console
        }
    });
});

 

  
	// === Sửa Admin (dùng event delegation) ===
	$(document).on("submit", ".editAdminForm", function(e){
	  e.preventDefault();
	  $.post("insert_data.php", $(this).serialize(), function(data){
		data = data.trim();
		if(data == "updated"){
		  
		  location.reload();
		} else {
		  alert("Error: " + data);
		}
	  });
	});
  
	// === Xoá Admin (dùng event delegation) ===
	$(document).on("click", ".deleteAdminBtn", function(){
	  if(confirm("Are you sure you want to delete this admin?")){
		var id = $(this).data("id");
		$.post("insert_data.php", {delete_admin:1, id:id}, function(data){
		  data = data.trim();
		  if(data == "deleted"){
			$("#row_"+id).remove();
		 
		  } else {
			alert("Error: " + data);
		  }
		});
	  }
	});
  
  });
  
</script>