// File: views/polling_unit_result.php
<?php
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
}

// Fetch polling units for the dropdown
\$pollingUnits = getAllPollingUnits(\$pdo);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>View Polling Unit Result</h2>
<form method="POST" action="">
    <label for="polling_unit_id">Select Polling Unit:</label>
    <select name="polling_unit_id" id="polling_unit_id" required>
        <option value="">-- Choose Polling Unit --</option>
        <?php foreach (\$pollingUnits as \$unit): ?>
            <option value="<?= htmlspecialchars(\$unit['uniqueid']) ?>">
                <?= htmlspecialchars(\$unit['polling_unit_name']) ?> (ID: <?= \$unit['uniqueid'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">View Result</button>
</form>

<?php if (\$error): ?>
    <p style="color:red;">
        <?= htmlspecialchars(\$error) ?>
    </p>
<?php endif; ?>

<?php if (!empty(\$results)): ?>
    <h3>Results for Polling Unit ID: <?= htmlspecialchars(\$polling_unit_id) ?></h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Party</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (\$results as \$row): ?>
                <tr>
                    <td><?= htmlspecialchars(\$row['party_abbreviation']) ?></td>
                    <td><?= htmlspecialchars(\$row['party_score']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>