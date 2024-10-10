<?php
session_start();
require "./app/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $division_id = $_POST['division_id'];
    $new_division_name = $_POST['new_division_name'];

    $query = "UPDATE divisions SET division_name = '$new_division_name' WHERE id = '$division_id'";
    if ($conn->query($query)) {
        $message = "تم تعديل الفرقة بنجاح";
    } else {
        $message = "حدث خطأ أثناء تعديل الفرقة";
    }
}
header('Location: add_unit.php');
exit();
