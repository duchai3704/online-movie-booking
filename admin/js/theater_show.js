
// === Validate form khi thêm show ===
function validateform() {
    const theater_name = document.myform.theater_name.value.trim();
    const show = document.myform.show.value.trim();
  
    if (theater_name === "") {
      alert("Vui lòng chọn tên rạp!");
      return false;
    } else if (show === "") {
      alert("Vui lòng nhập giờ chiếu!");
      return false;
    }
    return true;
  }
  
  // === Reset form khi đóng modal ===
  document.addEventListener("DOMContentLoaded", function () {
    const addShowModal = document.getElementById("add_show");
    if (addShowModal) {
      addShowModal.addEventListener("hidden.bs.modal", function () {
        // reset form khi đóng modal thêm show
        const form = addShowModal.querySelector("form");
        if (form) form.reset();
      });
    }
  
    // === Thêm hiệu ứng xác nhận trước khi xóa ===
    const deleteForms = document.querySelectorAll('form[action="insert_data.php"][id="insert_movie"] input[name="deletetime"]');
    deleteForms.forEach((btn) => {
      btn.addEventListener("click", function (e) {
        const confirmDelete = confirm("Bạn có chắc muốn xóa suất chiếu này không?");
        if (!confirmDelete) e.preventDefault();
      });
    });
  
    // === Kiểm tra cập nhật show ===
    const updateForms = document.querySelectorAll('form[action="insert_data.php"][id="insert_movie"] input[name="updatetime"]');
    updateForms.forEach((btn) => {
      btn.addEventListener("click", function (e) {
        const timeInput = btn.closest("form").querySelector("#edit_time");
        if (!timeInput.value) {
          alert("Vui lòng nhập giờ chiếu mới!");
          e.preventDefault();
        }
      });
    });
  });
  