<?php
session_start();
include('includes/config.php');
include('includes/provenance-log.php');

if(strlen($_SESSION['alogin']) == 0){
    header('location: index.php');
    exit;
}

// Handle deletion
if(isset($_GET['del']) && isset($_GET['id'])){
    $id = intval($_GET['id']);

    // Get old data before delete
    $stmt = $dbh->prepare("SELECT * FROM tblequipment WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete
    $sql = "DELETE FROM tblequipment WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();

    // Log Provenance
    logProvenance($_SESSION['alogin'], 'delete_equipment', 'tblequipment', $id, json_encode($oldData), '{}');

    $msg = "Equipment deleted successfully!";
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Manage Equipment</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Manage Equipment</h2>
    <?php if(isset($msg)){ ?><p style="color:green;"><?php echo $msg; ?></p><?php } ?>
    
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>#</th>
            <th>Equipment Name</th>
            <th>Category</th>
            <th>Cost/Day</th>
            <th>Condition</th>
            <th>Image</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT * FROM tblequipment ORDER BY id DESC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        foreach($results as $result){
        ?>
        <tr>
            <td><?php echo htmlentities($cnt); ?></td>
            <td><?php echo htmlentities($result->equipment_name); ?></td>
            <td><?php echo htmlentities($result->equipment_category); ?></td>
            <td><?php echo htmlentities($result->cost_per_day); ?></td>
            <td><?php echo htmlentities($result->equipment_condition); ?></td>
            <td><img src="img/equipment/<?php echo htmlentities($result->equipment_image); ?>" width="80"></td>
            <td>
                <a href="edit-equipment.php?id=<?php echo $result->id; ?>">Edit</a> |
                <a href="manage-equipment.php?del=1&id=<?php echo $result->id; ?>" onclick="return confirm('Delete this equipment?');">Delete</a>
            </td>
        </tr>
        <?php $cnt++; } ?>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
