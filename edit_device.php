<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'egra2at') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";

$history_id = $_GET['history_id'];

$query = "SELECT * FROM device_history dh
INNER JOIN department_list dep_list ON dh.department_id = dep_list.id
INNER JOIN devices d ON dh.serial_number = d.serial_number
INNER JOIN devices_list dev_list ON dev_list.id = d.device_id
 WHERE history_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $history_id); // Use parameter binding for safety
$stmt->execute();
$result = $stmt->get_result();
$device = $result->fetch_assoc();

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$has_fix_date = !empty($device['fix_date']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام البيانات من النموذج
    $entry_date = $_POST['entry_date'] ?? $device['entry_date'];
    $fix_date = $_POST['fix_date'] ?? $device['fix_date']; // Keep existing value if not provided
    $fault_type = $_POST['fault_type'] ?? $device['fault_type'];
    $exit_date = $_POST['exit_date'] ?? $device['exit_date'];
    $who_fixed = $_POST['who_fixed'] ?? $device['who_fixed'];
    $operation_permission = $_POST['operation_permission'] ?? $device['operation_permission'];
    $department_id = $_POST['department_id'] ?? $device['department_id'];
    $tools_used = $_POST['tools_used'] ?? $device['tools_used'];
    $is_approved = isset($_POST['is_approved']) ? 1 : 0; // Get checkbox value
    $reviewed_by = $_POST['reviewed_by'] ?? $device['reviewed_by']; // Get reviewed_by value

    // تحديث البيانات في جدول device_history
    $update_query = "UPDATE device_history 
                     SET entry_date = ?, fix_date = ?, fault_type = ?, exit_date = ?, who_fixed = ?, operation_permission = ?, department_id = ?, tools_used = ?, is_approved = ?, reviewed_by = ?
                     WHERE history_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssssssssssi', $entry_date, $fix_date, $fault_type, $exit_date, $who_fixed, $operation_permission, $department_id, $tools_used, $is_approved, $reviewed_by, $history_id);
    $stmt->execute();

    // إعادة التوجيه إلى صفحة الأجهزة بعد التعديل
    header('Location: all_devices.php');
    exit();
}
?>

<?php include "partials/header.php"; ?>
<?php include "partials/navBar.php"; ?>

<div class="container mt-5" dir="rtl">
    <h3>تعديل بيانات الجهاز</h3>

    <form method="POST" action="">
        <div class="form-group">
            <label for="device_name">اسم الجهاز</label>
            <select disabled class="form-select" id="device_id" name="device_id" required>
                <?php
                // جلب أسماء الأجهزة من قاعدة البيانات
                $query = "SELECT id, device_name FROM devices_list";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    $selected = ($row['id'] == $device['device_id']) ? "selected" : "";
                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['device_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="serial_number">رقم المسلسل</label>
            <input disabled type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo htmlspecialchars($device['serial_number']); ?>">
        </div>

        <div class="form-group">
            <label for="entry_date">تاريخ الدخول</label>
            <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo !empty($device['entry_date']) ? $device['entry_date'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="fix_date">تاريخ التصليح</label>
            <input
                type="date"
                name="fix_date"
                id="fix_date"
                class="form-control"
                value="<?php echo !empty($device['fix_date']) ? $device['fix_date'] : ''; ?>"
                <?php echo !$is_admin ? 'disabled' : ''; ?>>
        </div>

        <div class="form-group">
            <label for="exit_date">تاريخ الخروج</label>
            <input disabled type="date" name="exit_date" id="exit_date" class="form-control" value="<?php echo !empty($device['exit_date']) ? $device['exit_date'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="who_fixed">من قام بالتصليح</label>
            <input disabled type="text" name="who_fixed" id="who_fixed" class="form-control" value="<?php echo htmlspecialchars($device['who_fixed']); ?>">
        </div>

        <div class="form-group">
            <label for="operation_permission">رقم اذن الشغل</label>
            <input type="text" name="operation_permission" id="operation_permission" class="form-control" value="<?php echo htmlspecialchars($device['operation_permission']); ?>" required>
        </div>

        <div class="form-group">
            <label for="department_id">القسم المختص</label>
            <select disabled class="form-select" id="department_id" name="department_id" required>
                <?php
                // جلب أسماء الأقسام من قاعدة البيانات
                $query = "SELECT id, department_name FROM department_list";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    $selected = ($row['id'] == $device['department_id']) ? "selected" : "";
                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['department_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fault_type">نوع العطل</label>
            <input disabled type="text" name="fault_type" id="fault_type" class="form-control" value="<?php echo htmlspecialchars($device['fault_type']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tools_used">الأدوات المستخدمة</label>
            <input disabled type="text" name="tools_used" id="tools_used" class="form-control" value="<?php echo htmlspecialchars($device['tools_used']); ?>" required>
        </div>

        <?php if ($is_admin): ?>
            <div class="form-group">
                <label for="is_approved">الموافقة علي التصليح</label>
                <input
                    type="checkbox"
                    name="is_approved"
                    id="is_approved"
                    value="1"
                    <?php echo !empty($device['is_approved']) ? 'checked' : ''; ?>
                    <?php echo !$has_fix_date ? 'disabled' : ''; ?>
                    onchange="document.getElementById('reviewed_by').style.display = this.checked ? 'block' : 'none';">
            </div>

            <div class="form-group" id="reviewed_by" style="display: <?php echo !empty($device['is_approved']) ? 'block' : 'none'; ?>;">
                <label for="reviewed_by_input">تم المراجعة من قبل</label>
                <input type="text" name="reviewed_by" id="reviewed_by_input" class="form-control" value="<?php echo htmlspecialchars($device['reviewed_by']); ?>">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-success mt-3">تحديث </button>
    </form>
</div>

<?php include "partials/footer.php"; ?>