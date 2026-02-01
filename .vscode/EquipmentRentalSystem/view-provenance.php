<?php
session_start();
include('includes/config.php');


$sql = "SELECT * FROM provenance_log ORDER BY timestamp DESC";
$query = $dbh->prepare($sql);
$query->execute();
$logs = $query->fetchAll(PDO::FETCH_OBJ);
foreach ($logs as $log) {
    echo "<hr><strong>Action:</strong> $log->action_type<br>";
    echo "<strong>User:</strong> $log->user_id<br>";
    echo "<strong>Table:</strong> $log->table_name<br>";
    echo "<strong>Record ID:</strong> $log->record_id<br>";
    echo "<strong>Old Data:</strong> $log->old_data<br>";
    echo "<strong>New Data:</strong> $log->new_data<br>";
    echo "<strong>Time:</strong> $log->timestamp<br>";
}

// Require admin login
if(strlen($_SESSION['alogin']) == 0){
    header('location: index.php');
    exit;
}
?>



<!DOCTYPE HTML>
<html>
<head>
    <title>Provenance Log Viewer</title>
    <style>
        td pre {
            white-space: pre-wrap;
            max-width: 400px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Data Provenance Log</h2>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Action Type</th>
            <th>Table</th>
            <th>Record ID</th>
            <th>Timestamp</th>
            <th>Old Data</th>
            <th>New Data</th>
        </tr>

        <?php
        $sql = "SELECT * FROM provenance_log ORDER BY id DESC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $logs = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        foreach($logs as $log){
        ?>
        <tr>
            <td><?php echo $cnt++; ?></td>
            <td><?php echo htmlentities($log->user_id); ?></td>
            <td><?php echo htmlentities($log->action_type); ?></td>
            <td><?php echo htmlentities($log->table_name); ?></td>
            <td><?php echo htmlentities($log->record_id); ?></td>
            <td><?php echo htmlentities($log->timestamp); ?></td>
            <td><pre><?php echo htmlentities($log->old_data); ?></pre></td>
            <td><pre><?php echo htmlentities($log->new_data); ?></pre></td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
