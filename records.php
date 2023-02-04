<?php
session_start();
include 'head.php';
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
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


  <div class="content">
  <h1>Records</h1>
    <div class="spacing">
    <div class="filter">
    <form method="post" action="records.php">
   <label for="start_date">Start Date</label>
   <input type="date" name="start_date" value="<?php echo $start_date; ?>">
   <label for="end_date">End Date</label>
   <input type="date" name="end_date" value="<?php echo $end_date; ?>">
   <label for="name">Name</label>
   <input type="text" name="name" value="<?php echo $name; ?>">
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

   <button onclick="downloadCSV()"><i class="fas fa-download"></i> Download CSV</button>
</form>
    </div>
    <table id="myTable">
    <tr>
        <th>Employee Name</th>
        <th class="small">Schedule In</th>
        <th class="small">Schedule Out</th>
        <th class="small">Shift Date <br> (d/m/y)</th>
        <th class="small">Time In</th>
        <th class="small">Time Out</th>
        
    </tr>
    <?php 
     $rownumber = 0;
    while ($row = mysqli_fetch_assoc($result)) { 
   
    $rownumber++;

    ?>
        
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_in'])); ?></td>
            <td class="small"><?php echo date('h:i a', strtotime($row['Schedule_out'])); ?></td>
            <td class="shift"><p><?php echo date("d/m/Y", strtotime($row['time_in'])); ?></p></td>
            <td class="timein"><p><?php echo date("g:i a", strtotime($row['time_in'])); ?></p></td>
            <td class="timeout"><p><?php echo date("g:i a", strtotime($row['time_out'])); ?></p></td>
        </tr>
    <?php } ?>
</table><br>
<div class="pagination">
    <?php
    if ($current_page > 1) {
        echo '<a href="records.php?page=' . ($current_page - 1) . '">Previous</a> ';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo '<span class="pagern">' . $i . '</span>';
        } else {
            echo '<a href="records.php?page=' . $i . '">' . $i . '</a> ';
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