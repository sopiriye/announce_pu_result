<?php
// File: controllers/get_lga_total_result.php
// This controller calculates the total results per party for all polling units under a specific LGA.
// It is triggered when a user selects an LGA and submits the form.

require_once __DIR__ . '/../config/db.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lga_id'])) {
    $lga_id = $_POST['lga_id'];

    // Step 1: Get all polling unit IDs under the selected LGA
    $stmt = $pdo->prepare("SELECT uniqueid FROM polling_unit WHERE lga_id = ?");
    $stmt->execute([$lga_id]);
    $pollingUnits = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($pollingUnits)) {
        // Step 2: Build query to sum scores for these polling units
        $placeholders = implode(',', array_fill(0, count($pollingUnits), '?'));
        $sql = "SELECT party_abbreviation, SUM(party_score) AS total_score
                FROM announced_pu_results
                WHERE polling_unit_uniqueid IN ($placeholders)
                GROUP BY party_abbreviation";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($pollingUnits);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['success'] = true;
        $response['data'] = $results;
    } else {
        $response['message'] = 'No polling units found for the selected LGA.';
    }
} else {
    $response['message'] = 'Invalid request. Please select a valid LGA.';
}

// Return JSON response for use in AJAX or form logic
echo json_encode($response);