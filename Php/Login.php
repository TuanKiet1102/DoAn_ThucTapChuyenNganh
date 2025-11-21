<?php
$user = $_POST['username'];
$pass = $_POST['password'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nhanvien";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM dangnhap WHERE TenDangNhap='$user' AND MatKhau='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['CapQuyen'];

    if ($role == "Admin" || $role == "Quan Ly") {
        header("Location: ../Html/Admin/Admin.html");
    } elseif ($role == "TeamLeader" || $role == "Leader") {
        header("Location: ../Html/leader.html");
    } elseif ($role == "User" || $role == "Nhan Vien") {
        header("Location: ../Html/Admin/User.html");
    } else {
        header("Location: ../Html/login.html?error=role");
    }
} else {
    header("Location: ../Html/login.html?error=login");
}

$conn->close();
?>
