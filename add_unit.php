<?php
session_start();

// Allow multiple roles: 'A', 'B', and 'C'
$allowed_roles = ['A', 'B', 'C'];
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add_division') {
        $division_name = $_POST['division_name'];
        $query = "INSERT INTO divisions (division_name) VALUES ('$division_name')";
        if ($conn->query($query)) {
            $message = "تم إضافة الفرقة بنجاح";
        } else {
            $message = "حدث خطأ أثناء إضافة الفرقة";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'add_regiment') {
        $regiment_name = $_POST['regiment_name'];
        $division_id = $_POST['division_id'];

        $query = "INSERT INTO regiments (regiment_name, division_id) VALUES ('$regiment_name', '$division_id')";
        if ($conn->query($query)) {
            $message = "تم إضافة اللواء بنجاح";
        } else {
            $message = "حدث خطأ أثناء إضافة اللواء";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'add_unit') {
        $unit_name = $_POST['unit_name'];
        $regiment_id = $_POST['regiment_id'];

        $query = "INSERT INTO units (unit_name, regiment_id) VALUES ('$unit_name', '$regiment_id')";
        if ($conn->query($query)) {
            $message = "تم إضافة الكتيبة بنجاح";
        } else {
            $message = "حدث خطأ أثناء إضافة الكتيبة";
        }
    }
}

include "partials/header.php";
include "partials/navBar.php";
?>

<div class="container mt-5" dir="rtl">
    <h2 class="text-center mb-4">إضافة (تعديل) فرقة أو لواء أو كتيبة <i class="bi bi-plus-square"></i></h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <h4>إضافة فرقة</h4>
            <form action="add_unit.php" method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="division_name" class="form-label">اسم الفرقة</label>
                    <input type="text" class="form-control" id="division_name" name="division_name" required>
                </div>
                <input type="hidden" name="action" value="add_division">
                <button type="submit" class="btn btn-primary">إضافة فرقة</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>إضافة لواء</h4>
            <form action="add_unit.php" method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="regiment_name" class="form-label">اسم اللواء</label>
                    <input type="text" class="form-control" id="regiment_name" name="regiment_name" required>
                </div>
                <div class="mb-3">
                    <label for="division_id" class="form-label">اختر فرقة</label>
                    <select class="form-select" id="division_id" name="division_id" required>
                        <option selected></option>
                        <?php
                        $result = $conn->query("SELECT * FROM divisions");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['division_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="add_regiment">
                <button type="submit" class="btn btn-primary">إضافة لواء</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>إضافة كتيبة</h4>
            <form action="add_unit.php" method="POST">
                <div class="mb-3">
                    <label for="unit_name" class="form-label">اسم الكتيبة</label>
                    <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                </div>
                <div class="mb-3">
                    <label for="regiment_id" class="form-label">اختر لواء</label>
                    <select class="form-select" id="regiment_id" name="regiment_id" required>
                        <option selected></option>
                        <?php
                        $result = $conn->query("SELECT * FROM regiments");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['regiment_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="add_unit">
                <button type="submit" class="btn btn-primary">إضافة كتيبة</button>
            </form>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-4">
            <h4>تعديل الفرقة</h4>
            <form action="edit_division.php" method="POST">
                <div class="mb-3">
                    <label for="edit_division_id" class="form-label">اختر فرقة</label>
                    <select class="form-select" id="edit_division_id" name="division_id" required>
                        <option selected></option>
                        <?php
                        $result = $conn->query("SELECT * FROM divisions");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['division_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_division_name" class="form-label">اسم الفرقة الجديد</label>
                    <input type="text" class="form-control" id="new_division_name" name="new_division_name" required>
                </div>
                <button type="submit" class="btn btn-warning">تعديل الفرقة</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>تعديل اللواء</h4>
            <form action="edit_regiment.php" method="POST">
                <div class="mb-3">
                    <label for="edit_regiment_id" class="form-label">اختر لواء</label>
                    <select class="form-select" id="edit_regiment_id" name="regiment_id" required>
                        <option selected></option>
                        <?php
                        $result = $conn->query("SELECT * FROM regiments");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['regiment_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_regiment_name" class="form-label">اسم اللواء الجديد</label>
                    <input type="text" class="form-control" id="new_regiment_name" name="new_regiment_name" required>
                </div>
                <button type="submit" class="btn btn-warning">تعديل اللواء</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>تعديل كتيبة</h4>
            <form action="edit_unit.php" method="POST">
                <div class="mb-3">
                    <label for="edit_unit_id" class="form-label">اختر كتيبة</label>
                    <select class="form-select" id="edit_unit_id" name="unit_id" required>
                        <option selected></option>
                        <?php
                        $result = $conn->query("SELECT * FROM units");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['unit_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_unit_name" class="form-label">اسم الكتيبة الجديد</label>
                    <input type="text" class="form-control" id="new_unit_name" name="new_unit_name" required>
                </div>
                <button type="submit" class="btn btn-warning">تعديل كتيبة</button>
            </form>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>