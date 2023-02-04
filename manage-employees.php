<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}


if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $schedule_in = mysqli_real_escape_string($conn, $_POST['schedule_in']);
    $schedule_out = mysqli_real_escape_string($conn, $_POST['schedule_out']);
    $sql = "INSERT INTO employees (name, schedule_in, schedule_out) VALUES ('$name', '$schedule_in', '$schedule_out')";
    mysqli_query($conn, $sql);
    
    $username = $_SESSION['username'];
    $action = "Records - Create Employee";
    $target = $name;
    $sql = "INSERT INTO audit (user, action, target) VALUES ('$username', '$action', '$target')";
    mysqli_query($conn, $sql);
    
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $username = $_SESSION['username'];
    $query = "SELECT name FROM employees WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $employee = mysqli_fetch_assoc($result);
    $name = $employee['name'];
    $sql = "DELETE FROM employees WHERE id = $id";
    mysqli_query($conn, $sql);
    $action = "Records - Delete Employee";
    $target = $name;
    $sql = "INSERT INTO audit (user, action, target) VALUES ('$username', '$action', '$target')";
    mysqli_query($conn, $sql);
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $schedule_in = mysqli_real_escape_string($conn, $_POST['schedule_in']);
    $schedule_out = mysqli_real_escape_string($conn, $_POST['schedule_out']);
    $sql = "UPDATE employees SET name='$name', schedule_in='$schedule_in', schedule_out='$schedule_out' WHERE id=$id";
    mysqli_query($conn, $sql);
    $log_user = $_SESSION['username'];
    $log_action = "Records - Edit Employee";
    $log_target = $name;
    $sql_log = "INSERT INTO audit (user, action, target) VALUES ('$log_user', '$log_action', '$log_target')";
    mysqli_query($conn, $sql_log);
}
    

$sql = "SELECT * FROM employees ORDER BY name";
$result = mysqli_query($conn, $sql);

?>

<div class="top">
<center>
<form method="post" action="manage-employees.php">
    <label for="name">Name</label>
    <input type="text" name="name" placeholder="Employee Name">
    <label for="schedule_in">Schedule In</label>
    <input type="time" name="schedule_in">
    <label for="schedule_out">Schedule Out</label>
    <input type="time" name="schedule_out">
    <br>
    <input type="submit" name="add" value="Add Employee">
</form>
</center>
</div>

  <div class="content">
  <h1>Manage Employee</h1>
   <div class="spacing">
   <table>
    <tr>
        <th>Name</th>
        <th class="small">Schedule In</th>
        <th class="small">Schedule Out</th>
        <th class="actions">Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['Name']; ?></td>
            <td class="small"><?php echo $row['Schedule_in']; ?></td>
            <td class="small"><?php echo $row['Schedule_out']; ?></td>
            <td class="actions">
                <a href="#" id="edit"onclick="document.getElementById('edit-form-<?php echo $row['id']; ?>').style.display='block'">Edit</a>
                <a href="#" id="delete"onclick="return confirm('Are you sure you want to delete this employee?') ? window.location.href='manage-employees.php?delete=<?php echo $row['id']; ?>' : false;">Delete</a>
                <div class="editform">
                <form id="edit-form-<?php echo $row['id']; ?>" style="display:none" method="post" action="manage-employees.php">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="name" value="<?php echo $row['Name']; ?>">
                    <input type="time" name="schedule_in"  value="<?php echo $row['Schedule_in']; ?>">
                    <input type="time" name="schedule_out" value="<?php echo $row['Schedule_out']; ?>">     

                    <input type="submit" name="edit" value="Save">
                </form>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>
   </div>
  </div>

  <?php include 'footer.php'; ?>