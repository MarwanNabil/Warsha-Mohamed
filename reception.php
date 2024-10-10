<?php
session_start();


if (!isset($_SESSION['username']) || $_SESSION['role'] != 'A') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $device_id = $_POST['device_id'];
    $unit_id = $_POST['unit_id'];
    $operation_permission = $_POST['operation_permission'];
    $serial_number = $_POST['serial_number'];
    $entry_date = $_POST['entry_date'];
    $serial_number_type = $_POST['serial_number_type'];
    $department_id = $_POST['department_id'];

    $result = $conn->query("SELECT count(*) as cnt FROM device_history WHERE operation_permission = '$operation_permission'");
    $row = $result->fetch_assoc();
    $is_repeated_operation_premission_number = $row['cnt'] > 0;

    if (!$is_repeated_operation_premission_number) {
        $result = $conn->query("SELECT count(*) as cnt FROM devices WHERE serial_number = '$serial_number'");
        $row = $result->fetch_assoc();

        $is_device_exists = $row['cnt'] > 0;
        if (!$is_device_exists) {
            $query = "INSERT INTO devices (device_id, unit_id, serial_number,serial_number_type) 
              VALUES ('$device_id', '$unit_id', '$serial_number', '$serial_number_type')";
            $conn->query($query);
        }

        $last_history_for_this_device = $conn->query("SELECT * FROM device_history WHERE serial_number = '$serial_number' ORDER BY entry_date DESC LIMIT 1");
        if ($last_history_for_this_device->num_rows > 0 || !$is_device_exists) {
            $last_history_row = $last_history_for_this_device->fetch_assoc();

            if (!$is_device_exists || $last_history_row['exit_date'] != null) {
                // in case of this device exited before or first time in your unit.
                $history_query = "INSERT INTO device_history (serial_number, entry_date,operation_permission, department_id) 
                                VALUES ('$serial_number', '$entry_date','$operation_permission', '$department_id');";
                $conn->query($history_query);
                $message = "تم اضافة الجهاز.";
            } else {
                $message = "هذا الجهاز ماذال موجود بالوحدة.";
            }
        }
    } else {
        $message = "هذا رقم تشغيل مقرر.";
    }
}

?>

<?php
include "partials/header.php";
include "partials/navBar.php";
?>

<div class="container mt-5" dir="rtl">


    <?php if (isset($message)): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-4">استلام الجهاز <i class="bi bi-laptop"></i></h2>
    <form action="reception.php" method="POST">

        <div class="row mb-3">
            <div class="col-lg-10 col-md-12">
                <label for="device_id" class="form-label">اسم الجهاز</label>
                <select class="form-select" id="device_id" name="device_id" required>
                    <option selected></option>
                    <?php
                    // جلب أسماء الأجهزة من قاعدة البيانات
                    $query = "SELECT id, device_name FROM devices_list";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['device_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class=" col-lg-2 col-md-12 mt-4">
                <a class="btn btn-primary" type="button" href='add_device.php'>
                    إضافة جهاز <i class="bi bi-plus-square"></i>
                </a>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-lg-3 col-md-12">
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

            <div class="col-lg-3 col-md-12">
                <label for="regiment_id" class="form-label">اختر لواء</label>
                <select class="form-select" id="regiment_id" name="regiment_id" required>
                    <option selected></option>
                </select>
            </div>

            <div class="col-lg-3 col-md-12">
                <label for="unit_id" class="form-label">اختر كتيبة</label>
                <select class="form-select" id="unit_id" name="unit_id" required>
                    <option selected></option>
                </select>
            </div>
            <div class="col-lg-3 col-md-12 mt-4">
                <a class="btn btn-primary" type="button" href='add_unit.php'>
                    إضافة أو تعديل <i class="bi bi-plus-square"></i>
                </a>
            </div>
        </div>

        <div class="mb-3">
            <label for="operation_permission" class="form-label">رقم إذن الشغل </label>
            <input type="text" class="form-control" id="operation_permission" name="operation_permission" required>
        </div>


        <div class="mb-3">
            <label for="serial_number" class="form-label">رقم المسلسل</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number" required>
        </div>

        <div class="mb-3">
            <label for="serial_number_type">نوع رقم المسلسل:</label>
            <select class="form-select" id="serial_number_type" name="serial_number_type" required>
                <option selected></option>
                <option value="manual">يدوي</option>
                <option value="factory">مصنع</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-lg-10 col-md-12">
                <label for="department_id" class="form-label">القسم المختص بالتصليح</label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <option selected></option>
                    <?php
                    $query = "SELECT id, department_name FROM department_list";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['department_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class=" col-lg-2 col-md-12 mt-4">
                <a class="btn btn-primary" type="button" href='add_department.php'>
                    إضافة قسم <i class="bi bi-plus-square"></i>
                </a>
            </div>
        </div>

        <div class="mb-3">
            <label for="entry_date" class="form-label">تاريخ الدخول</label>
            <input type="date" class="form-control" id="entry_date" name="entry_date" required>
        </div>

        <div class="px-4">
            <button type="submit" class="btn btn-primary w-100">حفظ <i class="bi bi-floppy"></i></button>
        </div>
    </form>
</div>

<script>
    document.getElementById('division_id').addEventListener('change', function() {
        const divisionId = this.value;
        fetch(`get_regiments.php?division_id=${divisionId}`)
            .then(response => response.json())
            .then(data => {
                const regimentSelect = document.getElementById('regiment_id');
                regimentSelect.innerHTML = '<option selected></option>'; // Reset options
                data.forEach(regiment => {
                    regimentSelect.innerHTML += `<option value="${regiment.id}">${regiment.regiment_name}</option>`;
                });
            });
    });

    document.getElementById('regiment_id').addEventListener('change', function() {
        const regimentId = this.value;
        fetch(`get_units.php?regiment_id=${regimentId}`)
            .then(response => response.json())
            .then(data => {
                const unitSelect = document.getElementById('unit_id');
                unitSelect.innerHTML = '<option selected></option>'; // Reset options
                data.forEach(unit => {
                    unitSelect.innerHTML += `<option value="${unit.id}">${unit.unit_name}</option>`;
                });
            });
    });
</script>

<?php include "partials/footer.php"; ?>