<?php
session_start();
include 'head.php';
require_once 'db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    if($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($_SESSION['role'] == 'admin') {
                header("location: index.php");
            } else {
                header("location: index.php");
            }
        } else {
            $_SESSION['message'] = "Username or password is incorrect";
        }
    }
    else {
        $_SESSION['message'] = "Username or password is incorrect";
    }
}
?>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    ?>

 <section class="center">
 <div class="login">
 <center>
 <form method="post" autocomplete="off" action="">
    <center><img src="img/logo.png"></center>
<table>
<tr>
    <td class="icon"><i class="fas fa-user"></i></td>
    <td><input type="text" name="username" placeholder="Username" required></td>
</tr>
<tr><td colspan="2"><hr></td></tr>
<tr>
    <td class="icon"><i class="fas fa-lock"></i></td>
    <td ><input type="password" name="password" placeholder="Password" required></td>
</tr>
<tr><td colspan="2" class="submit"><input type="submit" name="login" value="Login"></td></tr>
</table>
  </form>
 </center>
 </div>
 </section>


  <?php include 'footer.php'; ?>