<?php include "partials/header.php";?>

<div class="login-body mt-5" dir="rtl">


    <div class="login-container">
 
        <h2 class="login-h2">تسجيل الدخول <i class="bi bi-box-arrow-in-right"></i></h2>

        <form action="app/login.php" method="post">
            <div class="mb-3">
                <label for="username" class="login-form-label">اسم المستخدم</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="login-form-label">كلمة المرور</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">تسجيل الدخول <i class="bi bi-box-arrow-in-right"></i></button>
        </form>
    </div>


</div>

<?php include "partials/footer.php";?>
