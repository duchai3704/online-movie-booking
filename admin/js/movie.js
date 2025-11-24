$(document).ready(function () {

    // ======== XEM TRƯỚC ẢNH UPLOAD ===========
    $("#img, #edit_img").on("change", function () {
      let file = this.files[0];
      if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
          $("#preview").html(
            `<img src="${e.target.result}" class="img-thumbnail mt-2" width="120">`
          );
        };
        reader.readAsDataURL(file);
      }
    });
  
    // ======== THÊM PHIM (AJAX) ===========
    $("#insert_movie").on("submit", function (e) {
      e.preventDefault();
      let formData = new FormData(this);
  
      // validation cơ bản
      if ($("#movie_name").val().trim() === "") {
        alert("Vui lòng nhập tên phim!");
        return false;
      }
      if ($("#directer_name").val().trim() === "") {
        alert("Vui lòng nhập tên đạo diễn!");
        return false;
      }
  
      $.ajax({
        url: "insert_data.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.includes("success")) {
            alert("✅ Thêm phim thành công!");
            $("#add_movie_modal").modal("hide");
            location.reload();
          } else {
            alert("❌ Thêm phim thất bại: " + response);
          }
        },
        error: function (xhr) {
          alert("Lỗi kết nối server: " + xhr.statusText);
        },
      });
    });
  
    // ======== CẬP NHẬT PHIM (EDIT) ===========
    $(document).on("submit", "form[id^='insert_movie'][action='insert_data.php']", function (e) {
      e.preventDefault();
      let formData = new FormData(this);
  
      $.ajax({
        url: "insert_data.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.includes("updated") || response.includes("success")) {
            alert("✅ Cập nhật phim thành công!");
            $(".modal").modal("hide");
            location.reload();
          } else {
            alert("❌ Lỗi khi cập nhật: " + response);
          }
        },
      });
    });
  
    // ======== XÓA PHIM (DELETE) ===========
    $(document).on("submit", "form#deletemovie", function (e) {
      e.preventDefault();
      if (!confirm("Bạn có chắc muốn xóa phim này không?")) return false;
  
      $.ajax({
        url: "insert_data.php",
        type: "POST",
        data: $(this).serialize(),
        success: function (response) {
          if (response.includes("deleted") || response.includes("success")) {
            alert("🗑️ Xóa phim thành công!");
            $(".modal").modal("hide");
            location.reload();
          } else {
            alert("❌ Lỗi khi xóa: " + response);
          }
        },
      });
    });
  
    // ======== VALIDATION TRỰC TIẾP (KHI GÕ) ===========
    $("#movie_name, #directer_name").on("blur", function () {
      if ($(this).val().trim() === "") {
        $(this).css("border-color", "red");
      } else {
        $(this).css("border-color", "#ccc");
      }
    });
  
  });
  