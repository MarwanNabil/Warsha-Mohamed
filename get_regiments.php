<?php
require "./app/db_connection.php";

$division_id = $_GET['division_id'];
$query = "SELECT * FROM regiments WHERE division_id = $division_id";
$result = $conn->query($query);
$regiments = [];
while ($row = $result->fetch_assoc()) {
    $regiments[] = $row;
}
echo json_encode($regiments);
