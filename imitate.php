<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("location: index.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id=$id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
?>