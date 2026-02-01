function logProvenance($userId, $actionType, $tableName, $recordId, $oldData, $newData) {
    include("config.php");
    $sql = "INSERT INTO provenance_log (user_id, action_type, table_name, record_id, old_data, new_data) 
            VALUES (:user_id, :action_type, :table_name, :record_id, :old_data, :new_data)";
    $query = $dbh->prepare($sql);
    $query->execute([
        ':user_id' => $userId,
        ':action_type' => $actionType,
        ':table_name' => $tableName,
        ':record_id' => $recordId,
        ':old_data' => $oldData,
        ':new_data' => $newData
    ]);
}
