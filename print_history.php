<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'A') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";

// الحصول على الرقم المسلسل من الرابط
if (!isset($_GET['serial_number'])) {
    echo "رقم المسلسل غير متوفر.";
    exit();
}

$serial_number = $_GET['serial_number'];

// جلب تاريخ الجهاز بناءً على الرقم المسلسل
$query = "SELECT *
            FROM device_history dh
            INNER JOIN devices d ON dh.serial_number = d.serial_number
            INNER JOIN devices_list dl ON d.device_id = dl.id
            INNER JOIN units u ON d.unit_id = u.id
            INNER JOIN regiments r ON r.id = u.regiment_id
            INNER JOIN divisions dv ON dv.id = r.division_id
            INNER JOIN department_list dep_list ON dep_list.id = dh.department_id
            WHERE dh.serial_number = ?
            ORDER BY dh.history_id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $serial_number);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

if (empty($history)) {
    echo "لا يوجد تاريخ لهذا الجهاز.";
    exit();
}
?>

<?php include "partials/header.php"; ?>

<!-- CSS for Printing -->
<style>
    @media print {
        .no-print {
            display: none;
        }

        .footer-print {
            position: fixed;
            bottom: 10px;
            left: 10px;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }

        .signature-space {
            margin-top: 5px;
            height: 40px;
            /* Space for signing */
        }
    }
</style>

<div class="container mt-5" dir="rtl">
    <h4 class="mt-5">سجل الجهاز: <?php echo htmlspecialchars($serial_number); ?></h4>

    <!-- Display device details before the table with wrapping -->
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
        <div><strong>اسم الجهاز:</strong> <?php echo htmlspecialchars($history[0]['device_name']); ?></div>
        <div><strong>القسم المختص:</strong> <?php echo htmlspecialchars($history[0]['department_name']); ?></div> <!-- Added department name here -->
        <div><strong>رقم المسلسل:</strong> <?php echo htmlspecialchars($history[0]['serial_number']); ?></div>
        <div><strong>نوع رقم المسلسل:</strong> <?php echo htmlspecialchars($history[0]['serial_number_type'] === "factory" ? "مصنع" : "يدوي"); ?></div>
        <div><strong>اسم الفرقة:</strong> <?php echo htmlspecialchars($history[0]['division_name']); ?></div>
        <div><strong>اسم اللواء:</strong> <?php echo htmlspecialchars($history[0]['regiment_name']); ?></div>
        <div><strong>اسم الكتيبة:</strong> <?php echo htmlspecialchars($history[0]['unit_name']); ?></div>
    </div>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>رقم اذن الشغل</th>
                <th>تاريخ الدخول</th>
                <th>تاريخ التصليح</th>
                <th>نوع العطل</th>
                <th>قطع الغيار المستخدمة</th>
                <th>من قام بالتصليح</th>
                <th>تاريخ الخروج</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $device): ?>
                <tr>
                    <td><?php echo htmlspecialchars($device['operation_permission']); ?></td>
                    <td><?php echo htmlspecialchars($device['entry_date']); ?></td>
                    <td><?php echo htmlspecialchars($device['fix_date']); ?></td>
                    <td><?php echo htmlspecialchars($device['fault_type']); ?></td>
                    <td><?php echo htmlspecialchars($device['tools_used']); ?></td>
                    <td><?php echo htmlspecialchars($device['who_fixed']); ?></td>
                    <td><?php echo htmlspecialchars($device['exit_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button class="btn btn-primary no-print" onclick="window.print();">طباعة</button>
</div>

<!-- Footer that will show in the printed version -->
<div class="footer-print">
    <div>مخــالصــه من ورشه الاشــارة الفـرعيه رقم ٤</div>
    <div> (...................) امضــاء قــائــد الورشة</div>
</div>

<?php include "partials/footer.php"; ?>
