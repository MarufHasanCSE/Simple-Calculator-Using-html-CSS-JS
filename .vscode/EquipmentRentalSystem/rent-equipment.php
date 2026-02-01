<?php
session_start();
include('includes/config.php');
include('includes/provenance-log.php');

// Assume student is logged in with session user ID
if(!isset($_SESSION['login'])){
    header('location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Store user_id in session during login
$equipment_id = intval($_GET['id']);

// Fetch equipment info
$sql = "SELECT * FROM tblequipment WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $equipment_id);
$query->execute();
$equipment = $query->fetch(PDO::FETCH_ASSOC);

if(!$equipment){
    echo "Invalid equipment selected.";
    exit;
}

// Handle form submit
if(isset($_POST['submit'])){
    $rent_start = $_POST['rent_start'];
    $rent_end = $_POST['rent_end'];

    $sql = "INSERT INTO rental_requests (user_id, equipment_id, rent_start, rent_end) 
            VALUES (:user_id, :equipment_id, :rent_start, :rent_end)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':equipment_id', $equipment_id);
    $query->bindParam(':rent_start', $rent_start);
    $query->bindParam(':rent_end', $rent_end);
    $query->execute();
    $lastId = $dbh->lastInsertId();

    // Log provenance
    $actionData = [
        'equipment_id' => $equipment_id,
        'rent_start' => $rent_start,
        'rent_end' => $rent_end
    ];
    logProvenance($user_id, 'rent_request', 'rental_requests', $lastId, '{}', json_encode($actionData));

    $msg = "Rental request submitted successfully!";
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Rent Equipment</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Rent: <?php echo htmlentities($equipment['equipment_name']); ?></h2>

    <?php if(isset($msg)){ ?><p style="color:green;"><?php echo $msg; ?></p><?php } ?>

    <form method="post">
        <p><strong>Category:</strong> <?php echo htmlentities($equipment['equipment_category']); ?></p>
        <p><strong>Condition:</strong> <?php echo htmlentities($equipment['equipment_condition']); ?></p>
        <p><strong>Cost Per Day:</strong> ₹<?php echo htmlentities($equipment['cost_per_day']); ?></p>
        <p><img src="img/equipment/<?php echo htmlentities($equipment['equipment_image']); ?>" width="300"></p>

        <label>Rental Start Date:</label>
        <input type="date" name="rent_start" required><br><br>

        <label>Rental End Date:</label>
        <input type="date" name="rent_end" required><br><br>

        <button type="submit" name="submit">Submit Request</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
