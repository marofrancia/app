<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
//ADD
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $time_in = mysqli_real_escape_string($conn, $_POST['time_in']);
    $time_out = mysqli_real_escape_string($conn, $_POST['time_out']);
    if ($time_out <= $time_in) {
    echo "<div class='error'>Time out must be greater than time in.</div>";
    } else {
    $check_sql = "SELECT * FROM schedlogs WHERE (time_in <= '$time_out' AND time_out >= '$time_in') AND name='$name'";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
    echo "<div class='error'>Time overlap with existing entry for the same name, please try again with different time in/out.</div>";
    } else {
    $sql = "INSERT INTO schedlogs (name, time_in, time_out) VALUES ('$name', '$time_in', '$time_out')";
    mysqli_query($conn, $sql);
    $user = $_SESSION['username']; 
    $action = "Records - Add Log";
    $target = $name;
    $sql = "INSERT INTO audit (user, action, target) VALUES ('$user', '$action', '$target')";
    mysqli_query($conn, $sql);
    }
    }
    }
//DELETE OR ARCHIVE 
if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $sql = "SELECT name, time_in, time_out FROM schedlogs WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
        $time_in = $row['time_in'];
        $time_out = $row['time_out'];
        $user = (isset($_SESSION['username'])) ? $_SESSION['username'] : 'unknown';
        $sql = "INSERT INTO audit (user, action, target, date) VALUES ('$user', 'Records - Archive Log', '$name', now())";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM schedlogs WHERE id = $id";
        mysqli_query($conn, $sql);
        
        $sql = "INSERT INTO archives (name, time_in, time_out) VALUES ('$name', '$time_in', '$time_out')";
        mysqli_query($conn, $sql);
}
        
//EDIT
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $time_in = mysqli_real_escape_string($conn, $_POST['time_in']);
    $time_out = mysqli_real_escape_string($conn, $_POST['time_out']);
    if ($time_out <= $time_in) {
    echo "<div class='error'>Time out must be greater than time in.</div>";
    } else {
    $check_sql = "SELECT * FROM schedlogs WHERE (time_in <= '$time_out' AND time_out >= '$time_in') AND name='$name' AND id != $id";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
    echo "<div class='error'>Time overlap with existing entry for the same name, please try again with different time in/out.</div>";
    } else {
    $sql = "UPDATE schedlogs SET name='$name', time_in='$time_in', time_out='$time_out' WHERE id=$id";
    mysqli_query($conn, $sql);
    $user = $_SESSION['username']; //assuming username is stored in a session after a successful login
    $sql = "INSERT INTO audit (user, action, target, date) VALUES ('$user', 'Records - Edit Log', '$name', now())";
    mysqli_query($conn, $sql);
}
}
}
//READ
$sql = "SELECT * FROM schedlogs ORDER BY id desc";
$result = mysqli_query($conn, $sql);

//FILTER AND PAGINATION
$entries_per_page = 20;
$current_page = 1;
if (isset($_GET['page'])) {
    $current_page = intval($_GET['page']);
}
if (isset($_POST['entries_per_page'])) {
    $entries_per_page = intval($_POST['entries_per_page']);
}
$limit_start = ($current_page - 1) * $entries_per_page;
$start_date = '';
$end_date = '';
$name = '';

if (isset($_POST['filter'])) {
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = date("Y-m-d 23:59:59", strtotime(mysqli_real_escape_string($conn, $_POST['end_date'])));
}
if (isset($_POST['filtername'])) {
    $name = mysqli_real_escape_string($conn, $_POST['filtername']);
}

$sql = "SELECT * FROM schedlogs";
if ($start_date != '' && $end_date != '') {
    $sql .= " WHERE time_in BETWEEN '$start_date' AND '$end_date'";
}

if ($name != '') {
    if ($start_date != '' && $end_date != '') {
        $sql .= " AND name = '$name'";
    } else {
        $sql .= " WHERE name = '$name'";
    }
}

$sql .= " ORDER BY id desc";
$sql .= " LIMIT $limit_start, $entries_per_page";

$result = mysqli_query($conn, $sql);

$total_entries_sql = "SELECT COUNT(*) FROM schedlogs";
if ($start_date != '' && $end_date != '') {
    $total_entries_sql .= " WHERE time_in BETWEEN '$start_date' AND '$end_date'";
}

$total_entries_result = mysqli_query($conn, $total_entries_sql);
$total_entries = mysqli_fetch_array($total_entries_result)[0];
$total_pages = ceil($total_entries / $entries_per_page);

?>


<div class="top">
    <center>
    <form method="post" action="index.php">
    <label for="name"> Employee Name </label>
    <select name="name" style="width: 500px">
    <option disabled selected value> -- SELECT EMPLOYEE -- </option>
  <?php
    $sql = "SELECT Name FROM Employees";
    $result2 = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result2)) {
      echo "<option value='" . $row['Name'] . "'>" . $row['Name'] . "</option>";
    }
  ?>
</select><br>
    <label for="time_in">Time In</label>
    <input type="datetime-local" name="time_in">
    <label for="time_out">Time Out</label>
    <input type="datetime-local" name="time_out">
    <input type="submit" name="add" value="Add Entry">
</form>
    </center>
</div>

  <div class="content">
  <h1>Manage Entries</h1>
    <div class="spacing">
    <div class="filter">
    <form method="post" action="index.php">
   <label for="start_date">Start Date</label>
   <input type="date" name="start_date" value="<?php echo $start_date; ?>">
   <label for="end_date">End Date</label>
   <input type="date" name="end_date" value="<?php echo $end_date; ?>">
   <label for="filtername">Name</label>
   <input type="text" name="filtername" value="<?php echo $name; ?>">
   <label for="entries_per_page">Entries Per Page</label>
   <select name="entries_per_page" style="width:80px;">
      <?php
         for ($i = 10; $i <= 200; $i += 10) {
            echo '<option value="' . $i . '"';
            if ($entries_per_page == $i) {
               echo ' selected';
            }
            echo '>' . $i . '</option>';
         }
      ?>
   </select>
   <input type="submit" name="filter" value="Filter">
</form>
    </div>
    <table id="myTable">
    <tr>
        <th class="small" width="20px">#</th>
        <th class="small">Date Entered</th>
        <th>Employee Name</th>
        <th class="small">Schedule In</th>
        <th class="small">Schedule Out</th>
        <th class="small">Shift Date <br> (d/m/y)</th>
        <th class="small">Time In</th>
        <th class="small">Time Out</th>
        <th class="actions">Actions</th>

        
    </tr>

    <!-- ROW NUMBER -->
    <?php 
     $rownumber = 0;
    while ($row = mysqli_fetch_assoc($result)) { 
   
    $rownumber++;

    ?>
        
        <tr>
        <td class="number"> <?php echo $rownumber; ?> </td>
        <td class="small"><?php echo date("m-d", strtotime($row['date_added']));?></td>

            
            <td><?php echo $row['name']; ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_in'])); ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_out'])); ?></td>
            <td class="shift"><p><?php echo date("d/m/Y", strtotime($row['time_in'])); ?></p></td>
            <td class="timein"><p><?php echo date("g:i a", strtotime($row['time_in'])); ?></p></td>
            <td class="timeout"><p><?php echo date("g:i a", strtotime($row['time_out'])); ?></p></td>
            <td class="actions">
                <a href="#" id="edit" onclick="document.getElementById('edit-form-<?php echo $row['id']; ?>').style.display='block'">Edit</a>
                <a href="#" id="delete" onclick="return confirm('Are you sure you want to archive this entry?') ? window.location.href='index.php?delete=<?php echo $row['id']; ?>' : false;">Archive</a>
                <div class="editform">
                    <form id="edit-form-<?php echo $row['id']; ?>" style="display:none" method="post" action="index.php">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <select name="name">
                    <?php 
                     $sql = "SELECT name FROM employees ORDER by name";
                    $empresult = mysqli_query($conn, $sql);
                    while ($employee = mysqli_fetch_array($empresult)) {
                    $selected = ($employee['name'] == $row['name']) ? 'selected' : '';
                    echo "<option value='{$employee['name']}' $selected>{$employee['name']}</option>";
                    }
                    ?>
                    </select>
                    <input type="datetime-local" name="time_in"  value="<?php echo $row['time_in']; ?>">
                    <input type="datetime-local" name="time_out" value="<?php echo $row['time_out']; ?>">     
                    <input type="submit" name="edit" value="Save">
                </form>
                </div>
            </td>
        </tr>
    <?php } ?>
</table><br>
<div class="pagination">
    <?php
    if ($current_page > 1) {
        echo '<a href="index.php?page=' . ($current_page - 1) . '">Previous</a> ';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo '<span class="pagern">' . $i . '</span>';
        } else {
            echo '<a href="index.php?page=' . $i . '">' . $i . '</a> ';
        }
    }
    
    if ($current_page < $total_pages) {
        echo '<a href="index.php?page=' . ($current_page + 1) . '">Next</a>';
    }
    ?>
</div>    
    </div>
    
  </div>




  <?php include 'footer.php'; ?>