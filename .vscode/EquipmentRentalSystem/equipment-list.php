<?php
session_start();
include('includes/config.php');

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Available Equipment</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Browse Available Equipment</h2>

    <div class="equipment-grid">
        <?php
        $sql = "SELECT * FROM tblequipment ORDER BY id DESC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        foreach($results as $item){
        ?>
        <div class="equipment-card" style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
            <img src="img/equipment/<?php echo htmlentities($item->equipment_image); ?>" width="250" height="150"><br>
            <strong><?php echo htmlentities($item->equipment_name); ?></strong><br>
            <em>Category:</em> <?php echo htmlentities($item->equipment_category); ?><br>
            <em>Condition:</em> <?php echo htmlentities($item->equipment_condition); ?><br>
            <em>Cost/Day:</em> ₹<?php echo htmlentities($item->cost_per_day); ?><br>
            <p><?php echo htmlentities(substr($item->equipment_description, 0, 120)); ?>...</p>
            <a href="rent-equipment.php?id=<?php echo $item->id; ?>">Rent Now</a>
        </div>
        <?php } ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
