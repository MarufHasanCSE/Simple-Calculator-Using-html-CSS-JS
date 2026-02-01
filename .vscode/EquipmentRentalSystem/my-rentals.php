<?php
session_start();
include('includes/config.php');

// Require login
if(!isset($_SESSION['login'])){
    header('location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // stored during student login
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>My Rentals</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>My Rental Requests</h2>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>#</th>
            <th>Equipment</th>
            <th>Rental Dates</th>
            <th>Status</th>
            <th>Image</th>
        </tr>

        <?php
        $sql = "SELECT rr.*, eq.equipment_name, eq.equipment_image 
                FROM rental_requests rr
                JOIN tblequipment eq ON rr.equipment_id = eq.id
                WHERE rr.user_id = :user_id
                ORDER BY rr.id DESC";
        $query = $dbh->prepare($sql);
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        foreach($results as $row){
        ?>
        <tr>
            <td><?php echo $cnt++; ?></td>
            <td><?php echo htmlentities($row->equipment_name); ?></td>
            <td><?php echo htmlentities($row->rent_start); ?> → <?php echo htmlentities($row->rent_end); ?></td>
            <td><?php echo htmlentities($row->status); ?></td>
            <td><img src="img/equipment/<?php echo htmlentities($row->equipment_image); ?>" width="100"></td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
