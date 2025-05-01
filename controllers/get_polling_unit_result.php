<?php
// File: controllers/get_polling_unit_result.php
// This controller is connected directly to the views/polling_unit_result.php form submission.
// It handles a basic POST request, fetches result for one polling unit, and outputs HTML.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/helpers.php';

\$results = [];
\$error = '';

if (\$_SERVER['REQUEST_METHOD'] === 'POST' && isset(\$_POST['polling_unit_id'])) {
    \$polling_unit_id = \$_POST['polling_unit_id'];

    \$stmt = \$pdo->prepare("SELECT party_abbreviation, party_score FROM announced_pu_results WHERE polling_unit_uniqueid = ?");
    \$stmt->execute([\$polling_unit_id]);
    \$results = \$stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty(\$results)) {
        \$error = "No results found for the selected polling unit.";
    }
} else {
    \$error = "Please select a valid polling unit.";
}

// This controller is typically included directly in polling_unit_result.php view
return [
    'results' => \$results,
    'error' => \$error,
    'polling_unit_id' => \$_POST['polling_unit_id'] ?? null
];