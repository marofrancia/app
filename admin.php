<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Page</title>
</head>
<body>
  <h1>Welcome to the Admin Page</h1>
  <p>You have access to special features as an admin.</p>
</body>
</html>