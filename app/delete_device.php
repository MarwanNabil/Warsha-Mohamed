<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = $_POST['serial_number'];

    $query = "DELETE FROM devices WHERE serial_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $serial_number);

    if ($stmt->execute()) {
        echo "تم حذف الجهاز بنجاح";
    } else {
        echo "حدث خطأ أثناء حذف الجهاز";
    }

    $stmt->close();
    $conn->close();
}
?>
