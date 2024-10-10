<?php
require "./app/db_connection.php";

$regiment_id = $_GET['regiment_id'];
$query = "SELECT * FROM units WHERE regiment_id = $regiment_id";
$result = $conn->query($query);
$units = [];
while ($row = $result->fetch_assoc()) {
    $units[] = $row;
}
echo json_encode($units);
