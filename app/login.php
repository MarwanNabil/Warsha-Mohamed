<?php
session_start();

require "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];


$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    

    if ($user['role'] == 'A') {
        header('Location: ../reception.php');
    } else if ($user['role'] == 'B') {
        header('Location: ../repair.php');
    }
} else {
    echo "اسم المستخدم أو كلمة المرور غير صحيحة";
}
}

$conn->close();
?>
