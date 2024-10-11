<?php
session_start();
require "./app/db_connection.php";
// Allow multiple roles: 'egra2at', 'tasle7', and 'C'
$allowed_roles = ['egra2at', 'tasle7', 'C'];
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: index.php');
    exit();
}

// صفحة إضافة اسم جهاز جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = $_POST['department_name'];

    $query = "INSERT INTO department_list (department_name) VALUES ('$department_name')";
    if ($conn->query($query) === TRUE) {
        $message = "تم إضافة القسم بنجاح!";
    } else {
        $message = "خطأ: " . $conn->error;
    }
}
include "partials/header.php";
include "partials/navBar.php";
?>
<div class="container mt-5" dir="rtl">

    <?php if (isset($message)): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="add_department.php" method="POST" class="mb-3">
        <div>
            <label for="department_name" class="form-label">اسم القسم الجديد</label>
            <input type="text" class="form-control" id="department_name" name="department_name" required>
        </div>
        <div class="p-4">
            <button type="submit" class="btn btn-primary w-100 ">إضافة قسم</button>
        </div>
    </form>
</div>
<?php include "partials/footer.php"; ?>