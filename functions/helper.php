function getAllPollingUnits(PDO $pdo): array {
    $stmt = $pdo->query("SELECT uniqueid, polling_unit_name FROM polling_unit WHERE state_id = 25");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}