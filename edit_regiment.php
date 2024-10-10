<?php
session_start();
require "./app/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regiment_id = $_POST['regiment_id'];
    $new_regiment_name = $_POST['new_regiment_name'];

    $query = "UPDATE regiments SET regiment_name = '$new_regiment_name' WHERE id = '$regiment_id'";
    if ($conn->query($query)) {
        $message = "تم تعديل اللواء بنجاح";
    } else {
        $message = "حدث خطأ أثناء تعديل اللواء";
    }
}
header('Location: add_unit.php');
exit();
