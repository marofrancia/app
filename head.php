<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link rel="shortcut icon" type="image/png" href="img/icon.png">
    <title>PayMaster</title>
    <script src="js/navi.js"></script>
    <script src="js/csv.js"></script>  
    <script src="js/imitate.js"></script>
</head>
<body>
<div class="topnav">
<div class="links">
  <?php 
    if (isset($_SESSION['username'])) {
    ?>
    <img src="img/icon.png">
    <a href="index.php">Home</a>
    <a href="records.php">Records</a>
    <a href="manage-employees.php">Manage Employees</a>
    <?php 
    }
    if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    ?>
    <a href="manage-users.php">User Management</a>
    <a href="archives.php">TRASH</a>
    <a href="audit.php">Audit Logs</a>
    <?php 
    } 
    ?>
</div>
<?php 
    if (isset($_SESSION['username'])) {
    ?>
  <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
   <?php }?>

</div>
