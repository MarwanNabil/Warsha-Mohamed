<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'egra2at') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";

$showAll = false;
$devices = [];
$deviceHistory = [];

if (isset($_GET['id']) && $_GET['id'] == 'all') {
    $showAll = true;
    $query = "SELECT *
              FROM devices d 
              INNER JOIN units u ON d.unit_id = u.id 
              INNER JOIN device_history h ON d.serial_number = h.serial_number
              ORDER BY device_history.exit_date DESC, device_history.entry_date DESC";

    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['serial_number'])) {
    $serial_number = $conn->real_escape_string($_POST['serial_number']);

    // Fetch device details
    $deviceQuery = "SELECT *
                    FROM devices d
                    INNER JOIN units u ON d.unit_id = u.id
                    INNER JOIN regiments r ON r.id = u.regiment_id
                    INNER JOIN divisions dv ON dv.id = r.division_id
                    INNER JOIN devices_list dl ON d.device_id = dl.id
                    LEFT JOIN device_history dh ON dh.serial_number = d.serial_number
                    WHERE d.serial_number = '$serial_number' OR dh.operation_permission = '$serial_number'";
    $deviceResult = $conn->query($deviceQuery);

    if ($deviceResult && $deviceResult->num_rows > 0) {
        $device = $deviceResult->fetch_assoc();

        // Fetch all device history for the specific device
        $historyQuery = "SELECT * FROM device_history WHERE serial_number = '$serial_number' OR  operation_permission = '$serial_number' ORDER BY entry_date DESC";
        $historyResult = $conn->query($historyQuery);
        while ($historyRow = $historyResult->fetch_assoc()) {
            $deviceHistory[] = $historyRow;
        }
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

    <h2 class="text-center pb-4">بحث عن جهاز <i class="bi bi-search"></i></h2>

    <form action="search-device.php" method="POST" class="mb-5">
        <div class="mb-3">
            <input type="text" class="form-control" name="serial_number" placeholder=" أدخل رقم الجهاز التسلسلي او اذن الشغل" required>
        </div>
        <div class="px-4">
            <button type="submit" class="btn btn-primary w-100">بحث <i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="mb-5 text-center">
        <a href="all_devices.php" class="btn btn-primary">عرض جميع الأجهزة <i class="bi bi-list-ul"></i></a>
    </div>

    <?php if (isset($device)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">تفاصيل الجهاز</h5>
                <p class="card-text">اسم الجهاز: <?php echo htmlspecialchars($device['device_name']); ?></p>
                <p class="card-text">رقم المسلسل: <?php echo htmlspecialchars($device['serial_number']); ?></p>
                <p class="card-text">نوع رقم المسلسل: <?php echo htmlspecialchars($device['serial_number_type'] === "factory" ? "مصنع" : "يدوي"); ?></p>
                <p class="card-text">اسم الفرقة: <?php echo htmlspecialchars($device['division_name']); ?></p>
                <p class="card-text">اسم اللواء: <?php echo htmlspecialchars($device['regiment_name']); ?></p>
                <p class="card-text">اسم الكتيبة: <?php echo htmlspecialchars($device['unit_name']); ?></p>
                <?php if (!empty($deviceHistory)): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>تاريخ الدخول</th>
                                <th>تاريخ التصليح</th>
                                <th>نوع العطل</th>
                                <th>الأدوات المستخدمة</th>
                                <th>من قام بالتصليح</th>
                                <th>رقم أذن الشغل</th>
                                <th>تاريخ الخروج</th>
                                <th>تم المراجعة من قبل</th>
                                <th>الموافقة على التصليح</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deviceHistory as $history): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($history['entry_date']); ?></td>
                                    <td><?php echo htmlspecialchars($history['fix_date']); ?></td>
                                    <td><?php echo htmlspecialchars($history['fault_type']); ?></td>
                                    <td><?php echo htmlspecialchars($history['tools_used']); ?></td>
                                    <td><?php echo htmlspecialchars($history['who_fixed']); ?></td>
                                    <td><?php echo htmlspecialchars($history['operation_permission']); ?></td>
                                    <td><?php echo htmlspecialchars($history['exit_date'] ?? 'غير متوفر'); ?></td>
                                    <td><?php echo htmlspecialchars($history['reviewed_by']); ?></td>
                                    <td><?php echo htmlspecialchars($history['is_approved'] ? 'صالح' : 'غير صالح'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">لا توجد معلومات عن التصليح للجهاز.</div>
                <?php endif; ?>

                <?php if (!empty($deviceHistory) && empty($deviceHistory[0]['exit_date']) && !empty($deviceHistory[0]['fix_date'])): ?>
                    <div class="alert alert-warning fs-5" role="alert">تم الإصلاح ومازال بالكتيبة</div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exitModal">إضافة تاريخ الخروج <i class="bi bi-calendar-plus"></i></button>
                <?php elseif (!empty($deviceHistory) && $deviceHistory[0]['exit_date']): ?>
                    <div class="alert alert-success fs-5" role="alert">تم الإصلاح</div>
                    <div class="alert alert-info fs-5" role="alert">تم خروج هذا الجهاز بتاريخ : <?php echo htmlspecialchars(end($deviceHistory)['exit_date']); ?></div>
                <?php else: ?>
                    <div class="alert alert-danger fs-5" role="alert">لم يتم الإصلاح حتى الأن</div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif (isset($_POST['serial_number'])): ?>
        <div class="alert alert-warning" role="alert">
            الجهاز غير موجود
        </div>
    <?php endif; ?>


</div>

<!-- Modal -->
<div class="modal fade" id="exitModal" tabindex="-1" aria-labelledby="exitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exitModalLabel">إضافة تاريخ الخروج</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exitForm">
                    <div class="mb-3">
                        <label for="exit_date" class="form-label">تاريخ الخروج</label>
                        <input type="date" class="form-control" id="exit_date" name="exit_date" required>
                    </div>
                    <input type="hidden" id="serial_number" value="<?php echo isset($device) ? htmlspecialchars($device['serial_number']) : ''; ?>">
                    <button type="submit" class="btn btn-success"
                        <?php echo !$device['is_approved'] ? 'disabled' : ''; ?>>
                        حفظ <i class="bi bi-floppy"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('exitForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var serialNumber = document.getElementById('serial_number').value;
        var exitDate = document.getElementById('exit_date').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/submit_exit.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert(xhr.responseText);
                location.reload();
            }
        };
        xhr.send('serial_number=' + encodeURIComponent(serialNumber) + '&exit_date=' + encodeURIComponent(exitDate));
    });

    // Function to delete device
    document.querySelectorAll('.delete-device').forEach(function(button) {
        button.addEventListener('click', function() {
            var serialNumber = this.getAttribute('data-serial-number');
            if (confirm('هل أنت متأكد أنك تريد حذف هذا الجهاز؟')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'app/delete_device.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                        location.reload();
                    }
                };
                xhr.send('serial_number=' + encodeURIComponent(serialNumber));
            }
        });
    });
</script>

<?php include "partials/footer.php"; ?>