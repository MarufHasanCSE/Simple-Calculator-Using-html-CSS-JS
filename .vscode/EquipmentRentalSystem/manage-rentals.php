<?php
session_start();
include('includes/config.php');
include('includes/provenance-log.php');

if(strlen($_SESSION['alogin']) == 0){
    header('location: index.php');
    exit;
}

// Approve/Decline logic
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $newStatus = $_GET['action'] === 'approve' ? 'Approved' : 'Declined';

    // Get old data
    $stmt = $dbh->prepare("SELECT * FROM rental_requests WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update
    $sql = "UPDATE rental_requests SET status = :status WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $newStatus);
    $query->bindParam(':id', $id);
    $query->execute();

    // Log provenance
    $newData = $oldData;
    $newData['status'] = $newStatus;
    logProvenance($_SESSION['alogin'], 'update_rental_status', 'rental_requests', $id, json_encode($oldData), json_encode($newData));

    $msg = "Rental request has been $newStatus.";
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Manage Rental Requests</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Rental Requests</h2>
    <?php if(isset($msg)){ ?><p style="color:green;"><?php echo $msg; ?></p><?php } ?>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Equipment</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT rr.*, eq.equipment_name 
                FROM rental_requests rr
                JOIN tblequipment eq ON rr.equipment_id = eq.id
                ORDER BY rr.id DESC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        foreach($results as $row){
        ?>
        <tr>
            <td><?php echo $cnt++; ?></td>
            <td><?php echo htmlentities($row->user_id); ?></td>
            <td><?php echo htmlentities($row->equipment_name); ?></td>
            <td><?php echo htmlentities($row->rent_start); ?></td>
            <td><?php echo htmlentities($row->rent_end); ?></td>
            <td><?php echo htmlentities($row->status); ?></td>
            <td>
                <?php if($row->status == 'Pending'){ ?>
                    <a href="?action=approve&id=<?php echo $row->id; ?>" onclick="return confirm('Approve this request?')">Approve</a> |
                    <a href="?action=decline&id=<?php echo $row->id; ?>" onclick="return confirm('Decline this request?')">Decline</a>
                <?php } else { echo '—'; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
