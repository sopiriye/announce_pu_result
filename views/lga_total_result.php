<?php
// File: views/lga_total_result.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/helpers.php';

\$results = [];
\$error = '';

if (\$_SERVER['REQUEST_METHOD'] === 'POST' && isset(\$_POST['lga_id'])) {
    \$lga_id = \$_POST['lga_id'];

    // Fetch polling units under the selected LGA
    \$stmt = \$pdo->prepare("SELECT uniqueid FROM polling_unit WHERE lga_id = ?");
    \$stmt->execute([\$lga_id]);
    \$pollingUnits = \$stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty(\$pollingUnits)) {
        // Prepare placeholders and fetch summed results for all polling units
        \$placeholders = implode(',', array_fill(0, count(\$pollingUnits), '?'));
        \$sql = "SELECT party_abbreviation, SUM(party_score) AS total_score 
                FROM announced_pu_results 
                WHERE polling_unit_uniqueid IN ($placeholders)
                GROUP BY party_abbreviation";
        \$stmt = \$pdo->prepare(\$sql);
        \$stmt->execute(\$pollingUnits);
        \$results = \$stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        \$error = "No polling units found for the selected LGA.";
    }
}

// Fetch all LGAs for the dropdown
\$lgas = getAllLGAs(\$pdo);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>View Total Result by Local Government</h2>
<form method="POST" action="">
    <label for="lga_id">Select LGA:</label>
    <select name="lga_id" id="lga_id" required>
        <option value="">-- Choose LGA --</option>
        <?php foreach (\$lgas as \$lga): ?>
            <option value="<?= htmlspecialchars(\$lga['lga_id']) ?>">
                <?= htmlspecialchars(\$lga['lga_name']) ?> (ID: <?= \$lga['lga_id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">View Total</button>
</form>

<?php if (\$error): ?>
    <p style="color:red;">
        <?= htmlspecialchars(\$error) ?>
    </p>
<?php endif; ?>

<?php if (!empty(\$results)): ?>
    <h3>Total Results by Party</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Party</th>
                <th>Total Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (\$results as \$row): ?>
                <tr>
                    <td><?= htmlspecialchars(\$row['party_abbreviation']) ?></td>
                    <td><?= htmlspecialchars(\$row['total_score']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>