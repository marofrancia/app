<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("location: index.php");
    exit;
}
if (isset($_POST['delete'])) {
    $sql = "DELETE FROM audit";
    mysqli_query($conn, $sql);
}
$sql = "SELECT * FROM audit ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

?>


  <div class="content">
  <h1>AUDIT LOGS</h1>
    <div class="spacing">
    <div class="filter">
<form method="post">
    <input type="submit" value="Delete All Rows" name="delete" onclick="return confirm('Are you sure you want to delete all rows?');"/>
    </form>
</div>
    <table>
    <tr>

        <th class="big">User</th>
        <th>Date</th>
        <th>Action</th>
        <th>Target</th>
        
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { 
    
      ?>
    
        <tr>
        <td class="big"><?php echo $row['user']; ?></td>
        <td><?php echo $row['date']; ?></td>
        <td width="200px" style="text-align:left;">
    <script>
        var action = "<?php echo $row['action']; ?>";
        switch (action) {
            case "Admin - Create User":
                document.write("<span class='admin'>" + action + "</span>");
                break;
            case "Admin - Edit User":
                document.write("<span class='admin'>" + action + "</span>");
                break;
            case "Admin - Delete User":
                document.write("<span class='admin'>" + action + "</span>");
                break;
            case "Records - Create Employee":
                document.write("<span class='employee'>" + action + "</span>");
                break;
            case "Records - Edit Employee":
                document.write("<span class='employee'>" + action + "</span>");
                break;
            case "Records - Delete Employee":
                document.write("<span class='employee'>" + action + "</span>");
                break;
            case "Records - Add Log":
                document.write("<span class='records'>" + action + "</span>");
                break;
            case "Records - Archive Log":
                document.write("<span class='records'>" + action + "</span>");
                break;
            case "Records - Edit Log":
                document.write("<span class='records'>" + action + "</span>");
                break;
            default:
                document.write(action);
        }
    </script>
</td>
        <td><?php echo $row['target']; ?></td>

        </tr>
    <?php } ?>
</table>
    </div>
  </div>

  <?php include 'footer.php'; ?>