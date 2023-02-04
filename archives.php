<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("location: index.php");
    exit;
}

if (isset($_POST['delete'])) {
    $sql = "DELETE FROM archives";
    mysqli_query($conn, $sql);
}

$sql = "SELECT * FROM archives";
$result = mysqli_query($conn, $sql);

?>

  <div class="content">
  <h1>Trash (AUTO DELETES EVERY 14 DAYS)</h1>
    <div class="spacing">
<div class="filter">
<form method="post">
    <input type="submit" value="Delete All Rows" name="delete" onclick="return confirm('Are you sure you want to delete all rows?');"/>
    </form>
</div>
    <table>
    <tr>
        <th>Employee Name</th>
        <th class="small">Schedule In</th>
        <th class="small">Schedule Out</th>
        <th class="small">Shift Date <br> (m-d)</th>
        <th class="small">Time In</th>
        <th class="small">Time Out</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_in'])); ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_out'])); ?></td>
            <td class="shift"><p><?php echo date("m-d", strtotime($row['time_in'])); ?></p></td>
            <td class="timein"><p><?php echo date("m-d g:i a", strtotime($row['time_in'])); ?></p></td>
            <td class="timeout"><p><?php echo date("m-d g:i a", strtotime($row['time_out'])); ?></p></td>
        </tr>
    <?php } ?>
</table>
   
    </div>
  </div>
