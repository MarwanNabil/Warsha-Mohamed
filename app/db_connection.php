<?php


$conn = new mysqli('localhost', 'root', '', 'device_system');
$conn->set_charset('utf8mb4'); 


if (!$conn) {
	echo "Connection failed!";
}