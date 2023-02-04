<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("location: index.php");
    exit;
}

if (isset($_POST['add'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Add audit data
    $loggedInUsername = mysqli_real_escape_string($conn, $_SESSION['username']);
    $action = "Admin - Create User";
    $target = $username;
    $date = date("Y-m-d H:i:s");
    
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    mysqli_query($conn, $sql);
    
    // Insert audit data
    $auditSql = "INSERT INTO audit (user, date, action, target) VALUES ('$loggedInUsername', '$date', '$action', '$target')";
    mysqli_query($conn, $auditSql);
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $user = $_SESSION['username'];
    $target = mysqli_query($conn, "SELECT username FROM users WHERE id = $id");
    $target = mysqli_fetch_assoc($target)['username'];
    $sql = "INSERT INTO audit (user, date, action, target) VALUES ('$user', NOW(), 'Admin - Delete User', '$target')";
    mysqli_query($conn, $sql);
    $sql = "DELETE FROM users WHERE id = $id";
    mysqli_query($conn, $sql);
    }

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $sql = "UPDATE users SET username='$username', password='$password', role='$role' WHERE id=$id";
    mysqli_query($conn, $sql);

    $loggedInUser = (isset($_SESSION['username']) ? $_SESSION['username'] : '');
    $target = $username;
    $action = "Admin - Edit User";
    $auditSql = "INSERT INTO audit (user, action, target, date) VALUES ('$loggedInUser', '$action', '$target', NOW())";
    mysqli_query($conn, $auditSql);
}


$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

?>

<div class="top">
<center>
<form method="post" action="manage-users.php">
    <label for="username">Username</label>
    <input type="text" name="username" placeholder="Username">
    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Password"><br>
    <label for="role">Role</label>
    <select name="role" style="width: 150px">
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select><br>
    <input type="submit" name="add" value="Add User">
</form>
</center>
</div>
   <div class="content">
   <h1>Manage Users</h1>
<div class="spacing">
<table>
    <tr>
        <th>Username</th>
        <th>Role</th>
        <th class="actions">Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td class="small"><?php echo $row['role']; ?></td>
            <td class="actions">
                <a href="#" id="edit" onclick="document.getElementById('edit-form-<?php echo $row['id']; ?>').style.display='block'">Edit</a>
                <a href="#" id="delete" onclick="return confirm('Are you sure you want to delete this user?') ? window.location.href='manage-users.php?delete=<?php echo $row['id']; ?>' : false;">Delete</a>               
                <a href="#" id="imitate" onclick="imitate(<?php echo $row['id']; ?>)">Imitate</a>
                <div class="editform">
                 <form id="edit-form-<?php echo $row['id']; ?>" style="display:none" method="post" action="manage-users.php">
                 <br>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="username" value="<?php echo $row['username']; ?>"><br>
                    <input type="password" name="password" placeholder="New Password"><br>
                    <select name="role">
                        <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?php echo $row['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    </select><br>
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
