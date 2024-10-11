<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'tasle7') {
    header('Location: index.php');
    exit();
}

require "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_input = $conn->real_escape_string($_POST['search_input']); // Prevent SQL injection
    $current_logged_in_user_department_id = $_SESSION['department_id'];

    // Query to get device details and its history by serial number or operation permission
    $query = "SELECT *
              FROM devices d
              INNER JOIN units u ON d.unit_id = u.id
              INNER JOIN regiments r ON r.id = u.regiment_id
              INNER JOIN divisions dv ON dv.id = r.division_id
              INNER JOIN device_history dh ON d.serial_number = dh.serial_number
              INNER JOIN devices_list dl ON dl.id = d.device_id
              INNER JOIN department_list dep_lis ON dh.department_id = dep_lis.id 
              WHERE (d.serial_number = '$search_input' OR dh.operation_permission = '$search_input') AND dh.department_id = '$current_logged_in_user_department_id'
              ORDER BY dh.history_id DESC
              LIMIT 1";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $device = $result->fetch_assoc();

        $typeDevice = $device['serial_number_type'] === "factory" ? "مصنع" : "يدوي";

        echo "اسم الجهاز: " . htmlspecialchars($device['device_name']) . "<br>";
        echo "اسم القسم المختص: " . htmlspecialchars($device['department_name']) . "<br>";
        echo "رقم المسلسل: " . htmlspecialchars($device['serial_number']) . "<br>";
        echo "رقم اذن الشغل: " . htmlspecialchars($device['operation_permission']) . "<br>";
        echo "نوع رقم المسلسل: " . htmlspecialchars($typeDevice) . "<br>";
        echo "اسم الفرقة: " . htmlspecialchars($device['division_name']) . "<br>";
        echo "اسم اللواء: " . htmlspecialchars($device['regiment_name']) . "<br>";
        echo "اسم الكتيبة: " . htmlspecialchars($device['unit_name']) . "<br>";
        echo "تاريخ الدخول: " . htmlspecialchars($device['entry_date']) . "<br>";

        if ($device['fix_date']) {
            echo "تاريخ التصليح: " . htmlspecialchars($device['fix_date']) . "<br>";
            echo "من قام بالتصليح: " . htmlspecialchars($device['who_fixed']) . "<br>";
            echo "نوع العطل: " . htmlspecialchars($device['fault_type']) . "<br>";
            echo "الادوات المستخدمة: " . htmlspecialchars($device['tools_used']) . "<br>";
            if (empty($device['exit_date'])) {
                echo '<div class="alert alert-info mt-2" role="alert">لم يتم الخروج ومازال بالكتيبة</div>';
            } else {
                echo "تاريخ الخروج: " . htmlspecialchars($device['exit_date']) . "<br>";
                echo '<div class="alert alert-info mt-2" role="alert">تم الخروج من الكتيبة</div>';
            }
        } else {
            echo <<<EOD
            <div class="alert alert-warning mt-2" role="alert">لا توجد معلومات عن التصليح للجهاز.</div>
  
            <div class="mb-3">
                <label for="fix_date" class="form-label">تاريخ التصليح</label>
                <input type="date" class="form-control" id="fix_date" name="fix_date" required>
            </div>

            <div class="mb-3">
                <label for="fault_type" class="form-label">نوع العطل</label>
                <textarea class="form-control" id="fault_type" name="fault_type" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="tools_used" class="form-label">قطع الغيار المستخدمة</label>
                <textarea class="form-control" id="tools_used" name="tools_used" required></textarea>
            </div>

            <div class="mb-3">
                <label for="who_fixed" class="form-label">من قام بالتصليح</label>
                <input type="text" class="form-control" id="who_fixed" name="who_fixed" placeholder="أدخل اسم الشخص الذي قام بالتصليح" required>
            </div>

            <button type="button" class="btn btn-success" id="repairButton" onclick="submitRepair('$search_input')"><i class="fas fa-save"></i> حفظ</button>
        EOD;
        }
    } else {
        echo '<div class="alert alert-danger mt-2" role="alert">الجهاز غير موجود</div>';
    }
}

$conn->close();
?>

<script>
    function submitRepair(serialNumber) {
        var repairDate = document.getElementById('fix_date').value;
        var faultType = document.getElementById('fault_type').value;
        var whoFixed = document.getElementById('who_fixed').value;
        var toolsUsed = document.getElementById('tools_used').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/submit_repair.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert(xhr.responseText);
                location.reload();
            } else {
                alert('خطأ في تقديم تفاصيل التصليح.'); // Error handling
            }
        };
        xhr.send('serial_number=' + encodeURIComponent(serialNumber) + '&fix_date=' + encodeURIComponent(repairDate) + '&fault_type=' + encodeURIComponent(faultType) + '&who_fixed=' + encodeURIComponent(whoFixed) + '&tools_used=' + encodeURIComponent(toolsUsed));
    }
</script>