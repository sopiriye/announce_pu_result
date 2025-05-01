<?php
// File: views/add_result.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/helpers.php';

\$message = '';

// Fetch parties for input generation
\$parties = getAllParties(\$pdo);
\$pollingUnits = getAllPollingUnits(\$pdo);

if (\$_SERVER['REQUEST_METHOD'] === 'POST' && isset(\$_POST['polling_unit_uniqueid'])) {
    \$polling_unit_uniqueid = \$_POST['polling_unit_uniqueid'];
    \$entered_by_user = 'admin';
    \$user_ip_address = \$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    \$date_entered = date('Y-m-d H:i:s');

    try {
        \$pdo->beginTransaction();

        foreach (\$parties as \$party) {
            \$party_abbreviation = \$party['partyid'];
            \$party_score = isset(\$_POST['score_' . \$party_abbreviation]) ? (int)\$_POST['score_' . \$party_abbreviation] : 0;

            \$stmt = \$pdo->prepare("INSERT INTO announced_pu_results (
                polling_unit_uniqueid, party_abbreviation, party_score, entered_by_user, date_entered, user_ip_address)
                VALUES (?, ?, ?, ?, ?, ?)");

            \$stmt->execute([
                \$polling_unit_uniqueid,
                \$party_abbreviation,
                \$party_score,
                \$entered_by_user,
                \$date_entered,
                \$user_ip_address
            ]);
        }

        \$pdo->commit();
        \$message = "Result submitted successfully.";
    } catch (Exception \$e) {
        \$pdo->rollBack();
        \$message = "Error: " . \$e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Submit New Polling Unit Result</h2>
<p style="color:green;">
    <?= htmlspecialchars(\$message) ?>
</p>

<form method="POST" action="">
    <label for="polling_unit_uniqueid">Select Polling Unit:</label>
    <select name="polling_unit_uniqueid" id="polling_unit_uniqueid" required>
        <option value="">-- Choose Polling Unit --</option>
        <?php foreach (\$pollingUnits as \$unit): ?>
            <option value="<?= htmlspecialchars(\$unit['uniqueid']) ?>">
                <?= htmlspecialchars(\$unit['polling_unit_name']) ?> (ID: <?= \$unit['uniqueid'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <h3>Enter Party Scores:</h3>
    <?php foreach (\$parties as \$party): ?>
        <label for="score_<?= \$party['partyid'] ?>">
            <?= htmlspecialchars(\$party['partyid']) ?>:
        </label>
        <input type="number" name="score_<?= \$party['partyid'] ?>" id="score_<?= \$party['partyid'] ?>" required><br>
    <?php endforeach; ?>

    <br>
    <button type="submit">Submit Result</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>