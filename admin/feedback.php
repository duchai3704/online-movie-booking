<?php 
session_start();
if (!isset($_SESSION['admin'])) {
  header("location:login.php");
  exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Feedback Page</title>

<?php include_once("./templates/top.php"); ?>
<?php include_once("./templates/navbar.php"); ?>

<div class="container-fluid">
  <div class="row">

    <?php include "./templates/sidebar.php"; ?>

      <div class="row mb-3">
        <div class="col-10">
          <h2>Feedback Management</h2>
        </div>
         
      </div>

      <!-- THÔNG BÁO -->
      <?php
      if (isset($_GET['success']) && $_GET['success'] == 'refund_completed') {
          $user = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : '';
          $amount = isset($_GET['amount']) ? number_format($_GET['amount'], 0, ',', '.') : '0';
          echo "<div class='alert alert-success alert-dismissible fade show'>
                  <strong>Thành công!</strong> Đã hoàn tiền cho khách hàng <b>$user</b> - Số tiền: <b>$amount VNĐ</b>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                </div>";
      }
      
      if (isset($_GET['error'])) {
          $error_msg = '';
          switch($_GET['error']) {
              case 'invalid_data':
                  $error_msg = 'Dữ liệu không hợp lệ!';
                  break;
              case 'user_not_found':
                  $id = isset($_GET['id']) ? $_GET['id'] : '';
                  $error_msg = "Không tìm thấy khách hàng ID: $id";
                  break;
              case 'update_balance_failed':
                  $error_msg = 'Lỗi cập nhật số dư!';
                  break;
              case 'insert_refund_failed':
                  $error_msg = 'Lỗi lưu log hoàn tiền!';
                  break;
              case 'update_feedback_failed':
                  $error_msg = 'Lỗi cập nhật trạng thái feedback!';
                  break;
              default:
                  $error_msg = 'Có lỗi xảy ra!';
          }
          echo "<div class='alert alert-danger alert-dismissible fade show'>
                  <strong>Lỗi!</strong> $error_msg
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                </div>";
      }
      ?>


      <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
          <thead class="thead-dark">
            <tr class="text-center">
              <th width="5%">ID</th>
              <th width="12%">Name</th>
              <th width="15%">Email</th>
              <th width="35%">Message</th>
              <th width="8%">Refund</th>
              <th width="7%">Edit</th>
              <th width="7%">Delete</th>
              <th width="11%">Refund Action</th>
            </tr>
          </thead>

          <tbody id="product_list">
          <?php
          include_once 'Database.php';
          $result = mysqli_query($conn,"SELECT * FROM feedback ORDER BY id DESC");

          if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)) {
              $id = $row['id'];
          ?>

          <tr class="text-center">
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td class="text-left"><?= nl2br(htmlspecialchars($row['massage'])); ?></td>

            <td>
              <?php 
              if ($row['refund_status'] == 1) {
                echo "<span class='badge badge-success'>Đã hoàn</span>";
              } else {
                echo "<span class='badge badge-secondary'>Chưa hoàn</span>";
              }
              ?>
            </td>

            <td>
              <button data-toggle="modal" data-target="#edit_feedback_modal<?= $id; ?>" 
                class="btn btn-primary btn-sm">Edit</button>
            </td>

            <td>
              <button data-toggle="modal" data-target="#delete_feedback_modal<?= $id; ?>" 
                class="btn btn-danger btn-sm">Delete</button>
            </td>

            <td>
              <?php if ($row['refund_status'] == 0) { ?>
              <button data-toggle="modal" data-target="#refund_feedback_modal<?= $id; ?>" 
                class="btn btn-warning btn-sm">Refund</button>
              <?php } else { ?>
              <button class="btn btn-secondary btn-sm" disabled>Đã hoàn</button>
              <?php } ?>
            </td>
          </tr>

          <!-- DELETE -->
          <div class="modal fade" id="delete_feedback_modal<?= $id; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Delete Feedback</h5>
                  <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                  <form action="insert_data.php" method="post">
                    <p>Bạn có chắc muốn xóa feedback ID <b><?= $id; ?></b>?</p>

                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <button type="submit" name="deletefeedback" class="btn btn-danger">Delete</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <!-- EDIT  -->
          <div class="modal fade" id="edit_feedback_modal<?= $id; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Edit Feedback</h5>
                  <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                  <form action="insert_data.php" method="post">
                    <input type="hidden" name="e_id" value="<?= htmlspecialchars($id); ?>">

                    <div class="form-group">
                      <label>Name</label>
                      <input type="text" class="form-control" name="edit_feedback_name" 
                             value="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="form-group">
                      <label>Email</label>
                      <input type="email" class="form-control" name="edit_feedback_email" 
                             value="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="form-group">
                      <label>Message</label>
                      <textarea class="form-control" name="edit_feedback_massage" rows="4" required><?= htmlspecialchars($row['massage'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <button type="submit" name="updatefeedback" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <!-- REFUND -->
          <div class="modal fade" id="refund_feedback_modal<?= $id; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Duyệt Hoàn Tiền</h5>
                  <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                  <form action="insert_data.php" method="post">
                    <input type="hidden" name="refund_feedback_id" value="<?= htmlspecialchars($id); ?>">

                    <div class="form-group">
                      <label>ID Khách hàng</label>
                      <input type="number" name="refund_user_id" class="form-control" min="1" placeholder="Nhập ID khách hàng" required>
                      <small class="form-text text-muted">Nhập ID của khách hàng cần hoàn tiền</small>
                    </div>

                    <div class="form-group">
                      <label>Số tiền hoàn</label>
                      <input type="number" name="refund_amount" class="form-control" min="0" step="0.01" placeholder="0" required>
                    </div>

                    <div class="form-group">
                      <label>Lý do hoàn tiền</label>
                      <textarea name="refund_reason" class="form-control" rows="3" placeholder="Nhập lý do hoàn tiền..." required></textarea>
                    </div>

                    <button type="submit" name="approve_refund" class="btn btn-success">Hoàn tiền</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <?php } } ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<?php include_once("./templates/footer.php"); ?>


<script>  
function validateform(){  
  var name = document.myform.name.value;  
  var email = document.myform.email.value;  
  var massage = document.myform.massage.value;  

  if (name == ""){  
    alert("Require Name");  
    return false;  
  } else if(email == ""){  
    alert("Require Email");  
    return false;  
  } else if(massage == ""){  
    alert("Require Message");  
    return false;  
  }
  return true;
}
</script>