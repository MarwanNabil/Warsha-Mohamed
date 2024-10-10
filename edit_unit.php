<?php
session_start();
require "./app/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_id = $_POST['unit_id'];
    $new_unit_name = $_POST['new_unit_name'];

    $query = "UPDATE units SET unit_name = '$new_unit_name' WHERE id = '$unit_id'";
    if ($conn->query($query)) {
        $message = "تم تعديل الكتيبة بنجاح";
    } else {
        $message = "حدث خطأ أثناء تعديل الكتيبة";
    }
}
header('Location: add_unit.php');
exit();
