<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot / Change Password</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background: #f0f2f5;
}
.login-container {
    max-width: 450px;
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.login-container h2 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
}
.input-group-text {
    width: 45px;
    justify-content: center;
}
p.error {
    color: #d9534f;
    font-size: 0.875rem;
    margin: 3px 0 10px 0;
}
.btn-submit {
    width: 100%;
    padding: 10px;
    font-weight: 600;
    border-radius: 8px;
}
#msg {
    text-align: center;
    margin-top: 15px;
}
</style>
</head>
<body>

<div class="login-container">
    <h2>Change Password</h2>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
        <p id="emailerror" class="error"></p>
    </div>

    <div class="mb-3">
        <label class="form-label">Old Password</label>
        <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Enter old password">
        <p id="oldpassworderror" class="error"></p>
    </div>

    <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="Enter new password">
        <p id="newpassworderror" class="error"></p>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm new password">
        <p id="cpassworderror" class="error"></p>
    </div>

    <button class="btn btn-primary btn-submit" id="login">Submit</button>
    <p id="msg" class="error"></p>
</div>

<script>
$(document).ready(function(){
    $("#login").click(function(e){
        e.preventDefault();

        // Xóa các thông báo cũ
        $(".error").text('');
        $("#msg").text('');

        var email = $("#email").val().trim();
        var oldpassword = $("#oldpassword").val().trim();
        var newpassword = $("#newpassword").val().trim();
        var cpassword = $("#cpassword").val().trim();
        var hasError = false;

        if(email == ''){
            $("#emailerror").text("Please enter your email.");
            hasError = true;
        }
        if(oldpassword == ''){
            $("#oldpassworderror").text("Please enter old password.");
            hasError = true;
        }
        if(newpassword == ''){
            $("#newpassworderror").text("Please enter new password.");
            hasError = true;
        }
        if(cpassword == ''){
            $("#cpassworderror").text("Please confirm new password.");
            hasError = true;
        }
        if(newpassword != '' && cpassword != '' && newpassword != cpassword){
            $("#cpassworderror").text("Passwords do not match.");
            hasError = true;
        }

        if(hasError) return false;

        // AJAX
        $.ajax({
            url: 'forget.php',
            type: 'POST',
            data: {
                email: email,
                oldpassword: oldpassword,
                newpassword: newpassword
            },
            success: function(response){
                response = response.trim();
                if(response == '1'){
                    // Redirect về trang login/index
                    window.location.href = "index.php";
                } else {
                    $("#msg").text("Invalid email or old password.");
                }
            },
            error: function(){
                $("#msg").text("Server error. Please try again.");
            }
        });
    });
});
</script>

</body>
</html>
