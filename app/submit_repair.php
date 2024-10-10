<?php

require "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $serial_number = isset($_POST['serial_number']) ? $conn->real_escape_string($_POST['serial_number']) : '';
    $fix_date = isset($_POST['fix_date']) ? $conn->real_escape_string($_POST['fix_date']) : '';
    $fault_type = isset($_POST['fault_type']) ? $conn->real_escape_string($_POST['fault_type']) : '';
    $who_fixed = isset($_POST['who_fixed']) ? $conn->real_escape_string($_POST['who_fixed']) : '';
    $tools_used = isset($_POST['tools_used']) ? $conn->real_escape_string($_POST['tools_used']) : '';

    // Get the last history for this serial number
    //serial number is considered either serial_number or operation_permission
    $result = $conn->query("SELECT * FROM device_history WHERE serial_number = '$serial_number' OR operation_permission = '$serial_number' ORDER BY history_id DESC LIMIT 1");
    $row = $result->fetch_assoc();
    $message = $row;
    $triggered_history_id = $row['history_id'];

    // Prepare the SQL statement to insert into device_history
    $historyQuery = "UPDATE device_history SET fix_date = '$fix_date', who_fixed = '$who_fixed', tools_used = '$tools_used', fault_type='$fault_type' WHERE history_id = '$triggered_history_id'";

    if ($conn->query($historyQuery)) {
        // Execute the history insert
        echo "تم تعديل بيانات التصليح إلى تاريخ الجهاز بنجاح.";
    } else {
        echo "حدث خطأ في إعداد الاستعلام لتاريخ الجهاز: " . $conn->error;
    }
}

$conn->close();
