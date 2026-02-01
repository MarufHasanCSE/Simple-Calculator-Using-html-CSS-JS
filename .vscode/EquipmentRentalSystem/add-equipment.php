<?php
session_start();
include('includes/config.php');
include('includes/provenance-log.php');

if(strlen($_SESSION['alogin']) == 0){
    header('location: index.php');
    exit;
}

if(isset($_POST['submit'])){
    $equipment_name = $_POST['equipment_name'];
    $equipment_category = $_POST['equipment_category'];
    $equipment_description = $_POST['equipment_description'];
    $serial_number = $_POST['serial_number'];
    $condition = $_POST['condition'];
    $cost_per_day = $_POST['cost_per_day'];
    $equipment_img = $_FILES["img"]["name"];

    move_uploaded_file($_FILES["img"]["tmp_name"], "img/equipment/".$_FILES["img"]["name"]);

    $sql = "INSERT INTO tblequipment (equipment_name, equipment_category, equipment_description, serial_number, equipment_condition, cost_per_day, equipment_image) 
            VALUES (:equipment_name, :equipment_category, :equipment_description, :serial_number, :condition, :cost_per_day, :equipment_image)";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':equipment_name', $equipment_name);
    $query->bindParam(':equipment_category', $equipment_category);
    $query->bindParam(':equipment_description', $equipment_description);
    $query->bindParam(':serial_number', $serial_number);
    $query->bindParam(':condition', $condition);
    $query->bindParam(':cost_per_day', $cost_per_day);
    $query->bindParam(':equipment_image', $equipment_img);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();

    // Provenance Log
    logProvenance($_SESSION['alogin'], 'add_equipment', 'tblequipment', $lastInsertId, '{}', json_encode($_POST));

    $msg = "Equipment added successfully!";
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Admin - Add Equipment</title>
    <!-- Include CSS and JS as needed -->
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="container">
    <h2>Add New Equipment</h2>
    <?php if(isset($msg)){ ?><p style="color:green;"><?php echo $msg; ?></p><?php } ?>
    <form method="post" enctype="multipart/form-data">
        <label>Equipment Name:</label>
        <input type="text" name="equipment_name" required>

        <label>Category:</label>
        <input type="text" name="equipment_category" required>

        <label>Description:</label>
        <textarea name="equipment_description" required></textarea>

        <label>Serial Number:</label>
        <input type="text" name="serial_number" required>

        <label>Condition:</label>
        <input type="text" name="condition" required>

        <label>Cost Per Day:</label>
        <input type="number" name="cost_per_day" required>

        <label>Upload Image:</label>
        <input type="file" name="img" accept="image/*" required>

        <button type="submit" name="submit">Add Equipment</button>
    </form>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>
