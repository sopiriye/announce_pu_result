<?php
// File: controllers/submit_result.php
// This controller handles POST submissions of results from the "Add Result" form.
// It is meant to be invoked separately (e.g., via AJAX) and returns JSON status.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/helpers.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['polling_unit_uniqueid'])) {
    $polling_unit_uniqueid = $_POST['polling_unit_uniqueid'];
    $entered_by_user = 'admin';
    $user_ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $date_entered = date('Y-m-d H:i:s');

    $parties = getAllParties($pdo);

    try {
        $pdo->beginTransaction();

        foreach ($parties as $party) {
            $party_abbreviation = $party['partyid'];
            $score_key = 'score_' . $party_abbreviation;
            $party_score = isset($_POST[$score_key]) ? (int)$_POST[$score_key] : 0;

            $stmt = $pdo->prepare("INSERT INTO announced_pu_results (
                polling_unit_uniqueid, party_abbreviation, party_score, entered_by_user, date_entered, user_ip_address
            ) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $polling_unit_uniqueid,
                $party_abbreviation,
                $party_score,
                $entered_by_user,
                $date_entered,
                $user_ip_address
            ]);
        }

        $pdo->commit();
        $response['success'] = true;
        $response['message'] = 'Result submitted successfully.';
    } catch (Exception $e) {
        $pdo->rollBack();
        $response['message'] = 'Submission failed: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request or missing polling unit ID.';
}

echo json_encode($response);