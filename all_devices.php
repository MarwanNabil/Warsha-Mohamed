<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'A') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";

$devices = [];

$query = "SELECT *
            FROM devices d
            INNER JOIN units u ON d.unit_id = u.id
            INNER JOIN regiments r ON r.id = u.regiment_id
            INNER JOIN divisions dv ON dv.id = r.division_id
            INNER JOIN device_history dh ON d.serial_number = dh.serial_number
            INNER JOIN devices_list dl ON dl.id = d.device_id
            INNER JOIN department_list dep_list ON dep_list.id = dh.department_id
            ORDER BY dh.serial_number ASC, dh.history_id ASC";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $devices[] = $row;
}

// Tracking previously displayed serial numbers
$displayedSerialNumbers = [];
?>

<?php
include "partials/header.php";
include "partials/navBar.php";
?>

<div class="container mt-5" dir="rtl">

    <h3 class="mt-5">كل الأجهزة</h3>
    <table class="table table-bordered table-responsive mt-3">
        <thead>
            <tr>
                <th>اسم الجهاز</th>
                <th>القسم المختص</th>
                <th>رقم المسلسل</th>
                <th>نوع رقم المسلسل</th>
                <th>رقم اذن الشغل</th>
                <th>اسم الفرقة</th>
                <th>اسم اللواء</th>
                <th>اسم الكتيبة</th>
                <th>تاريخ الدخول</th>
                <th>تاريخ التصليح</th>
                <th>نوع العطل</th>
                <th>قطع الغيار المستخدمة</th>
                <th>من قام بالتصليح</th>
                <th>تاريخ الخروج</th>
                <th>الإجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($devices as $device): ?>
                <tr>
                    <td><?php echo htmlspecialchars($device['device_name']); ?></td>
                    <td><?php echo htmlspecialchars($device['department_name']); ?></td>
                    <td><?php echo htmlspecialchars($device['serial_number']); ?></td>
                    <td><?php echo htmlspecialchars($device['serial_number_type'] === "factory" ? "مصنع" : "يدوي"); ?></td>
                    <td><?php echo htmlspecialchars($device['operation_permission']); ?></td>
                    <td><?php echo htmlspecialchars($device['division_name']); ?></td>
                    <td><?php echo htmlspecialchars($device['regiment_name']); ?></td>
                    <td><?php echo htmlspecialchars($device['unit_name']); ?></td>
                    <td><?php echo htmlspecialchars($device['entry_date']); ?></td>
                    <td><?php echo htmlspecialchars($device['fix_date']); ?></td>
                    <td><?php echo htmlspecialchars($device['fault_type']); ?></td>
                    <td><?php echo htmlspecialchars($device['tools_used']); ?></td>
                    <td><?php echo htmlspecialchars($device['who_fixed']); ?></td>
                    <td><?php echo htmlspecialchars($device['exit_date']); ?></td>
                    <td>
                        <?php if (isset($device['exit_date']) && $device['exit_date'] !== null): ?>
                            <!-- Disabled Edit Button -->
                            <!-- <button class="btn btn-secondary btn-sm" disabled>تعديل</button> -->
                        <?php else: ?>
                            <!-- Active Edit Button -->
                            <a href="edit_device.php?history_id=<?php echo htmlspecialchars($device['history_id']); ?>" class="btn btn-primary btn-sm">تعديل</a>
                        <?php endif; ?>

                        <!-- Display the Print button only for the first serial number in the group -->
                        <?php if (!in_array($device['serial_number'], $displayedSerialNumbers)): ?>
                            <a href="print_history.php?serial_number=<?php echo htmlspecialchars($device['serial_number']); ?>" class="btn btn-warning btn-sm mt-1">طباعة</a>
                            <?php $displayedSerialNumbers[] = $device['serial_number']; ?>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
<?php include "partials/footer.php"; ?>