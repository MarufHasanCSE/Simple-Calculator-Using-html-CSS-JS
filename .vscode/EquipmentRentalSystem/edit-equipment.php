<?php
session_start();
include('includes/config.php');
include('includes/provenance-log.php');

if(strlen($_SESSION['alogin']) == 0){
    header('location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch current data
$sql = "SELECT * FROM tblequipment WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id);
$query->execute();
$equipment = $query->fetch(PDO::FETCH_ASSOC);

if(!$equipment){
    echo "Invalid Equipment ID.";
    exit;
}

if(isset($_POST['submit'])){
    $equipment_name = $_POST['equipment_name'];
    $equipment_category = $_POST['equipment_category'];
    $equipment_description = $_POST['equipment_description'];
    $serial_number = $_POST['serial_number'];
    $condition = $_POST['condition'];
    $cost_per_day = $_POST['cost_per_day'];

    $image = $equipment['equipment_image']; // default to existing image
    if(!empty($_FILES["img"]["name"])){
        $image = $_FILES["img"]["name"];
        move_uploaded_file($_FILES["img"]["tmp_name"], "img/equipment/".$image);
    }

    $sql = "UPDATE tblequipment SET 
            equipment_name = :equipment_name,
            equipment_category = :equipment_category,
            equipment_description = :equipment_description,
            serial_number = :serial_number,
            equipment_condition = :condition,
            cost_per_day = :cost_per_day,
            equipment_image = :image
            WHERE id = :id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':equipment_name', $equipment_name);
    $query->bindParam(':equipment_category', $equipment_category);
    $query->bindParam(':equipment_description', $equipment_description);
    $query->bindParam(':serial_number', $serial_number);
    $query->bindParam(':condition', $condition);
    $query->bindParam(':cost_per_day', $cost_per_day);
    $query->bindParam(':image', $image);
    $query->bindParam(':id', $id);
    $query->execute();

    // Provenance log
    logProvenance($_SESSION['alogin'], 'edit_equipment', 'tblequipment', $id, json_encode($equipment), json_encode($_POST));

    $msg = "Equipment updated successfully!";
    // Refresh current data
    header("Location: edit-equipment.php?id=$id&updated=1");
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Edit Equipment</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Edit Equipment</h2>
    <?php if(isset($_GET['updated'])){ ?><p style="color:green;">Equipment updated successfully!</p><?php } ?>
    <form method="post" enctype="multipart/form-data">
        <label>Equipment Name:</label>
        <input type="text" name="equipment_name" value="<?php echo htmlentities($equipment['equipment_name']); ?>" required>

        <label>Category:</label>
        <input type="text" name="equipment_category" value="<?php echo htmlentities($equipment['equipment_category']); ?>" required>

        <label>Description:</label>
        <textarea name="equipment_description" required><?php echo htmlentities($equipment['equipment_description']); ?></textarea>

        <label>Serial Number:</label>
        <input type="text" name="serial_number" value="<?php echo htmlentities($equipment['serial_number']); ?>" required>

        <label>Condition:</label>
        <input type="text" name="condition" value="<?php echo htmlentities($equipment['equipment_condition']); ?>" required>

        <label>Cost Per Day:</label>
        <input type="number" name="cost_per_day" value="<?php echo htmlentities($equipment['cost_per_day']); ?>" required>

        <label>Current Image:</label><br>
        <img src="img/equipment/<?php echo $equipment['equipment_image']; ?>" width="150"><br><br>

        <label>Upload New Image (optional):</label>
        <input type="file" name="img" accept="image/*">

        <button type="submit" name="submit">Update Equipment</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
