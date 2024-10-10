<?php
session_start();

require "db_connection.php";

$is_good = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = $_POST['serial_number'];
    $exit_date = $_POST['exit_date'];

    $result = $conn->query("SELECT * FROM device_history WHERE serial_number = '$serial_number' ORDER BY entry_date DESC LIMIT 1");
    $row = $result->fetch_assoc();
    $triggered_history_id = $row['history_id'];

    if ($row['exit_date'] == null) {
        $query = "UPDATE device_history SET exit_date = '$exit_date' WHERE history_id = '$triggered_history_id'";

        if ($conn->query($query)) {
            echo "تم إضافة تاريخ الخروج بنجاح";
            $is_good = true;
        }
    }

    if ($is_good)
        echo "حدث خطأ أثناء إضافة تاريخ الخروج";
}
$conn->close();
